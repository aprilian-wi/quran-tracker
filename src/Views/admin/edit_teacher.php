<?php
// src/Views/admin/edit_teacher.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Helpers/functions.php';

// Get teacher_id from URL parameter
$teacher_id = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : 0;

// Fetch teacher data
$User = new User($pdo);
$teacher = $User->findById($teacher_id);

// Check if teacher exists
if (!$teacher || $teacher['role'] !== 'teacher') {
    redirectTo(BASE_URL . 'public/index.php?page=admin/teachers');
}

include __DIR__ . '/../layouts/main.php';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h3 class="mb-0"><i class="bi bi-person-gear"></i> Sunting Data Guru</h3>
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

        <!-- Edit Teacher Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Informasi Guru</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_teacher" class="needs-validation">
                    <?= csrfInput() ?>
                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                    <input type="hidden" name="action" value="update_info">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= h($teacher['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= h($teacher['email']) ?>" required>
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
                <h5 class="mb-0"><i class="bi bi-lock"></i> Rubah Password (Opsional)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_teacher" class="needs-validation">
                    <?= csrfInput() ?>
                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                    <input type="hidden" name="action" value="update_password">

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru *</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">Minimal 6 karakter</small>
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

        <!-- Delete Teacher -->
        <div class="card card-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-trash"></i> Zona Berbahaya</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="bi bi-exclamation-triangle"></i>
                    Menghapus guru ini akan memutuskan hubungan guru ke semua kelas. Tindakan ini tidak dapat dibatalkan.
                </p>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Hapus Guru
                </button>
            </div>
        </div>

        <!-- Assigned Classes -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-house-door"></i> Assigned Classes</h5>
            </div>
            <div class="card-body">
                <?php
                // Fetch classes assigned to this teacher via classes_teachers table or direct teacher_id
                $stmt = $pdo->prepare("SELECT DISTINCT c.* FROM classes c LEFT JOIN classes_teachers ct ON c.id = ct.class_id WHERE c.teacher_id = ? OR ct.teacher_id = ? ORDER BY c.name");
                $stmt->execute([$teacher_id, $teacher_id]);
                $classes = $stmt->fetchAll();
                ?>
                <?php if (count($classes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Class Name</th>
                                    <th>Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td><strong><?= h($class['name']) ?></strong></td>
                                        <td>
                                            <?php
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM children WHERE class_id = ?");
                                            $stmt->execute([$class['id']]);
                                            $student_count = $stmt->fetch()['count'];
                                            ?>
                                            <span class="badge bg-primary"><?= $student_count ?> student<?= $student_count !== 1 ? 's' : '' ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No classes assigned to this teacher yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Teacher Summary Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Ringkasan Guru</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Nama Lengkap</label>
                    <p class="fw-bold"><?= h($teacher['name']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <p><?= h($teacher['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Peran</label>
                    <p><span class="badge bg-info"><?= ucfirst($teacher['role']) ?></span></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Tgl. Dibuat</label>
                    <p><?= date('d M Y H:i', strtotime($teacher['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hapus Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Apakah kamu yakin mau menghapus <span id="teacherName"><?= h($teacher['name']) ?></span>?</strong></p>
                <p class="text-muted">Tindakan ini tidak dapat dibatalkan. Hubungan guru ke kelas akan diputuskan.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Ketik nama guru untuk konfirmasi tindakan.
                </div>
                <input type="text" class="form-control" id="confirmName" placeholder="Ketik nama guru di sini...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_teacher" style="display:inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        Hapus Guru
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmName').addEventListener('input', function() {
    const teacherName = <?= json_encode($teacher['name']) ?>;
    const isMatch = this.value.trim() === teacherName;
    document.getElementById('confirmDeleteBtn').disabled = !isMatch;
});
</script>
