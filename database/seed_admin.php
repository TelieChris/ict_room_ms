<?php
/**
 * Run in browser once (then delete) OR run via CLI:
 * php database/seed_admin.php
 *
 * It will create/update the default admin user with a fresh password hash.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

$username = 'admin';
$password = 'Admin@12345'; // change after first login

$pdo = db();

// Ensure roles exist
$pdo->exec("INSERT INTO roles (name, description) VALUES
('admin','IT Technician - full access'),
('teacher','ICT Teacher/Lab Assistant - manage usage & report issues'),
('viewer','School management - view only')
ON DUPLICATE KEY UPDATE description=VALUES(description)");

$stmt = $pdo->prepare("SELECT id FROM roles WHERE name='admin' LIMIT 1");
$stmt->execute();
$roleId = (int)$stmt->fetchColumn();
if ($roleId <= 0) {
  die('Admin role not found.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
  INSERT INTO users (role_id, username, full_name, email, password_hash, is_active)
  VALUES (:role_id, :username, :full_name, NULL, :hash, 1)
  ON DUPLICATE KEY UPDATE role_id=VALUES(role_id), full_name=VALUES(full_name), password_hash=VALUES(password_hash), is_active=1
");
$stmt->execute([
  ':role_id' => $roleId,
  ':username' => $username,
  ':full_name' => 'System Administrator',
  ':hash' => $hash,
]);

echo "Admin user seeded/updated.\nUsername: {$username}\nPassword: {$password}\n";


