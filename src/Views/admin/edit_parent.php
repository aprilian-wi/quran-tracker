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
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h3 class="mb-0"><i class="bi bi-person-gear"></i> Sunting Wali Siswa</h3>
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
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Informasi Wali Siswa</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent" class="needs-validation">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_info">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= h($parent['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= h($parent['email']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-lock"></i> Rubah Password (Optional)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent" class="needs-validation">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="update_password">

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru *</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">Minimum 6 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-circle"></i> Perbarui Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Children Management -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-hearts"></i> Anak</h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addChildModal">
                        <i class="bi bi-plus-circle"></i> Tambah Anak
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($children) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Kelas</th>
                                    <th style="width: 180px;">Tindakan</th>
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
                                                    <i class="bi bi-pencil"></i> Sunting
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-delete-child"
                                                        data-child-id="<?= $child['id'] ?>"
                                                        data-child-name="<?= h($child['name']) ?>">
                                                    <i class="bi bi-trash"></i> Hapus
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
                        <i class="bi bi-info-circle"></i> Tidak anak yang terhubung dengan wali siswa ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Delete Parent -->
        <div class="card card-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-trash"></i> Zona Bahaya</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Menghapus wali siswa ini akan melepaskan semua anak yang terhubung dengannya. Tindakan ini tidak dapat dibatalkan.
                </p>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Hapus Wali Siswa
                </button>
            </div>
        </div>
    </div>

    <!-- Parent Summary Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Ringkasan Wali Siswa</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Nama Lengkap</label>
                    <p class="fw-bold"><?= h($parent['name']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <p><?= h($parent['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Peran</label>
                    <p><span class="badge bg-info"><?= ucfirst($parent['role']) ?></span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Akun Dibuat</label>
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
                <h5 class="modal-title">Tambah Anak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                <?= csrfInput() ?>
                <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                <input type="hidden" name="action" value="add_child">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="childName" class="form-label">Nama Anak *</label>
                        <input type="text" class="form-control" id="childName" name="child_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="childDob" class="form-label">Tanggal Lahir (Opsional)</label>
                        <input type="date" class="form-control" id="childDob" name="child_dob">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tambah Anak</button>
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
                <h5 class="modal-title">Sunting Data Anak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent">
                <?= csrfInput() ?>
                <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                <input type="hidden" name="action" value="update_child">
                <input type="hidden" name="child_id" id="editChildId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editChildName" class="form-label">Nama Anak *</label>
                        <input type="text" class="form-control" id="editChildName" name="child_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editChildDob" class="form-label">Tanggal Lahir (Opsional)</label>
                        <input type="date" class="form-control" id="editChildDob" name="child_dob">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Perbarui Anak</button>
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
                <h5 class="modal-title">Hapus Anak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus <strong id="deleteChildName"></strong>?</p>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_parent" style="display:inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <input type="hidden" name="action" value="delete_child">
                    <input type="hidden" name="child_id" id="deleteChildId" value="">
                    <button type="submit" class="btn btn-danger">Hapus Anak</button>
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
                <h5 class="modal-title">Hapus Wali Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Apakah Anda yakin ingin menghapus <span id="parentName"><?= h($parent['name']) ?></span>?</strong></p>
                <p class="text-muted">Tindakan ini tidak dapat dibatalkan. Semua anak yang ditugaskan kepada wali ini akan dibatalkan penugasannya.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Ketik nama wali untuk mengonfirmasi penghapusan.
                </div>
                <input type="text" class="form-control" id="confirmName" placeholder="Ketik nama wali di sini...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_parent" style="display:inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" value="<?= $parent['id'] ?>">
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        Hapus Wali Siswa
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
