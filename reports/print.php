<?php

require_once __DIR__ . '/../includes/auth.php';
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

if ($category > 0) { $where[] = "a.category_id = :category"; $params[':category'] = $category; }
if ($location > 0) { $where[] = "a.location_id = :location"; $params[':location'] = $location; }
if ($status !== '') { $where[] = "a.status = :status"; $params[':status'] = $status; }
if ($q !== '') { $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR a.serial_number LIKE :q)"; $params[':q'] = '%' . $q . '%'; }

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
$sql .= " ORDER BY c.name, a.asset_name, a.asset_code LIMIT 2000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assets = $stmt->fetchAll();

audit_log('REPORT_PRINT', 'assets', null, 'Printed inventory report');

$title = 'Inventory Report';
$generatedAt = date('Y-m-d H:i');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print { display: none !important; }
      .table { font-size: 12px; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="bg-white">
  <div class="container-fluid py-3">
    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
      <div>
        <div class="fw-bold"><?php echo htmlspecialchars($title); ?></div>
        <div class="small text-secondary">GS Remera TSS • Generated: <?php echo htmlspecialchars($generatedAt); ?></div>
        <div class="small text-secondary">Total items: <?php echo count($assets); ?></div>
      </div>
      <div class="no-print d-flex gap-2">
        <button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/reports/index.php') . (!empty($_GET) ? ('?' . http_build_query($_GET)) : '')); ?>">
          Back
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
          <tr class="small text-secondary">
            <th style="width:110px;">Code</th>
            <th>Asset</th>
            <th style="width:120px;">Category</th>
            <th style="width:90px;">Condition</th>
            <th style="width:110px;">Status</th>
            <th style="width:130px;">Location</th>
            <th style="width:150px;">Serial</th>
            <th style="width:110px;">Purchase</th>
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
              <td class="small"><?php echo htmlspecialchars($a['serial_number'] ?: '-'); ?></td>
              <td class="small"><?php echo htmlspecialchars($a['purchase_date'] ?: '-'); ?></td>
              <td class="small"><?php echo htmlspecialchars($a['notes'] ?: '-'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>


