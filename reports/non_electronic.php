<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();

$category = (int)($_GET['category'] ?? 0);
$status = trim($_GET['status'] ?? '');
$location = (int)($_GET['location'] ?? 0);
$q = trim($_GET['q'] ?? '');

$sid = (int)$_SESSION['user']['school_id'];
$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$where = ["a.school_id = :sid"];
$params = [':sid' => $sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $where[] = "a.location_id = :assigned_lid";
    $params[':assigned_lid'] = $assigned_lid;
}

if ($category > 0) {
  $where[] = "a.category_id = :category";
  $params[':category'] = $category;
}
if ($location > 0) {
  $where[] = "a.location_id = :location";
  $params[':location'] = $location;
}
if ($status !== '') {
  $where[] = "a.status = :status";
  $params[':status'] = $status;
}
if ($q !== '') {
  $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR a.serial_number LIKE :q)";
  $params[':q'] = '%' . $q . '%';
}
$where[] = "c.type = 'Non-Electronic'";

$sql = "
  SELECT
    a.asset_code, a.asset_name, a.brand, a.model, a.serial_number,
    a.purchase_date, a.asset_condition, a.quantity,
    a.power_adapter, a.power_adapter_status,
    a.display_cable, a.display_cable_type, a.display_cable_status,
    a.qty_available, a.qty_in_use, a.qty_maintenance, a.qty_faulty, a.qty_lost,
    a.status, a.notes,
    c.name AS category_name,
    l.name AS location_name
  FROM assets a
  JOIN asset_categories c ON c.id = a.category_id
  JOIN locations l ON l.id = a.location_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY c.name, a.asset_name, a.asset_code LIMIT 1000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assets = $stmt->fetchAll();

$stmt_cat = $pdo->prepare("SELECT id, name FROM asset_categories WHERE school_id = ? AND type = 'Non-Electronic' ORDER BY name");
$stmt_cat->execute([$sid]);
$categories = $stmt_cat->fetchAll();

$stmt_loc = $pdo->prepare("SELECT id, name FROM locations WHERE school_id = ? ORDER BY name");
$stmt_loc->execute([$sid]);
$locations = $stmt_loc->fetchAll();

// Role-based report title logic
$reportIdentity = 'Asset Inventory';
if (is_super_admin()) {
    $reportIdentity = 'The system report';
} elseif (is_head_teacher()) {
    $reportIdentity = $_SESSION['user']['school_name'] ?? 'School Report';
} elseif (is_it_technician() && !empty($_SESSION['user']['location_id'])) {
    $stmt_header_loc = $pdo->prepare("SELECT name FROM locations WHERE id = ?");
    $stmt_header_loc->execute([$_SESSION['user']['location_id']]);
    $reportIdentity = $stmt_header_loc->fetchColumn() ?: 'ICT Lab Report';
}

$fullTitle = $reportIdentity . ' - Non-Electronic Assets';
layout_header($fullTitle, 'reports');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <a href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>" class="btn btn-sm btn-light border text-secondary"><i class="bi bi-arrow-left"></i> Back</a>
      <h1 class="h4 mb-0 text-primary fw-bold"><?php echo htmlspecialchars($reportIdentity); ?> <span class="text-secondary fw-normal">- Non-Electronic Assets</span></h1>
    </div>
    <div class="text-secondary">Inventory of furniture and other non-electronic items.</div>
  </div>
  <div class="d-flex gap-2">
    <?php
      $qs = $_GET;
      $printUrl = url('/reports/print_non_electronic.php') . (empty($qs) ? '' : ('?' . http_build_query($qs)));
    ?>
    <a class="btn btn-outline-secondary" target="_blank" href="<?php echo htmlspecialchars($printUrl); ?>">
      <i class="bi bi-printer me-1"></i> Print / PDF
    </a>
  </div>
</div>



