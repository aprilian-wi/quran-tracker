<?php
// src/Views/admin/manage_short_prayers.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$prayers = $controller->getShortPrayers();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-journal-text"></i> Kelola Daftar Doa</h3>

<div class="row align-items-center mb-4 g-3">
    <div class="col-12 col-md-8">
        <p class="mb-0 text-muted">Kelola doa pendek dengan teks Arab dan terjemahannya untuk pelacakan kemajuan.</p>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="?page=admin/create_short_prayer" class="btn btn-primary w-100 w-md-auto shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Doa Pendek Baru
        </a>
    </div>
</div>

<?php if (empty($prayers)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada doa pendek ditemukan. Tambahkan doa pendek pertama Anda.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Judul</th>
                            <th>Teks Arab</th>
                            <th>Terjemahan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prayers as $prayer): ?>
                            <tr>
                                <td><?= h($prayer['title']) ?></td>
                                <td><?= h($prayer['arabic_text']) ?></td>
                                <td><?= h($prayer['translation']) ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="?page=admin/edit_short_prayer&id=<?= $prayer['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $prayer['id'] ?>, '<?= h($prayer['title']) ?>')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus "<span id="itemTitle"></span>"?</p>
                <p class="text-danger"><strong>Peringatan:</strong> Ini juga akan menghapus semua catatan kemajuan yang terkait dengan doa pendek ini.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="?page=admin/delete_short_prayer" style="display: inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" id="deleteItemId">
                    <button type="submit" class="btn btn-danger">Hapus Doa Pendek</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('deleteItemId').value = id;
    document.getElementById('itemTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

