<?php
// index.php — Dashboard

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/url.php';

require_login();

$pdo = db();

// ── Stats ────────────────────────────────────────────────
$total       = (int)$pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
$available   = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='Available'")->fetchColumn();
$in_use      = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='In Use'")->fetchColumn();
$maintenance = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='Maintenance'")->fetchColumn();
$lost        = (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status='Lost'")->fetchColumn();

// Percentages for progress bars (guard against division by zero)
function asset_pct(int $n, int $total): int {
  return $total > 0 ? (int)round($n / $total * 100) : 0;
}

// ── Recent assignments ───────────────────────────────────
$recentStmt = $pdo->query("
  SELECT a.asset_name, u.full_name AS assigned_to, asn.assigned_at, asn.status
  FROM   assignments asn
  JOIN   assets a ON a.id = asn.asset_id
  JOIN   users  u ON u.id = asn.user_id
  ORDER  BY asn.assigned_at DESC
  LIMIT  6
");
$recentAssignments = $recentStmt ? $recentStmt->fetchAll() : [];

layout_header('Dashboard', 'dashboard');
?>

<!-- ── Page Header ──────────────────────────────────────── -->
<div class="page-header">
  <div class="page-header-title">
    <h1>Dashboard</h1>
    <p>Welcome back — here's a real-time overview of ICT room assets.</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>">
      <i class="bi bi-file-earmark-bar-graph me-1"></i> Reports
    </a>
    <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/assets/create.php')); ?>">
      <i class="bi bi-plus-lg me-1"></i> Add Asset
    </a>
  </div>
</div>

<!-- ── Stat Cards ───────────────────────────────────────── -->
<div class="row g-3 mb-4">

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-stat stat-primary">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">Total Assets</div>
          <div class="stat-value"><?php echo $total; ?></div>
          <div class="stat-trend text-muted">
            <i class="bi bi-archive"></i> All tracked devices
          </div>
        </div>
        <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-stat stat-success">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">Available</div>
          <div class="stat-value"><?php echo $available; ?></div>
          <div class="stat-trend text-success">
            <i class="bi bi-arrow-up"></i> <?php echo asset_pct($available, $total); ?>% of total
          </div>
        </div>
        <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-stat stat-warning">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">In Use</div>
          <div class="stat-value"><?php echo $in_use; ?></div>
          <div class="stat-trend text-warning">
            <i class="bi bi-arrow-left-right"></i> <?php echo asset_pct($in_use, $total); ?>% of total
          </div>
        </div>
        <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card-stat stat-danger">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">Maintenance</div>
          <div class="stat-value"><?php echo $maintenance; ?></div>
          <div class="stat-trend text-danger">
            <i class="bi bi-exclamation-triangle"></i> Needs attention
          </div>
        </div>
        <div class="stat-icon"><i class="bi bi-wrench"></i></div>
      </div>
    </div>
  </div>

</div>

<!-- ── Lower Row ────────────────────────────────────────── -->
<div class="row g-3">

  <!-- Status Breakdown -->
  <div class="col-12 col-xl-5">
    <div class="card h-100">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="h6 mb-0 fw-semibold" style="font-family:'Sora',sans-serif;">Asset Status Breakdown</h2>
          <span class="badge bg-primary-subtle text-primary"><?php echo $total; ?> total</span>
        </div>
        <div class="stat-bar-row">
          <?php
            $bars = [
              ['label' => 'Available',   'count' => $available,   'color' => '#059669', 'pct' => asset_pct($available, $total)],
              ['label' => 'In Use',      'count' => $in_use,      'color' => '#d97706', 'pct' => asset_pct($in_use, $total)],
              ['label' => 'Maintenance', 'count' => $maintenance, 'color' => '#dc2626', 'pct' => asset_pct($maintenance, $total)],
              ['label' => 'Lost',        'count' => $lost,        'color' => '#6b7280', 'pct' => asset_pct($lost, $total)],
            ];
            foreach ($bars as $bar):
          ?>
          <div class="stat-bar-item">
            <div class="stat-bar-label"><?php echo htmlspecialchars($bar['label']); ?></div>
            <div class="stat-bar-track">
              <div class="stat-bar-fill"
                   style="width:<?php echo $bar['pct']; ?>%;background:<?php echo $bar['color']; ?>;"></div>
            </div>
            <div class="stat-bar-count"><?php echo $bar['count']; ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <hr class="my-3">
        <a href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>"
           class="btn btn-sm btn-outline-secondary w-100">
          <i class="bi bi-file-earmark-bar-graph me-1"></i> View Full Report
        </a>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body p-4">
        <h2 class="h6 fw-semibold mb-3" style="font-family:'Sora',sans-serif;">Quick Actions</h2>
        <div class="d-flex flex-column gap-2">

          <a class="quick-action" href="<?php echo htmlspecialchars(url('/admin/assets/index.php')); ?>">
            <div class="quick-action-icon"><i class="bi bi-pc-display text-primary"></i></div>
            <span>Manage Assets</span>
            <i class="bi bi-chevron-right ms-auto text-muted" style="font-size:.75rem;"></i>
          </a>

          <a class="quick-action" href="<?php echo htmlspecialchars(url('/teacher/assignments/index.php')); ?>">
            <div class="quick-action-icon"><i class="bi bi-box-arrow-in-right text-warning"></i></div>
            <span>Assignments</span>
            <i class="bi bi-chevron-right ms-auto text-muted" style="font-size:.75rem;"></i>
          </a>

          <a class="quick-action" href="<?php echo htmlspecialchars(url('/teacher/maintenance/index.php')); ?>">
            <div class="quick-action-icon"><i class="bi bi-wrench-adjustable text-danger"></i></div>
            <span>Report Maintenance</span>
            <i class="bi bi-chevron-right ms-auto text-muted" style="font-size:.75rem;"></i>
          </a>

          <a class="quick-action" href="<?php echo htmlspecialchars(url('/reports/index.php')); ?>">
            <div class="quick-action-icon"><i class="bi bi-printer text-success"></i></div>
            <span>Inventory Reports</span>
            <i class="bi bi-chevron-right ms-auto text-muted" style="font-size:.75rem;"></i>
          </a>

        </div>
      </div>
    </div>
  </div>

  <!-- Recent Assignments -->
  <div class="col-12 col-sm-6 col-xl-4">
    <div class="card h-100">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="h6 fw-semibold mb-0" style="font-family:'Sora',sans-serif;">Recent Assignments</h2>
          <a href="<?php echo htmlspecialchars(url('/teacher/assignments/index.php')); ?>"
             class="btn btn-sm btn-outline-primary">View all</a>
        </div>

        <?php if (empty($recentAssignments)): ?>
          <div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
            <div class="small">No assignments yet.</div>
          </div>
        <?php else: ?>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($recentAssignments as $row): ?>
              <?php
            $statusColorMap = ['Active' => 'success', 'Returned' => 'secondary'];
          $statusColor = isset($statusColorMap[$row['status']]) ? $statusColorMap[$row['status']] : 'warning';
              ?>
              <div class="d-flex align-items-start gap-2">
                <div class="rounded-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:32px;height:32px;font-size:.8rem;">
                  <i class="bi bi-pc-display"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <div class="fw-semibold small text-truncate">
                    <?php echo htmlspecialchars($row['asset_name']); ?>
                  </div>
                  <div class="text-muted" style="font-size:.75rem;">
                    <?php echo htmlspecialchars($row['assigned_to']); ?> ·
                    <?php echo htmlspecialchars(date('M j', strtotime($row['assigned_at']))); ?>
                  </div>
                </div>
                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?> ms-auto flex-shrink-0">
                  <?php echo htmlspecialchars($row['status'] ?? '—'); ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div><!-- /row -->

<?php layout_footer(); ?>