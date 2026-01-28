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

$where = [];
$params = [];

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

$sql = "
  SELECT
    a.asset_code, a.asset_name, a.brand, a.model, a.serial_number,
    a.purchase_date, a.asset_condition, a.status, a.notes,
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

$categories = $pdo->query("SELECT id, name FROM asset_categories ORDER BY name")->fetchAll();
$locations = $pdo->query("SELECT id, name FROM locations ORDER BY name")->fetchAll();

// audit report view (lightweight)
audit_log('REPORT_VIEW', 'assets', null, 'Viewed inventory report');

layout_header('Reports', 'reports');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Reports</h1>
    <div class="text-secondary">Inventory report (print / PDF-ready).</div>
  </div>
  <div class="d-flex gap-2">
    <?php
      $qs = $_GET;
      $printUrl = url('/reports/print.php') . (empty($qs) ? '' : ('?' . http_build_query($qs)));
      $excelUrl = url('/reports/export_excel.php') . (empty($qs) ? '' : ('?' . http_build_query($qs)));
    ?>
    <a class="btn btn-outline-success" href="<?php echo htmlspecialchars($excelUrl); ?>">
      <i class="bi bi-filetype-xlsx me-1"></i> Export Excel
    </a>
    <a class="btn btn-outline-secondary" target="_blank" href="<?php echo htmlspecialchars($printUrl); ?>">
      <i class="bi bi-printer me-1"></i> Print / Save PDF
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
          <option value="0">All</option>
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
          <option value="0">All</option>
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
          <option value="">All</option>
          <?php foreach (['Available','In Use','Maintenance','Lost'] as $s): ?>
            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo ($status === $s) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-outline-secondary" type="submit">
          <i class="bi bi-funnel me-1"></i> Apply Filters
        </button>
        <a class="btn btn-light border" href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>">
          Reset
        </a>
        <div class="ms-auto small text-secondary d-flex align-items-center">
          Showing <span class="fw-semibold ms-1"><?php echo count($assets); ?></span> results
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Code</th>
            <th>Asset</th>
            <th>Category</th>
            <th>Condition</th>
            <th>Status</th>
            <th>Location</th>
            <th>Serial</th>
            <th>Purchase</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$assets): ?>
            <tr><td colspan="9" class="text-center text-secondary py-4">No assets match your filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($assets as $a): ?>
            <tr>
              <td class="fw-semibold"><?php echo htmlspecialchars($a['asset_code']); ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($a['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars(trim(($a['brand'] ?? '') . ' ' . ($a['model'] ?? ''))); ?></div>
              </td>
              <td><?php echo htmlspecialchars($a['category_name']); ?></td>
              <td><?php echo htmlspecialchars($a['asset_condition']); ?></td>
              <td><?php echo htmlspecialchars($a['status']); ?></td>
              <td><?php echo htmlspecialchars($a['location_name']); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($a['serial_number'] ?: '-'); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($a['purchase_date'] ?: '-'); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($a['notes'] ?: '-'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="small text-secondary mt-2">
      Tip: Use your browser “Print” → “Save as PDF” for a PDF export on shared hosting.
    </div>
  </div>
</div>

<?php layout_footer(); ?>


