<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();

$assigneeType = trim($_GET['assignee_type'] ?? '');
$status = trim($_GET['status'] ?? 'Active');
$q = trim($_GET['q'] ?? '');

$sid = (int)$_SESSION['user']['school_id'];
$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$where = ["ast.school_id = :sid"];
$params = [':sid' => $sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $where[] = "a.location_id = :assigned_lid";
    $params[':assigned_lid'] = $assigned_lid;
}

if ($assigneeType !== '') { $where[] = "ast.assigned_to_type = :atype"; $params[':atype'] = $assigneeType; }
if ($status === 'Active') { $where[] = "ast.returned_date IS NULL"; } 
elseif ($status === 'Returned') { $where[] = "ast.returned_date IS NOT NULL"; }
if ($q !== '') { $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR ast.assigned_to_name LIKE :q)"; $params[':q'] = '%' . $q . '%'; }

$sql = "
  SELECT 
    ast.id as assignment_id, ast.assigned_to_type, ast.assigned_to_name, 
    ast.assigned_date, ast.expected_return_date, ast.returned_date,
    ast.return_adapter_status, ast.return_display_cable_status, ast.return_notes, ast.notes as assignment_notes,
    a.asset_code, a.asset_name,
    c.name AS category_name
  FROM asset_assignments ast
  JOIN assets a ON a.id = ast.asset_id
  JOIN asset_categories c ON c.id = a.category_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY ast.assigned_date DESC, ast.assigned_to_name LIMIT 2000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assignments = $stmt->fetchAll();

audit_log('REPORT_PRINT', 'asset_assignments', null, 'Printed assignments report');

$title = 'Asset Assignments Report';
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
    body { font-family: 'Outfit', sans-serif; background: #f8fafc; color: #0f172a; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .print-container { background: white; max-width: 1200px; margin: 2rem auto; padding: 3rem; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    @media print {
      @page { size: A4 landscape; margin: 10mm; }
      body { background: white !important; }
      .print-container { margin: 0; padding: 0; box-shadow: none; border-radius: 0; max-width: none; }
      .no-print { display: none !important; }
      .table { font-size: 11px; }
      .table th { background-color: #f8fafc !important; }
    }
    .brand-title { font-size: 1.75rem; font-weight: 800; color: #16a34a; letter-spacing: -0.5px; margin-bottom: 0.25rem; }
    .brand-subtitle { color: #64748b; font-weight: 500; font-size: 0.95rem; }
    .table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #475569; background: #f8fafc; border-bottom-width: 2px; }
    .table td, .table th { border-color: #e2e8f0; }
    .status-badge { display: inline-block; padding: 0.25em 0.6em; font-size: 0.75rem; font-weight: 600; line-height: 1; text-align: center; border-radius: 4px; }
    .status-Active { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-Overdue { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-Returned { background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
  </style>
</head>
<body>
  <div class="container-fluid print-container">
    
    <div class="row mb-5 align-items-end border-bottom pb-4 border-2">
      <div class="col-8">
        <div class="brand-title">GS Remera TSS • ICT Room</div>
        <div class="brand-subtitle"><?php echo htmlspecialchars($title); ?></div>
      </div>
      <div class="col-4 text-end">
        <div class="no-print mb-3">
          <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="window.print()">Print / Save PDF</button>
          <a class="btn btn-sm btn-outline-secondary px-3 ms-2" href="<?php echo htmlspecialchars(url('/reports/assignments.php') . (!empty($_GET) ? ('?' . http_build_query($_GET)) : '')); ?>">Close</a>
        </div>
        <div class="small text-secondary">
          <strong>Date Generated:</strong> <?php echo htmlspecialchars($generatedAt); ?><br>
          <strong>Total Records:</strong> <?php echo count($assignments); ?>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead>
          <tr>
            <th style="width:110px;">Asset Code</th>
            <th>Asset Details</th>
            <th style="width:160px;">Assigned To</th>
            <th style="width:130px;">Type</th>
            <th style="width:110px;">Assigned Date</th>
            <th style="width:110px;">Expected Return</th>
            <th style="width:110px;">Returned Date</th>
            <th>Return Condition</th>
            <th style="width:100px;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$assignments): ?>
            <tr><td colspan="8" class="text-center text-secondary py-5">No assignment records match the selected criteria.</td></tr>
          <?php endif; ?>
          <?php foreach ($assignments as $ast): ?>
            <?php 
              $isReturned = !empty($ast['returned_date']);
              $isOverdue = !$isReturned && $ast['expected_return_date'] && strtotime($ast['expected_return_date']) < time(); 
              $statusClass = $isReturned ? 'status-Returned' : ($isOverdue ? 'status-Overdue' : 'status-Active');
              $statusText = $isReturned ? 'Returned' : ($isOverdue ? 'Overdue' : 'Active');
            ?>
            <tr>
              <td class="fw-bold text-success"><?php echo htmlspecialchars($ast['asset_code']); ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($ast['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($ast['category_name']); ?></div>
              </td>
              <td class="fw-semibold text-dark"><?php echo htmlspecialchars($ast['assigned_to_name']); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($ast['assigned_to_type']); ?></td>
              <td class="small"><?php echo htmlspecialchars($ast['assigned_date']); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($ast['expected_return_date'] ?: '-'); ?></td>
              <td class="small"><?php echo htmlspecialchars($ast['returned_date'] ?: '-'); ?></td>
              <td class="small" style="font-size: 0.65rem;">
                <?php if ($ast['returned_date']): ?>
                  <?php if ($ast['return_adapter_status'] && $ast['return_adapter_status'] !== 'N/A'): ?>
                    <div class="text-success fw-bold">Pwr: <?php echo htmlspecialchars($ast['return_adapter_status']); ?></div>
                  <?php endif; ?>
                  <?php if ($ast['return_display_cable_status'] && $ast['return_display_cable_status'] !== 'N/A'): ?>
                    <div class="text-info fw-bold">Cable: <?php echo htmlspecialchars($ast['return_display_cable_status']); ?></div>
                  <?php endif; ?>
                  <div class="text-secondary mt-1" style="font-style: italic;"><?php echo htmlspecialchars($ast['return_notes'] ?: '-'); ?></div>
                <?php else: ?>
                  <span class="text-secondary">-</span>
                <?php endif; ?>
              </td>
              <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
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
