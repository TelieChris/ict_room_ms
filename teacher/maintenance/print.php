<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['it_technician','teacher','super_admin']);

$pdo = db();

$statusFilter = trim($_GET['status'] ?? 'open'); // open|inprogress|resolved|all
$sid = (int)$_SESSION['user']['school_id'];
$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$whereClauses = ["ml.school_id = :sid"];
$params = [':sid' => $sid];

if ($statusFilter === 'open') {
  $whereClauses[] = "ml.status = 'Open'";
} elseif ($statusFilter === 'inprogress') {
  $whereClauses[] = "ml.status = 'In Progress'";
} elseif ($statusFilter === 'resolved') {
  $whereClauses[] = "ml.status = 'Resolved'";
}

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $whereClauses[] = "a.location_id = :assigned_lid";
    $params[':assigned_lid'] = $assigned_lid;
}

$where = "WHERE " . implode(" AND ", $whereClauses);

$stmt = $pdo->prepare("
  SELECT
    ml.*,
    a.asset_code, a.asset_name,
    c.name AS category_name,
    l.name AS location_name
  FROM maintenance_logs ml
  JOIN assets a ON a.id = ml.asset_id
  JOIN asset_categories c ON c.id = a.category_id
  JOIN locations l ON l.id = a.location_id
  {$where}
  ORDER BY ml.id DESC
  LIMIT 2000
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

audit_log('REPORT_PRINT', 'maintenance_logs', null, 'Printed maintenance report');

$title = 'Maintenance & Fault Report';
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
        <div class="small text-secondary">Filter: <?php
          $label = 'All';
          if ($statusFilter === 'open') $label = 'Open';
          elseif ($statusFilter === 'inprogress') $label = 'In Progress';
          elseif ($statusFilter === 'resolved') $label = 'Resolved';
          echo htmlspecialchars($label);
        ?></div>
        <div class="small text-secondary">Total items: <?php echo count($rows); ?></div>
      </div>
      <div class="no-print d-flex gap-2">
        <button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/maintenance/index.php') . (!empty($_GET) ? ('?' . http_build_query($_GET)) : '')); ?>">
          Back
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
          <tr class="small text-secondary">
            <th style="width:110px;">Asset Code</th>
            <th>Asset</th>
            <th style="width:120px;">Category</th>
            <th style="width:120px;">Location</th>
            <th style="width:110px;">Reported</th>
            <th>Issue</th>
            <th style="width:110px;">Status</th>
            <th>Action Taken</th>
            <th style="width:140px;">Technician</th>
            <th style="width:90px;">Cost</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="10" class="text-center text-secondary py-4">No maintenance records found.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $m): ?>
            <tr>
              <td class="fw-semibold"><?php echo htmlspecialchars($m['asset_code']); ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($m['asset_name']); ?></div>
              </td>
              <td><?php echo htmlspecialchars($m['category_name']); ?></td>
              <td><?php echo htmlspecialchars($m['location_name']); ?></td>
              <td class="small"><?php echo htmlspecialchars($m['reported_date']); ?></td>
              <td class="small"><?php echo nl2br(htmlspecialchars($m['issue_description'])); ?></td>
              <td class="small"><?php echo htmlspecialchars($m['status']); ?></td>
              <td class="small"><?php echo nl2br(htmlspecialchars($m['action_taken'] ?: '-')); ?></td>
              <td class="small"><?php echo htmlspecialchars($m['technician_name'] ?: '-'); ?></td>
              <td class="small text-end"><?php echo ($m['cost'] === null) ? '-' : htmlspecialchars(number_format((float)$m['cost'], 2)); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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



