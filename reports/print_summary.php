<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();
$sid = (int)$_SESSION['user']['school_id'];
$assigned_lid = $_SESSION['user']['location_id'] ?? null;

$where_assets = "a.school_id = :sid_assets";
$params = [
    ':sid_assets' => $sid,
    ':sid_categories' => $sid
];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $where_assets .= " AND a.location_id = :assigned_lid";
    $params[':assigned_lid'] = $assigned_lid;
}

// Aggregate data by category
$sql = "
    SELECT 
        c.name as category_name,
        SUM(CASE 
            WHEN (a.qty_available + a.qty_in_use + a.qty_maintenance + a.qty_lost + a.qty_faulty) > 0 
            THEN (a.qty_available + a.qty_in_use + a.qty_maintenance + a.qty_lost + a.qty_faulty) 
            ELSE a.quantity 
        END) as total_assets_count,
        SUM(CASE WHEN a.qty_available > 0 THEN a.qty_available ELSE (CASE WHEN a.status = 'Available' THEN a.quantity ELSE 0 END) END) as count_available,
        SUM(CASE WHEN a.qty_in_use > 0 THEN a.qty_in_use ELSE (CASE WHEN a.status = 'In Use' THEN a.quantity ELSE 0 END) END) as count_in_use,
        SUM(CASE WHEN a.qty_maintenance > 0 THEN a.qty_maintenance ELSE (CASE WHEN a.status = 'Maintenance' THEN a.quantity ELSE 0 END) END) as count_maintenance,
        SUM(CASE WHEN a.qty_lost > 0 THEN a.qty_lost ELSE (CASE WHEN a.status = 'Lost' THEN a.quantity ELSE 0 END) END) as count_lost,
        SUM(CASE WHEN a.qty_faulty > 0 THEN a.qty_faulty ELSE (CASE WHEN a.status = 'Faulty' THEN a.quantity ELSE 0 END) END) as count_faulty,
        
        -- Power Adapter Stats
        SUM(CASE WHEN a.power_adapter = 'Yes' AND a.power_adapter_status = 'Working' THEN a.quantity ELSE 0 END) as pwr_working,
        SUM(CASE WHEN a.power_adapter = 'Yes' AND a.power_adapter_status = 'Damaged' THEN a.quantity ELSE 0 END) as pwr_damaged,
        SUM(CASE WHEN a.power_adapter = 'Yes' AND a.power_adapter_status = 'Missing' THEN a.quantity ELSE 0 END) as pwr_missing,
        
        -- Display Cable Stats
        SUM(CASE WHEN a.display_cable = 'Yes' AND a.display_cable_status = 'Working' THEN a.quantity ELSE 0 END) as cable_working,
        SUM(CASE WHEN a.display_cable = 'Yes' AND a.display_cable_status = 'Damaged' THEN a.quantity ELSE 0 END) as cable_damaged,
        SUM(CASE WHEN a.display_cable = 'Yes' AND a.display_cable_status = 'Missing' THEN a.quantity ELSE 0 END) as cable_missing,
        c.type
    FROM asset_categories c
    LEFT JOIN assets a ON a.category_id = c.id AND $where_assets
    WHERE c.school_id = :sid_categories
    GROUP BY c.id, c.name
    ORDER BY c.name ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$summary = $stmt->fetchAll();

// Grand Totals
$totals = [
    'assets' => 0,
    'available' => 0,
    'in_use' => 0,
    'maintenance' => 0,
    'lost' => 0,
    'faulty' => 0,
    'pwr_working' => 0,
    'pwr_damaged' => 0,
    'pwr_missing' => 0,
    'cable_working' => 0,
    'cable_damaged' => 0,
    'cable_missing' => 0
];

foreach ($summary as $row) {
    if ($row['total_assets_count'] > 0) {
        $totals['assets'] += $row['total_assets_count'];
        $totals['available'] += $row['count_available'];
        $totals['in_use'] += $row['count_in_use'];
        $totals['maintenance'] += $row['count_maintenance'];
        $totals['lost'] += $row['count_lost'];
        $totals['faulty'] += $row['count_faulty'];
        $totals['pwr_working'] += $row['pwr_working'];
        $totals['pwr_damaged'] += $row['pwr_damaged'];
        $totals['pwr_missing'] += $row['pwr_missing'];
        $totals['cable_working'] += $row['cable_working'];
        $totals['cable_damaged'] += $row['cable_damaged'];
        $totals['cable_missing'] += $row['cable_missing'];
    }
}

audit_log('REPORT_PRINT', 'inventory_summary', null, 'Printed inventory summary report');

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

