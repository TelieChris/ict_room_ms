<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . url('/admin/users/index.php'));
  exit;
}

if (!csrf_verify()) {
  flash_set('error', 'Security check failed. Please try again.');
  header('Location: ' . url('/admin/users/index.php'));
  exit;
}

$pdo = db();
$id = (int)($_POST['id'] ?? 0);
$to = (int)($_POST['to'] ?? -1);
$to = ($to === 1) ? 1 : 0;

$me = auth_user();
$myId = $me ? (int)$me['id'] : 0;
if ($id <= 0) {
  flash_set('error', 'Invalid user.');
  header('Location: ' . url('/admin/users/index.php'));
  exit;
}
if ($id === $myId) {
  flash_set('error', 'You cannot change your own active status.');
  header('Location: ' . url('/admin/users/index.php'));
  exit;
}

$sid = (int)$_SESSION['user']['school_id'];

try {
  $stmt = $pdo->prepare("SELECT username, is_active FROM users WHERE id=:id AND school_id=:sid LIMIT 1");
  $stmt->execute([':id' => $id, ':sid' => $sid]);
  $u = $stmt->fetch();
  if (!$u) {
    flash_set('error', 'User not found.');
    header('Location: ' . url('/admin/users/index.php'));
    exit;
  }

  $stmt = $pdo->prepare("UPDATE users SET is_active=:a WHERE id=:id AND school_id=:sid");
  $stmt->execute([':a' => $to, ':id' => $id, ':sid' => $sid]);

  $label = ($to === 1) ? 'Activated' : 'Disabled';
  audit_log('USER_STATUS', 'users', $id, "{$label} user {$u['username']}");
  flash_set('success', "{$label} user successfully.");
} catch (Throwable $e) {
  flash_set('error', 'Failed to update user status.');
}

header('Location: ' . url('/admin/users/index.php'));
exit;




