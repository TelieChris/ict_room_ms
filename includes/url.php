<?php

require_once __DIR__ . '/../config/config.php';

function base_url(): string
{
  $b = defined('BASE_URL') ? (string)BASE_URL : '';
  $b = rtrim($b, '/');
  return $b;
}

function url(string $path): string
{
  if ($path === '') return base_url() ?: '/';
  if ($path[0] !== '/') $path = '/' . $path;
  $full = base_url() . $path;
  return $full === '' ? '/' : $full;
}




