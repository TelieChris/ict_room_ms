<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['super_admin']);

$pdo = db();
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
$isSuper = is_super_admin();
$schools = $isSuper ? $pdo->query("SELECT id, name FROM schools ORDER BY name")->fetchAll() : [];

$sid = (int)($_SESSION['user']['school_id'] ?? 0);
$locations = $pdo->prepare("SELECT id, name FROM locations WHERE school_id = ? ORDER BY name");
$locations->execute([$sid]);
$locations = $locations->fetchAll();

$errors = [];

function uv(string $k, $d = '')
{
  return $_POST[$k] ?? $d;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify()) {
    $errors[] = 'Security check failed. Please refresh and try again.';
  }

  $role_id = (int)uv('role_id', 0);
  $username = trim((string)uv('username', ''));
  $full_name = trim((string)uv('full_name', ''));
  $email = trim((string)uv('email', ''));
  $password = (string)uv('password', '');
  $password2 = (string)uv('password2', '');
  $is_active = (int)uv('is_active', 1) === 1 ? 1 : 0;
  $form_school_id = (int)uv('school_id', 0);
  $location_id = (int)uv('location_id', 0);

  if ($role_id <= 0) $errors[] = 'Role is required.';
  if ($isSuper && $form_school_id <= 0) $errors[] = 'School is required.';
  if ($username === '') $errors[] = 'Username is required.';
  if ($full_name === '') $errors[] = 'Full name is required.';
  if ($password === '') $errors[] = 'Password is required.';
  if ($password !== $password2) $errors[] = 'Passwords do not match.';
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is not valid.';

  if (!$errors) {
    try {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $sid = $isSuper ? $form_school_id : (int)$_SESSION['user']['school_id'];
      $stmt = $pdo->prepare("
        INSERT INTO users (school_id, role_id, location_id, username, full_name, email, password_hash, is_active)
        VALUES (:school_id, :role_id, :location_id, :username, :full_name, :email, :hash, :active)
      ");
      $stmt->execute([
        ':school_id' => $sid,
        ':role_id' => $role_id,
        ':location_id' => ($location_id > 0) ? $location_id : null,
        ':username' => $username,
        ':full_name' => $full_name,
        ':email' => ($email !== '') ? $email : null,
        ':hash' => $hash,
        ':active' => $is_active,
      ]);

      $newId = (int)$pdo->lastInsertId();
      audit_log('USER_CREATE', 'users', $newId, "Created user {$username}");
      flash_set('success', 'User created successfully.');
      header('Location: ' . url('/admin/users/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Failed to create user. (Username/email might already exist.)';
    }
  }
}

layout_header('Add User', 'users');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Add User</h1>
    <div class="text-secondary">Create an account and assign a role.</div>
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
            <option value="<?php echo (int)$r['id']; ?>" <?php echo ((int)uv('role_id', 0) === (int)$r['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($r['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if ($isSuper): ?>
        <div class="col-12 col-md-4">
          <label class="form-label">School</label>
          <select class="form-select" name="school_id" required>
            <option value="">Select...</option>
            <?php foreach ($schools as $sch): ?>
              <option value="<?php echo (int)$sch['id']; ?>" <?php echo ((int)uv('school_id', 0) === (int)$sch['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($sch['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

      <div class="col-12 col-md-<?php echo $isSuper ? '4' : '8'; ?>">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required value="<?php echo htmlspecialchars(uv('username', '')); ?>">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="is_active">
          <option value="1" <?php echo ((int)uv('is_active', 1) === 1) ? 'selected' : ''; ?>>Active</option>
          <option value="0" <?php echo ((int)uv('is_active', 1) === 0) ? 'selected' : ''; ?>>Disabled</option>
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Assigned ICT Lab (Optional)</label>
        <select class="form-select" name="location_id">
          <option value="0">Not specific / All Labs Access</option>
          <?php foreach ($locations as $l): ?>
            <option value="<?php echo (int)$l['id']; ?>" <?php echo ((int)uv('location_id', 0) === (int)$l['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($l['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Full Name</label>
        <input class="form-control" name="full_name" required value="<?php echo htmlspecialchars(uv('full_name', '')); ?>">
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Email (optional)</label>
        <input class="form-control" name="email" value="<?php echo htmlspecialchars(uv('email', '')); ?>">
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Confirm Password</label>
        <input class="form-control" type="password" name="password2" required>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Create User
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/users/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>




