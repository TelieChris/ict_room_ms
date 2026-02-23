<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/url.php';

require_login();

$pdo = db();

// Basic Status Stats
$sid = (int)$_SESSION['user']['school_id'];
$stats = [
  'total' => 0,
  'available' => 0,
  'in_use' => 0,
  'maintenance' => 0,
  'lost' => 0,
];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM assets WHERE school_id = ? AND status = ?");
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM assets WHERE school_id = ?");
$stmt_total->execute([$sid]);
$stats['total'] = (int)$stmt_total->fetchColumn();

$stmt_status = $pdo->prepare("SELECT COUNT(*) FROM assets WHERE school_id = ? AND status = ?");
foreach (['Available', 'In Use', 'Maintenance', 'Lost'] as $status) {
    $stmt_status->execute([$sid, $status]);
    $stats[strtolower(str_replace(' ', '_', $status))] = (int)$stmt_status->fetchColumn();
}

// Condition Stats for Chart
$stmt_cond = $pdo->prepare("SELECT asset_condition, COUNT(*) as count FROM assets WHERE school_id = ? GROUP BY asset_condition");
$stmt_cond->execute([$sid]);
$conditionData = $stmt_cond->fetchAll(PDO::FETCH_KEY_PAIR);
$conditions = ['New', 'Good', 'Fair', 'Damaged'];
$conditionCounts = [];
foreach ($conditions as $cnd) {
    $conditionCounts[] = (int)($conditionData[$cnd] ?? 0);
}