<div class="card table-card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-12 col-md-4">
        <label class="form-label small text-secondary">Search</label>
        <input class="form-control" name="q" placeholder="Asset code, name, serial..." value="<?php echo htmlspecialchars($q); ?>">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label small text-secondary">Category</label>
        <select class="form-select" name="category">
          <option value="0">All Categories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo ($category === (int)$c['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label small text-secondary">Location</label>
        <select class="form-select" name="location">
          <option value="0">All Locations</option>
          <?php foreach ($locations as $l): ?>
            <option value="<?php echo (int)$l['id']; ?>" <?php echo ($location === (int)$l['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($l['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label small text-secondary">Status</label>
        <select class="form-select" name="status">
          <option value="">All Statuses</option>
          <?php foreach (['Available','In Use','Maintenance','Lost','Faulty'] as $s): ?>
            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo ($status === $s) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <a class="btn btn-light border" href="<?php echo htmlspecialchars(url('/reports/inventory.php')); ?>">
          Reset
        </a>
        <div class="ms-auto small text-secondary d-flex align-items-center">
          Showing <span class="fw-semibold ms-1"><?php echo count($assets); ?></span> entries
          <span class="mx-2 text-dark">|</span>
          Total Units: <span class="fw-bold text-dark ms-1">
            <?php 
              $totalUnits = 0;
              foreach ($assets as $a) {
                $breakdownSum = (int)($a['qty_available'] + $a['qty_in_use'] + $a['qty_maintenance'] + $a['qty_lost'] + $a['qty_faulty']);
                $totalUnits += ($breakdownSum > 0) ? $breakdownSum : (int)($a['quantity'] ?? 1);
              }
              echo $totalUnits;
            ?>
          </span>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle table-hover">
        <thead>
          <tr class="text-secondary small">
            <th>Code</th>
            <th>Asset</th>
            <th>Category</th>
            <th>Qty</th>
            <th>Condition</th>
            <th>Status</th>
            <th>Location</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$assets): ?>
            <tr><td colspan="8" class="text-center text-secondary py-5">No assets match your filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($assets as $a): ?>
            <tr>
              <td class="fw-bold text-primary"><?php echo htmlspecialchars($a['asset_code']); ?></td>
              <td>
                <div class="fw-bold"><?php echo htmlspecialchars($a['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars(trim(($a['brand'] ?? '') . ' ' . ($a['model'] ?? ''))); ?></div>
              </td>
              <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle"><?php echo htmlspecialchars($a['category_name']); ?></span></td>
              <td class="text-center">
                <?php $qty = (int)($a['quantity'] ?? 1); ?>
                <?php if ($qty > 1): ?>
                  <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold"><?php echo $qty; ?></span>
                <?php else: ?>
                  <span class="text-secondary small">1</span>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($a['asset_condition']); ?></td>
              <td>
                <?php
                if ($a['qty_available'] > 0 || $a['qty_in_use'] > 0 || $a['qty_maintenance'] > 0 || $a['qty_faulty'] > 0 || $a['qty_lost'] > 0) {
                  echo '<div class="d-flex flex-wrap gap-1">';
                  if ($a['qty_available'] > 0) echo '<span class="badge bg-success bg-opacity-10 text-success border border-success-subtle x-small" style="font-size:0.65rem;">Work: '.$a['qty_available'].'</span> ';
                  if ($a['qty_in_use'] > 0) echo '<span class="badge bg-info bg-opacity-10 text-info border border-info-subtle x-small" style="font-size:0.65rem;">Use: '.$a['qty_in_use'].'</span> ';
                  if ($a['qty_maintenance'] > 0) echo '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle x-small" style="font-size:0.65rem;">Maint: '.$a['qty_maintenance'].'</span> ';
                  if ($a['qty_faulty'] > 0) echo '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle x-small" style="font-size:0.65rem;">Dmg: '.$a['qty_faulty'].'</span> ';
                  if ($a['qty_lost'] > 0) echo '<span class="badge bg-dark bg-opacity-10 text-dark border border-dark-subtle x-small" style="font-size:0.65rem;">Lost: '.$a['qty_lost'].'</span> ';
                  echo '</div>';
                } else {
                  $sColor = 'secondary';
                  if ($a['status'] === 'Available') $sColor = 'success';
                  elseif ($a['status'] === 'In Use') $sColor = 'info';
                  elseif ($a['status'] === 'Maintenance') $sColor = 'warning';
                  elseif ($a['status'] === 'Faulty') $sColor = 'danger';
                  ?>
                  <span class="badge bg-<?php echo $sColor; ?> bg-opacity-10 text-<?php echo $sColor; ?> border border-<?php echo $sColor; ?>-subtle">
                    <?php echo htmlspecialchars($a['status']); ?>
                  </span>
                <?php } ?>
              </td>
              <td class="small"><?php echo htmlspecialchars($a['location_name']); ?></td>
              <td class="small text-secondary text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($a['notes'] ?: ''); ?>">
                <?php echo htmlspecialchars($a['notes'] ?: '-'); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
