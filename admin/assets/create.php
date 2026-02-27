<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['it_technician','super_admin']); // teachers cannot create assets

$pdo = db();
$sid = (int)$_SESSION['user']['school_id'];

$stmt_cat = $pdo->prepare("SELECT id, name FROM asset_categories WHERE school_id = ? ORDER BY name");
$stmt_cat->execute([$sid]);
$categories = $stmt_cat->fetchAll();

$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$loc_sql = "SELECT id, name FROM locations WHERE school_id = ?";
$loc_params = [$sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $loc_sql .= " AND id = ?";
    $loc_params[] = $assigned_lid;
}
$loc_sql .= " ORDER BY name";

$stmt_loc = $pdo->prepare($loc_sql);
$stmt_loc->execute($loc_params);
$locations = $stmt_loc->fetchAll();

$errors = [];

function field(string $key, $default = '')
{
  return $_POST[$key] ?? $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $asset_code = trim((string)field('asset_code'));
  $asset_name = trim((string)field('asset_name'));
  $category_id = (int)field('category_id', 0);
  $brand = trim((string)field('brand'));
  $model = trim((string)field('model'));
  $serial_number = trim((string)field('serial_number'));
  $purchase_date = trim((string)field('purchase_date'));
  $asset_condition = (string)field('asset_condition', 'Good');
  $power_adapter = (string)field('power_adapter', 'No');
  $power_adapter_status = (string)field('power_adapter_status', 'N/A');
  $display_cable = (string)field('display_cable', 'No');
  $display_cable_type = (string)field('display_cable_type', 'N/A');
  $display_cable_status = (string)field('display_cable_status', 'N/A');
  $status = (string)field('status', 'Available');
  $location_id = (int)field('location_id', 0);
  $notes = trim((string)field('notes'));

  if ($asset_code === '') $errors[] = 'Asset code is required.';
  if ($asset_name === '') $errors[] = 'Asset name is required.';
  if ($category_id <= 0) $errors[] = 'Category is required.';
  if ($location_id <= 0) $errors[] = 'Location is required.';

  $image_path = null;
  if (!empty($_FILES['image']['name'])) {
    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
    $tmp = $_FILES['image']['tmp_name'] ?? '';
    $size = (int)($_FILES['image']['size'] ?? 0);
    if (!is_uploaded_file($tmp)) {
      $errors[] = 'Invalid image upload.';
    } elseif ($size > 2 * 1024 * 1024) {
      $errors[] = 'Image too large (max 2MB).';
    } else {
      $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
        $errors[] = 'Image must be JPG, PNG, or WEBP.';
      } else {
        $filename = 'asset_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = UPLOAD_DIR . $filename;
        if (!move_uploaded_file($tmp, $dest)) {
          $errors[] = 'Failed to save uploaded image.';
        } else {
          $image_path = UPLOAD_URL_PREFIX . $filename;
        }
      }
    }
  }

  if (!$errors) {
    $stmt = $pdo->prepare("
      INSERT INTO assets
      (school_id, asset_code, asset_name, category_id, brand, model, serial_number, purchase_date, asset_condition, power_adapter, power_adapter_status, display_cable, display_cable_type, display_cable_status, status, location_id, image_path, notes)
      VALUES
      (:school_id, :asset_code, :asset_name, :category_id, :brand, :model, :serial_number, :purchase_date, :asset_condition, :power_adapter, :power_adapter_status, :display_cable, :display_cable_type, :display_cable_status, :status, :location_id, :image_path, :notes)
    ");
    try {
      $stmt->execute([
        ':school_id' => $sid,
        ':asset_code' => $asset_code,
        ':asset_name' => $asset_name,
        ':category_id' => $category_id,
        ':brand' => $brand ?: null,
        ':model' => $model ?: null,
        ':serial_number' => $serial_number ?: null,
        ':purchase_date' => $purchase_date ?: null,
        ':asset_condition' => $asset_condition,
        ':power_adapter' => $power_adapter,
        ':power_adapter_status' => $power_adapter_status,
        ':display_cable' => $display_cable,
        ':display_cable_type' => $display_cable_type,
        ':display_cable_status' => $display_cable_status,
        ':status' => $status,
        ':location_id' => $location_id,
        ':image_path' => $image_path,
        ':notes' => $notes ?: null,
      ]);
      audit_log('ASSET_CREATE', 'assets', (int)$pdo->lastInsertId(), "Created asset {$asset_code}");
      header('Location: ' . url('/admin/assets/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Failed to save asset. (Possible duplicate asset code.)';
    }
  }
}

layout_header('Add Asset', 'assets');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Add Asset</h1>
    <div class="text-secondary">Register a new ICT asset in the inventory.</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/assets/index.php')); ?>">
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
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Asset Code</label>
        <input class="form-control" name="asset_code" required value="<?php echo htmlspecialchars(field('asset_code')); ?>">
      </div>
      <div class="col-12 col-md-8">
        <label class="form-label">Asset Name</label>
        <input class="form-control" name="asset_name" required value="<?php echo htmlspecialchars(field('asset_name')); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Category</label>
        <select class="form-select" name="category_id" required>
          <option value="">Select...</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)field('category_id', 0) === (int)$c['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Brand</label>
        <input class="form-control" name="brand" value="<?php echo htmlspecialchars(field('brand')); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Model</label>
        <input class="form-control" name="model" value="<?php echo htmlspecialchars(field('model')); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Serial Number</label>
        <input class="form-control" name="serial_number" value="<?php echo htmlspecialchars(field('serial_number')); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Purchase Date</label>
        <input type="date" class="form-control" name="purchase_date" value="<?php echo htmlspecialchars(field('purchase_date')); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Condition</label>
        <select class="form-select" name="asset_condition">
          <?php foreach (['New','Good','Fair','Damaged'] as $cnd): ?>
            <option value="<?php echo htmlspecialchars($cnd); ?>" <?php echo (field('asset_condition','Good') === $cnd) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cnd); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Power Adapter?</label>
        <select class="form-select" name="power_adapter" id="power_adapter_select">
          <option value="No" <?php echo (field('power_adapter','No') === 'No') ? 'selected' : ''; ?>>No</option>
          <option value="Yes" <?php echo (field('power_adapter','No') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
        </select>
      </div>
      <div class="col-12 col-md-4" id="power_status_group">
        <label class="form-label">Adapter Status</label>
        <select class="form-select" name="power_adapter_status">
          <?php foreach (['N/A','Working','Damaged','Missing'] as $pas): ?>
            <option value="<?php echo htmlspecialchars($pas); ?>" <?php echo (field('power_adapter_status','N/A') === $pas) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($pas); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4 cable-group">
        <label class="form-label" id="cable_label">Display Cable?</label>
        <select class="form-select" name="display_cable" id="display_cable_select">
          <option value="No" <?php echo (field('display_cable','No') === 'No') ? 'selected' : ''; ?>>No</option>
          <option value="Yes" <?php echo (field('display_cable','No') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
        </select>
      </div>
      <div class="col-12 col-md-4 cable-group">
        <label class="form-label" id="cable_type_label">Display Cable Type</label>
        <select class="form-select" name="display_cable_type">
          <?php foreach (['N/A','HDMI','VGA','DisplayPort','DVI','USB-C','Printing Cable','Other'] as $dct): ?>
            <option value="<?php echo htmlspecialchars($dct); ?>" <?php echo (field('display_cable_type','N/A') === $dct) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($dct); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4 cable-group">
        <label class="form-label" id="cable_status_label">Display Cable Status</label>
        <select class="form-select" name="display_cable_status">
          <?php foreach (['N/A','Working','Damaged','Missing'] as $dcs): ?>
            <option value="<?php echo htmlspecialchars($dcs); ?>" <?php echo (field('display_cable_status','N/A') === $dcs) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($dcs); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <?php foreach (['Available','In Use','Maintenance','Lost'] as $s): ?>
            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo (field('status','Available') === $s) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Location</label>
        <select class="form-select" name="location_id" required>
          <option value="">Select...</option>
          <?php foreach ($locations as $l): ?>
            <option value="<?php echo (int)$l['id']; ?>" <?php echo ((int)field('location_id', 0) === (int)$l['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($l['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Image (optional)</label>
        <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
        <div class="form-text">Max 2MB. JPG/PNG/WEBP only.</div>
      </div>
      <div class="col-12">
        <label class="form-label">Notes (optional)</label>
        <textarea class="form-control" rows="3" name="notes"><?php echo htmlspecialchars(field('notes')); ?></textarea>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Asset
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/assets/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const categorySelect = document.querySelector('select[name="category_id"]');
  const cableGroups = document.querySelectorAll('.cable-group');
  const cableLabel = document.getElementById('cable_label');
  const cableTypeLabel = document.getElementById('cable_type_label');
  const cableStatusLabel = document.getElementById('cable_status_label');
  const powerAdapterSelect = document.getElementById('power_adapter_select');
  const powerStatusGroup = document.getElementById('power_status_group');

  function updateVisibility() {
    const categoryName = categorySelect.options[categorySelect.selectedIndex].text.toLowerCase();
    const isProjector = categoryName.includes('projector');
    const isDesktop = categoryName.includes('desktop') || categoryName.includes('computer');
    const isPrinter = categoryName.includes('printer');

    if (isProjector || isDesktop || isPrinter) {
      cableGroups.forEach(g => g.style.display = 'block');
      if (isPrinter) {
        cableLabel.textContent = 'Printing Cable?';
        cableTypeLabel.textContent = 'Printing Cable Type';
        cableStatusLabel.textContent = 'Printing Cable Status';
      } else {
        cableLabel.textContent = 'Display Cable?';
        cableTypeLabel.textContent = 'Display Cable Type';
        cableStatusLabel.textContent = 'Display Cable Status';
      }
    } else {
      cableGroups.forEach(g => g.style.display = 'none');
    }

    // Power adapter status visibility
    if (powerAdapterSelect.value === 'Yes') {
      powerStatusGroup.style.display = 'block';
    } else {
      powerStatusGroup.style.display = 'none';
    }
  }

  categorySelect.addEventListener('change', updateVisibility);
  powerAdapterSelect.addEventListener('change', updateVisibility);
  updateVisibility();
});
</script>


