<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['super_admin']); // Only super admins can manage locations

$pdo = db();
$sid = (int)$_SESSION['user']['school_id'];

$locations = $pdo->prepare("SELECT * FROM locations WHERE school_id = ? ORDER BY name");
$locations->execute([$sid]);
$locations = $locations->fetchAll();

layout_header('Manage ICT Labs (Locations)', 'locations');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">ICT Labs (Locations)</h1>
    <div class="text-secondary">Manage the physical labs available in your school.</div>
  </div>
  <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/locations/create.php')); ?>">
    <i class="bi bi-plus-lg me-1"></i> Add Lab
  </a>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Name</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$locations): ?>
            <tr><td colspan="2" class="text-center text-secondary py-4">No labs found.</td></tr>
          <?php endif; ?>
          <?php foreach ($locations as $l): ?>
            <tr>
              <td>
                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($l['name']); ?></div>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" 
                   href="<?php echo htmlspecialchars(url('/admin/locations/edit.php')); ?>?id=<?php echo (int)$l['id']; ?>">
                  <i class="bi bi-pencil"></i> Edit
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
