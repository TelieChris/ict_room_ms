<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['super_admin', 'it_technician', 'head_teacher']);

$pdo = db();
$sid = (int)$_SESSION['user']['school_id'];
$isSuper = is_super_admin();

// Handle approve / reject action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $action = (string)($_POST['action'] ?? '');
    $aid    = (int)($_POST['assignment_id'] ?? 0);
    $note   = trim((string)($_POST['approval_note'] ?? ''));
    $me     = auth_user();
    $myId   = (int)$me['id'];

    if (in_array($action, ['approve', 'reject'], true) && $aid > 0) {
        // Fetch assignment (school-scope unless super admin)
        $q = "SELECT aa.*, a.asset_code, a.asset_name FROM asset_assignments aa JOIN assets a ON a.id = aa.asset_id WHERE aa.id = :id AND aa.approval_status = 'pending'";
        $p = [':id' => $aid];
        if (!$isSuper) { $q .= " AND aa.school_id = :sid"; $p[':sid'] = $sid; }
        $stmt = $pdo->prepare($q);
        $stmt->execute($p);
        $assignment = $stmt->fetch();

        if ($assignment) {
            $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
            // Update assignment approval status
            $pdo->prepare("UPDATE asset_assignments SET approval_status=:s, approved_by=:by, approved_at=NOW(), approval_note=:note WHERE id=:id")
                ->execute([':s' => $newStatus, ':by' => $myId, ':note' => $note ?: null, ':id' => $aid]);

            if ($action === 'approve') {
                // Mark asset as In Use
                $pdo->prepare("UPDATE assets SET status='In Use' WHERE id=:id")->execute([':id' => $assignment['asset_id']]);
                flash_set('success', "Assignment for {$assignment['asset_name']} approved.");
                audit_log('ASSIGN_APPROVE', 'asset_assignments', $aid, "Approved assignment for {$assignment['asset_code']}");
            } else {
                // Return asset to Available
                $pdo->prepare("UPDATE assets SET status='Available' WHERE id=:id")->execute([':id' => $assignment['asset_id']]);
                flash_set('error', "Assignment for {$assignment['asset_name']} rejected.");
                audit_log('ASSIGN_REJECT', 'asset_assignments', $aid, "Rejected assignment for {$assignment['asset_code']}. Note: {$note}");
            }
        }
    }
    header('Location: ' . url('/admin/approvals/index.php'));
    exit;
}

// Fetch pending assignments
$filter = trim($_GET['status'] ?? 'pending');
$whereParts = ["aa.approval_status = :status"];
$params = [':status' => ($filter === 'all' ? '%' : $filter)];
if ($filter === 'all') $whereParts = ["1=1"];

if (!$isSuper) { $whereParts[] = "aa.school_id = :sid"; $params[':sid'] = $sid; }

