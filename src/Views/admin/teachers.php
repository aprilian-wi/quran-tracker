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
                                
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>    

<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada guru ditemukan. <a href="<?= BASE_URL ?>public/index.php?page=create_teacher">Tambahkan sekarang</a>
    </div>
<?php endif; ?>
</div>
