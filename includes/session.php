<?php

require_once __DIR__ . '/../config/config.php';

function start_secure_session(): void
{
  if (session_status() === PHP_SESSION_ACTIVE) return;

  session_name(SESSION_NAME);

  $cookieParams = session_get_cookie_params();

  // Support both modern (array) and older PHP versions for session cookie params
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
      'lifetime' => 0,
      'path' => $cookieParams['path'] ?? '/',
      'domain' => $cookieParams['domain'] ?? '',
      'secure' => $secure,
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
  } else {
    // Older PHP: no array support, no native SameSite flag
    session_set_cookie_params(
      0,
      $cookieParams['path'] ?? '/',
      $cookieParams['domain'] ?? '',
      $secure,
      true
    );
  }

  session_start();
}

function session_regenerate_safe(): void
{
  if (session_status() !== PHP_SESSION_ACTIVE) return;
  session_regenerate_id(true);
}


