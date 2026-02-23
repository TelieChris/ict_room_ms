<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin']); // Only admins can manage schools

$pdo = db();

$schools = $pdo->query("SELECT * FROM schools ORDER BY name")->fetchAll();

layout_header('School Management', 'schools');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Schools</h1>
    <div class="text-secondary">Manage educational institutions using this system.</div>
  </div>
  <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/schools/create.php')); ?>">
    <i class="bi bi-plus-lg me-1"></i> Add School
  </a>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>ID</th>
            <th>School Name</th>
            <th>Address</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$schools): ?>
            <tr><td colspan="5" class="text-center text-secondary py-4">No schools found.</td></tr>
          <?php endif; ?>
          <?php foreach ($schools as $s): ?>
            <tr>
              <td><span class="badge text-bg-light border"><?php echo (int)$s['id']; ?></span></td>
              <td><div class="fw-semibold"><?php echo htmlspecialchars($s['name']); ?></div></td>
              <td><div class="small text-secondary"><?php echo htmlspecialchars($s['address'] ?: '-'); ?></div></td>
              <td class="small text-secondary"><?php echo htmlspecialchars($s['created_at']); ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" 
                   href="<?php echo htmlspecialchars(url('/admin/schools/edit.php')); ?>?id=<?php echo (int)$s['id']; ?>">
                  <i class="bi bi-pencil"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
