<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['super_admin']);

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

$isSuper = is_super_admin();
$sid_from_session = (int)$_SESSION['user']['school_id'];

try {
  $query = "SELECT username, is_active FROM users WHERE id=:id";
  $params = [':id' => $id];
  if (!$isSuper) {
      $query .= " AND school_id=:sid";
      $params[':sid'] = $sid_from_session;
  }
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $u = $stmt->fetch();
  if (!$u) {
    flash_set('error', 'User not found.');
    header('Location: ' . url('/admin/users/index.php'));
    exit;
  }

  $update_query = "UPDATE users SET is_active=:a WHERE id=:id";
  $update_params = [':a' => $to, ':id' => $id];
  if (!$isSuper) {
      $update_query .= " AND school_id=:sid";
      $update_params[':sid'] = $sid_from_session;
  }
  $stmt = $pdo->prepare($update_query);
  $stmt->execute($update_params);

  $label = ($to === 1) ? 'Activated' : 'Disabled';
  audit_log('USER_STATUS', 'users', $id, "{$label} user {$u['username']}");
  flash_set('success', "{$label} user successfully.");
} catch (Throwable $e) {
  flash_set('error', 'Failed to update user status.');
}

header('Location: ' . url('/admin/users/index.php'));
exit;




