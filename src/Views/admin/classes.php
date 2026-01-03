<?php
// src/Views/admin/classes.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$classes = $controller->classes();

include __DIR__ . '/../layouts/main.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-secondary"><i class="bi bi-building me-2"></i>Kelas</h4>
            <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#createClassModal">
                <i class="bi bi-plus-lg"></i> Kelas Baru
            </button>
        </div>

        <?php if (count($classes) > 0): ?>
            <div class="table-responsive rounded border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-3">Nama</th>
                            <th class="py-3">Guru</th>
                            <th class="py-3">Siswa</th>
                            <th class="py-3 text-end pe-3" style="width:250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark"><?= h($class['name']) ?></td>
                                <td><span class="text-muted"><?= $class['teacher_names'] ?? '<em class="text-muted small">Belum Ditugaskan</em>' ?></span></td>
                                <td>
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2">
                                        <?= $class['student_count'] ?> Siswa
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>public/index.php?page=teacher/class_students&class_id=<?= $class['id'] ?>" 
                                           class="btn btn-light border text-primary hover-primary" title="Lihat Siswa">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=admin/edit_class&class_id=<?= $class['id'] ?>" 
                                           class="btn btn-light border text-warning hover-warning" title="Edit Kelas">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=<?= $_SESSION['role'] === 'superadmin' ? 'teacher/update_progress&class_id=' . $class['id'] : 'teacher/class_students&class_id=' . $class['id'] ?>" 
                                           class="btn btn-light border text-success hover-success" title="Perbarui Kemajuan">
                                            <i class="bi bi-graph-up"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center py-5 border">
                <div class="mb-3"><i class="bi bi-building text-muted display-4"></i></div>
                <h5 class="text-muted">Tidak ada kelas ditemukan</h5>
                <p class="text-muted mb-3">Mulai dengan membuat kelas pertama Anda.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">Buat Kelas</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
.hover-warning:hover { background-color: #ffc107 !important; color: black !important; border-color: #ffc107 !important; }
.hover-success:hover { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
</style>

<!-- Create Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_class">
            <?= csrfInput() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Kelas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tetapkan Guru (tekan Ctrl/Cmd untuk memilih beberapa)</label>
                        <select name="teacher_ids[]" class="form-select" multiple>
                            <?php
                            $teachers = $controller->teachers();
                            foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= h($teacher['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Kelas</button>
                </div>
            </div>
        </form>
    </div>
</div>
