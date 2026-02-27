<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';

require_login();
require_role(['it_technician', 'super_admin']);

$pdo = db();

$sid = (int)$_SESSION['user']['school_id'];

$stmt = $pdo->prepare("
  SELECT a.*, u.username, u.full_name
  FROM audit_logs a
  LEFT JOIN users u ON u.id = a.user_id
  WHERE a.school_id = :sid
  ORDER BY a.id DESC
  LIMIT 200
");
$stmt->execute([':sid' => $sid]);
$logs = $stmt->fetchAll();

layout_header('Audit Log', 'audit');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Audit Log</h1>
    <div class="text-secondary">Tracks important actions for accountability.</div>
  </div>
  <span class="badge text-bg-light border">Last 200 events</span>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Entity</th>
            <th>Description</th>
            <th class="text-end">IP</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$logs): ?>
            <tr><td colspan="6" class="text-center text-secondary py-4">No audit events yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($logs as $row): ?>
            <tr>
              <td class="small text-secondary"><?php echo htmlspecialchars($row['created_at']); ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($row['full_name'] ?: 'System'); ?></div>
                <div class="small text-secondary"><?php echo htmlspecialchars($row['username'] ?: '-'); ?></div>
              </td>
              <td><span class="badge text-bg-secondary"><?php echo htmlspecialchars($row['action']); ?></span></td>
              <td class="small">
                <?php echo htmlspecialchars(($row['entity'] ?: '-') . (($row['entity_id']) ? ' #' . $row['entity_id'] : '')); ?>
              </td>
              <td><?php echo htmlspecialchars($row['description'] ?: '-'); ?></td>
              <td class="text-end small text-secondary"><?php echo htmlspecialchars($row['ip_address'] ?: '-'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php layout_footer(); ?>




