<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/audit.php';
require_once __DIR__ . '/../../includes/url.php';

require_login();
require_role(['it_technician','super_admin']); // teachers cannot edit assets

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$sid = (int)$_SESSION['user']['school_id'];

$assigned_lid = $_SESSION['user']['location_id'] ?? null;
$where_asset = "id = :id AND school_id = :sid";
$asset_params = [':id' => $id, ':sid' => $sid];

if ($assigned_lid && !is_super_admin() && !is_head_teacher()) {
    $where_asset .= " AND location_id = :assigned_lid";
    $asset_params[':assigned_lid'] = $assigned_lid;
}

$stmt = $pdo->prepare("SELECT * FROM assets WHERE $where_asset LIMIT 1");
$stmt->execute($asset_params);
$asset = $stmt->fetch();
if (!$asset) {
  http_response_code(404);
  die('Asset not found.');
}

$stmt_cat = $pdo->prepare("SELECT id, name FROM asset_categories WHERE school_id = ? ORDER BY name");
$stmt_cat->execute([$sid]);
$categories = $stmt_cat->fetchAll();

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

function v(string $k, $fallback = '')
{
  return $_POST[$k] ?? $fallback;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $asset_code = trim((string)v('asset_code', $asset['asset_code']));
  $asset_name = trim((string)v('asset_name', $asset['asset_name']));
  $category_id = (int)v('category_id', $asset['category_id']);
  $brand = trim((string)v('brand', $asset['brand']));
  $model = trim((string)v('model', $asset['model']));
  $serial_number = trim((string)v('serial_number', $asset['serial_number']));
  $purchase_date = trim((string)v('purchase_date', $asset['purchase_date']));
  $asset_condition = (string)v('asset_condition', $asset['asset_condition']);
  $power_adapter = (string)v('power_adapter', $asset['power_adapter']);
  $power_adapter_status = (string)v('power_adapter_status', $asset['power_adapter_status']);
  $display_cable = (string)v('display_cable', $asset['display_cable']);
  $display_cable_type = (string)v('display_cable_type', $asset['display_cable_type']);
  $display_cable_status = (string)v('display_cable_status', $asset['display_cable_status']);
  $status = (string)v('status', $asset['status']);
  $location_id = (int)v('location_id', $asset['location_id']);
  $notes = trim((string)v('notes', $asset['notes']));

  if ($asset_code === '') $errors[] = 'Asset code is required.';
  if ($asset_name === '') $errors[] = 'Asset name is required.';
  if ($category_id <= 0) $errors[] = 'Category is required.';
  if ($location_id <= 0) $errors[] = 'Location is required.';

  $image_path = $asset['image_path'];
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
    try {
      $stmt = $pdo->prepare("
        UPDATE assets SET
          asset_code=:asset_code,
          asset_name=:asset_name,
          category_id=:category_id,
          brand=:brand,
          model=:model,
          serial_number=:serial_number,
          purchase_date=:purchase_date,
          asset_condition=:asset_condition,
          power_adapter=:power_adapter,
          power_adapter_status=:power_adapter_status,
          display_cable=:display_cable,
          display_cable_type=:display_cable_type,
          display_cable_status=:display_cable_status,
          status=:status,
          location_id=:location_id,
          image_path=:image_path,
          notes=:notes
        WHERE id=:id
      ");
      $stmt->execute([
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
        ':image_path' => $image_path ?: null,
        ':notes' => $notes ?: null,
        ':id' => $id,
      ]);
      audit_log('ASSET_UPDATE', 'assets', $id, "Updated asset {$asset_code}");
      header('Location: ' . url('/admin/assets/index.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Failed to update asset. (Possible duplicate asset code.)';
    }
  }
}

layout_header('Edit Asset', 'assets');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Edit Asset</h1>
    <div class="text-secondary"><?php echo htmlspecialchars($asset['asset_code']); ?> • <?php echo htmlspecialchars($asset['asset_name']); ?></div>
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
        <input class="form-control" name="asset_code" required value="<?php echo htmlspecialchars(v('asset_code', $asset['asset_code'])); ?>">
      </div>
      <div class="col-12 col-md-8">
        <label class="form-label">Asset Name</label>
        <input class="form-control" name="asset_name" required value="<?php echo htmlspecialchars(v('asset_name', $asset['asset_name'])); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Category</label>
        <select class="form-select" name="category_id" id="category_select" required>
          <option value="">Select...</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)v('category_id', $asset['category_id']) === (int)$c['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Brand</label>
        <input class="form-control" name="brand" value="<?php echo htmlspecialchars(v('brand', $asset['brand'])); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Model</label>
        <input class="form-control" name="model" value="<?php echo htmlspecialchars(v('model', $asset['model'])); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Serial Number</label>
        <input class="form-control" name="serial_number" value="<?php echo htmlspecialchars(v('serial_number', $asset['serial_number'])); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Purchase Date</label>
        <input type="date" class="form-control" name="purchase_date" value="<?php echo htmlspecialchars(v('purchase_date', $asset['purchase_date'])); ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Condition</label>
        <select class="form-select" name="asset_condition">
          <?php foreach (['New','Good','Fair','Damaged'] as $cnd): ?>
            <option value="<?php echo htmlspecialchars($cnd); ?>" <?php echo (v('asset_condition', $asset['asset_condition']) === $cnd) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cnd); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Power Adapter?</label>
        <select class="form-select" name="power_adapter" id="power_adapter_select">
          <option value="No" <?php echo (v('power_adapter', $asset['power_adapter']) === 'No') ? 'selected' : ''; ?>>No</option>
          <option value="Yes" <?php echo (v('power_adapter', $asset['power_adapter']) === 'Yes') ? 'selected' : ''; ?>>Yes</option>
        </select>
      </div>
      <div class="col-12 col-md-4" id="power_status_group">
        <label class="form-label">Adapter Status</label>
        <select class="form-select" name="power_adapter_status">
          <?php foreach (['N/A','Working','Damaged','Missing'] as $pas): ?>
            <option value="<?php echo htmlspecialchars($pas); ?>" <?php echo (v('power_adapter_status', $asset['power_adapter_status']) === $pas) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($pas); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4 cable-group">
        <label class="form-label" id="cable_label">Display Cable?</label>
        <select class="form-select" name="display_cable" id="display_cable_select">
          <option value="No" <?php echo (v('display_cable', $asset['display_cable']) === 'No') ? 'selected' : ''; ?>>No</option>
          <option value="Yes" <?php echo (v('display_cable', $asset['display_cable']) === 'Yes') ? 'selected' : ''; ?>>Yes</option>
        </select>
      </div>
      <div class="col-12 col-md-4 cable-group">
        <label class="form-label" id="cable_type_label">Display Cable Type</label>
        <select class="form-select" name="display_cable_type">
          <?php foreach (['N/A','HDMI','VGA','DisplayPort','DVI','USB-C','Printing Cable','Other'] as $dct): ?>
            <option value="<?php echo htmlspecialchars($dct); ?>" <?php echo (v('display_cable_type', $asset['display_cable_type']) === $dct) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($dct); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4 cable-group">
        <label class="form-label" id="cable_status_label">Display Cable Status</label>
        <select class="form-select" name="display_cable_status">
          <?php foreach (['N/A','Working','Damaged','Missing'] as $dcs): ?>
            <option value="<?php echo htmlspecialchars($dcs); ?>" <?php echo (v('display_cable_status', $asset['display_cable_status']) === $dcs) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($dcs); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <?php foreach (['Available','In Use','Maintenance','Lost'] as $s): ?>
            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo (v('status', $asset['status']) === $s) ? 'selected' : ''; ?>>
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
            <option value="<?php echo (int)$l['id']; ?>" <?php echo ((int)v('location_id', $asset['location_id']) === (int)$l['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($l['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Replace Image (optional)</label>
        <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
        <?php if (!empty($asset['image_path'])): ?>
          <div class="form-text">Current: <?php echo htmlspecialchars($asset['image_path']); ?></div>
        <?php endif; ?>
      </div>
      <div class="col-12">
        <label class="form-label">Notes (optional)</label>
        <textarea class="form-control" rows="3" name="notes"><?php echo htmlspecialchars(v('notes', $asset['notes'])); ?></textarea>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check2 me-1"></i> Save Changes
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/admin/assets/index.php')); ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php layout_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const categorySelect = document.getElementById('category_select');
  const cableGroups = document.querySelectorAll('.cable-group');
  const cableLabel = document.getElementById('cable_label');
  const cableTypeLabel = document.getElementById('cable_type_label');
  const cableStatusLabel = document.getElementById('cable_status_label');
  const powerAdapterSelect = document.getElementById('power_adapter_select');
  const powerStatusGroup = document.getElementById('power_status_group');

  function updateVisibility() {
    if (!categorySelect || !cableLabel) return;
    
    const categoryName = categorySelect.options[categorySelect.selectedIndex].text.toLowerCase();
    const isProjector = categoryName.includes('projector');
    const isDesktop = categoryName.includes('desktop') || categoryName.includes('computer');
    const isPrinter = categoryName.includes('printer');

    if (isProjector || isDesktop || isPrinter) {
      cableGroups.forEach(g => g.style.display = 'block');
      if (isPrinter) {
        cableLabel.textContent = 'Printing Cable?';
        if (cableTypeLabel) cableTypeLabel.textContent = 'Printing Cable Type';
        if (cableStatusLabel) cableStatusLabel.textContent = 'Printing Cable Status';
      } else {
        cableLabel.textContent = 'Display Cable?';
        if (cableTypeLabel) cableTypeLabel.textContent = 'Display Cable Type';
        if (cableStatusLabel) cableStatusLabel.textContent = 'Display Cable Status';
      }
    } else {
      cableGroups.forEach(g => g.style.display = 'none');
    }

    // Power adapter status visibility
    if (powerAdapterSelect && powerStatusGroup) {
      if (powerAdapterSelect.value === 'Yes') {
        powerStatusGroup.style.display = 'block';
      } else {
        powerStatusGroup.style.display = 'none';
      }
    }
  }

  if (categorySelect) categorySelect.addEventListener('change', updateVisibility);
  if (powerAdapterSelect) powerAdapterSelect.addEventListener('change', updateVisibility);
  updateVisibility();
});
</script>


