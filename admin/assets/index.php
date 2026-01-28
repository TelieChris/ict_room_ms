<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();

$pdo = db();

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$category = (int)($_GET['category'] ?? 0);

$where = [];
$params = [];

if ($q !== '') {
  // MySQL PDO with emulated prepares disabled cannot reuse the same named placeholder multiple times.
  $where[] = "(a.asset_code LIKE :q1 OR a.asset_name LIKE :q2 OR a.serial_number LIKE :q3)";
  $params[':q1'] = '%' . $q . '%';
  $params[':q2'] = '%' . $q . '%';
  $params[':q3'] = '%' . $q . '%';
}
if ($status !== '') {
  $where[] = "a.status = :status";
  $params[':status'] = $status;
}
if ($category > 0) {
  $where[] = "a.category_id = :cat";
  $params[':cat'] = $category;
}

$sql = "
  SELECT a.*, c.name AS category_name, l.name AS location_name
  FROM assets a
  JOIN asset_categories c ON c.id = a.category_id
  JOIN locations l ON l.id = a.location_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY a.id DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assets = $stmt->fetchAll();

$categories = $pdo->query("SELECT id, name FROM asset_categories ORDER BY name")->fetchAll();

layout_header('Assets', 'assets');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Assets</h1>
    <div class="text-secondary">Manage inventory, status, and details.</div>
  </div>
  <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/assets/create.php')); ?>">
    <i class="bi bi-plus-lg me-1"></i> Add Asset
  </a>
</div>

<div class="card table-card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-12 col-md-5">
        <label class="form-label small text-secondary">Search</label>
        <input class="form-control" name="q" placeholder="Asset code, name, serial..." value="<?php echo htmlspecialchars($q); ?>">
      </div>
      <div class="col-12 col-md-3">
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
      <div class="col-12 col-md-1 d-grid">
        <button class="btn btn-outline-secondary" type="submit">
          <i class="bi bi-funnel"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Code</th>
            <th>Asset</th>
            <th>Category</th>
            <th>Status</th>
            <th>Location</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$assets): ?>
            <tr><td colspan="6" class="text-center text-secondary py-4">No assets found.</td></tr>
          <?php endif; ?>
          <?php foreach ($assets as $a): ?>
            <tr>
              <td class="fw-semibold"><?php echo htmlspecialchars($a['asset_code']); ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($a['asset_name']); ?></div>
                <div class="small text-secondary">
                  <?php echo htmlspecialchars(trim(($a['brand'] ?? '') . ' ' . ($a['model'] ?? ''))); ?>
                  <?php if (!empty($a['serial_number'])): ?>
                    • S/N: <?php echo htmlspecialchars($a['serial_number']); ?>
                  <?php endif; ?>
                </div>
              </td>
              <td><?php echo htmlspecialchars($a['category_name']); ?></td>
              <td>
                <?php
                  $badge = 'secondary';
                  if ($a['status'] === 'Available') $badge = 'success';
                  elseif ($a['status'] === 'In Use') $badge = 'warning';
                  elseif ($a['status'] === 'Maintenance') $badge = 'danger';
                ?>
                <span class="badge text-bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($a['status']); ?></span>
              </td>
              <td><?php echo htmlspecialchars($a['location_name']); ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars(url('/admin/assets/edit.php')); ?>?id=<?php echo (int)$a['id']; ?>">
                  <i class="bi bi-pencil"></i>
                </a>
                <a class="btn btn-sm btn-outline-danger"
                   data-confirm="Delete this asset? This action cannot be undone."
                   href="<?php echo htmlspecialchars(url('/admin/assets/delete.php')); ?>?id=<?php echo (int)$a['id']; ?>">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php layout_footer(); ?>


