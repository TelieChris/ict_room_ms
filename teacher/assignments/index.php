<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['it_technician','teacher','super_admin','head_teacher']);

$pdo = db();

$filter  = trim($_GET['filter'] ?? 'open'); // open | all | returned
$me      = auth_user();
$myId    = (int)$me['id'];
$myRole  = $me['role'] ?? '';

$sid = (int)$_SESSION['user']['school_id'];
$whereClauses = ["aa.school_id = :sid"];
if ($filter === 'open') $whereClauses[] = "aa.returned_date IS NULL";
elseif ($filter === 'returned') $whereClauses[] = "aa.returned_date IS NOT NULL";

// Teachers only see their own assignments
if ($myRole === 'teacher') {
    $whereClauses[] = "aa.created_by = :uid";
}

$assigned_lid = $_SESSION['user']['location_id'] ?? null;
if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $whereClauses[] = "a.location_id = :assigned_lid";
}

$where = "WHERE " . implode(" AND ", $whereClauses);

$stmt = $pdo->prepare("
  SELECT
    aa.*,
    a.asset_code, a.asset_name, a.status AS asset_status,
    c.name AS category_name,
    l.name AS location_name
  FROM asset_assignments aa
  JOIN assets a ON a.id = aa.asset_id
  JOIN asset_categories c ON c.id = a.category_id
  JOIN locations l ON l.id = a.location_id
  {$where}
  ORDER BY aa.id DESC
  LIMIT 200
");
$params = [':sid' => $sid];
if ($myRole === 'teacher') {
    $params[':uid'] = $myId;
}
if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $params[':assigned_lid'] = $assigned_lid;
}
$stmt->execute($params);
$rows = $stmt->fetchAll();

layout_header('Assignments', 'assignments');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Assignments</h1>
    <div class="text-secondary">Assign assets to ICT Room, teachers, or classes and track returns.</div>
  </div>
  <?php if (!is_head_teacher()): ?>
  <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/teacher/assignments/create.php')); ?>">
    <i class="bi bi-plus-lg me-1"></i> New Assignment
  </a>
  <?php endif; ?>
</div>

<div class="card table-card mb-3">
  <div class="card-body">
    <form class="d-flex align-items-center gap-2 flex-wrap" method="get">
      <div class="small text-secondary me-1">Filter:</div>
      <?php
        $filters = [
          'open' => 'Open (Not Returned)',
          'returned' => 'Returned',
          'all' => 'All',
        ];
      ?>
      <select class="form-select" name="filter" style="max-width:240px;">
        <?php foreach ($filters as $k => $label): ?>
          <option value="<?php echo htmlspecialchars($k); ?>" <?php echo ($filter === $k) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($label); ?>
          </option>
        <?php endforeach; ?>
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
            <th>Assigned To</th>
            <th>Assigned</th>
            <th>Expected Return</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="6" class="text-center text-secondary py-4">No assignments found.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($r['asset_code']); ?> • <?php echo htmlspecialchars($r['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($r['category_name']); ?> • <?php echo htmlspecialchars($r['location_name']); ?></div>
              </td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($r['assigned_to_type']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($r['assigned_to_name']); ?></div>
              </td>
              <td class="small text-secondary"><?php echo htmlspecialchars($r['assigned_date']); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($r['expected_return_date'] ?: '-'); ?></td>
              <td>
                <?php if ($r['approval_status'] === 'pending'): ?>
                  <span class="badge text-bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Awaiting Approval</span>
                <?php elseif ($r['approval_status'] === 'rejected'): ?>
                  <span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                <?php elseif (!empty($r['returned_date'])): ?>
                  <span class="badge text-bg-secondary"><?php echo htmlspecialchars($r['returned_date']); ?></span>
                <?php else: ?>
                  <span class="badge text-bg-success">Active</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <?php if (empty($r['returned_date']) && $r['approval_status'] === 'approved'): ?>
                  <a class="btn btn-sm btn-success"
                     href="<?php echo htmlspecialchars(url('/teacher/assignments/return.php')); ?>?id=<?php echo (int)$r['id']; ?>"
                     title="Return Asset">
                    <i class="bi bi-check2-circle"></i>
                  </a>
                <?php elseif ($r['approval_status'] === 'pending'): ?>
                  <span class="text-secondary small">Pending…</span>
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


