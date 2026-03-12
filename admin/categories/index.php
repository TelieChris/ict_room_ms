<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['super_admin']);

$pdo = db();
$sid = (int)$_SESSION['user']['school_id'];

$categories = $pdo->prepare("SELECT * FROM asset_categories WHERE school_id = ? ORDER BY name");
$categories->execute([$sid]);
$categories = $categories->fetchAll();

layout_header('Manage Asset Categories', 'categories');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Asset Categories</h1>
    <div class="text-secondary">Define the types of assets (e.g., Computer, Chair, Projector) available in your school.</div>
  </div>
  <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/admin/categories/create.php')); ?>">
    <i class="bi bi-plus-lg me-1"></i> Add Category
  </a>
</div>

<div class="card table-card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Category Name</th>
            <th>Type</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$categories): ?>
            <tr><td colspan="2" class="text-center text-secondary py-4">No categories found.</td></tr>
          <?php endif; ?>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td>
                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($c['name']); ?></div>
              </td>
              <td>
                <span class="badge <?php echo $c['type'] === 'Electronic' ? 'bg-primary' : 'bg-secondary'; ?> bg-opacity-10 text-<?php echo $c['type'] === 'Electronic' ? 'primary' : 'secondary'; ?> border border-<?php echo $c['type'] === 'Electronic' ? 'primary-subtle' : 'secondary-subtle'; ?>">
                  <?php echo $c['type']; ?>
                </span>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" 
                   href="<?php echo htmlspecialchars(url('/admin/categories/edit.php')); ?>?id=<?php echo (int)$c['id']; ?>">
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
