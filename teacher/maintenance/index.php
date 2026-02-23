<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['admin','teacher']);

$pdo = db();

$statusFilter = trim($_GET['status'] ?? 'open'); // open|inprogress|resolved|all

$sid = (int)$_SESSION['user']['school_id'];
$whereClauses = ["ml.school_id = :sid"];
if ($statusFilter === 'open') {
  $whereClauses[] = "ml.status = 'Open'";
} elseif ($statusFilter === 'inprogress') {
  $whereClauses[] = "ml.status = 'In Progress'";
} elseif ($statusFilter === 'resolved') {
  $whereClauses[] = "ml.status = 'Resolved'";
}
$where = "WHERE " . implode(" AND ", $whereClauses);

$stmt = $pdo->prepare("
  SELECT
    ml.*,
    a.asset_code, a.asset_name, a.status AS asset_status,
    c.name AS category_name,
    l.name AS location_name
  FROM maintenance_logs ml
  JOIN assets a ON a.id = ml.asset_id
  JOIN asset_categories c ON c.id = a.category_id
  JOIN locations l ON l.id = a.location_id
  {$where}
  ORDER BY ml.id DESC
  LIMIT 200
");
$stmt->execute([':sid' => $sid]);
$rows = $stmt->fetchAll();

layout_header('Maintenance', 'maintenance');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Maintenance & Fault Reporting</h1>
    <div class="text-secondary">Report issues and track maintenance history.</div>
  </div>
  <div class="d-flex gap-2">
    <?php
      $qs = $_GET;
      $printUrl = url('/teacher/maintenance/print.php') . (empty($qs) ? '' : ('?' . http_build_query($qs)));
    ?>
    <a class="btn btn-outline-secondary" target="_blank" href="<?php echo htmlspecialchars($printUrl); ?>">
      <i class="bi bi-printer me-1"></i> Print / Save PDF
    </a>
    <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/teacher/maintenance/create.php')); ?>">
      <i class="bi bi-wrench-adjustable me-1"></i> New Ticket
    </a>
  </div>
</div>

<div class="card table-card mb-3">
  <div class="card-body">
    <form class="d-flex align-items-center gap-2 flex-wrap" method="get">
      <div class="small text-secondary me-1">Status:</div>
      <select class="form-select" name="status" style="max-width:240px;">
        <option value="open" <?php echo ($statusFilter === 'open') ? 'selected' : ''; ?>>Open</option>
        <option value="inprogress" <?php echo ($statusFilter === 'inprogress') ? 'selected' : ''; ?>>In Progress</option>
        <option value="resolved" <?php echo ($statusFilter === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
        <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>All</option>
      </select>
      <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i></button>
    </form>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Asset</th>
            <th>Issue</th>
            <th>Status</th>
            <th>Reported</th>
            <th>Technician / Cost</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="6" class="text-center text-secondary py-4">No maintenance records found.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $m): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($m['asset_code']); ?> • <?php echo htmlspecialchars($m['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($m['category_name']); ?> • <?php echo htmlspecialchars($m['location_name']); ?></div>
              </td>
              <td>
                <div class="small text-secondary">Reported: <?php echo htmlspecialchars($m['reported_date']); ?></div>
                <div><?php echo nl2br(htmlspecialchars($m['issue_description'])); ?></div>
              </td>
              <td>
                <?php
                  $badge = 'secondary';
                  if ($m['status'] === 'Open') $badge = 'danger';
                  elseif ($m['status'] === 'In Progress') $badge = 'warning';
                  elseif ($m['status'] === 'Resolved') $badge = 'success';
                ?>
                <span class="badge text-bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($m['status']); ?></span>
              </td>
              <td class="small text-secondary">
                <?php echo htmlspecialchars($m['created_at']); ?>
              </td>
              <td class="small text-secondary">
                <?php if (!empty($m['technician_name'])): ?>
                  Tech: <?php echo htmlspecialchars($m['technician_name']); ?><br>
                <?php endif; ?>
                <?php if ($m['cost'] !== null): ?>
                  Cost: <?php echo htmlspecialchars(number_format((float)$m['cost'], 2)); ?>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="<?php echo htmlspecialchars(url('/teacher/maintenance/edit.php')); ?>?id=<?php echo (int)$m['id']; ?>">
                  <i class="bi bi-pencil"></i>
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


