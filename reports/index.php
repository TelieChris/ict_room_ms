<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/url.php';

require_login();

layout_header('Reports Dashboard', 'reports');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h1 class="h3 mb-1 fw-bold text-primary">Reports Dashboard</h1>
    <div class="text-secondary">Select a report type to view detailed insights.</div>
  </div>
</div>

<div class="row g-4">
  <!-- Asset Inventory Report Card -->
  <div class="col-12 col-md-4">
    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
      <div class="card-body p-4 d-flex flex-column">
        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
          <i class="bi bi-box-seam fs-3"></i>
        </div>
        <h4 class="h5 fw-bold mb-2">Asset Inventory</h4>
        <p class="text-secondary small mb-4 flex-grow-1">
          Comprehensive overview of all physical hardware, their conditions, current locations, and operational status.
        </p>
        <a href="<?php echo htmlspecialchars(url('/reports/inventory.php')); ?>" class="btn btn-primary w-100 fw-semibold rounded-pill">
          View Report <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Asset Assignments Report Card -->
  <div class="col-12 col-md-4">
    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
      <div class="card-body p-4 d-flex flex-column">
        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
          <i class="bi bi-person-workspace fs-3"></i>
        </div>
        <h4 class="h5 fw-bold mb-2">Asset Assignments</h4>
        <p class="text-secondary small mb-4 flex-grow-1">
          Track which assets are currently assigned to teachers or staff members, including assignment and expected return dates.
        </p>
        <a href="<?php echo htmlspecialchars(url('/reports/assignments.php')); ?>" class="btn btn-outline-success w-100 fw-semibold rounded-pill">
          View Report <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Maintenance Logs Report Card -->
  <div class="col-12 col-md-4">
    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
      <div class="card-body p-4 d-flex flex-column">
        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
          <i class="bi bi-tools fs-3"></i>
        </div>
        <h4 class="h5 fw-bold mb-2">Maintenance Logs</h4>
        <p class="text-secondary small mb-4 flex-grow-1">
          Detailed history of reported issues, repair actions taken, technician notes, and associated maintenance costs.
        </p>
        <a href="<?php echo htmlspecialchars(url('/reports/maintenance.php')); ?>" class="btn btn-outline-danger w-100 fw-semibold rounded-pill">
          View Report <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-5px);
}
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<?php layout_footer(); ?>


