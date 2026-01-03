<?php
// src/Views/admin/edit_school.php
$pageTitle = 'Edit School';
include __DIR__ . '/../layouts/main.php';

require_once __DIR__ . '/../../Controllers/SystemAdminController.php';

$id = $_GET['id'] ?? 0;
$controller = new SystemAdminController($pdo);
$school = $controller->getSchool($id);
$admins = $controller->getSchoolAdmins($id);

if (!$school) {
    echo "<div class='alert alert-danger'>School not found.</div>";
    exit;
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-building"></i> Edit Sekolah: <?= h($school['name']) ?></h3>
        <a href="index.php?page=admin/schools" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="schoolTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Detail Sekolah</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab">Admin Sekolah</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="schoolTabsContent">
                        
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <form action="index.php?page=admin/update_school" method="POST">
                                <?= csrfInput() ?>
                                <input type="hidden" name="id" value="<?= $school['id'] ?>">

                                <div class="mb-3">
                                    <label class="form-label">Nama Sekolah</label>
                                    <input type="text" name="name" class="form-control" required value="<?= h($school['name']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" class="form-control" rows="3"><?= h($school['address']) ?></textarea>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Admins Tab -->
                        <div class="tab-pane fade" id="admins" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Ini adalah administrator yang dapat mengelola data sekolah ini.
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Bergabung</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($admins)): ?>
                                        <tr><td colspan="4" class="text-center text-muted">Tidak ada admin ditemukan untuk sekolah ini.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($admins as $admin): ?>
                                            <tr>
                                                <td><?= h($admin['name']) ?></td>
                                                <td><?= h($admin['email']) ?></td>
                                                <td><?= date('d M Y', strtotime($admin['created_at'])) ?></td>
                                                <td>
                                                    <a href="index.php?page=admin/edit_school_admin&id=<?= $admin['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit Admin">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                    <!-- Future: Reset Password or Remove functionality -->
                                                    <a href="mailto:<?= h($admin['email']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-envelope"></i> Kontak
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            
                            <!-- Future feature: Add Admin button could go here -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
