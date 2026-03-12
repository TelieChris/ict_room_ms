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

$stmt_cat = $pdo->prepare("SELECT id, name, type FROM asset_categories WHERE school_id = ? ORDER BY name");
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
  $quantity = max(1, (int)field('quantity', 1));
  $power_adapter = (string)field('power_adapter', 'No');
  $power_adapter_status = (string)field('power_adapter_status', 'N/A');
  $display_cable = (string)field('display_cable', 'No');
  $display_cable_type = (string)field('display_cable_type', 'N/A');
  $display_cable_status = (string)field('display_cable_status', 'N/A');
  $status = (string)field('status', 'Available');
  $location_id = (int)field('location_id', 0);
  $notes = trim((string)field('notes'));

  // Breakdown quantities
  $qty_available = max(0, (int)field('qty_available', 0));
  $qty_in_use = max(0, (int)field('qty_in_use', 0));
  $qty_maintenance = max(0, (int)field('qty_maintenance', 0));
  $qty_faulty = max(0, (int)field('qty_faulty', 0));
  $qty_lost = max(0, (int)field('qty_lost', 0));

  // Determine if the selected category is non-electronic (using database field)
  $isNonElectronic = false;
  foreach ($categories as $c) {
    if ((int)$c['id'] === $category_id) { 
      $selectedCatName = $c['name']; 
      $isNonElectronic = ($c['type'] === 'Non-Electronic');
      break; 
    }
  }

  // Auto-generate asset code for non-electronic assets
  if ($isNonElectronic) {
    $prefix = strtoupper(preg_replace('/[^A-Z]/', '', strtoupper(explode(' ', $selectedCatName)[0])));
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM assets a JOIN asset_categories c ON c.id = a.category_id WHERE a.school_id = ? AND c.name = ?");
    $countStmt->execute([$sid, $selectedCatName]);
    $nextNum = (int)$countStmt->fetchColumn() + 1;
    $asset_code = $prefix . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    // Clear inapplicable fields
    $serial_number = '';
    $brand = '';
    $model = '';
    $purchase_date = '';
    $power_adapter = 'No';
    $power_adapter_status = 'N/A';
    $display_cable = 'No';
    $display_cable_type = 'N/A';
    $display_cable_status = 'N/A';
    
    // For non-electronic items, total quantity is the sum of status-based quantities
    $quantity = $qty_available + $qty_in_use + $qty_maintenance + $qty_faulty + $qty_lost;
    if ($quantity <= 0) $quantity = 1; // Fallback

    // Set a primary status for compatibility with older reports (highest quantity)
    $breakdown = [
      'Available' => $qty_available,
      'In Use' => $qty_in_use,
      'Maintenance' => $qty_maintenance,
      'Faulty' => $qty_faulty,
      'Lost' => $qty_lost
    ];
    arsort($breakdown);
    if (reset($breakdown) > 0) {
        $status = key($breakdown);
    }
  } else {
    // For ICT assets, we reset breakdown to 0 to be safe, or set 1 for the selected status
    $qty_available = ($status === 'Available') ? $quantity : 0;
    $qty_in_use = ($status === 'In Use') ? $quantity : 0;
    $qty_maintenance = ($status === 'Maintenance') ? $quantity : 0;
    $qty_faulty = ($status === 'Faulty') ? $quantity : 0;
    $qty_lost = ($status === 'Lost') ? $quantity : 0;
  }

  if (!$isNonElectronic && $asset_code === '') $errors[] = 'Asset code is required.';
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
      (school_id, asset_code, asset_name, category_id, brand, model, serial_number, purchase_date, asset_condition, quantity, qty_available, qty_in_use, qty_maintenance, qty_faulty, qty_lost, power_adapter, power_adapter_status, display_cable, display_cable_type, display_cable_status, status, location_id, image_path, notes)
      VALUES
      (:school_id, :asset_code, :asset_name, :category_id, :brand, :model, :serial_number, :purchase_date, :asset_condition, :quantity, :qty_available, :qty_in_use, :qty_maintenance, :qty_faulty, :qty_lost, :power_adapter, :power_adapter_status, :display_cable, :display_cable_type, :display_cable_status, :status, :location_id, :image_path, :notes)
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
        ':quantity' => $quantity,
        ':qty_available' => $qty_available,
        ':qty_in_use' => $qty_in_use,
        ':qty_maintenance' => $qty_maintenance,
        ':qty_faulty' => $qty_faulty,
        ':qty_lost' => $qty_lost,
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
      <div class="col-12 col-md-4" id="asset_code_group">
        <label class="form-label">Asset Code</label>
        <input class="form-control" name="asset_code" value="<?php echo htmlspecialchars(field('asset_code')); ?>">
        <div class="form-text non-elec-hint" style="display:none;">Auto-generated for non-electronic assets.</div>
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
            <option value="<?php echo (int)$c['id']; ?>" 
                    data-type="<?php echo htmlspecialchars($c['type']); ?>"
                    <?php echo ((int)field('category_id', 0) === (int)$c['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-4 ict-field">
        <label class="form-label">Brand</label>
        <input class="form-control" name="brand" value="<?php echo htmlspecialchars(field('brand')); ?>">
      </div>
      <div class="col-12 col-md-4 ict-field">
        <label class="form-label">Model</label>
        <input class="form-control" name="model" value="<?php echo htmlspecialchars(field('model')); ?>">
      </div>
      <div class="col-12 col-md-4 ict-field">
        <label class="form-label">Serial Number</label>
        <input class="form-control" name="serial_number" value="<?php echo htmlspecialchars(field('serial_number')); ?>">
      </div>
      <div class="col-12 col-md-4 ict-field">
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
      <div class="col-12 col-md-4" id="quantity_group">
        <label class="form-label">Total Quantity</label>
        <input type="number" class="form-control" name="quantity" min="1" value="<?php echo (int)field('quantity', 1); ?>" id="quantity_input">
        <div class="form-text">Number of units.</div>
      </div>

      <!-- Non-Electronic Breakdown -->
      <div class="col-12 non-elec-breakdown" style="display:none;">
        <div class="card bg-light border-0">
          <div class="card-body p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap me-2"></i>Status Breakdown</h6>
            <div class="row g-2">
              <div class="col-6 col-md-2">
                <label class="x-small text-secondary text-uppercase fw-bold">Working</label>
                <input type="number" class="form-control form-control-sm breakdown-input" name="qty_available" value="<?php echo (int)field('qty_available', 0); ?>" data-status="Available">
              </div>
              <div class="col-6 col-md-2">
                <label class="x-small text-secondary text-uppercase fw-bold">In Use</label>
                <input type="number" class="form-control form-control-sm breakdown-input" name="qty_in_use" value="<?php echo (int)field('qty_in_use', 0); ?>" data-status="In Use">
              </div>
              <div class="col-6 col-md-2">
                <label class="x-small text-secondary text-uppercase fw-bold">Maintenance</label>
                <input type="number" class="form-control form-control-sm breakdown-input" name="qty_maintenance" value="<?php echo (int)field('qty_maintenance', 0); ?>" data-status="Maintenance">
              </div>
              <div class="col-12 col-md-3">
                <label class="x-small text-secondary text-uppercase fw-bold text-danger">Damaged / Faulty</label>
                <input type="number" class="form-control form-control-sm breakdown-input" name="qty_faulty" value="<?php echo (int)field('qty_faulty', 0); ?>" data-status="Faulty">
              </div>
              <div class="col-6 col-md-2">
                <label class="x-small text-secondary text-uppercase fw-bold">Lost</label>
                <input type="number" class="form-control form-control-sm breakdown-input" name="qty_lost" value="<?php echo (int)field('qty_lost', 0); ?>" data-status="Lost">
              </div>
            </div>
            <div class="form-text mt-2 small text-primary italic">Enter specific counts for each status. The Total Quantity will be calculated automatically.</div>
          </div>
        </div>
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
      <div class="col-12 col-md-4 ict-field">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" id="status_select">
          <?php foreach (['Available','In Use','Maintenance','Lost','Faulty'] as $s): ?>
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
// Check if a category is non-electronic based on its data-type attribute
function isNonElectronicSelection(option) {
  return option.getAttribute('data-type') === 'Non-Electronic';
}

document.addEventListener('DOMContentLoaded', function() {
  const categorySelect = document.querySelector('select[name="category_id"]');
  const cableGroups = document.querySelectorAll('.cable-group');
  const cableLabel = document.getElementById('cable_label');
  const cableTypeLabel = document.getElementById('cable_type_label');
  const cableStatusLabel = document.getElementById('cable_status_label');
  const powerAdapterSelect = document.getElementById('power_adapter_select');
  const powerStatusGroup = document.getElementById('power_status_group');
  const powerAdapterGroup = powerAdapterSelect ? powerAdapterSelect.closest('.col-12') : null;
  const ictFields = document.querySelectorAll('.ict-field');
  const assetCodeGroup = document.getElementById('asset_code_group');
  const assetCodeInput = assetCodeGroup ? assetCodeGroup.querySelector('input') : null;
  const nonElecHints = document.querySelectorAll('.non-elec-hint');

  function updateVisibility() {
    const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
    const lower = categoryName.toLowerCase();
    const isProjector = lower.includes('projector');
    const isDesktop = lower.includes('desktop') || lower.includes('computer');
    const isPrinter = lower.includes('printer');
    const nonElec = isNonElectronicSelection(categorySelect.options[categorySelect.selectedIndex]);

    const breakdownGroup = document.querySelector('.non-elec-breakdown');
    const quantityInput = document.getElementById('quantity_input');

    // Hide ICT-only fields (asset code, brand, model, serial, purchase date, adapters, cables)
    if (nonElec) {
      ictFields.forEach(f => f.style.display = 'none');
      if (assetCodeGroup) assetCodeGroup.style.display = 'none';
      nonElecHints.forEach(h => h.style.display = 'block');
      cableGroups.forEach(g => g.style.display = 'none');
      if (powerAdapterGroup) powerAdapterGroup.style.display = 'none';
      if (powerStatusGroup) powerStatusGroup.style.display = 'none';
      
      if (breakdownGroup) breakdownGroup.style.display = 'block';
      if (quantityInput) quantityInput.readOnly = true;
    } else {
      ictFields.forEach(f => f.style.display = 'block');
      if (assetCodeGroup) assetCodeGroup.style.display = 'block';
      nonElecHints.forEach(h => h.style.display = 'none');
      if (powerAdapterGroup) powerAdapterGroup.style.display = 'block';
      
      if (breakdownGroup) breakdownGroup.style.display = 'none';
      if (quantityInput) quantityInput.readOnly = false;

      if (isProjector || isDesktop || isPrinter) {
        cableGroups.forEach(g => g.style.display = 'block');
        if (isPrinter) {
          if (cableLabel) cableLabel.textContent = 'Printing Cable?';
          if (cableTypeLabel) cableTypeLabel.textContent = 'Printing Cable Type';
          if (cableStatusLabel) cableStatusLabel.textContent = 'Printing Cable Status';
        } else {
          if (cableLabel) cableLabel.textContent = 'Display Cable?';
          if (cableTypeLabel) cableTypeLabel.textContent = 'Display Cable Type';
          if (cableStatusLabel) cableStatusLabel.textContent = 'Display Cable Status';
        }
      } else {
        cableGroups.forEach(g => g.style.display = 'none');
      }
      if (powerAdapterSelect && powerStatusGroup) {
        powerStatusGroup.style.display = powerAdapterSelect.value === 'Yes' ? 'block' : 'none';
      }
    }
  }

  // Auto-calculate total quantity from breakdown
  const breakdownInputs = document.querySelectorAll('.breakdown-input');
  breakdownInputs.forEach(input => {
    input.addEventListener('input', function() {
      let total = 0;
      breakdownInputs.forEach(i => total += (parseInt(i.value) || 0));
      const quantityInput = document.getElementById('quantity_input');
      if (quantityInput) quantityInput.value = total;
    });
  });

  categorySelect.addEventListener('change', updateVisibility);
  if (powerAdapterSelect) powerAdapterSelect.addEventListener('change', updateVisibility);
  updateVisibility();
});
</script>


