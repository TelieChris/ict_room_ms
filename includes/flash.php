<?php

require_once __DIR__ . '/session.php';

function flash_set(string $type, string $message): void
{
  start_secure_session();
  $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
  start_secure_session();
  if (empty($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) return null;
  $f = $_SESSION['_flash'];
  unset($_SESSION['_flash']);
  return $f;
}




