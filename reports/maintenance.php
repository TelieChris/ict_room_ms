<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();

$logStatus = trim($_GET['status'] ?? '');
$q = trim($_GET['q'] ?? '');

$sid = (int)$_SESSION['user']['school_id'];
$where = ["m.school_id = :sid"];
$params = [':sid' => $sid];

if ($logStatus !== '') {
  $where[] = "m.status = :mstatus";
  $params[':mstatus'] = $logStatus;
}

if ($q !== '') {
  $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR m.issue_description LIKE :q OR m.technician_name LIKE :q)";
  $params[':q'] = '%' . $q . '%';
}

$sql = "
  SELECT 
    m.id as maintenance_id, m.issue_description, m.reported_date, 
    m.action_taken, m.technician_name, m.cost, m.status as log_status,
    a.asset_code, a.asset_name,
    c.name AS category_name
  FROM maintenance_logs m
  JOIN assets a ON a.id = m.asset_id
  JOIN asset_categories c ON c.id = a.category_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY m.reported_date DESC, m.id DESC LIMIT 1000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// audit report view
audit_log('REPORT_VIEW', 'maintenance_logs', null, 'Viewed maintenance report');

layout_header('Maintenance Report', 'reports');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <a href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>" class="btn btn-sm btn-light border text-secondary"><i class="bi bi-arrow-left"></i> Back</a>
      <h1 class="h4 mb-0 text-danger fw-bold">Maintenance Logs Report</h1>
    </div>
    <div class="text-secondary">History of reported issues, repairs, and associated costs.</div>
  </div>
  <div class="d-flex gap-2">
    <?php
      $qs = $_GET;
      $printUrl = url('/reports/print_maintenance.php') . (empty($qs) ? '' : ('?' . http_build_query($qs)));
    ?>
    <a class="btn btn-outline-secondary" target="_blank" href="<?php echo htmlspecialchars($printUrl); ?>">
      <i class="bi bi-printer me-1"></i> Print / PDF
    </a>
  </div>
</div>

<div class="card table-card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-12 col-md-6">
        <label class="form-label small text-secondary">Search</label>
        <input class="form-control" name="q" placeholder="Asset code, issue, technician..." value="<?php echo htmlspecialchars($q); ?>">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label small text-secondary">Status</label>
        <select class="form-select" name="status">
          <option value="">All Statuses</option>
          <option value="Open" <?php echo ($logStatus === 'Open') ? 'selected' : ''; ?>>Open</option>
          <option value="In Progress" <?php echo ($logStatus === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
          <option value="Resolved" <?php echo ($logStatus === 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
        </select>
      </div>
      <div class="col-12 col-md-3 d-flex gap-2">
        <button class="btn btn-danger w-100" type="submit">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <a class="btn btn-light border w-100" href="<?php echo htmlspecialchars(url('/reports/maintenance.php')); ?>">
          Reset
        </a>
      </div>
    </form>
    <div class="mt-3 small text-secondary text-end">
      Showing <span class="fw-semibold ms-1"><?php echo count($logs); ?></span> logs. Total Cost: 
      <strong class="text-dark">
        RWF <?php echo number_format(array_sum(array_column($logs, 'cost')), 2); ?>
      </strong>
    </div>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle table-hover">
        <thead>
          <tr class="text-secondary small">
            <th>Reported Date</th>
            <th>Asset Information</th>
            <th style="max-width:300px;">Issue Description</th>
            <th>Status</th>
            <th>Technician</th>
            <th>Cost (RWF)</th>
            <th>Actions Taken</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$logs): ?>
            <tr><td colspan="7" class="text-center text-secondary py-5">No maintenance records match your filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td class="small fw-semibold"><?php echo htmlspecialchars($log['reported_date']); ?></td>
              <td>
                <div class="fw-bold text-danger"><?php echo htmlspecialchars($log['asset_code']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($log['asset_name']); ?></div>
              </td>
              <td class="small text-secondary text-wrap" style="max-width: 300px;">
                <?php echo nl2br(htmlspecialchars($log['issue_description'])); ?>
              </td>
              <td>
                <?php
                  $sColor = 'secondary';
                  if ($log['log_status'] === 'Open') $sColor = 'danger';
                  elseif ($log['log_status'] === 'In Progress') $sColor = 'warning';
                  elseif ($log['log_status'] === 'Resolved') $sColor = 'success';
                ?>
                <span class="badge bg-<?php echo $sColor; ?> bg-opacity-10 text-<?php echo $sColor; ?> border border-<?php echo $sColor; ?>-subtle">
                  <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>
                  <?php echo htmlspecialchars($log['log_status']); ?>
                </span>
              </td>
              <td class="small text-secondary"><?php echo htmlspecialchars($log['technician_name'] ?: 'Unassigned'); ?></td>
              <td class="small font-monospace"><?php echo $log['cost'] ? number_format($log['cost'], 2) : '-'; ?></td>
              <td class="small text-secondary text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($log['action_taken'] ?: ''); ?>">
                <?php echo htmlspecialchars($log['action_taken'] ?: '-'); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
