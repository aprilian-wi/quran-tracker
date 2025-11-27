<?php
// src/Views/admin/teachers.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$teachers = $controller->teachers();

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-person-badge"></i> Daftar Guru</h3>
    <a href="<?= BASE_URL ?>public/index.php?page=create_teacher" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Tambah Guru
    </a>
</div>

<?php if (count($teachers) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-person"></i> Nama</th>
                    <th><i class="bi bi-envelope"></i> Email</th>
                    <th><i class="bi bi-calendar"></i> Tgl. Dibuat</th>
                    <th style="width: 200px;"><i class="bi bi-gear"></i> Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td>
                            <strong><?= h($teacher['name']) ?></strong>
                        </td>
                        <td>
                            <span class="text-muted"><?= h($teacher['email']) ?></span>
                        </td>
                        <td>
                            <small class="text-muted"><?= date('d M Y', strtotime($teacher['created_at'])) ?></small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_profile&teacher_id=<?= $teacher['id'] ?>" 
                                   class="btn btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=edit_teacher&teacher_id=<?= $teacher['id'] ?>" 
                                   class="btn btn-outline-warning" title="Edit Teacher">
                                    <i class="bi bi-pencil"></i> Sunting
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-delete-teacher" 
                                        data-teacher-id="<?= $teacher['id'] ?>" 
                                        data-teacher-name="<?= h($teacher['name']) ?>" 
                                        title="Delete Teacher">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal (Shared) -->
    <div class="modal fade" id="deleteTeacherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Hapus Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apa kamu yakin menghapus <strong id="modalTeacherName"></strong>?</p>
                    <p class="text-muted small">Semua data yang terhubung akan dihapus</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteTeacherForm" method="POST" action="<?= BASE_URL ?>public/index.php?page=delete_teacher" style="display:inline;">
                        <?= csrfInput() ?>
                        <input type="hidden" id="deleteTeacherId" name="teacher_id" value="">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus Guru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
// Attach event listeners to all delete buttons using data attributes
document.querySelectorAll('.btn-delete-teacher').forEach(btn => {
    btn.addEventListener('click', function() {
        const teacherId = this.getAttribute('data-teacher-id');
        const teacherName = this.getAttribute('data-teacher-name');

        document.getElementById('deleteTeacherId').value = teacherId;
        document.getElementById('modalTeacherName').textContent = teacherName;

        const modal = new bootstrap.Modal(document.getElementById('deleteTeacherModal'));
        modal.show();
    });
});
</script>

<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada guru ditemukan. <a href="<?= BASE_URL ?>public/index.php?page=create_teacher">Tambahkan sekarang</a>
    </div>
<?php endif; ?>
</div>
