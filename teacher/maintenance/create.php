<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin','teacher']);

$pdo = db();

// Assets that can be reported (any status)
$assets = $pdo->query("
  SELECT id, asset_code, asset_name, status
  FROM assets
  ORDER BY asset_name, asset_code
  LIMIT 500
")->fetchAll();

$errors = [];

function mval(string $k, $d = '')
{
  return $_POST[$k] ?? $d;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $asset_id = (int)mval('asset_id', 0);
  $issue_description = trim((string)mval('issue_description', ''));
  $reported_date = trim((string)mval('reported_date', ''));
  $technician_name = trim((string)mval('technician_name', ''));
  $cost = trim((string)mval('cost', ''));

  if ($asset_id <= 0) $errors[] = 'Please select an asset.';
  if ($issue_description === '') $errors[] = 'Issue description is required.';
  if ($reported_date === '') $errors[] = 'Reported date is required.';

  $costVal = null;
  if ($cost !== '') {
    if (!is_numeric($cost)) {
      $errors[] = 'Cost must be a number.';
    } else {
      $costVal = (float)$cost;
    }
  }

  if (!$errors) {
    $user = auth_user();
    $created_by = (int)$user['id'];

    try {
      $pdo->beginTransaction();

      $stmt = $pdo->prepare("
        INSERT INTO maintenance_logs
          (asset_id, issue_description, reported_date, action_taken, technician_name, cost, status, created_by)
        VALUES
          (:asset_id, :issue_description, :reported_date, NULL, :technician_name, :cost, 'Open', :created_by)
      ");
      $stmt->execute([
        ':asset_id' => $asset_id,
        ':issue_description' => $issue_description,
        ':reported_date' => $reported_date,
        ':technician_name' => $technician_name ?: null,
        ':cost' => $costVal,
        ':created_by' => $created_by,
      ]);

      // Set asset status to Maintenance
      $pdo->prepare("UPDATE assets SET status='Maintenance' WHERE id=:id")->execute([':id' => $asset_id]);

      $logId = (int)$pdo->lastInsertId();
      $pdo->commit();

      // For audit description, fetch asset code
      $codeStmt = $pdo->prepare("SELECT asset_code FROM assets WHERE id=:id");
      $codeStmt->execute([':id' => $asset_id]);
      $code = $codeStmt->fetchColumn() ?: ('#' . $asset_id);

      audit_log('MAINT_CREATE', 'maintenance_logs', $logId, "Reported issue for asset {$code}");

      header('Location: ' . url('/teacher/maintenance/index.php'));
      exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $errors[] = 'Failed to create maintenance record.';
    }
  }
}

layout_header('New Maintenance Ticket', 'maintenance');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">New Maintenance Ticket</h1>
    <div class="text-secondary">Report an issue for an asset.</div>
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
      <div class="col-12">
        <label class="form-label">Asset</label>
        <select class="form-select" name="asset_id" required>
          <option value="">Select...</option>
          <?php foreach ($assets as $a): ?>
            <option value="<?php echo (int)$a['id']; ?>" <?php echo ((int)mval('asset_id', 0) === (int)$a['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($a['asset_code'] . ' • ' . $a['asset_name'] . ' (' . $a['status'] . ')'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Reported Date</label>
        <input type="date" class="form-control" name="reported_date" required
               value="<?php echo htmlspecialchars(mval('reported_date', date('Y-m-d'))); ?>">
      </div>

      <div class="col-12 col-md-8">
        <label class="form-label">Issue Description</label>
        <textarea class="form-control" name="issue_description" rows="3" required><?php echo htmlspecialchars(mval('issue_description', '')); ?></textarea>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Technician (optional)</label>
        <input class="form-control" name="technician_name" value="<?php echo htmlspecialchars(mval('technician_name', '')); ?>">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Estimated Cost (optional)</label>
        <input class="form-control" name="cost" value="<?php echo htmlspecialchars(mval('cost', '')); ?>">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Ticket
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/maintenance/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>




