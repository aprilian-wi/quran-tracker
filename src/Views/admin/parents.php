<?php
// src/Views/admin/parents.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$parents = $controller->parents();

include __DIR__ . '/../layouts/main.php';
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-secondary"><i class="bi bi-people me-2"></i>Wali Siswa</h4>
            <a href="<?= BASE_URL ?>public/index.php?page=create_parent" class="btn btn-success px-4">
                <i class="bi bi-person-plus-fill"></i> Tambah Wali
            </a>
        </div>

        <?php if (count($parents) > 0): ?>
            <div class="table-responsive rounded border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-3">Nama</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Anak</th>
                            <th class="py-3">Tanggal Dibuat</th>
                            <th class="py-3 text-end pe-3" style="width: 250px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parents as $parent): ?>
                            <tr>
                                <td class="ps-3 fw-medium text-dark"><?= h($parent['name']) ?></td>
                                <td class="text-muted"><?= h($parent['email']) ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2">
                                        <?= $parent['child_count'] ?> Anak
                                    </span>
                                </td>
                                <td class="text-muted"><?= date('d M Y', strtotime($parent['created_at'])) ?></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>public/index.php?page=parent/my_children&parent_id=<?= $parent['id'] ?>" 
                                           class="btn btn-light border text-primary hover-primary" title="Lihat Anak">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>public/index.php?page=edit_parent&parent_id=<?= $parent['id'] ?>" 
                                           class="btn btn-light border text-warning hover-warning" title="Edit Wali">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-light border text-success hover-success btn-add-child" 
                                                data-parent-id="<?= $parent['id'] ?>" 
                                                data-parent-name="<?= h($parent['name']) ?>"
                                                title="Tambah Anak">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                        <button class="btn btn-light border text-danger hover-delete" 
                                                onclick="confirmDelete(<?= $parent['id'] ?>, 'parent')" 
                                                title="Hapus Wali">
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
                <div class="mb-3"><i class="bi bi-people text-muted display-4"></i></div>
                <h5 class="text-muted">Tidak ada wali siswa ditemukan</h5>
                <p class="text-muted mb-3">Tambahkan wali siswa baru untuk memulai.</p>
                <a href="<?= BASE_URL ?>public/index.php?page=create_parent" class="btn btn-success">Tambah Wali Siswa</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
