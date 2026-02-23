<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/layout.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/url.php';
require_once __DIR__ . '/../../includes/audit.php';

require_login();
require_role(['admin', 'teacher']);

$pdo = db();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: ' . url('/teacher/assignments/index.php'));
    exit;
}

$sid = (int)$_SESSION['user']['school_id'];

// Fetch assignment details
$stmt = $pdo->prepare("
    SELECT aa.*, a.asset_code, a.asset_name, a.power_adapter, a.power_adapter_status
    FROM asset_assignments aa
    JOIN assets a ON a.id = aa.asset_id
    WHERE aa.id = :id AND aa.school_id = :sid
    LIMIT 1
");
$stmt->execute([':id' => $id, ':sid' => $sid]);
$assignment = $stmt->fetch();

if (!$assignment || !empty($assignment['returned_date'])) {
    header('Location: ' . url('/teacher/assignments/index.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $return_adapter_status = $_POST['return_adapter_status'] ?? 'N/A';
    $return_notes = trim($_POST['return_notes'] ?? '');

    try {
        $pdo->beginTransaction();

        // 1. Update assignment
        $stmt = $pdo->prepare("
            UPDATE asset_assignments 
            SET returned_date = CURDATE(), 
                return_adapter_status = :status, 
                return_notes = :notes 
            WHERE id = :id AND school_id = :sid
        ");
        $stmt->execute([
            ':status' => $return_adapter_status,
            ':notes' => $return_notes ?: null,
            ':id' => $id,
            ':sid' => $sid
        ]);

        // 2. Update asset status and adapter condition
        $stmt = $pdo->prepare("
            UPDATE assets 
            SET status = 'Available', 
                power_adapter_status = :adapter_status 
            WHERE id = :asset_id AND school_id = :sid
        ");
        $stmt->execute([
            ':adapter_status' => ($assignment['power_adapter'] === 'Yes') ? $return_adapter_status : 'N/A',
            ':asset_id' => $assignment['asset_id'],
            ':sid' => $sid
        ]);

        $pdo->commit();

        audit_log('ASSIGN_RETURN', 'asset_assignments', $id, "Returned asset {$assignment['asset_code']} with adapter status: {$return_adapter_status}");
        
        header('Location: ' . url('/teacher/assignments/index.php?success=1'));
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $errors[] = "Failed to process return: " . $e->getMessage();
    }
}

layout_header('Return Asset', 'assignments');
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Return Asset</h1>
        <div class="text-secondary">Process the return of assigned hardware.</div>
    </div>
    <a class="btn btn-outline-secondary" href="<?php echo htmlspecialchars(url('/teacher/assignments/index.php')); ?>">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card table-card">
            <div class="card-body p-4">
                <form method="post">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Asset Being Returned</label>
                            <div class="fw-bold fs-5"><?php echo htmlspecialchars($assignment['asset_code']); ?></div>
                            <div class="text-secondary small"><?php echo htmlspecialchars($assignment['asset_name']); ?></div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <label class="form-label text-secondary small">Assigned To</label>
                            <div class="fw-bold"><?php echo htmlspecialchars($assignment['assigned_to_name']); ?></div>
                            <div class="text-secondary small"><?php echo htmlspecialchars($assignment['assigned_to_type']); ?></div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <div class="col-12">
                            <h6 class="mb-3"><i class="bi bi-plug me-2"></i>Power Adapter Verification</h6>
                            <?php if ($assignment['power_adapter'] === 'Yes'): ?>
                                <p class="text-secondary small mb-3">This asset was issued with a power adapter. Please verify its condition upon return.</p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Adapter Condition</label>
                                        <select class="form-select" name="return_adapter_status" required>
                                            <option value="Working" <?php echo ($assignment['power_adapter_status'] === 'Working') ? 'selected' : ''; ?>>Working</option>
                                            <option value="Damaged">Damaged</option>
                                            <option value="Missing">Missing</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 small text-secondary">
                                            <i class="bi bi-info-circle me-1"></i> Initial Status: <strong><?php echo htmlspecialchars($assignment['power_adapter_status']); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="p-3 bg-light rounded-3 text-secondary">
                                    <i class="bi bi-info-circle me-1"></i> No power adapter was recorded for this asset.
                                    <input type="hidden" name="return_adapter_status" value="N/A">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Return Notes (optional)</label>
                            <textarea class="form-control" name="return_notes" rows="3" placeholder="Any issues or observations during return..."></textarea>
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                                <i class="bi bi-check-circle me-2"></i> Confirm Return
                            </button>
                            <a href="<?php echo htmlspecialchars(url('/teacher/assignments/index.php')); ?>" class="btn btn-link text-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Return Guidelines</h6>
                <ul class="small mb-0 opacity-75 list-unstyled d-flex flex-column gap-2">
                    <li><i class="bi bi-check2-circle me-2"></i> Inspect the physical condition of the asset.</li>
                    <li><i class="bi bi-plug me-2"></i> Check if the charger/adapter is present and functional.</li>
                    <li><i class="bi bi-chat-left-text me-2"></i> Record any new damages in the notes section.</li>
                    <li><i class="bi bi-arrow-repeat me-2"></i> Status will be updated to "Available" automatically.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php layout_footer(); ?>
