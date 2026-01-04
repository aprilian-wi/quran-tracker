<?php
// src/Views/admin/schools.php
$pageTitle = 'Manage Schools';
include __DIR__ . '/../layouts/main.php';

require_once __DIR__ . '/../../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$schools = $controller->getAllSchools();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-building"></i> Kelola Sekolah</h3>
    <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="mb-0">Ikhtisar semua sekolah yang terdaftar (Tenant) dalam sistem.</p>
    <a href="?page=admin/create_school" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Sekolah Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Sekolah</th>
                        <th>Alamat</th>
                        <th class="text-center">Guru</th>
                        <th class="text-center">Siswa</th>
                        <th class="text-center">Kelas</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schools)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada sekolah ditemukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($schools as $school): ?>
                            <tr>
                                <td><?= $school['id'] ?></td>
                                <td><strong><?= h($school['name']) ?></strong></td>
                                <td><?= h($school['address'] ?? '-') ?></td>
                                <td class="text-center"><span class="badge bg-info text-dark"><?= $school['teacher_count'] ?></span></td>
                                <td class="text-center"><span class="badge bg-success"><?= $school['parent_count'] ?></span></td> <!-- Assuming parent count roughly equals student count for now, or we should fix query -->
                                <td class="text-center"><span class="badge bg-secondary"><?= $school['class_count'] ?></span></td>
                                <td><?= date('d M Y', strtotime($school['created_at'])) ?></td>
                                <td>
                                    <?php if ($school['id'] != 1): // Prevent editing/deleting Main School easily ?>
                                        <a href="?page=admin/edit_school&id=<?= $school['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus" onclick="confirmDelete(<?= $school['id'] ?>, '<?= h($school['name']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">System</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus <strong id="deleteTargetName"></strong>?</p>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <strong>PERINGATAN KRITIS:</strong>
                    Ini akan menghapus SEMUA data yang terkait dengan sekolah ini, termasuk:
                    <ul>
                        <li>Semua Pengguna (Admin, Guru, Orang Tua)</li>
                        <li>Semua Siswa & Kelas</li>
                        <li>Semua Data Kemajuan</li>
                    </ul>
                    Tindakan ini TIDAK DAPAT dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="?page=admin/delete_school">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" id="deleteTargetId">
                    <button type="submit" class="btn btn-danger">Ya, Hapus Sekolah</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteTargetId').value = id;
    document.getElementById('deleteTargetName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
