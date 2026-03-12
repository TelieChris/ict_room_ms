<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../includes/flash.php';

require_login();
require_role(['super_admin']);

$pdo = db();
$sid = (int)$_SESSION['user']['school_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim((string)($_POST['name'] ?? ''));
  $type = (string)($_POST['type'] ?? 'Electronic');

  if ($name === '') {
    $errors[] = 'Category name is required.';
  } else {
    // Check for duplicates
    $check = $pdo->prepare("SELECT id FROM asset_categories WHERE school_id = ? AND name = ?");
    $check->execute([$sid, $name]);
    if ($check->fetch()) {
      $errors[] = 'A category with this name already exists.';
    }
  }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare("INSERT INTO asset_categories (school_id, name, type) VALUES (?, ?, ?)");
      $stmt->execute([$sid, $name, $type]);
      $catId = $pdo->lastInsertId();
      
      audit_log('CATEGORY_CREATE', 'asset_categories', $catId, "Created category: {$name}");
      flash_set('success', 'Category created successfully.');
      header('Location: ' . url('/admin/categories/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'An error occurred while creating the category.';
    }
  }
}

layout_header('Add Asset Category', 'categories');
?>

<div class="mb-3">
  <a class="btn btn-link link-secondary p-0 text-decoration-none" href="<?php echo htmlspecialchars(url('/admin/categories/index.php')); ?>">
    <i class="bi bi-arrow-left me-1"></i> Back to Categories
  </a>
</div>

<div class="row">
  <div class="col-12 col-md-6 col-lg-5">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h1 class="h4 mb-3">Add Category</h1>
        
        <?php if ($errors): ?>
          <div class="alert alert-danger py-2 mb-3">
            <ul class="mb-0 small">
              <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-4">
            <label class="form-label fw-semibold">Category Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. Printer, Laptop, Table" required autofocus>
            <div class="form-text mt-2 small text-secondary">A clear name for the type of assets that will belong to this category.</div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Category Type</label>
            <div class="d-flex gap-3 mt-1">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeElec" value="Electronic" checked>
                <label class="form-check-label" for="typeElec">Electronic</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeNonElec" value="Non-Electronic">
                <label class="form-check-label" for="typeNonElec">Non-Electronic</label>
              </div>
            </div>
            <div class="form-text mt-2 small text-secondary"><strong>Electronic</strong> assets have individual tracking (serial numbers, cables).<br><strong>Non-Electronic</strong> assets allow bulk quantity breakdowns (e.g., 30 chairs).</div>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary" type="submit">
              <i class="bi bi-check2-circle me-1"></i> Save Category
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
