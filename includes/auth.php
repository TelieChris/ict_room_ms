<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/url.php';

function auth_user(): ?array
{
  start_secure_session();
  return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
  return auth_user() !== null;
}

function require_login(): void
{
  if (!is_logged_in()) {
    header('Location: ' . url('/auth/login.php'));
    exit;
  }
}

function require_role(array $allowedRoles): void
{
  $user = auth_user();
  if (!$user) {
    header('Location: ' . url('/auth/login.php'));
    exit;
  }

  $role = $user['role'] ?? '';
  if (!in_array($role, $allowedRoles, true)) {
    http_response_code(403);
    include __DIR__ . '/../views/errors/403.php';
    exit;
  }
}


