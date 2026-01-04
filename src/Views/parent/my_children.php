<?php
// src/Views/parent/my_children.php
require_once __DIR__ . '/../../Controllers/ParentController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new ParentController($pdo);
$children = $controller->myChildren();
$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;

// Get parent name if viewing specific parent
$parent_name = '';
if ($parent_id && (hasRole('superadmin') || hasRole('school_admin') || hasRole('teacher'))) {
    require_once __DIR__ . '/../../Models/User.php';
    $userModel = new User($pdo);
    $parent = $userModel->findById($parent_id);
    $parent_name = $parent ? $parent['name'] : 'Unknown Parent';
} elseif (hasRole('parent')) {
    $parent_name = $_SESSION['name'] ?? 'My';
}

if (isPwa() && hasRole('parent')) {
    include __DIR__ . '/../layouts/pwa.php';
    include __DIR__ . '/my_children_pwa.php';
    return;
}

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-people"></i> Anak Dari <?= h($parent_name) ?></h3>
    <?php if (hasRole('superadmin') || hasRole('school_admin')): ?>
        <a href="<?= BASE_URL ?>public/index.php?page=admin/parents" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    <?php endif; ?>
</div>

<?php if (empty($children)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada anak ditemukan.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-person"></i> Nama</th>
                    <th><i class="bi bi-calendar"></i> Tanggal Lahir</th>
                    <th><i class="bi bi-house-door"></i> Kelas</th>                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($children as $child): ?>
                    <tr>
                        <td>
                            <strong><?= h($child['name']) ?></strong>
                        </td>
                        <td>
                            <?= $child['date_of_birth'] ? date('d M Y', strtotime($child['date_of_birth'])) : '-' ?>
                        </td>
                        <td>
                            <?= h($child['class_name'] ?? 'Belum Ada Kelas') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
