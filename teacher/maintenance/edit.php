<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin','teacher']);

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$sid = (int)$_SESSION['user']['school_id'];

$stmt = $pdo->prepare("
  SELECT ml.*, a.asset_code, a.asset_name
  FROM maintenance_logs ml
  JOIN assets a ON a.id = ml.asset_id
  WHERE ml.id = :id AND ml.school_id = :sid
  LIMIT 1
");
$stmt->execute([':id' => $id, ':sid' => $sid]);
$row = $stmt->fetch();
if (!$row) {
  header('Location: ' . url('/teacher/maintenance/index.php'));
  exit;
}

$errors = [];

function med(string $k, $d)
{
  return $_POST[$k] ?? $d;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = (string)med('status', $row['status']);
  $action_taken = trim((string)med('action_taken', $row['action_taken']));
  $technician_name = trim((string)med('technician_name', $row['technician_name']));
  $cost = trim((string)med('cost', $row['cost']));

  if (!in_array($status, ['Open','In Progress','Resolved'], true)) {
    $errors[] = 'Invalid status selected.';
  }

  $costVal = null;
  if ($cost !== '') {
    if (!is_numeric($cost)) {
      $errors[] = 'Cost must be a number.';
    } else {
      $costVal = (float)$cost;
    }
  }

  if (!$errors) {
    try {
      $pdo->beginTransaction();

      $stmt = $pdo->prepare("
        UPDATE maintenance_logs
        SET status=:status, action_taken=:action_taken, technician_name=:technician_name, cost=:cost
        WHERE id=:id AND school_id=:sid
      ");
      $stmt->execute([
        ':status' => $status,
        ':action_taken' => $action_taken ?: null,
        ':technician_name' => $technician_name ?: null,
        ':cost' => $costVal,
        ':id' => $id,
        ':sid' => $sid
      ]);

      // Link status to asset status
      if ($status === 'Resolved') {
        $pdo->prepare("UPDATE assets SET status='Available' WHERE id=:id AND school_id=:sid")->execute([':id' => (int)$row['asset_id'], ':sid' => $sid]);
      } else {
        $pdo->prepare("UPDATE assets SET status='Maintenance' WHERE id=:id AND school_id=:sid")->execute([':id' => (int)$row['asset_id'], ':sid' => $sid]);
      }

      $pdo->commit();

      audit_log('MAINT_UPDATE', 'maintenance_logs', $id, "Updated maintenance status for asset {$row['asset_code']} to {$status}");

      header('Location: ' . url('/teacher/maintenance/index.php'));
      exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $errors[] = 'Failed to update maintenance record.';
    }
  }
}

layout_header('Update Maintenance Ticket', 'maintenance');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Update Maintenance Ticket</h1>
    <div class="text-secondary">
      <?php echo htmlspecialchars($row['asset_code']); ?> • <?php echo htmlspecialchars($row['asset_name']); ?>
    </div>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/maintenance/index.php')); ?>">
    <i class="bi bi-arrow-left me-1"></i> Back
  </a>
</div>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Please fix the following:</div>
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card table-card">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <?php foreach (['Open','In Progress','Resolved'] as $s): ?>
            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo (med('status', $row['status']) === $s) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-8">
        <label class="form-label">Action Taken (optional)</label>
        <textarea class="form-control" name="action_taken" rows="3"><?php echo htmlspecialchars(med('action_taken', $row['action_taken'])); ?></textarea>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Technician (optional)</label>
        <input class="form-control" name="technician_name" value="<?php echo htmlspecialchars(med('technician_name', $row['technician_name'])); ?>">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Final Cost (optional)</label>
        <input class="form-control" name="cost" value="<?php echo htmlspecialchars(med('cost', $row['cost'])); ?>">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Changes
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/maintenance/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>