// Recent Activity
$stmt_logs = $pdo->prepare("
  SELECT l.*, u.full_name 
  FROM audit_logs l 
  LEFT JOIN users u ON u.id = l.user_id 
  WHERE l.school_id = ?
  ORDER BY l.created_at DESC 
  LIMIT 6
");
$stmt_logs->execute([$sid]);
$recentLogs = $stmt_logs->fetchAll();

layout_header('Dashboard', 'dashboard');
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h1 class="h4 mb-1 fw-bold">System Overview</h1>
    <div class="text-secondary small">Real-time monitoring of ICT inventory and activity.</div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-white border shadow-sm btn-sm px-3" href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>">
      <i class="bi bi-file-earmark-text me-1"></i> Reports
    </a>
    <a class="btn btn-primary btn-sm px-3 shadow-sm" href="<?php echo htmlspecialchars(url('/admin/assets/create.php')); ?>">
      <i class="bi bi-plus-lg me-1"></i> Add Asset
    </a>
  </div>
</div>

<!-- Primary Stats Grid -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
      <div class="card-body p-3 text-white">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="opacity-75 small fw-medium mb-1">Total Fixed Assets</div>
            <div class="fs-2 fw-bold"><?php echo $stats['total']; ?></div>
          </div>
          <div class="bg-white bg-opacity-20 rounded-3 p-3">
            <i class="bi bi-bank fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm" style="background: white;">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-secondary small fw-medium mb-1">Available Now</div>
            <div class="fs-2 fw-bold text-success"><?php echo $stats['available']; ?></div>
          </div>
          <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
            <i class="bi bi-check-circle fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm" style="background: white;">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-secondary small fw-medium mb-1">Currently assigned</div>
            <div class="fs-2 fw-bold text-primary"><?php echo $stats['in_use']; ?></div>
          </div>
          <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
            <i class="bi bi-person-check fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm" style="background: white;">
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-secondary small fw-medium mb-1">In Maintenance</div>
            <div class="fs-2 fw-bold text-danger"><?php echo $stats['maintenance']; ?></div>
          </div>
          <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
            <i class="bi bi-tools fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Analytics Section -->
  <div class="col-12 col-xl-8">
    <div class="row g-4 mb-4">
      <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-4">Availability Overview</h6>
            <div style="height: 220px;">
              <canvas id="availabilityChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h6 class="fw-bold mb-4">Condition Distribution</h6>
            <div style="height: 220px;">
              <canvas id="conditionChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Table-style Feed -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="p-3 d-flex align-items-center justify-content-between">
          <h6 class="fw-bold mb-0">System Log Activity</h6>
          <a href="<?php echo url('/admin/audit/index.php'); ?>" class="btn btn-sm btn-link text-decoration-none">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="bg-light text-secondary small">
              <tr>
                <th class="ps-3 py-2 border-0">Action</th>
                <th class="py-2 border-0">Details</th>
                <th class="py-2 border-0">User</th>
                <th class="py-2 border-0 pe-3 text-end">Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentLogs as $log): ?>
                <tr>
                  <td class="ps-3">
                    <?php
                      $actionColor = 'secondary';
                      if (strpos($log['action'], 'CREATE') !== false) $actionColor = 'success';
                      elseif (strpos($log['action'], 'UPDATE') !== false) $actionColor = 'primary';
                      elseif (strpos($log['action'], 'DELETE') !== false) $actionColor = 'danger';
                      elseif (strpos($log['action'], 'ASSIGN') !== false) $actionColor = 'info';
                    ?>
                    <span class="badge rounded-pill bg-<?php echo $actionColor; ?> bg-opacity-10 text-<?php echo $actionColor; ?> px-2">
                      <?php echo htmlspecialchars($log['action']); ?>
                    </span>
                  </td>
                  <td>
                    <div class="text-dark fw-medium"><?php echo htmlspecialchars($log['description']); ?></div>
                    <div class="text-secondary small italic"><?php echo htmlspecialchars($log['entity'] ?: '-'); ?> #<?php echo htmlspecialchars($log['entity_id'] ?: '-'); ?></div>
                  </td>
                  <td><?php echo htmlspecialchars($log['full_name'] ?: 'System'); ?></td>
                  <td class="pe-3 text-end text-secondary small"><?php echo date('H:i', strtotime($log['created_at'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Sidebar Components -->
  <div class="col-12 col-xl-4">
    <!-- Quick Actions Card -->
    <div class="card border-0 shadow-sm mb-4 bg-light">
      <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Quick Navigation</h6>
        <div class="d-grid gap-2">
          <a class="btn btn-white border shadow-sm text-start py-2" href="<?php echo url('/admin/assets/index.php'); ?>">
            <i class="bi bi-pc-display me-2 text-primary"></i> Asset Inventory
          </a>
          <a class="btn btn-white border shadow-sm text-start py-2" href="<?php echo url('/teacher/assignments/index.php'); ?>">
            <i class="bi bi-person-plus me-2 text-success"></i> Manage Assignments
          </a>
          <a class="btn btn-white border shadow-sm text-start py-2" href="<?php echo url('/teacher/maintenance/index.php'); ?>">
            <i class="bi bi-wrench-adjustable me-2 text-danger"></i> Maintenance Center
          </a>
          <a class="btn btn-white border shadow-sm text-start py-2" href="<?php echo url('/admin/users/index.php'); ?>">
            <i class="bi bi-people me-2 text-info"></i> User Management
          </a>
        </div>
      </div>
    </div>

    <!-- Health Summary -->
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h6 class="fw-bold mb-3">Asset Health Snapshot</h6>
        <div class="d-flex flex-column gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-heart-fill"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small fw-semibold">Good/New Condition</span>
                <span class="small text-secondary"><?php echo round(($conditionCounts[0] + $conditionCounts[1]) / max(1, $stats['total']) * 100); ?>%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: <?php echo round(($conditionCounts[0] + $conditionCounts[1]) / max(1, $stats['total']) * 100); ?>%"></div>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
              <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small fw-semibold">Fair/Damaged Condition</span>
                <span class="small text-secondary"><?php echo round(($conditionCounts[2] + $conditionCounts[3]) / max(1, $stats['total']) * 100); ?>%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: <?php echo round(($conditionCounts[2] + $conditionCounts[3]) / max(1, $stats['total']) * 100); ?>%"></div>
              </div>
            </div>
          </div>
          
          <div class="mt-2 text-center">
            <a href="<?php echo url('/reports/inventory.php'); ?>" class="small text-secondary text-decoration-none">Explore full report →</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Availability Chart
const availabilityCtx = document.getElementById('availabilityChart').getContext('2d');
new Chart(availabilityCtx, {
    type: 'bar',
    data: {
        labels: ['Available', 'In Use', 'Maintenance', 'Lost'],
        datasets: [{
            label: 'Asset Count',
            data: [
                <?php echo $stats['available']; ?>,
                <?php echo $stats['in_use']; ?>,
                <?php echo $stats['maintenance']; ?>,
                <?php echo $stats['lost']; ?>
            ],
            backgroundColor: [
                'rgba(22, 163, 74, 0.7)',  // Success
                'rgba(79, 70, 229, 0.7)',  // Primary
                'rgba(220, 38, 38, 0.7)',  // Danger
                'rgba(107, 114, 128, 0.7)' // Gray
            ],
            borderRadius: 6,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false }, ticks: { stepSize: 10 } },
            x: { grid: { display: false } }
        }
    }
});

// Condition Chart
const conditionCtx = document.getElementById('conditionChart').getContext('2d');
new Chart(conditionCtx, {
    type: 'doughnut',
    data: {
        labels: ['New', 'Good', 'Fair', 'Damaged'],
        datasets: [{
            data: [<?php echo implode(',', $conditionCounts); ?>],
            backgroundColor: [
                '#0ea5e9', // Sky
                '#10b981', // Emerald
                '#f59e0b', // Amber
                '#ef4444'  // Red
            ],
            hoverOffset: 10,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                position: 'right',
                labels: { usePointStyle: true, font: { size: 11 } }
            }
        }
    }
});
</script>

<?php layout_footer(); ?>
