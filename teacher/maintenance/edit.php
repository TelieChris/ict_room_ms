<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['it_technician','teacher','super_admin']);

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

  if ($status === 'Resolved' && $action_taken === '') {
    $errors[] = 'Please provide a description of the action taken to resolve this issue.';
  }

  $resolved_date = trim((string)med('resolved_date', $row['resolved_date']));
  if ($status === 'Resolved' && $resolved_date === '') {
    $errors[] = 'Resolution date is required when resolving a ticket.';
  }

  $asset_status = (string)med('asset_status', 'Available');
  if ($status === 'Resolved' && !in_array($asset_status, ['Available','In Use','Maintenance','Lost','Faulty'], true)) {
    $errors[] = 'Invalid resulting asset status selected.';
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
        SET status=:status, action_taken=:action_taken, technician_name=:technician_name, cost=:cost, resolved_date=:resolved_date
        WHERE id=:id AND school_id=:sid
      ");
      $stmt->execute([
        ':status' => $status,
        ':action_taken' => $action_taken ?: null,
        ':technician_name' => $technician_name ?: null,
        ':cost' => $costVal,
        ':resolved_date' => ($status === 'Resolved') ? ($resolved_date ?: date('Y-m-d')) : null,
        ':id' => $id,
        ':sid' => $sid
      ]);

      // Link status to asset status
      if ($status === 'Resolved') {
        $pdo->prepare("UPDATE assets SET status=:status WHERE id=:id AND school_id=:sid")->execute([':status' => $asset_status, ':id' => (int)$row['asset_id'], ':sid' => $sid]);
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
        <label class="form-label">Maintenance Status</label>
        <select class="form-select" name="status" id="maintenanceStatus">
          <?php foreach (['Open','In Progress','Resolved'] as $s): ?>
            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo (med('status', $row['status']) === $s) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-4" id="assetStatusContainer" style="display: <?php echo ($status === 'Resolved') ? 'block' : 'none'; ?>;">
        <label class="form-label text-primary fw-semibold">Resulting Asset Status</label>
        <select class="form-select border-primary" name="asset_status">
          <?php foreach (['Available','Lost','Faulty'] as $as): ?>
            <option value="<?php echo htmlspecialchars($as); ?>" <?php echo (med('asset_status', 'Available') === $as) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($as); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-text">Choose the status the asset will have after resolution.</div>
      </div>

      <div class="col-12 col-md-4" id="resolvedDateContainer" style="display: <?php echo ($status === 'Resolved') ? 'block' : 'none'; ?>;">
        <label class="form-label">Resolution Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control" name="resolved_date" id="resolvedDateField" 
               value="<?php echo htmlspecialchars(med('resolved_date', $row['resolved_date'] ?: date('Y-m-d'))); ?>">
      </div>

      <div class="col-12 col-md-8">
        <label class="form-label" id="actionTakenLabel">Action Taken <span class="text-danger d-none" id="actionTakenRequired">*</span></label>
        <textarea class="form-control" name="action_taken" id="actionTakenField" rows="3"><?php echo htmlspecialchars(med('action_taken', $row['action_taken'])); ?></textarea>
        <div class="form-text">Describe how the issue was resolved or what work was done.</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const statusSelect = document.getElementById('maintenanceStatus');
  const assetStatusContainer = document.getElementById('assetStatusContainer');
  const resolvedDateContainer = document.getElementById('resolvedDateContainer');
  const resolvedDateField = document.getElementById('resolvedDateField');
  const actionTakenRequired = document.getElementById('actionTakenRequired');
  const actionTakenField = document.getElementById('actionTakenField');

  function toggleResolutionFields() {
    if (statusSelect.value === 'Resolved') {
      assetStatusContainer.style.display = 'block';
      resolvedDateContainer.style.display = 'block';
      resolvedDateField.setAttribute('required', 'required');
      actionTakenRequired.classList.remove('d-none');
      actionTakenField.setAttribute('required', 'required');
    } else {
      assetStatusContainer.style.display = 'none';
      resolvedDateContainer.style.display = 'none';
      resolvedDateField.removeAttribute('required');
      actionTakenRequired.classList.add('d-none');
      actionTakenField.removeAttribute('required');
    }
  }

  statusSelect.addEventListener('change', toggleResolutionFields);
  
  // Initialize on load
  toggleResolutionFields();
});
</script>




