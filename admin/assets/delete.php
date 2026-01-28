<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['admin']); // delete is admin-only

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
  $code = null;
  try {
    $stmt = $pdo->prepare("SELECT asset_code FROM assets WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $code = $stmt->fetchColumn() ?: null;
  } catch (Throwable $e) {}

  $stmt = $pdo->prepare("DELETE FROM assets WHERE id = :id");
  $stmt->execute([':id' => $id]);
  audit_log('ASSET_DELETE', 'assets', $id, $code ? "Deleted asset {$code}" : "Deleted asset #{$id}");
}

header('Location: ' . url('/admin/assets/index.php'));
exit;


