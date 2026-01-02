<?php
// src/Views/admin/teachers.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$teachers = $controller->teachers();

include __DIR__ . '/../layouts/main.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-secondary"><i class="bi bi-person-badge me-2"></i>Daftar Guru</h4>
            <a href="<?= BASE_URL ?>public/index.php?page=create_teacher" class="btn btn-success px-4">
                <i class="bi bi-person-plus-fill"></i> Tambah Guru
            </a>
        </div>

        <?php if (count($teachers) > 0): ?>
            <div class="table-responsive rounded border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-3">Nama</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Tgl. Dibuat</th>
                            <th class="py-3 text-end pe-3" style="width: 200px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td class="ps-3 fw-medium text-dark"><?= h($teacher['name']) ?></td>
                                <td class="text-muted"><?= h($teacher['email']) ?></td>
                                <td class="text-muted"><?= date('d M Y', strtotime($teacher['created_at'])) ?></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_profile&teacher_id=<?= $teacher['id'] ?>" 
                                           class="btn btn-light border text-primary hover-primary" title="View Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=edit_teacher&teacher_id=<?= $teacher['id'] ?>" 
                                           class="btn btn-light border text-warning hover-warning" title="Edit Teacher">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-light border text-danger hover-delete" 
                                                onclick="confirmDelete(<?= $teacher['id'] ?>, 'teacher')" 
                                                title="Delete Teacher">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>    
        <?php else: ?>
            <div class="alert alert-light text-center py-5 border">
                <div class="mb-3"><i class="bi bi-person-badge text-muted display-4"></i></div>
                <h5 class="text-muted">Tidak ada guru ditemukan</h5>
                <p class="text-muted mb-3">Mulai dengan menambahkan guru baru ke sistem.</p>
                <a href="<?= BASE_URL ?>public/index.php?page=create_teacher" class="btn btn-success">Tambah Guru</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
.hover-warning:hover { background-color: #ffc107 !important; color: black !important; border-color: #ffc107 !important; }
.hover-delete:hover { background-color: #dc3545 !important; color: white !important; border-color: #dc3545 !important; }
</style>

<script>
function confirmDelete(id, type) {
    if (confirm(`Hapus guru ini? Tindakan ini tidak dapat dibatalkan.`)) {
        window.location.href = `?page=delete_${type}&id=${id}`;
    }
}
</script>
