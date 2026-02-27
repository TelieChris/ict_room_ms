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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') $errors[] = 'Lab name is required.';

    // Check for duplicate name
    if (!$errors) {
        $stmtCheck = $pdo->prepare("SELECT id FROM locations WHERE school_id = ? AND name = ?");
        $stmtCheck->execute([$sid, $name]);
        if ($stmtCheck->fetchColumn()) {
            $errors[] = 'A lab with this name already exists in this school.';
        }
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO locations (school_id, name) VALUES (?, ?)");
            $stmt->execute([$sid, $name]);
            
            $newId = $pdo->lastInsertId();
            audit_log('LOCATION_CREATE', 'locations', $newId, "Created location: {$name}");
            flash_set('success', 'Lab created successfully.');
            header('Location: ' . url('/admin/locations/index.php'));
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Failed to create lab.';
        }
    }
}

layout_header('Add ICT Lab', 'locations');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Add ICT Lab</h1>
    <div class="text-secondary">Register a new physical lab room.</div>
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
        <input class="form-control" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" placeholder="e.g. ICT Room 1, Library">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Create Lab
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/locations/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>
