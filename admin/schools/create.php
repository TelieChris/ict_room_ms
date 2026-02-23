<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin']);

$pdo = db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') $errors[] = 'School name is required.';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO schools (name, address) VALUES (?, ?)");
            $stmt->execute([$name, $address]);
            
            $newId = $pdo->lastInsertId();
            audit_log('SCHOOL_CREATE', 'schools', $newId, "Created school: {$name}");
            flash_set('success', 'School created successfully.');
            header('Location: ' . url('/admin/schools/index.php'));
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Failed to create school.';
        }
    }
}

layout_header('Add School', 'schools');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Add School</h1>
    <div class="text-secondary">Register a new institution.</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/schools/index.php')); ?>">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card table-card">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12">
        <label class="form-label">School Name</label>
        <input class="form-control" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
      </div>

      <div class="col-12">
        <label class="form-label">Address / Location Details</label>
        <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Create School
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/schools/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>
