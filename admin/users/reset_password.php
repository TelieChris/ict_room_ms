<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin']);

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $id]);
$u = $stmt->fetch();
if (!$u) {
  flash_set('error', 'User not found.');
  header('Location: ' . url('/admin/users/index.php'));
  exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $errors[] = 'Security check failed. Please refresh and try again.';
  }
  $p1 = (string)($_POST['password'] ?? '');
  $p2 = (string)($_POST['password2'] ?? '');
  if ($p1 === '') $errors[] = 'New password is required.';
  if ($p1 !== $p2) $errors[] = 'Passwords do not match.';

  if (!$errors) {
    try {
      $hash = password_hash($p1, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE users SET password_hash=:h WHERE id=:id");
      $stmt->execute([':h' => $hash, ':id' => (int)$u['id']]);

      audit_log('USER_PASSWORD_RESET', 'users', (int)$u['id'], "Reset password for {$u['username']}");
      flash_set('success', 'Password reset successfully.');
      header('Location: ' . url('/admin/users/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Failed to reset password.';
    }
  }
}

layout_header('Reset Password', 'users');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Reset Password</h1>
    <div class="text-secondary"><?php echo htmlspecialchars($u['full_name']); ?> • <?php echo htmlspecialchars($u['username']); ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/users/index.php')); ?>">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Please fix the following:</div>
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card table-card">
  <div class="card-body">
    <form method="post" class="row g-3">
      <?php echo csrf_field(); ?>

      <div class="col-12 col-md-6">
        <label class="form-label">New Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Confirm New Password</label>
        <input class="form-control" type="password" name="password2" required>
      </div>

      <div class="col-12">
        <div class="alert alert-warning mb-0">
          Share the new password securely with the user (avoid public chat groups).
        </div>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Reset Password
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/users/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>




