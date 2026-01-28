<?php

require_once __DIR__ . '/session.php';

function csrf_token(): string
{
  start_secure_session();
  if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
  $t = csrf_token();
  return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($t) . '">';
}

function csrf_verify(): bool
{
  start_secure_session();
  $sent = $_POST['_csrf'] ?? '';
  $real = $_SESSION['_csrf_token'] ?? '';
  return is_string($sent) && is_string($real) && $sent !== '' && hash_equals($real, $sent);
}




