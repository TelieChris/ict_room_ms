<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['it_technician', 'super_admin']); // delete is admin-only

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

  $sid = (int)$_SESSION['user']['school_id'];
  $code = null;
  try {
    $stmt = $pdo->prepare("SELECT asset_code FROM assets WHERE id=:id AND school_id=:sid");
    $stmt->execute([':id' => $id, ':sid' => $sid]);
    $code = $stmt->fetchColumn() ?: null;
  } catch (Throwable $e) {}

  if ($code) {
    $stmt = $pdo->prepare("DELETE FROM assets WHERE id = :id AND school_id = :sid");
    $stmt->execute([':id' => $id, ':sid' => $sid]);
    audit_log('ASSET_DELETE', 'assets', $id, "Deleted asset {$code}");
  }

header('Location: ' . url('/admin/assets/index.php'));
exit;


