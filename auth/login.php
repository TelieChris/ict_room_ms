<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/url.php';

start_secure_session();

if (!empty($_SESSION['user'])) {
  header('Location: ' . url('/index.php'));
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = 'Please enter username and password.';
  } else {
    $pdo = db();
    $stmt = $pdo->prepare("
      SELECT u.id, u.username, u.full_name, u.password_hash, u.is_active, r.name AS role
      FROM users u
      JOIN roles r ON r.id = u.role_id
      WHERE u.username = :username
      LIMIT 1
    ");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if (!$user || (int)$user['is_active'] !== 1 || !password_verify($password, $user['password_hash'])) {
      $error = 'Invalid credentials.';
    } else {
      session_regenerate_safe();
      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'],
        'role' => $user['role'],
      ];

      $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id")->execute([':id' => (int)$user['id']]);
      audit_log('LOGIN', 'users', (int)$user['id'], 'User logged in');

      header('Location: ' . url('/index.php'));
      exit;
    }
  }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • ICT Room Asset Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?php echo htmlspecialchars(url('/assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-7 col-lg-5">
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-primary bg-opacity-10 text-primary mb-3" style="width:64px;height:64px;">
            <i class="bi bi-shield-lock fs-2"></i>
          </div>
          <h1 class="h4 mb-1">ICT Room Asset Management</h1>
          <div class="text-secondary">GS Remera TSS</div>
        </div>

        <div class="card table-card">
          <div class="card-body p-4">
            <h2 class="h6 mb-3">Sign in</h2>

            <?php if ($error): ?>
              <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100" type="submit">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </button>
            </form>

            <hr class="my-4">
            <div class="small text-secondary">
              First time setup? Import `database/schema.sql` then run `database/seed_admin.php`.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>
</html>


