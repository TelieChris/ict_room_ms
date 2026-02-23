<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/url.php';

require_login();

$pdo = db();

$stats = [
  'total' => 0,
  'available' => 0,
  'in_use' => 0,
  'maintenance' => 0,
];

$stats['total'] = (int)$pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
$stats['available'] = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='Available'")->fetchColumn();
$stats['in_use'] = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='In Use'")->fetchColumn();
$stats['maintenance'] = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='Maintenance'")->fetchColumn();

layout_header('Dashboard', 'dashboard');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Dashboard</h1>
    <div class="text-secondary">Quick overview of ICT room assets.</div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>">
      <i class="bi bi-file-earmark-text me-1"></i> Reports
    </a>
    <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/assets/create.php')); ?>">
      <i class="bi bi-plus-lg me-1"></i> Add Asset
    </a>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-secondary small">Total Assets</div>
          <div class="fs-3 fw-bold"><?php echo $stats['total']; ?></div>
        </div>
        <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
          <i class="bi bi-box-seam fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-secondary small">Available</div>
          <div class="fs-3 fw-bold"><?php echo $stats['available']; ?></div>
        </div>
        <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
          <i class="bi bi-check2-circle fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-secondary small">In Use</div>
          <div class="fs-3 fw-bold"><?php echo $stats['in_use']; ?></div>
        </div>
        <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
          <i class="bi bi-arrow-left-right fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-secondary small">Maintenance</div>
          <div class="fs-3 fw-bold"><?php echo $stats['maintenance']; ?></div>
        </div>
        <div class="rounded-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
          <i class="bi bi-wrench fs-4"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-12 col-xl-8">
    <div class="card table-card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <h2 class="h6 mb-0">Status Overview</h2>
          <span class="text-secondary small">Simple chart placeholder</span>
        </div>
        <hr class="my-3">
        <div class="p-3 bg-light rounded-3 border">
          <div class="row g-3">
            <div class="col-6 col-lg-3">
              <div class="small text-secondary">Available</div>
              <div class="fw-semibold"><?php echo $stats['available']; ?></div>
            </div>
            <div class="col-6 col-lg-3">
              <div class="small text-secondary">In Use</div>
              <div class="fw-semibold"><?php echo $stats['in_use']; ?></div>
            </div>
            <div class="col-6 col-lg-3">
              <div class="small text-secondary">Maintenance</div>
              <div class="fw-semibold"><?php echo $stats['maintenance']; ?></div>
            </div>
            <div class="col-6 col-lg-3">
              <div class="small text-secondary">Lost</div>
              <div class="fw-semibold"><?php
                echo (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='Lost'")->fetchColumn();
              ?></div>
            </div>
          </div>
          <div class="small text-secondary mt-3">
            (Optional) Next step: add Chart.js for a lightweight bar chart.
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="card table-card">
      <div class="card-body">
        <h2 class="h6">Quick Actions</h2>
        <div class="d-grid gap-2">
          <a class="btn btn-outline-primary" href="<?php echo htmlspecialchars(url('/admin/assets/index.php')); ?>">
            <i class="bi bi-pc-display me-1"></i> Manage Assets
          </a>
          <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/maintenance/index.php')); ?>">
            <i class="bi bi-wrench-adjustable me-1"></i> Report Maintenance
          </a>
          <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>">
            <i class="bi bi-printer me-1"></i> Inventory Reports
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php layout_footer(); ?>


