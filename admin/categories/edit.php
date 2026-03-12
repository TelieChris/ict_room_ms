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
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM asset_categories WHERE id = ? AND school_id = ?");
$stmt->execute([$id, $sid]);
$category = $stmt->fetch();

if (!$category) {
  http_response_code(404);
  die('Category not found.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim((string)($_POST['name'] ?? ''));
  $type = (string)($_POST['type'] ?? 'Electronic');

  if ($name === '') {
    $errors[] = 'Category name is required.';
  } else {
    // Check for duplicates (excluding current ID)
    $check = $pdo->prepare("SELECT id FROM asset_categories WHERE school_id = ? AND name = ? AND id != ?");
    $check->execute([$sid, $name, $id]);
    if ($check->fetch()) {
      $errors[] = 'A category with this name already exists.';
    }
  }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare("UPDATE asset_categories SET name = ?, type = ? WHERE id = ?");
      $stmt->execute([$name, $type, $id]);
      
      audit_log('CATEGORY_UPDATE', 'asset_categories', $id, "Updated category to: {$name}");
      flash_set('success', 'Category updated successfully.');
      header('Location: ' . url('/admin/categories/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'An error occurred while updating the category.';
    }
  }
}

layout_header('Edit Asset Category', 'categories');
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
        <h1 class="h4 mb-3">Edit Category</h1>
        
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
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? $category['name']); ?>" required autofocus>
            <div class="form-text mt-2 small text-secondary">Rename this category. This will update all assets assigned to it.</div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Category Type</label>
            <div class="d-flex gap-3 mt-1">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeElec" value="Electronic" <?php echo ($category['type'] === 'Electronic') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="typeElec">Electronic</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeNonElec" value="Non-Electronic" <?php echo ($category['type'] === 'Non-Electronic') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="typeNonElec">Non-Electronic</label>
              </div>
            </div>
            <div class="form-text mt-2 small text-secondary">Switching types will change how new assets are recorded for this category.</div>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary" type="submit">
              <i class="bi bi-check2-circle me-1"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
