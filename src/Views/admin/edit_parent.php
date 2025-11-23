<?php
// src/Views/admin/edit_parent.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Helpers/functions.php';

// Get parent_id from URL parameter
$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;

// Fetch parent data
$User = new User($pdo);
$parent = $User->findById($parent_id);

// Check if parent exists
if (!$parent || $parent['role'] !== 'parent') {
    redirectTo(BASE_URL . 'public/index.php?page=admin/parents');
}

require_once __DIR__ . '/../../Models/Child.php';
include __DIR__ . '/../layouts/main.php';

// Fetch children for this parent
$childModel = new Child($pdo);
$children = $childModel->getByParent($parent_id);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/parents" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h3 class="mb-0"><i class="bi bi-person-gear"></i> Edit Parent</h3>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Edit Parent Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Parent Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent" class="needs-validation">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_info">

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= h($parent['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= h($parent['email']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-lock"></i> Change Password (Optional)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent" class="needs-validation">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_password">

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password *</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-circle"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Children Management -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-hearts"></i> Children (<?= count($children) ?>)</h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addChildModal">
                        <i class="bi bi-plus-circle"></i> Add Child
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($children) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Date of Birth</th>
                                    <th>Class</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($children as $child): ?>
                                    <tr>
                                        <td><strong><?= h($child['name']) ?></strong></td>
                                        <td><?= $child['date_of_birth'] ? date('d M Y', strtotime($child['date_of_birth'])) : '<em class="text-muted">N/A</em>' ?></td>
                                        <td><?= $child['class_name'] ? h($child['class_name']) : '<em class="text-muted">None</em>' ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-warning btn-edit-child" 
                                                        data-child-id="<?= $child['id'] ?>"
                                                        data-child-name="<?= h($child['name']) ?>"
                                                        data-child-dob="<?= $child['date_of_birth'] ?>">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-delete-child"
                                                        data-child-id="<?= $child['id'] ?>"
                                                        data-child-name="<?= h($child['name']) ?>">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No children registered for this parent yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Delete Parent -->
        <div class="card card-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-trash"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Deleting this parent will unassign all their children. This action cannot be undone.
                </p>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Delete Parent
                </button>
            </div>
        </div>
    </div>

    <!-- Parent Summary Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Parent Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Full Name</label>
                    <p class="fw-bold"><?= h($parent['name']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <p><?= h($parent['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Role</label>
                    <p><span class="badge bg-info"><?= ucfirst($parent['role']) ?></span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Account Created</label>
                    <p><?= date('d M Y H:i', strtotime($parent['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Child Modal -->
<div class="modal fade" id="addChildModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Child</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                <?= csrfInput() ?>
                <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                <input type="hidden" name="action" value="add_child">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="childName" class="form-label">Child Name *</label>
                        <input type="text" class="form-control" id="childName" name="child_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="childDob" class="form-label">Date of Birth (Optional)</label>
                        <input type="date" class="form-control" id="childDob" name="child_dob">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Child</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Child Modal -->
<div class="modal fade" id="editChildModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Child</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                <?= csrfInput() ?>
                <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                <input type="hidden" name="action" value="update_child">
                <input type="hidden" name="child_id" id="editChildId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editChildName" class="form-label">Child Name *</label>
                        <input type="text" class="form-control" id="editChildName" name="child_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editChildDob" class="form-label">Date of Birth (Optional)</label>
                        <input type="date" class="form-control" id="editChildDob" name="child_dob">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Child</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Child Modal -->
<div class="modal fade" id="deleteChildModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Child</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteChildName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent" style="display:inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="delete_child">
                    <input type="hidden" name="child_id" id="deleteChildId" value="">
                    <button type="submit" class="btn btn-danger">Delete Child</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Edit child button listeners
document.querySelectorAll('.btn-edit-child').forEach(btn => {
    btn.addEventListener('click', function() {
        const childId = this.getAttribute('data-child-id');
        const childName = this.getAttribute('data-child-name');
        const childDob = this.getAttribute('data-child-dob');

        document.getElementById('editChildId').value = childId;
        document.getElementById('editChildName').value = childName;
        document.getElementById('editChildDob').value = childDob || '';

        const modal = new bootstrap.Modal(document.getElementById('editChildModal'));
        modal.show();
    });
});

// Delete child button listeners
document.querySelectorAll('.btn-delete-child').forEach(btn => {
    btn.addEventListener('click', function() {
        const childId = this.getAttribute('data-child-id');
        const childName = this.getAttribute('data-child-name');

        document.getElementById('deleteChildId').value = childId;
        document.getElementById('deleteChildName').textContent = childName;

        const modal = new bootstrap.Modal(document.getElementById('deleteChildModal'));
        modal.show();
    });
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Parent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Are you sure you want to delete <span id="parentName"><?= h($parent['name']) ?></span>?</strong></p>
                <p class="text-muted">This action cannot be undone. All children assigned to this parent will be unassigned.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Type the parent's name to confirm deletion.
                </div>
                <input type="text" class="form-control" id="confirmName" placeholder="Type parent name here...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_parent" style="display:inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        Delete Parent
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmName').addEventListener('input', function() {
    const parentName = <?= json_encode($parent['name']) ?>;
    const isMatch = this.value.trim() === parentName;
    document.getElementById('confirmDeleteBtn').disabled = !isMatch;
});
</script>