$title = $reportIdentity . ' - Inventory Summary';
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
    body {
      font-family: 'Outfit', sans-serif;
      background: #f8fafc;
      color: #0f172a;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .print-container {
      background: white;
      max-width: 1000px;
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
      .table th { background-color: #f8fafc !important; }
    }
    .is-generating-pdf { background: white !important; }
    .is-generating-pdf .print-container { margin: 0; padding: 0; box-shadow: none; border-radius: 0; max-width: none; }
    .is-generating-pdf .no-print { display: none !important; }
    table { page-break-inside: auto; width: 100%; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    .brand-title { font-size: 1.75rem; font-weight: 800; color: #4f46e5; letter-spacing: -0.5px; margin-bottom: 0.25rem; }
    .brand-subtitle { color: #64748b; font-weight: 500; font-size: 0.95rem; }
    .stat-box { border: 1px solid #e2e8f0; padding: 1rem; border-radius: 0.5rem; text-align: center; height: 100%; }
    .stat-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #0f172a; }
    .table th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #475569; background: #f8fafc; border-bottom-width: 2px; }
    .table td { border: 1px solid #e2e8f0 !important; font-size: 0.85rem; }
    .table th { border: 1px solid #e2e8f0 !important; }
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
          <button class="btn btn-sm btn-success px-3 shadow-sm ms-2" onclick="downloadPDF('print-content', 'summary_report', 'landscape')">Download PDF</button>
          <a class="btn btn-sm btn-outline-secondary px-3 ms-2" href="<?php echo htmlspecialchars(url('/reports/summary.php')); ?>">Close</a>
        </div>
        <div class="small text-secondary">
          <strong>Date Generated:</strong> <?php echo htmlspecialchars($generatedAt); ?><br>
          <strong>Total Items:</strong> <?php echo number_format($totals['assets']); ?>
        </div>
      </div>
    </div>

    <!-- Overview Stats -->
    <div class="row g-3 mb-5">
      <div class="col-3">
        <div class="stat-box">
          <div class="stat-label">Total Assets</div>
          <div class="stat-value"><?php echo number_format($totals['assets']); ?></div>
        </div>
      </div>
      <div class="col-3">
        <div class="stat-box">
          <div class="stat-label">Total Working</div>
          <div class="stat-value" style="color: #166534;"><?php echo number_format($totals['available'] + $totals['in_use']); ?></div>
        </div>
      </div>
      <div class="col-3">
        <div class="stat-box">
          <div class="stat-label">Maintenance</div>
          <div class="stat-value" style="color: #92400e;"><?php echo number_format($totals['maintenance']); ?></div>
        </div>
      </div>
      <div class="col-3">
        <div class="stat-box">
          <div class="stat-label">Missing/Lost/Faulty</div>
          <div class="stat-value" style="color: #991b1b;"><?php echo number_format($totals['lost'] + $totals['faulty']); ?></div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th class="ps-3">Category</th>
            <th class="text-center" style="width: 80px;">Total</th>
            <th class="text-center">Asset Status Breakdown</th>
            <th class="text-center">Adapters</th>
            <th class="text-center">Cables</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($summary as $row): if (($row['total_assets_count'] ?? 0) == 0) continue; 
            $catLow = strtolower($row['category_name']);
            $isNonElec = ($row['type'] === 'Non-Electronic');
            $cableLabel = (strpos($catLow, 'printer') !== false) || (strpos($catLow, 'scanner') !== false) ? 'USB' : 'Display';
          ?>
            <tr>
              <td class="ps-3 fw-bold"><?php echo htmlspecialchars($row['category_name']); ?></td>
              <td class="text-center fw-bold"><?php echo $row['total_assets_count']; ?></td>
              <td class="text-center small">
                Avail: <strong><?php echo $row['count_available']; ?></strong> | 
                Use: <strong><?php echo $row['count_in_use']; ?></strong> | 
                Maint: <strong><?php echo $row['count_maintenance']; ?></strong> | 
                Lost: <strong><?php echo $row['count_lost']; ?></strong> | 
                Flt: <strong><?php echo $row['count_faulty']; ?></strong>
              </td>
              <td class="text-center small">
                <?php if (!$isNonElec): ?>
                  Ok: <strong><?php echo $row['pwr_working']; ?></strong> | 
                  Bad: <strong><?php echo $row['pwr_damaged']; ?></strong> | 
                  Miss: <strong><?php echo $row['pwr_missing']; ?></strong>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td class="text-center small">
                <?php if (!$isNonElec): ?>
                  <span class="text-secondary fw-bold" style="font-size: 0.65rem; display: block; margin-bottom: 2px;"><?php echo $cableLabel; ?></span>
                  Ok: <strong><?php echo $row['cable_working']; ?></strong> | 
                  Bad: <strong><?php echo $row['cable_damaged']; ?></strong> | 
                  Miss: <strong><?php echo $row['cable_missing']; ?></strong>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <div class="mt-4 text-center small text-secondary no-print">
      <em>This summary provides an aggregate view of hardware health across all classrooms.</em>
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
