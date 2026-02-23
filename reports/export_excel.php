<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();

$category = (int)($_GET['category'] ?? 0);
$status = trim($_GET['status'] ?? '');
$location = (int)($_GET['location'] ?? 0);
$q = trim($_GET['q'] ?? '');

$sid = (int)$_SESSION['user']['school_id'];
$where = ["a.school_id = :sid"];
$params = [':sid' => $sid];

if ($category > 0) { $where[] = "a.category_id = :category"; $params[':category'] = $category; }
if ($location > 0) { $where[] = "a.location_id = :location"; $params[':location'] = $location; }
if ($status !== '') { $where[] = "a.status = :status"; $params[':status'] = $status; }
if ($q !== '') { $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR a.serial_number LIKE :q)"; $params[':q'] = '%' . $q . '%'; }

$sql = "
  SELECT
    a.asset_code, a.asset_name,
    c.name AS category_name,
    a.brand, a.model, a.serial_number,
    a.purchase_date, a.asset_condition, a.status,
    l.name AS location_name
    , a.notes
  FROM assets a
  JOIN asset_categories c ON c.id = a.category_id
  JOIN locations l ON l.id = a.location_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY c.name, a.asset_name, a.asset_code LIMIT 5000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

audit_log('REPORT_EXPORT', 'assets', null, 'Exported inventory report (Excel/CSV)');

// Excel-friendly CSV export (works reliably on shared hosting)
$filename = 'inventory_report_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM so Excel opens Unicode correctly
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

fputcsv($out, [
  'Asset Code',
  'Asset Name',
  'Category',
  'Brand',
  'Model',
  'Serial Number',
  'Purchase Date',
  'Condition',
  'Status',
  'Location',
  'Notes',
]);

foreach ($rows as $r) {
  fputcsv($out, [
    $r['asset_code'],
    $r['asset_name'],
    $r['category_name'],
    $r['brand'] ?? '',
    $r['model'] ?? '',
    $r['serial_number'] ?? '',
    $r['purchase_date'] ?? '',
    $r['asset_condition'],
    $r['status'],
    $r['location_name'],
    $r['notes'] ?? '',
  ]);
}

fclose($out);
exit;


