<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';

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
        COUNT(a.id) as total_assets,
        SUM(CASE WHEN a.status = 'Available' THEN 1 ELSE 0 END) as count_available,
        SUM(CASE WHEN a.status = 'In Use' THEN 1 ELSE 0 END) as count_in_use,
        SUM(CASE WHEN a.status = 'Maintenance' THEN 1 ELSE 0 END) as count_maintenance,
        SUM(CASE WHEN a.status = 'Lost' THEN 1 ELSE 0 END) as count_lost,
        
        -- Power Adapter Stats
        SUM(CASE WHEN a.power_adapter = 'Yes' AND a.power_adapter_status = 'Working' THEN 1 ELSE 0 END) as pwr_working,
        SUM(CASE WHEN a.power_adapter = 'Yes' AND a.power_adapter_status = 'Damaged' THEN 1 ELSE 0 END) as pwr_damaged,
        SUM(CASE WHEN a.power_adapter = 'Yes' AND a.power_adapter_status = 'Missing' THEN 1 ELSE 0 END) as pwr_missing,
        
        -- Display Cable Stats
        SUM(CASE WHEN a.display_cable = 'Yes' AND a.display_cable_status = 'Working' THEN 1 ELSE 0 END) as cable_working,
        SUM(CASE WHEN a.display_cable = 'Yes' AND a.display_cable_status = 'Damaged' THEN 1 ELSE 0 END) as cable_damaged,
        SUM(CASE WHEN a.display_cable = 'Yes' AND a.display_cable_status = 'Missing' THEN 1 ELSE 0 END) as cable_missing
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
    'pwr_working' => 0,
    'pwr_damaged' => 0,
    'pwr_missing' => 0,
    'cable_working' => 0,
    'cable_damaged' => 0,
    'cable_missing' => 0
];

foreach ($summary as $row) {
    if ($row['total_assets'] > 0) {
        $totals['assets'] += $row['total_assets'];
        $totals['available'] += $row['count_available'];
        $totals['in_use'] += $row['count_in_use'];
        $totals['maintenance'] += $row['count_maintenance'];
        $totals['lost'] += $row['count_lost'];
        $totals['pwr_working'] += $row['pwr_working'];
        $totals['pwr_damaged'] += $row['pwr_damaged'];
        $totals['pwr_missing'] += $row['pwr_missing'];
        $totals['cable_working'] += $row['cable_working'];
        $totals['cable_damaged'] += $row['cable_damaged'];
        $totals['cable_missing'] += $row['cable_missing'];
    }
}

layout_header('Inventory Summary & Analytics', 'reports');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold text-primary">Inventory Summary</h1>
        <div class="text-secondary">High-level overview of assets and hardware health.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo htmlspecialchars(url('/reports/print_summary.php')); ?>" target="_blank" class="btn btn-outline-primary px-3 shadow-sm">
            <i class="bi bi-printer me-2"></i>Print Report
        </a>
        <a href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>" class="btn btn-light px-3 shadow-sm border">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<!-- Grand Totals Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-3">
                <div class="small opacity-75">Total Assets</div>
                <div class="h3 fw-bold mb-0"><?php echo number_format($totals['assets']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body p-3">
                <div class="small opacity-75">Available / In Use</div>
                <div class="h3 fw-bold mb-0"><?php echo number_format($totals['available'] + $totals['in_use']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body p-3">
                <div class="small opacity-75">Maintenance</div>
                <div class="h3 fw-bold mb-0"><?php echo number_format($totals['maintenance']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body p-3">
                <div class="small opacity-75">Lost / Damaged</div>
                <div class="h3 fw-bold mb-0"><?php echo number_format($totals['lost']); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Category Breakdown -->
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-list-task me-2 text-primary"></i>Category Breakdown</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Category</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Power Adapters</th>
                            <th class="text-center">Cables</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php foreach ($summary as $row): if ($row['total_assets'] == 0) continue; 
                            $catLow = strtolower($row['category_name']);
                            $cableName = (strpos($catLow, 'printer') !== false) ? 'Printing' : 'Display';
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo htmlspecialchars($row['category_name']); ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill"><?php echo $row['total_assets']; ?></span>
                                </td>
                                <td class="text-center small">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <div class="d-flex gap-2">
                                            <span class="text-success" title="Available">● <?php echo $row['count_available']; ?></span>
                                            <span class="text-info" title="In Use">● <?php echo $row['count_in_use']; ?></span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <span class="text-warning" title="Maintenance">● <?php echo $row['count_maintenance']; ?></span>
                                            <span class="text-danger" title="Lost">● <?php echo $row['count_lost']; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center small">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <span class="text-success" title="Working">Working: <?php echo $row['pwr_working']; ?></span>
                                        <?php if ($row['pwr_damaged'] > 0 || $row['pwr_missing'] > 0): ?>
                                            <div class="d-flex gap-2">
                                                <span class="text-danger" title="Damaged">! <?php echo $row['pwr_damaged']; ?></span>
                                                <span class="text-dark" title="Missing">? <?php echo $row['pwr_missing']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center small">
                                    <div class="d-flex flex-column gap-1 align-items-center text-truncate">
                                        <div class="text-secondary mb-1 fw-bold" style="font-size: 0.7rem;"><?php echo $cableName; ?></div>
                                        <span class="text-success" title="Working">Working: <?php echo $row['cable_working']; ?></span>
                                        <?php if ($row['cable_damaged'] > 0 || $row['cable_missing'] > 0): ?>
                                            <div class="d-flex gap-2">
                                                <span class="text-danger" title="Damaged">! <?php echo $row['cable_damaged']; ?></span>
                                                <span class="text-dark" title="Missing">? <?php echo $row['cable_missing']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .text-primary { color: black !important; }
    .shadow-sm { box-shadow: none !important; border: 1px solid #ddd !important; }
    .bg-light { background-color: transparent !important; }
}
</style>

<?php layout_footer(); ?>
