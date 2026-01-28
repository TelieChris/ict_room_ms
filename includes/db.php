<?php

require_once __DIR__ . '/../config/config.php';

function db(): PDO
{
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
  } catch (Throwable $e) {
    if (APP_ENV === 'development') {
      die('DB connection failed: ' . $e->getMessage());
    }
    die('Database connection failed.');
  }

  return $pdo;
}




