<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin','teacher']);

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
  header('Location: ' . url('/teacher/assignments/index.php'));
  exit;
}

try {
  $stmt = $pdo->prepare("
    SELECT aa.id, aa.asset_id, aa.returned_date, a.asset_code
    FROM asset_assignments aa
    JOIN assets a ON a.id = aa.asset_id
    WHERE aa.id = :id
    LIMIT 1
  ");
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch();
  if (!$row) {
    header('Location: ' . url('/teacher/assignments/index.php'));
    exit;
  }

  if (!empty($row['returned_date'])) {
    header('Location: ' . url('/teacher/assignments/index.php'));
    exit;
  }

  $pdo->beginTransaction();

  $pdo->prepare("UPDATE asset_assignments SET returned_date = CURDATE() WHERE id=:id")->execute([':id' => $id]);

  // Auto status update back to Available
  $pdo->prepare("UPDATE assets SET status='Available' WHERE id=:id")->execute([':id' => (int)$row['asset_id']]);

  $pdo->commit();

  audit_log('ASSIGN_RETURN', 'asset_assignments', $id, "Returned asset {$row['asset_code']}");
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
}

header('Location: ' . url('/teacher/assignments/index.php'));
exit;