.hover-warning:hover { background-color: #ffc107 !important; color: black !important; border-color: #ffc107 !important; }
.hover-success:hover { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
.hover-delete:hover { background-color: #dc3545 !important; color: white !important; border-color: #dc3545 !important; }
</style>

<script>
function confirmDelete(id, type) {
    if (confirm(`Hapus wali murid ini? Tindakan ini tidak dapat dibatalkan.`)) {
        window.location.href = `?page=delete_${type}&id=${id}`;
    }
}
</script>
    
    <!-- Add Children Modal -->
    <div class="modal fade" id="addChildrenModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambahkan anak untuk <span id="addModalParentName"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addChildrenForm" method="POST" action="<?= BASE_URL ?>public/index.php?page=add_children">
                    <?= csrfInput() ?>
                    <input type="hidden" name="parent_id" id="addModalParentId" value="">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            Anda dapat menambahkan beberapa anak sekaligus (maks 10). Tanggal lahir opsional. Format: YYYY-MM-DD.
                        </div>

                        <div id="childrenRows">
                            <div class="row mb-2 child-row">
                                <div class="col-md-6">
                                    <input type="text" name="children[0][name]" class="form-control" placeholder="Nama lengkap anak" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" name="children[0][dob]" class="form-control" placeholder="Tanggal lahir (opsional)">
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" disabled>Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <div>
                                <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-secondary">Tambah anak lain</button>
                            </div>
                            <div>
                                <span class="text-muted small">Maks 10 anak per permintaan</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambah Anak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Helper: update remove button state
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.child-row');
        rows.forEach((row, idx) => {
            const removeBtn = row.querySelector('.remove-row');
            removeBtn.disabled = rows.length <= 1;
        });
    }

    // Attach event listeners to all add-child buttons using data attributes
    document.querySelectorAll('.btn-add-child').forEach(btn => {
        btn.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');
            const parentName = this.getAttribute('data-parent-name');

            document.getElementById('addModalParentId').value = parentId;
            document.getElementById('addModalParentName').textContent = parentName;

            // reset rows - build DOM nodes to avoid template literal parsing issues
            const container = document.getElementById('childrenRows');
            container.innerHTML = '';
            const row = document.createElement('div');
            row.className = 'row mb-2 child-row';

            const col1 = document.createElement('div'); col1.className = 'col-md-6';
            const nameInput = document.createElement('input');
            nameInput.type = 'text'; nameInput.name = 'children[0][name]'; nameInput.className = 'form-control';
            nameInput.placeholder = 'Nama lengkap anak'; nameInput.required = true;
            col1.appendChild(nameInput);

            const col2 = document.createElement('div'); col2.className = 'col-md-4';
            const dobInput = document.createElement('input');
            dobInput.type = 'date'; dobInput.name = 'children[0][dob]'; dobInput.className = 'form-control';
            dobInput.placeholder = 'Tanggal lahir (opsional)';
            col2.appendChild(dobInput);

            const col3 = document.createElement('div'); col3.className = 'col-md-2 text-end';
            const removeBtn = document.createElement('button'); removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger remove-row'; removeBtn.disabled = true; removeBtn.textContent = 'Hapus';
            col3.appendChild(removeBtn);

            row.appendChild(col1); row.appendChild(col2); row.appendChild(col3);
            container.appendChild(row);

            updateRemoveButtons();
            const modal = new bootstrap.Modal(document.getElementById('addChildrenModal'));
            modal.show();
        });
    });

    // Add row button listener
    document.getElementById('addRowBtn').addEventListener('click', function() {
        const rows = document.querySelectorAll('.child-row');
        if (rows.length >= 10) return alert('Maksimal 10 anak per permintaan');
        const idx = rows.length;
        const container = document.getElementById('childrenRows');
        const div = document.createElement('div');
        div.className = 'row mb-2 child-row';

        const c1 = document.createElement('div'); c1.className = 'col-md-6';
        const inName = document.createElement('input'); inName.type = 'text'; inName.name = 'children[' + idx + '][name]';
        inName.className = 'form-control'; inName.placeholder = 'Nama lengkap anak'; inName.required = true;
        c1.appendChild(inName);

        const c2 = document.createElement('div'); c2.className = 'col-md-4';
        const inDob = document.createElement('input'); inDob.type = 'date'; inDob.name = 'children[' + idx + '][dob]';
        inDob.className = 'form-control'; inDob.placeholder = 'Tanggal lahir (opsional)';
        c2.appendChild(inDob);

        const c3 = document.createElement('div'); c3.className = 'col-md-2 text-end';
        const btnRem = document.createElement('button'); btnRem.type = 'button'; btnRem.className = 'btn btn-sm btn-danger remove-row'; btnRem.textContent = 'Hapus';
        c3.appendChild(btnRem);

        div.appendChild(c1); div.appendChild(c2); div.appendChild(c3);
        container.appendChild(div);
        updateRemoveButtons();
    });

    // Remove row listener
    document.getElementById('childrenRows').addEventListener('click', function(e) {
        if (e.target && e.target.matches('.remove-row')) {
            const row = e.target.closest('.child-row');
            row.remove();
            // re-index names
            document.querySelectorAll('.child-row').forEach((r, i) => {
                const nameInput = r.querySelector('input[type="text"]');
                const dobInput = r.querySelector('input[type="date"]');
                nameInput.name = 'children[' + i + '][name]';
                dobInput.name = 'children[' + i + '][dob]';
            });
            updateRemoveButtons();
        }
    });

    // Client-side validation on submit
    document.getElementById('addChildrenForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.child-row');
        if (rows.length === 0) {
            e.preventDefault(); alert('Mohon tambahkan setidaknya satu anak'); return;
        }
        if (rows.length > 10) {
            e.preventDefault(); alert('Maksimal 10 anak per permintaan'); return;
        }
        // Validate names
        for (const row of rows) {
            const name = row.querySelector('input[type="text"]').value.trim();
            if (!name) { e.preventDefault(); alert('Nama anak wajib diisi'); return; }
        }
    });
    </script>


</div>