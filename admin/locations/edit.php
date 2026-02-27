<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['super_admin']);

$pdo = db();
$errors = [];
$sid = (int)$_SESSION['user']['school_id'];
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ? AND school_id = ?");
$stmt->execute([$id, $sid]);
$location = $stmt->fetch();

if (!$location) {
    flash_set('error', 'Location not found.');
    header('Location: ' . url('/admin/locations/index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') $errors[] = 'Lab name is required.';

    // Check for duplicate name
    if (!$errors) {
        $stmtCheck = $pdo->prepare("SELECT id FROM locations WHERE school_id = ? AND name = ? AND id != ?");
        $stmtCheck->execute([$sid, $name, $id]);
        if ($stmtCheck->fetchColumn()) {
            $errors[] = 'A lab with this name already exists in this school.';
        }
    }

    if (!$errors) {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE locations SET name = ? WHERE id = ?");
            $stmtUpdate->execute([$name, $id]);
            
            audit_log('LOCATION_UPDATE', 'locations', $id, "Updated location name to: {$name}");
            flash_set('success', 'Lab updated successfully.');
            header('Location: ' . url('/admin/locations/index.php'));
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Failed to update lab.';
        }
    }
}

layout_header('Edit ICT Lab', 'locations');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Edit ICT Lab</h1>
    <div class="text-secondary">Modify an existing physical lab room.</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/locations/index.php')); ?>">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger" role="alert">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card table-card">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Lab/Location Name</label>
        <input class="form-control" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? $location['name']); ?>">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Changes
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/locations/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>
