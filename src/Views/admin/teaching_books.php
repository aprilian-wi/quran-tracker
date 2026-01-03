<?php
// src/Views/admin/teaching_books.php
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$books = $controller->teachingBooks();

include __DIR__ . '/../layouts/main.php';
?>

<h3><i class="bi bi-book"></i> Buku Ajar Quran</h3>

<div class="row align-items-center mb-4 g-3">
    <div class="col-12 col-md-8">
        <p class="mb-0 text-muted">Konfigurasikan buku pengajaran (Jilid) dan jumlah halamannya untuk pelacakan kemajuan.</p>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="?page=admin/create_teaching_book" class="btn btn-primary w-100 w-md-auto shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Buku Baru
        </a>
    </div>
</div>

<?php if (empty($books)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada buku pengajaran yang dikonfigurasi. Tambahkan buku pertama Anda untuk mulai melacak kemajuan.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nomor Jilid</th>
                            <th>Judul</th>
                            <th>Total Halaman</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><strong>Jilid <?= $book['volume_number'] ?></strong></td>
                                <td><?= h($book['title']) ?></td>
                                <td><?= $book['total_pages'] ?> halaman</td>
                                <td><?= date('d M Y', strtotime($book['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="?page=admin/edit_teaching_book&id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete(<?= $book['id'] ?>, '<?= h($book['title']) ?>')" title="Hapus">
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
                <p>Apakah Anda yakin ingin menghapus "<span id="bookTitle"></span>"?</p>
                <p class="text-danger"><strong>Peringatan:</strong> Ini juga akan menghapus semua catatan kemajuan yang terkait dengan buku ini.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="?page=admin/delete_teaching_book" style="display: inline;">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" id="deleteBookId">
                    <button type="submit" class="btn btn-danger">Hapus Buku</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(bookId, bookTitle) {
    document.getElementById('deleteBookId').value = bookId;
    document.getElementById('bookTitle').textContent = bookTitle;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
