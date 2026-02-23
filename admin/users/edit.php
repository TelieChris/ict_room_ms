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

$sid = (int)$_SESSION['user']['school_id'];

$stmt = $pdo->prepare("
  SELECT u.id, u.role_id, u.username, u.full_name, u.email, u.is_active
  FROM users u
  WHERE u.id = :id AND u.school_id = :sid
  LIMIT 1
");
$stmt->execute([':id' => $id, ':sid' => $sid]);
$userRow = $stmt->fetch();
if (!$userRow) {
  flash_set('error', 'User not found.');
  header('Location: ' . url('/admin/users/index.php'));
  exit;
}

$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
$errors = [];

function ev(string $k, $d)
{
  return $_POST[$k] ?? $d;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $errors[] = 'Security check failed. Please refresh and try again.';
  }

  $role_id = (int)ev('role_id', $userRow['role_id']);
  $username = trim((string)ev('username', $userRow['username']));
  $full_name = trim((string)ev('full_name', $userRow['full_name']));
  $email = trim((string)ev('email', $userRow['email']));
  $is_active = (int)ev('is_active', $userRow['is_active']) === 1 ? 1 : 0;

  if ($role_id <= 0) $errors[] = 'Role is required.';
  if ($username === '') $errors[] = 'Username is required.';
  if ($full_name === '') $errors[] = 'Full name is required.';
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is not valid.';

  // Safety: admin cannot disable own account here
  $me = auth_user();
  if ($me && (int)$me['id'] === (int)$userRow['id'] && $is_active === 0) {
    $errors[] = 'You cannot disable your own account.';
  }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare("
        UPDATE users
        SET role_id=:role_id, username=:username, full_name=:full_name, email=:email, is_active=:active
        WHERE id=:id AND school_id=:sid
      ");
      $stmt->execute([
        ':role_id' => $role_id,
        ':username' => $username,
        ':full_name' => $full_name,
        ':email' => ($email !== '') ? $email : null,
        ':active' => $is_active,
        ':id' => $id,
        ':sid' => $sid,
      ]);

      audit_log('USER_UPDATE', 'users', $id, "Updated user {$username}");
      flash_set('success', 'User updated successfully.');
      header('Location: ' . url('/admin/users/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Failed to update user. (Username/email might already exist.)';
    }
  }
}

layout_header('Edit User', 'users');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Edit User</h1>
    <div class="text-secondary"><?php echo htmlspecialchars($userRow['full_name']); ?> • <?php echo htmlspecialchars($userRow['username']); ?></div>
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

      <div class="col-12 col-md-4">
        <label class="form-label">Role</label>
        <select class="form-select" name="role_id" required>
          <option value="">Select...</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?php echo (int)$r['id']; ?>" <?php echo ((int)ev('role_id', $userRow['role_id']) === (int)$r['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($r['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required value="<?php echo htmlspecialchars(ev('username', $userRow['username'])); ?>">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="is_active">
          <option value="1" <?php echo ((int)ev('is_active', $userRow['is_active']) === 1) ? 'selected' : ''; ?>>Active</option>
          <option value="0" <?php echo ((int)ev('is_active', $userRow['is_active']) === 0) ? 'selected' : ''; ?>>Disabled</option>
        </select>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Full Name</label>
        <input class="form-control" name="full_name" required value="<?php echo htmlspecialchars(ev('full_name', $userRow['full_name'])); ?>">
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Email (optional)</label>
        <input class="form-control" name="email" value="<?php echo htmlspecialchars(ev('email', $userRow['email'])); ?>">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Changes
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/users/index.php')); ?>">Cancel</a>
        <a class="btn btn-outline-secondary ms-auto"
           href="<?php echo htmlspecialchars(url('/admin/users/reset_password.php')); ?>?id=<?php echo (int)$userRow['id']; ?>">
          <i class="bi bi-key me-1"></i> Reset Password
        </a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>




