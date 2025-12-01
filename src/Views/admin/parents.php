<?php
// src/Views/admin/parents.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);
$parents = $controller->parents();

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-people"></i> Wali Siswa</h3>
    <a href="<?= BASE_URL ?>public/index.php?page=create_parent" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Tambah Wali Siswa
    </a>
</div>

<?php if (count($parents) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-person"></i> Nama</th>
                    <th><i class="bi bi-envelope"></i> Email</th>
                    <th><i class="bi bi-people-fill"></i> Anak</th>
                    <th><i class="bi bi-calendar"></i> Tanggal Dibuat</th>
                    <th style="width: 300px;"><i class="bi bi-gear"></i> Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parents as $parent): ?>
                    <tr>
                        <td>
                            <strong><?= h($parent['name']) ?></strong>
                        </td>
                        <td>
                            <span class="text-muted"><?= h($parent['email']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?= $parent['child_count'] ?> Anak
                            </span>
                        </td>
                        <td>
                            <small class="text-muted"><?= date('d M Y', strtotime($parent['created_at'])) ?></small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= BASE_URL ?>public/index.php?page=parent/my_children&parent_id=<?= $parent['id'] ?>" 
                                   class="btn btn-outline-primary" title="View Children">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                <a href="<?= BASE_URL ?>public/index.php?page=edit_parent&parent_id=<?= $parent['id'] ?>" 
                                   class="btn btn-outline-warning" title="Edit Parent">
                                    <i class="bi bi-pencil"></i> Sunting
                                </a>
                                <button type="button" class="btn btn-outline-success btn-add-child" 
                                        data-parent-id="<?= $parent['id'] ?>" 
                                        data-parent-name="<?= h($parent['name']) ?>">
                                    <i class="bi bi-person-plus"></i> Tambah Anak
                                </button>                                
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
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
                            You can add multiple children at once (max 10). Date of birth is optional. Format: YYYY-MM-DD.
                        </div>

                        <div id="childrenRows">
                            <div class="row mb-2 child-row">
                                <div class="col-md-6">
                                    <input type="text" name="children[0][name]" class="form-control" placeholder="Child full name" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" name="children[0][dob]" class="form-control" placeholder="Date of birth (optional)">
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" disabled>Remove</button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <div>
                                <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-secondary">Add another child</button>
                            </div>
                            <div>
                                <span class="text-muted small">Max 10 children per request</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Children</button>
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
            nameInput.placeholder = 'Child full name'; nameInput.required = true;
            col1.appendChild(nameInput);

            const col2 = document.createElement('div'); col2.className = 'col-md-4';
            const dobInput = document.createElement('input');
            dobInput.type = 'date'; dobInput.name = 'children[0][dob]'; dobInput.className = 'form-control';
            dobInput.placeholder = 'Date of birth (optional)';
            col2.appendChild(dobInput);

            const col3 = document.createElement('div'); col3.className = 'col-md-2 text-end';
            const removeBtn = document.createElement('button'); removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger remove-row'; removeBtn.disabled = true; removeBtn.textContent = 'Remove';
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
        if (rows.length >= 10) return alert('Maximum 10 children per request');
        const idx = rows.length;
        const container = document.getElementById('childrenRows');
        const div = document.createElement('div');
        div.className = 'row mb-2 child-row';

        const c1 = document.createElement('div'); c1.className = 'col-md-6';
        const inName = document.createElement('input'); inName.type = 'text'; inName.name = 'children[' + idx + '][name]';
        inName.className = 'form-control'; inName.placeholder = 'Child full name'; inName.required = true;
        c1.appendChild(inName);

        const c2 = document.createElement('div'); c2.className = 'col-md-4';
        const inDob = document.createElement('input'); inDob.type = 'date'; inDob.name = 'children[' + idx + '][dob]';
        inDob.className = 'form-control'; inDob.placeholder = 'Date of birth (optional)';
        c2.appendChild(inDob);

        const c3 = document.createElement('div'); c3.className = 'col-md-2 text-end';
        const btnRem = document.createElement('button'); btnRem.type = 'button'; btnRem.className = 'btn btn-sm btn-danger remove-row'; btnRem.textContent = 'Remove';
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
            e.preventDefault(); alert('Please add at least one child'); return;
        }
        if (rows.length > 10) {
            e.preventDefault(); alert('Maximum 10 children per request'); return;
        }
        // Validate names
        for (const row of rows) {
            const name = row.querySelector('input[type="text"]').value.trim();
            if (!name) { e.preventDefault(); alert('Child name is required'); return; }
        }
    });
    </script>

<?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No parents found. <a href="<?= BASE_URL ?>public/index.php?page=create_parent">Create one now</a>
    </div>
<?php endif; ?>
</div>