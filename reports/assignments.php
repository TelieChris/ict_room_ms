<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';
require_once __DIR__ . '/../includes/audit.php';

require_login();

$pdo = db();

$assigneeType = trim($_GET['assignee_type'] ?? '');
$status = trim($_GET['status'] ?? 'Active'); // Default to active assignments
$q = trim($_GET['q'] ?? '');

$sid = (int)$_SESSION['user']['school_id'];
$where = ["ast.school_id = :sid"];
$params = [':sid' => $sid];

if ($assigneeType !== '') {
  $where[] = "ast.assigned_to_type = :atype";
  $params[':atype'] = $assigneeType;
}

if ($status === 'Active') {
  $where[] = "ast.returned_date IS NULL";
} elseif ($status === 'Returned') {
  $where[] = "ast.returned_date IS NOT NULL";
}

if ($q !== '') {
  $where[] = "(a.asset_code LIKE :q OR a.asset_name LIKE :q OR ast.assigned_to_name LIKE :q)";
  $params[':q'] = '%' . $q . '%';
}

$sql = "
  SELECT 
    ast.id as assignment_id, ast.assigned_to_type, ast.assigned_to_name, 
    ast.assigned_date, ast.expected_return_date, ast.returned_date,
    ast.return_adapter_status, ast.return_notes, ast.notes as assignment_notes,
    a.asset_code, a.asset_name,
    c.name AS category_name
  FROM asset_assignments ast
  JOIN assets a ON a.id = ast.asset_id
  JOIN asset_categories c ON c.id = a.category_id
";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY ast.assigned_date DESC, ast.assigned_to_name LIMIT 1000";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assignments = $stmt->fetchAll();

// Get distinct assignee types for filter (scoped to school)
$stmt_types = $pdo->prepare("SELECT DISTINCT assigned_to_type FROM asset_assignments WHERE school_id = ? ORDER BY assigned_to_type");
$stmt_types->execute([$sid]);
$assigneeTypes = $stmt_types->fetchAll(PDO::FETCH_COLUMN);

// audit report view
audit_log('REPORT_VIEW', 'asset_assignments', null, 'Viewed assignments report');

layout_header('Assignments Report', 'reports');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <a href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>" class="btn btn-sm btn-light border text-secondary"><i class="bi bi-arrow-left"></i> Back</a>
      <h1 class="h4 mb-0 text-success fw-bold">Asset Assignments Report</h1>
    </div>
    <div class="text-secondary">Track asset allocation to staff and departments.</div>
  </div>
  <div class="d-flex gap-2">
    <?php
      $qs = $_GET;
      $printUrl = url('/reports/print_assignments.php') . (empty($qs) ? '' : ('?' . http_build_query($qs)));
    ?>
    <a class="btn btn-outline-secondary" target="_blank" href="<?php echo htmlspecialchars($printUrl); ?>">
      <i class="bi bi-printer me-1"></i> Print / PDF
    </a>
  </div>
</div>

<div class="card table-card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get">
      <div class="col-12 col-md-5">
        <label class="form-label small text-secondary">Search</label>
        <input class="form-control" name="q" placeholder="Asset code, name, or assignee..." value="<?php echo htmlspecialchars($q); ?>">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label small text-secondary">Assignee Type</label>
        <select class="form-select" name="assignee_type">
          <option value="">All Types</option>
          <?php foreach ($assigneeTypes as $type): ?>
            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($assigneeType === $type) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($type); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label small text-secondary">Status</label>
        <select class="form-select" name="status">
          <option value="">All Statuses</option>
          <option value="Active" <?php echo ($status === 'Active') ? 'selected' : ''; ?>>Active</option>
          <option value="Returned" <?php echo ($status === 'Returned') ? 'selected' : ''; ?>>Returned</option>
        </select>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-success" type="submit">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <a class="btn btn-light border" href="<?php echo htmlspecialchars(url('/reports/assignments.php')); ?>">
          Reset
        </a>
        <div class="ms-auto small text-secondary d-flex align-items-center">
          Showing <span class="fw-semibold ms-1"><?php echo count($assignments); ?></span> records
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle table-hover">
        <thead>
          <tr class="text-secondary small">
            <th>Asset Code</th>
            <th>Asset Details</th>
            <th>Assigned To</th>
            <th>Type</th>
            <th>Assigned Date</th>
            <th>Expected Return</th>
            <th>Returned Date</th>
            <th>Adapter (Return)</th>
            <th>Return Notes</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$assignments): ?>
            <tr><td colspan="8" class="text-center text-secondary py-5">No assignment records match your filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($assignments as $ast): ?>
            <tr>
              <td class="fw-bold text-success"><?php echo htmlspecialchars($ast['asset_code']); ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($ast['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($ast['category_name']); ?></div>
              </td>
              <td class="fw-semibold"><?php echo htmlspecialchars($ast['assigned_to_name']); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($ast['assigned_to_type']); ?></td>
              <td class="small"><?php echo htmlspecialchars($ast['assigned_date']); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($ast['expected_return_date'] ?: '-'); ?></td>
              <td class="small"><?php echo htmlspecialchars($ast['returned_date'] ?: '-'); ?></td>
              <td class="small">
                <?php if ($ast['returned_date']): ?>
                  <?php if ($ast['return_adapter_status'] === 'Working'): ?>
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Working</span>
                  <?php elseif ($ast['return_adapter_status'] === 'Damaged'): ?>
                    <span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Damaged</span>
                  <?php elseif ($ast['return_adapter_status'] === 'Missing'): ?>
                    <span class="text-dark"><i class="bi bi-x-circle-fill me-1"></i>Missing</span>
                  <?php else: ?>
                    <span class="text-secondary">-</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-secondary small italic">Pending</span>
                <?php endif; ?>
              </td>
              <td class="small text-secondary">
                <div class="text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($ast['return_notes'] ?: ''); ?>">
                  <?php echo htmlspecialchars($ast['return_notes'] ?: '-'); ?>
                </div>
              </td>
              <td>
                <?php if ($ast['returned_date']): ?>
                  <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">Returned</span>
                <?php else: ?>
                  <?php 
                    $isOverdue = $ast['expected_return_date'] && strtotime($ast['expected_return_date']) < time(); 
                    $badgeClass = $isOverdue ? 'danger' : 'success';
                    $badgeText = $isOverdue ? 'Overdue' : 'Active';
                  ?>
                  <span class="badge bg-<?php echo $badgeClass; ?> bg-opacity-10 text-<?php echo $badgeClass; ?> border border-<?php echo $badgeClass; ?>-subtle">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> <?php echo $badgeText; ?>
                  </span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