$where = implode(" AND ", $whereParts);
$stmt = $pdo->prepare("
  SELECT
    aa.*,
    a.asset_code, a.asset_name,
    c.name AS category_name,
    u.full_name AS submitted_by_name,
    u.username AS submitted_by_username,
    aprv.full_name AS approved_by_name
  FROM asset_assignments aa
  JOIN assets a ON a.id = aa.asset_id
  JOIN asset_categories c ON c.id = a.category_id
  JOIN users u ON u.id = aa.created_by
  LEFT JOIN users aprv ON aprv.id = aa.approved_by
  WHERE {$where}
  ORDER BY aa.created_at DESC
  LIMIT 300
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Count per status for badges
$countStmt = $pdo->prepare(
    "SELECT approval_status, COUNT(*) as cnt FROM asset_assignments aa" .
    (!$isSuper ? " WHERE aa.school_id = :sid" : "") .
    " GROUP BY approval_status"
);
if (!$isSuper) $countStmt->execute([':sid' => $sid]);
else $countStmt->execute();
$counts = [];
foreach ($countStmt->fetchAll() as $row) { $counts[$row['approval_status']] = (int)$row['cnt']; }

layout_header('Approvals', 'approvals');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Assignment Approvals</h1>
    <div class="text-secondary">Review and approve asset assignment requests submitted by teachers.</div>
  </div>
</div>

<!-- Status filter tabs -->
<ul class="nav nav-tabs mb-3">
  <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $k => $label): ?>
    <li class="nav-item">
      <a class="nav-link <?php echo ($filter === $k) ? 'active' : ''; ?>"
         href="<?php echo htmlspecialchars(url('/admin/approvals/index.php') . '?status=' . $k); ?>">
        <?php echo htmlspecialchars($label); ?>
        <?php if (isset($counts[$k]) && $counts[$k] > 0): ?>
          <span class="badge rounded-pill <?php echo $k === 'pending' ? 'bg-warning text-dark' : 'bg-secondary'; ?> ms-1">
            <?php echo $counts[$k]; ?>
          </span>
        <?php endif; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<div class="card table-card">
  <div class="card-body">
    <?php if (!$rows): ?>
      <div class="text-center text-secondary py-5">
        <i class="bi bi-clipboard2-check fs-1 d-block mb-2"></i>
        No <?php echo htmlspecialchars($filter); ?> assignment requests found.
      </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Asset</th>
            <th>Assigned To</th>
            <th>Submitted By</th>
            <th>Date</th>
            <th>Status</th>
            <?php if ($filter === 'pending'): ?><th class="text-end">Actions</th><?php endif; ?>
            <?php if ($filter !== 'pending'): ?><th>Decision By</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($r['asset_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($r['asset_code']); ?> &bull; <?php echo htmlspecialchars($r['category_name']); ?></div>
              </td>
              <td>
                <div><?php echo htmlspecialchars($r['assigned_to_type']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($r['assigned_to_name']); ?></div>
              </td>
              <td>
                <div><?php echo htmlspecialchars($r['submitted_by_name']); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($r['submitted_by_username']); ?></div>
              </td>
              <td class="small text-secondary"><?php echo htmlspecialchars($r['assigned_date']); ?></td>
              <td>
                <?php
                  $badge = ['pending' => 'warning text-dark', 'approved' => 'success', 'rejected' => 'danger'];
                  $bs = $badge[$r['approval_status']] ?? 'secondary';
                ?>
                <span class="badge text-bg-<?php echo $bs; ?>"><?php echo ucfirst($r['approval_status']); ?></span>
                <?php if ($r['approval_note']): ?>
                  <div class="small text-secondary mt-1"><?php echo htmlspecialchars($r['approval_note']); ?></div>
                <?php endif; ?>
              </td>

              <?php if ($filter === 'pending'): ?>
              <td class="text-end">
                <!-- Approve -->
                <form method="post" class="d-inline" onsubmit="return confirm('Approve this assignment?');">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="approve">
                  <input type="hidden" name="assignment_id" value="<?php echo (int)$r['id']; ?>">
                  <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-check2-circle me-1"></i>Approve
                  </button>
                </form>
                <!-- Reject -->
                <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                        data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo (int)$r['id']; ?>">
                  <i class="bi bi-x-circle me-1"></i>Reject
                </button>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal<?php echo (int)$r['id']; ?>" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form method="post">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="assignment_id" value="<?php echo (int)$r['id']; ?>">
                        <div class="modal-header">
                          <h5 class="modal-title">Reject Assignment</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <p class="text-secondary small">
                            Rejecting assignment for <strong><?php echo htmlspecialchars($r['asset_name']); ?></strong>
                            submitted by <strong><?php echo htmlspecialchars($r['submitted_by_name']); ?></strong>.
                          </p>
                          <label class="form-label">Reason (optional)</label>
                          <textarea class="form-control" name="approval_note" rows="3"
                                    placeholder="e.g. Asset reserved for lab use, please check with IT Technician."></textarea>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-danger">Confirm Reject</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
              <?php endif; ?>

              <?php if ($filter !== 'pending'): ?>
              <td>
                <div class="small"><?php echo htmlspecialchars($r['approved_by_name'] ?? '—'); ?></div>
                <div class="small text-secondary"><?php echo $r['approved_at'] ? htmlspecialchars(substr($r['approved_at'], 0, 16)) : ''; ?></div>
              </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php layout_footer(); ?>
