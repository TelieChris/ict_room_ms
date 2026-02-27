<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../includes/csrf.php';

require_login();
require_role(['it_technician','teacher','super_admin']);

$pdo = db();

$sid = (int)$_SESSION['user']['school_id'];

$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$asset_sql = "SELECT id, asset_code, asset_name FROM assets WHERE status = 'Available' AND school_id = ?";
$asset_params = [$sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $asset_sql .= " AND location_id = ?";
    $asset_params[] = $assigned_lid;
}
$asset_sql .= " ORDER BY asset_name, asset_code LIMIT 500";

$stmt_assets = $pdo->prepare($asset_sql);
$stmt_assets->execute($asset_params);
$assets = $stmt_assets->fetchAll();

$errors = [];

function f(string $k, $d = '')
{
  return $_POST[$k] ?? $d;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $asset_id = (int)f('asset_id', 0);
  $assigned_to_type = (string)f('assigned_to_type', '');
  $assigned_to_name = trim((string)f('assigned_to_name', ''));
  $assigned_date = trim((string)f('assigned_date', ''));
  $expected_return_date = trim((string)f('expected_return_date', ''));
  $notes = trim((string)f('notes', ''));

  if ($asset_id <= 0) $errors[] = 'Please select an asset.';
  if (!in_array($assigned_to_type, ['ICT Room','Teacher','Class/Department','Head Teacher','DOD','DOS','Accountant'], true)) $errors[] = 'Please select who the asset is assigned to.';
  if ($assigned_to_name === '') $errors[] = 'Assigned-to name is required (e.g. ICT Room, Mr. John, S4 A, Math Dept).';
  if ($assigned_date === '') $errors[] = 'Assigned date is required.';

  // Ensure asset is still available at time of submit (race-safe)
  if (!$errors) {
    $stmt = $pdo->prepare("SELECT status, asset_code FROM assets WHERE id=:id AND school_id=:sid LIMIT 1");
    $stmt->execute([':id' => $asset_id, ':sid' => $sid]);
    $asset = $stmt->fetch();
    if (!$asset) {
      $errors[] = 'Selected asset not found.';
    } elseif ($asset['status'] !== 'Available') {
      $errors[] = 'Selected asset is not available anymore.';
    }
  }

  if (!$errors) {
    $user = auth_user();
    $created_by = (int)$user['id'];
    $userRole   = $user['role'] ?? '';

    // Teachers submitting their own assignment need approval
    $needsApproval = ($userRole === 'teacher');
    $approvalStatus = $needsApproval ? 'pending' : 'approved';
    $approvedBy     = $needsApproval ? null : $created_by;
    $approvedAt     = $needsApproval ? null : date('Y-m-d H:i:s');

    try {
      $pdo->beginTransaction();

      $stmt = $pdo->prepare("
        INSERT INTO asset_assignments
          (school_id, approval_status, approved_by, approved_at, asset_id, assigned_to_type, assigned_to_name, assigned_date, expected_return_date, returned_date, notes, created_by)
        VALUES
          (:school_id, :approval_status, :approved_by, :approved_at, :asset_id, :type, :name, :assigned_date, :expected_return_date, NULL, :notes, :created_by)
      ");
      $stmt->execute([
        ':school_id'           => $sid,
        ':approval_status'     => $approvalStatus,
        ':approved_by'         => $approvedBy,
        ':approved_at'         => $approvedAt,
        ':asset_id'            => $asset_id,
        ':type'                => $assigned_to_type,
        ':name'                => $assigned_to_name,
        ':assigned_date'       => $assigned_date,
        ':expected_return_date'=> ($expected_return_date !== '') ? $expected_return_date : null,
        ':notes'               => $notes ?: null,
        ':created_by'          => $created_by,
      ]);

      $assignmentId = (int)$pdo->lastInsertId();

      // Only mark asset as In Use if auto-approved
      if (!$needsApproval) {
        $pdo->prepare("UPDATE assets SET status='In Use' WHERE id=:id AND school_id=:sid")->execute([':id' => $asset_id, ':sid' => $sid]);
      }

      $pdo->commit();

      audit_log('ASSIGN_CREATE', 'asset_assignments', $assignmentId,
        "Assigned asset {$asset['asset_code']} to {$assigned_to_type}: {$assigned_to_name} [status: {$approvalStatus}]");

      if ($needsApproval) {
        flash_set('warning', 'Assignment submitted and is awaiting approval by an IT Technician or Head Teacher.');
      } else {
        flash_set('success', 'Assignment created successfully.');
      }
      header('Location: ' . url('/teacher/assignments/index.php'));
      exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $errors[] = 'Failed to create assignment.';
    }
  }
}

$currentUser = auth_user();
$myFullName   = $currentUser['full_name'] ?? '';
$myRole       = $currentUser['role'] ?? '';

layout_header('New Assignment', 'assignments');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">New Assignment</h1>
    <div class="text-secondary">Assign an available asset and track expected return.</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/assignments/index.php')); ?>">
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
        <label class="form-label">Asset (Available only)</label>
        <select class="form-select" name="asset_id" required>
          <option value="">Select...</option>
          <?php foreach ($assets as $a): ?>
            <option value="<?php echo (int)$a['id']; ?>" <?php echo ((int)f('asset_id', 0) === (int)$a['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($a['asset_code'] . ' • ' . $a['asset_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-text">If the asset is not listed, it is currently In Use / Maintenance / Lost.</div>
      </div>

      <?php if ($myRole === 'teacher'): ?>
        <!-- Teacher: locked to self-assignment -->
        <input type="hidden" name="assigned_to_type" value="Teacher">
        <div class="col-12 col-md-4">
          <label class="form-label">Assigned To</label>
          <input class="form-control bg-light" type="text" value="Teacher" disabled>
          <div class="form-text">Teachers can only assign to themselves.</div>
        </div>
        <div class="col-12 col-md-8">
          <label class="form-label">Your Name</label>
          <input type="hidden" name="assigned_to_name" value="<?php echo htmlspecialchars($myFullName); ?>">
          <input class="form-control bg-light" type="text" value="<?php echo htmlspecialchars($myFullName); ?>" disabled>
          <div class="form-text text-success"><i class="bi bi-person-check me-1"></i>Assigned to you.</div>
        </div>
      <?php else: ?>
        <!-- Non-teacher: full choice -->
        <div class="col-12 col-md-4">
          <label class="form-label">Assigned To</label>
          <select class="form-select" name="assigned_to_type" id="assignedToType" required
                  data-my-name="<?php echo htmlspecialchars($myFullName); ?>"
                  data-my-role="<?php echo htmlspecialchars($myRole); ?>">
            <option value="">Select...</option>
            <?php foreach (['ICT Room','Teacher','Class/Department','Head Teacher','DOD','DOS','Accountant'] as $t): ?>
              <option value="<?php echo htmlspecialchars($t); ?>" <?php echo (f('assigned_to_type', '') === $t) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($t); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-8">
          <label class="form-label">Name (Teacher / Class / Department)</label>
          <input class="form-control" name="assigned_to_name" id="assignedToName" required
                 placeholder="e.g. ICT Room, Mr. Jean, S4 A, Science Dept"
                 value="<?php echo htmlspecialchars(f('assigned_to_name', '')); ?>">
          <div class="form-text autofill-hint d-none text-success">
            <i class="bi bi-person-check me-1"></i>Auto-filled with your name. You can still edit it.
          </div>
        </div>
      <?php endif; ?>

      <div class="col-12 col-md-4">
        <label class="form-label">Assigned Date</label>
        <input type="date" class="form-control" name="assigned_date" required value="<?php echo htmlspecialchars(f('assigned_date', date('Y-m-d'))); ?>">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Expected Return Date (optional)</label>
        <input type="date" class="form-control" name="expected_return_date" value="<?php echo htmlspecialchars(f('expected_return_date', '')); ?>">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Notes (optional)</label>
        <input class="form-control" name="notes" value="<?php echo htmlspecialchars(f('notes', '')); ?>">
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Assignment
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/assignments/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>

<script>
(function () {
  const typeSelect  = document.getElementById('assignedToType');
  const nameInput   = document.getElementById('assignedToName');
  const hint        = document.querySelector('.autofill-hint');
  const myName      = typeSelect ? typeSelect.dataset.myName : '';

  function updateAutoFill() {
    if (!typeSelect || !nameInput) return;
    if (typeSelect.value === 'Teacher' && myName) {
      // Only auto-fill if the field is currently empty or was auto-filled
      if (nameInput.dataset.autofilled === '1' || nameInput.value === '') {
        nameInput.value = myName;
        nameInput.dataset.autofilled = '1';
        hint && hint.classList.remove('d-none');
      }
    } else {
      // Clear only if we previously auto-filled it
      if (nameInput.dataset.autofilled === '1') {
        nameInput.value = '';
        nameInput.dataset.autofilled = '0';
        hint && hint.classList.add('d-none');
      }
    }
  }

  // Allow user edits to break the auto-fill link
  nameInput && nameInput.addEventListener('input', function () {
    if (this.value !== myName) {
      this.dataset.autofilled = '0';
      hint && hint.classList.add('d-none');
    }
  });

  typeSelect && typeSelect.addEventListener('change', updateAutoFill);

  // Run on page load in case the form is repopulated after an error
  updateAutoFill();
})();
</script>




