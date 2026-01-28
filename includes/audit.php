<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

function audit_log(string $action, ?string $entity = null, $entityId = null, ?string $description = null): void
{
  try {
    $pdo = db();
    $user = auth_user();
    $userId = $user ? (int)$user['id'] : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt = $pdo->prepare("
      INSERT INTO audit_logs (user_id, action, entity, entity_id, description, ip_address, user_agent)
      VALUES (:user_id, :action, :entity, :entity_id, :description, :ip, :ua)
    ");
    $stmt->execute([
      ':user_id' => $userId,
      ':action' => $action,
      ':entity' => $entity,
      ':entity_id' => ($entityId === null) ? null : (string)$entityId,
      ':description' => $description,
      ':ip' => $ip,
      ':ua' => $ua ? substr($ua, 0, 255) : null,
    ]);
  } catch (Throwable $e) {
    // Never break the app if audit logging fails.
  }
}




