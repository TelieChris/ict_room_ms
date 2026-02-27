<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';

require_login();
require_role(['super_admin']);

$pdo = db();

$isSuper = is_super_admin();
$school_filter = (int)($_GET['school_id'] ?? 0);

$where = [];
$params = [];

if (!$isSuper) {
    $where[] = "u.school_id = :sid";
    $params[':sid'] = (int)$_SESSION['user']['school_id'];
} elseif ($school_filter > 0) {
    $where[] = "u.school_id = :school_filter";
    $params[':school_filter'] = $school_filter;
}

$query = "
  SELECT u.id, u.username, u.full_name, u.email, u.is_active, u.last_login_at, u.created_at, 
         r.name AS role, s.name AS school_name, l.name AS location_name
  FROM users u
  JOIN roles r ON r.id = u.role_id
  JOIN schools s ON s.id = u.school_id
  LEFT JOIN locations l ON l.id = u.location_id
";

if ($where) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY u.id DESC LIMIT 200";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

$schools = $isSuper ? $pdo->query("SELECT id, name FROM schools ORDER BY name")->fetchAll() : [];

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
    <?php if ($isSuper): ?>
      <form class="d-flex gap-2 align-items-center me-2">
        <select name="school_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="0">All Schools</option>
          <?php foreach ($schools as $sch): ?>
            <option value="<?php echo (int)$sch['id']; ?>" <?php echo $school_filter === (int)$sch['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($sch['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php endif; ?>
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
            <?php if ($isSuper): ?><th>School</th><?php endif; ?>
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
                  <?php if (!empty($u['location_name'])): ?> • <span class="text-primary fw-semibold"><?php echo htmlspecialchars($u['location_name']); ?></span><?php endif; ?>
                </div>
              </td>
              <?php if ($isSuper): ?>
                <td><div class="small fw-semibold"><?php echo htmlspecialchars($u['school_name']); ?></div></td>
              <?php endif; ?>
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


