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

$sid = (int)$_SESSION['user']['school_id'];
$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$where = ["a.school_id = :sid"];
$params = [':sid' => $sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $where[] = "a.location_id = :assigned_lid";
    $params[':assigned_lid'] = $assigned_lid;
}

if ($category > 0) { $where[] = "a.category_id = :category"; $params[':category'] = $category; }
if ($location > 0) { $where[] = "a.location_id = :location"; $params[':location'] = $location; }
if ($status !== '') { $where[] = "a.status = :status"; $params[':status'] = $status; }
if ($q !== '') { $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR a.serial_number LIKE :q)"; $params[':q'] = '%' . $q . '%'; }

$sql = "
  SELECT
    a.asset_code, a.asset_name, a.brand, a.model, a.serial_number,
    a.purchase_date, a.asset_condition, a.power_adapter, a.power_adapter_status,
    a.display_cable, a.display_cable_type, a.display_cable_status,
    a.status, a.notes,
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

$title = 'Asset Inventory Report';
$generatedAt = date('Y-m-d H:i');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title); ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background: #f8fafc;
      color: #0f172a;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .print-container {
      background: white;
      max-width: 1200px;
      margin: 2rem auto;
      padding: 3rem;
      border-radius: 1rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    @media print {
      @page { size: A4 landscape; margin: 10mm; }
      body { background: white !important; }
      .print-container { margin: 0; padding: 0; box-shadow: none; border-radius: 0; max-width: none; }
      .no-print { display: none !important; }
      .table { font-size: 11px; }
      .table th { background-color: #f8fafc !important; }
    }
    .brand-title { font-size: 1.75rem; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px; margin-bottom: 0.25rem; }
    .brand-subtitle { color: #64748b; font-weight: 500; font-size: 0.95rem; }
    .table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #475569; background: #f8fafc; border-bottom-width: 2px; }
    .table td, .table th { border-color: #e2e8f0; }
    .status-badge { display: inline-block; padding: 0.25em 0.6em; font-size: 0.75rem; font-weight: 600; line-height: 1; text-align: center; border-radius: 4px; }
    .status-Available { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-In-Use { background-color: #fef08a; color: #854d0e; border: 1px solid #fde047; }
    .status-Maintenance { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-Lost { background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
  </style>
</head>
<body>
  <div class="container-fluid print-container">
    
    <div class="row mb-5 align-items-end border-bottom pb-4 border-2">
      <div class="col-8">
        <div class="brand-title"><?php echo htmlspecialchars($_SESSION['user']['school_name'] ?? APP_NAME); ?> • ICT Room</div>
        <div class="brand-subtitle"><?php echo htmlspecialchars($title); ?></div>
      </div>
      <div class="col-4 text-end">
        <div class="no-print mb-3">
          <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="window.print()">Print / Save PDF</button>
          <a class="btn btn-sm btn-outline-secondary px-3 ms-2" href="<?php echo htmlspecialchars(url('/reports/inventory.php') . (!empty($_GET) ? ('?' . http_build_query($_GET)) : '')); ?>">Close</a>
        </div>
        <div class="small text-secondary">
          <strong>Date Generated:</strong> <?php echo htmlspecialchars($generatedAt); ?><br>
          <strong>Total Items:</strong> <?php echo count($assets); ?>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead>
          <tr>
            <th style="width:100px;">Code</th>
            <th>Asset Information</th>
            <th style="width:130px;">Category</th>
            <th style="width:110px;">Cables/Adapters</th>
            <th style="width:90px;">Condition</th>
            <th style="width:100px;">Status</th>
            <th style="width:120px;">Location</th>
            <th style="width:130px;">Serial</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$assets): ?>
            <tr><td colspan="8" class="text-center text-secondary py-5">No assets match the selected criteria.</td></tr>
          <?php endif; ?>
          <?php foreach ($assets as $a): ?>
            <?php $statusClass = 'status-' . str_replace(' ', '-', $a['status']); ?>
            <tr>
              <td class="fw-bold text-primary"><?php echo htmlspecialchars($a['asset_code']); ?></td>
              <td>
                <div class="fw-bold"><?php echo htmlspecialchars($a['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars(trim(($a['brand'] ?? '') . ' ' . ($a['model'] ?? ''))); ?></div>
              </td>
              <td class="small fw-semibold text-secondary"><?php echo htmlspecialchars($a['category_name']); ?></td>
              <td class="small" style="font-size: 0.65rem;">
                <?php if ($a['power_adapter'] === 'Yes'): ?>
                  <div class="text-success fw-bold">Power Adapter: <?php echo htmlspecialchars($a['power_adapter_status']); ?></div>
                <?php endif; ?>
                <?php if ($a['display_cable'] === 'Yes'): ?>
                  <?php 
                    $catName = strtolower($a['category_name']);
                    $cableLabel = (strpos($catName, 'printer') !== false) ? 'Printing Cable' : 'Display Cable';
                  ?>
                  <div class="text-info fw-bold"><?php echo $cableLabel; ?>: <?php echo htmlspecialchars($a['display_cable_status']); ?></div>
                <?php endif; ?>
                <?php if ($a['power_adapter'] !== 'Yes' && $a['display_cable'] !== 'Yes'): ?>
                  <span class="text-secondary">-</span>
                <?php endif; ?>
              </td>
              <td class="small"><?php echo htmlspecialchars($a['asset_condition']); ?></td>
              <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($a['status']); ?></span></td>
              <td class="small"><?php echo htmlspecialchars($a['location_name']); ?></td>
              <td class="small font-monospace text-secondary"><?php echo htmlspecialchars($a['serial_number'] ?: '-'); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($a['notes'] ?: '-'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <div class="mt-4 text-center small text-secondary no-print">
      <em>This is an automatically generated system report.</em>
    </div>
    
    <div class="mt-5 pt-5 d-flex justify-content-between text-dark text-center" style="page-break-inside: avoid;">
      <div style="width: 250px;">
        <div class="fw-semibold mb-4 pb-2 border-bottom border-dark border-opacity-25"></div>
        <div class="small fw-bold text-uppercase">Prepared By</div>
        <div class="small text-secondary mt-1">Name, Date & Signature</div>
      </div>
      <div style="width: 250px;">
        <div class="fw-semibold mb-4 pb-2 border-bottom border-dark border-opacity-25"></div>
        <div class="small fw-bold text-uppercase">Approved By</div>
        <div class="small text-secondary mt-1">Head Teacher / Principal</div>
      </div>
    </div>

  </div>
</body>
</html>
