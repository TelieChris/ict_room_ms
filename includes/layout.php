<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/url.php';
require_once __DIR__ . '/flash.php';

function layout_header(string $title, string $active = ''): void
{
  start_secure_session();
  $user = auth_user();
  $role = $user['role'] ?? '';
  $fullName = $user['full_name'] ?? '';
  $appTitle = APP_NAME;

  $navItems = [
    ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => url('/index.php'), 'key' => 'dashboard', 'roles' => ['admin','teacher','viewer']],
    ['label' => 'Assets', 'icon' => 'bi-pc-display', 'href' => url('/admin/assets/index.php'), 'key' => 'assets', 'roles' => ['admin','teacher','viewer']],
    ['label' => 'Assignments', 'icon' => 'bi-box-arrow-in-right', 'href' => url('/teacher/assignments/index.php'), 'key' => 'assignments', 'roles' => ['admin','teacher','viewer']],
    ['label' => 'Maintenance', 'icon' => 'bi-wrench-adjustable', 'href' => url('/teacher/maintenance/index.php'), 'key' => 'maintenance', 'roles' => ['admin','teacher']],
    ['label' => 'Reports', 'icon' => 'bi-file-earmark-text', 'href' => url('/reports/index.php'), 'key' => 'reports', 'roles' => ['admin','teacher','viewer']],
    ['label' => 'Audit Log', 'icon' => 'bi-journal-check', 'href' => url('/admin/audit/index.php'), 'key' => 'audit', 'roles' => ['admin']],
    ['label' => 'Users', 'icon' => 'bi-people', 'href' => url('/admin/users/index.php'), 'key' => 'users', 'roles' => ['admin']],
    ['label' => 'Schools', 'icon' => 'bi-building', 'href' => url('/admin/schools/index.php'), 'key' => 'schools', 'roles' => ['admin']],
  ];

  ?>
  <!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title); ?> • <?php echo htmlspecialchars($appTitle); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo htmlspecialchars(url('/assets/css/app.css')); ?>" rel="stylesheet">
  </head>
  <body>
    <div class="d-flex app-shell">
      <aside class="sidebar d-none d-md-flex flex-column p-3">
        <div class="d-flex align-items-center gap-2 mb-3 text-white">
          <div class="rounded-3 bg-white bg-opacity-10 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
            <i class="bi bi-shield-check"></i>
          </div>
          <div>
            <div class="brand"><?php echo htmlspecialchars($appTitle); ?></div>
            <div class="small text-white-50"><?php echo htmlspecialchars($user['school_name'] ?? 'System'); ?></div>
          </div>
        </div>

        <div class="small text-white-50 mb-2">MENU</div>
        <nav class="nav nav-pills flex-column gap-1">
          <?php foreach ($navItems as $item): ?>
            <?php if (!in_array($role, $item['roles'], true)) continue; ?>
            <a class="nav-link <?php echo ($active === $item['key']) ? 'active' : ''; ?>"
               href="<?php echo htmlspecialchars($item['href']); ?>">
              <i class="bi <?php echo htmlspecialchars($item['icon']); ?> me-2"></i>
              <?php echo htmlspecialchars($item['label']); ?>
            </a>
          <?php endforeach; ?>
        </nav>

        <div class="mt-auto pt-3 border-top border-white border-opacity-10">
          <div class="text-white-50 small">Signed in as</div>
          <div class="text-white fw-semibold"><?php echo htmlspecialchars($fullName ?: 'User'); ?></div>
          <div class="text-white-50 small text-uppercase"><?php echo htmlspecialchars($role ?: '-'); ?></div>
          <div class="mt-2">
            <a class="btn btn-sm btn-outline-light" href="<?php echo htmlspecialchars(url('/auth/logout.php')); ?>">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
          </div>
        </div>
      </aside>

      <main class="content">
        <nav class="navbar navbar-expand bg-white border-bottom">
          <div class="container-fluid">
            <span class="navbar-brand d-md-none fw-bold"><?php echo htmlspecialchars($appTitle); ?></span>
            <div class="ms-auto d-flex align-items-center gap-2">
              <span class="text-secondary small d-none d-sm-inline">
                <?php echo htmlspecialchars($fullName ?: ''); ?>
              </span>
              <a class="btn btn-sm btn-outline-secondary d-md-none" href="<?php echo htmlspecialchars(url('/auth/logout.php')); ?>">
                <i class="bi bi-box-arrow-right"></i>
              </a>
            </div>
          </div>
        </nav>

        <div class="container-fluid py-4">
          <?php if ($flash = flash_get()): ?>
            <?php
              $type = $flash['type'] ?? 'info';
              $msg = $flash['message'] ?? '';
              $map = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];
              $bs = $map[$type] ?? 'info';
            ?>
            <div class="alert alert-<?php echo htmlspecialchars($bs); ?> py-2">
              <?php echo htmlspecialchars($msg); ?>
            </div>
          <?php endif; ?>
  <?php
}

function layout_footer(): void
{
  ?>
        </div>
      </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo htmlspecialchars(url('/assets/js/app.js')); ?>"></script>
  </body>
  </html>
  <?php
}


