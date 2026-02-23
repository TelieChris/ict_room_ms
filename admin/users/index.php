<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';

require_login();
require_role(['admin']);

$pdo = db();

$sid = (int)$_SESSION['user']['school_id'];

$stmt = $pdo->prepare("
  SELECT u.id, u.username, u.full_name, u.email, u.is_active, u.last_login_at, u.created_at, r.name AS role
  FROM users u
  JOIN roles r ON r.id = u.role_id
  WHERE u.school_id = :sid
  ORDER BY u.id DESC
  LIMIT 200
");
$stmt->execute([':sid' => $sid]);
$users = $stmt->fetchAll();

layout_header('Users', 'users');
$me = auth_user();
$myId = $me ? (int)$me['id'] : 0;
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Users</h1>
    <div class="text-secondary">Manage accounts and roles (Admin only).</div>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <span class="badge text-bg-light border">Last 200 users</span>
    <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/users/create.php')); ?>">
      <i class="bi bi-plus-lg me-1"></i> Add User
    </a>
  </div>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>User</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$users): ?>
            <tr><td colspan="6" class="text-center text-secondary py-4">No users found.</td></tr>
          <?php endif; ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($u['full_name']); ?></div>
                <div class="small text-secondary">
                  <?php echo htmlspecialchars($u['username']); ?>
                  <?php if (!empty($u['email'])): ?> • <?php echo htmlspecialchars($u['email']); ?><?php endif; ?>
                </div>
              </td>
              <td><span class="badge text-bg-secondary text-uppercase"><?php echo htmlspecialchars($u['role']); ?></span></td>
              <td>
                <?php if ((int)$u['is_active'] === 1): ?>
                  <span class="badge text-bg-success">Active</span>
                <?php else: ?>
                  <span class="badge text-bg-danger">Disabled</span>
                <?php endif; ?>
              </td>
              <td class="small text-secondary"><?php echo htmlspecialchars($u['last_login_at'] ?: '-'); ?></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($u['created_at']); ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="<?php echo htmlspecialchars(url('/admin/users/edit.php')); ?>?id=<?php echo (int)$u['id']; ?>">
                  <i class="bi bi-pencil"></i>
                </a>
                <a class="btn btn-sm btn-outline-secondary"
                   href="<?php echo htmlspecialchars(url('/admin/users/reset_password.php')); ?>?id=<?php echo (int)$u['id']; ?>">
                  <i class="bi bi-key"></i>
                </a>

                <form method="post" action="<?php echo htmlspecialchars(url('/admin/users/toggle_active.php')); ?>"
                      class="d-inline">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                  <input type="hidden" name="to" value="<?php echo ((int)$u['is_active'] === 1) ? '0' : '1'; ?>">
                  <?php
                    $isMe = ((int)$u['id'] === $myId);
                    $btnClass = ((int)$u['is_active'] === 1) ? 'btn-outline-danger' : 'btn-outline-success';
                    $icon = ((int)$u['is_active'] === 1) ? 'bi-person-x' : 'bi-person-check';
                    $label = ((int)$u['is_active'] === 1) ? 'Disable' : 'Activate';
                  ?>
                  <button class="btn btn-sm <?php echo $btnClass; ?>"
                          type="submit"
                          <?php echo $isMe ? 'disabled' : ''; ?>
                          data-confirm="<?php echo $isMe ? '' : ($label . ' this user?'); ?>">
                    <i class="bi <?php echo $icon; ?>"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="small text-secondary">
        Note: you can’t disable your own account (safety).
      </div>
    </div>
  </div>
</div>

<?php layout_footer(); ?>


