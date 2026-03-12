<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();

$logStatus = trim($_GET['status'] ?? '');
$q = trim($_GET['q'] ?? '');

$sid = (int)$_SESSION['user']['school_id'];
$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$where = ["m.school_id = :sid"];
$params = [':sid' => $sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $where[] = "a.location_id = :assigned_lid";
    $params[':assigned_lid'] = $assigned_lid;
}

if ($logStatus !== '') { $where[] = "m.status = :mstatus"; $params[':mstatus'] = $logStatus; }
if ($q !== '') { $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR m.issue_description LIKE :q OR m.technician_name LIKE :q)"; $params[':q'] = '%' . $q . '%'; }

$sql = "
  SELECT 
    m.id as maintenance_id, m.issue_description, m.reported_date, m.resolved_date,
    m.action_taken, m.technician_name, m.cost, m.status as log_status,
    a.asset_code, a.asset_name,
    c.name AS category_name
  FROM maintenance_logs m
  JOIN assets a ON a.id = m.asset_id
  JOIN asset_categories c ON c.id = a.category_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY m.reported_date DESC, m.id DESC LIMIT 2000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

$totalCost = array_sum(array_column($logs, 'cost'));

audit_log('REPORT_PRINT', 'maintenance_logs', null, 'Printed maintenance report');

// Role-based title logic
$reportIdentity = 'Asset Inventory';
$schoolDisplay = $_SESSION['user']['school_name'] ?? APP_NAME;

if (is_super_admin()) {
    $reportIdentity = 'The system report';
    $schoolDisplay = 'System Administration';
} elseif (is_head_teacher()) {
    $reportIdentity = $_SESSION['user']['school_name'] ?? APP_NAME;
    $schoolDisplay = $_SESSION['user']['school_name'] ?? APP_NAME;
} elseif (is_it_technician() && !empty($_SESSION['user']['location_id'])) {
    $stmt_print_loc = $pdo->prepare("SELECT name FROM locations WHERE id = ?");
    $stmt_print_loc->execute([$_SESSION['user']['location_id']]);
    $reportIdentity = $stmt_print_loc->fetchColumn() ?: 'ICT Lab Report';
}

$title = $reportIdentity . ' - Maintenance History';
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  
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
    .is-generating-pdf { background: white !important; }
    .is-generating-pdf .print-container { margin: 0; padding: 0; box-shadow: none; border-radius: 0; max-width: none; }
    .is-generating-pdf .no-print { display: none !important; }
    table { page-break-inside: auto; width: 100%; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    .brand-title { font-size: 1.75rem; font-weight: 800; color: #dc2626; letter-spacing: -0.5px; margin-bottom: 0.25rem; }
    .brand-subtitle { color: #64748b; font-weight: 500; font-size: 0.95rem; }
    .table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #475569; background: #f8fafc; border-bottom-width: 2px; }
    .table td, .table th { border: 1px solid #e2e8f0 !important; }
    .status-badge { display: inline-block; padding: 0.25em 0.6em; font-size: 0.75rem; font-weight: 600; line-height: 1; text-align: center; border-radius: 4px; }
    .status-Open { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-In-Progress { background-color: #fef08a; color: #854d0e; border: 1px solid #fde047; }
    .status-Resolved { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-Faulty { background-color: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; }
  </style>
</head>
<body>
  <div id="print-content" class="container-fluid print-container">
    
    <div class="row mb-5 align-items-end border-bottom pb-4 border-2">
      <div class="col-8">
        <div class="brand-title"><?php echo htmlspecialchars($schoolDisplay); ?> • ICT Room</div>
        <div class="brand-subtitle"><?php echo htmlspecialchars($title); ?></div>
      </div>
      <div class="col-4 text-end">
        <div class="no-print mb-3">
          <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="window.print()">Print Report</button>
          <button class="btn btn-sm btn-success px-3 shadow-sm ms-2" onclick="downloadPDF('print-content', 'maintenance_report', 'landscape')">Download PDF</button>
          <a class="btn btn-sm btn-outline-secondary px-3 ms-2" href="<?php echo htmlspecialchars(url('/reports/maintenance.php') . (!empty($_GET) ? ('?' . http_build_query($_GET)) : '')); ?>">Close</a>
        </div>
        <div class="small text-secondary">
          <strong>Date Generated:</strong> <?php echo htmlspecialchars($generatedAt); ?><br>
          <strong>Total Records:</strong> <?php echo count($logs); ?><br>
          <strong>Total Cost:</strong> RWF <?php echo number_format($totalCost, 2); ?>
        </div>
      </div>
    </div>

    <div style="overflow-x:auto;">
      <table class="table table-bordered table-sm align-middle">
        <thead>
          <tr>
            <th style="width:120px;">Reported / Resolved</th>
            <th>Asset Information</th>
            <th style="width:250px;">Issue Description</th>
            <th style="width:100px;">Status</th>
            <th style="width:140px;">Technician</th>
            <th style="width:110px;">Cost (RWF)</th>
            <th style="width:180px;">Actions Taken</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$logs): ?>
            <tr><td colspan="7" class="text-center text-secondary py-5">No maintenance records match the selected criteria.</td></tr>
          <?php endif; ?>
          <?php foreach ($logs as $log): ?>
            <?php 
              $statusClass = 'status-' . str_replace(' ', '-', $log['log_status']);
            ?>
            <tr>
              <td class="small">
                <div class="fw-semibold">Rep: <?php echo htmlspecialchars($log['reported_date']); ?></div>
                <?php if ($log['log_status'] === 'Resolved' && !empty($log['resolved_date'])): ?>
                  <div class="text-success fw-bold">Res: <?php echo htmlspecialchars($log['resolved_date']); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-bold text-danger"><?php echo htmlspecialchars($log['asset_code']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($log['asset_name']); ?></div>
              </td>
              <td class="small text-dark"><?php echo nl2br(htmlspecialchars($log['issue_description'])); ?></td>
              <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($log['log_status']); ?></span></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($log['technician_name'] ?: 'Unassigned'); ?></td>
              <td class="small font-monospace"><?php echo $log['cost'] ? number_format($log['cost'], 2) : '-'; ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($log['action_taken'] ?: '-'); ?></td>
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
        <div class="small text-secondary mt-1"> Head Teacher   </div>
      </div>
    </div>

  </div>
    <script>
      function downloadPDF(elementId, filename, orientation) {
        const element = document.getElementById(elementId);
        element.classList.add('is-generating-pdf');
        
        const opt = {
          margin:       0.4,
          filename:     filename + '.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true, letterRendering: true, scrollX: 0, scrollY: 0 },
          jsPDF:        { unit: 'in', format: 'a4', orientation: orientation },
          pagebreak:    { mode: ['css', 'legacy'] }
        };
        
        html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
          const totalPages = pdf.internal.getNumberOfPages();
          for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(9);
            pdf.setTextColor(150);
            pdf.text('Page ' + i + ' of ' + totalPages, pdf.internal.pageSize.getWidth() - 1.2, pdf.internal.pageSize.getHeight() - 0.2);
          }
        }).save().then(() => {
          element.classList.remove('is-generating-pdf');
        });
      }
    </script>
</body>
</html>
