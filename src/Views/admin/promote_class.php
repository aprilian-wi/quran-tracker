<?php
// src/Views/admin/promote_class.php
// This view handles mass promotion/migration of students from one class to another.
global $pdo;

// Fetch all classes for the dropdowns
$stmt = $pdo->prepare("SELECT id, name FROM classes WHERE school_id = ? ORDER BY name");
$stmt->execute([$_SESSION['school_id']]);
$classes = $stmt->fetchAll();

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-arrow-up-right-circle"></i> Promosi / Pindah Kelas Massal</h3>
    <a href="<?= BASE_URL ?>public/index.php?page=admin/classes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Kelas
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Gunakan fitur ini untuk memindahkan siswa dari satu kelas ke kelas lain secara massal (misalnya saat kenaikan kelas).
        </div>

        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=promote_class_action" id="promoteForm">
            <?= csrfInput() ?>
            
            <div class="row mb-4">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Dari Kelas (Sumber)</label>
                    <select id="source_class" name="source_class_id" class="form-select" required>
                        <option value="">-- Pilih Kelas Asal --</option>
                        <option value="-1" class="fw-bold text-danger">⚠️ Siswa Belum Ada Kelas (Unassigned)</option>
                        <option disabled>------------------------</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end justify-content-center py-2 py-md-0">
                    <i class="bi bi-arrow-right fs-2 text-muted d-none d-md-block"></i>
                    <i class="bi bi-arrow-down fs-2 text-muted d-md-none"></i>
                </div>
                
                <div class="col-md-5">
                    <label class="form-label fw-bold">Ke Kelas (Tujuan)</label>
                    <select id="target_class" name="target_class_id" class="form-select" required>
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Student List Container -->
            <div id="student_selection_area" style="display: none;">
                <h5 class="mb-3 border-bottom pb-2">Pilih Siswa untuk Dipindahkan</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAllPromotion" checked>
                        <label class="form-check-label" for="checkAllPromotion">Pilih Semua</label>
                    </div>
                    <span class="badge bg-primary" id="studentCountBadge">0 Siswa</span>
                </div>

                <div class="list-group mb-4" id="student_list" style="max-height: 400px; overflow-y: auto;">
                    <!-- Students will be loaded here via AJAX -->
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Memuat siswa...</p>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Apakah Anda yakin ingin memindahkan siswa yang dipilih?')">
                        <i class="bi bi-arrow-left-right"></i> Proses Perpindahan
                    </button>
                </div>
            </div>
            
            <div id="empty_state" class="text-center py-5 text-muted bg-light rounded border">
                Silakan pilih "Kelas Asal" terlebih dahulu untuk melihat daftar siswa.
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sourceSelect = document.getElementById('source_class');
    const targetSelect = document.getElementById('target_class');
    const studentArea = document.getElementById('student_selection_area');
    const emptyState = document.getElementById('empty_state');
    const studentList = document.getElementById('student_list');
    const countBadge = document.getElementById('studentCountBadge');
    const checkAll = document.getElementById('checkAllPromotion');

    // Handle Source Class Change
    sourceSelect.addEventListener('change', function() {
        const classId = this.value;
        
        // Prevent selecting same class as target
        disableSameOption(targetSelect, classId);

        if (!classId) {
            studentArea.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        // Show loading state
        studentArea.style.display = 'block';
        emptyState.style.display = 'none';
        studentList.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-primary"></div><p class="mt-2">Memuat data siswa...</p></div>';

        // Fetch students via AJAX
        fetch(`<?= BASE_URL ?>public/index.php?page=api/get_class_students&class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                studentList.innerHTML = '';
                countBadge.textContent = `${data.length} Siswa`;
                
                if (data.length === 0) {
                    studentList.innerHTML = '<div class="alert alert-warning">Tidak ada siswa di kelas ini.</div>';
                } else {
                    data.forEach(student => {
                        const item = document.createElement('label');
                        item.className = 'list-group-item list-group-item-action d-flex align-items-center gap-3';
                        item.style.cursor = 'pointer';
                        item.innerHTML = `
                            <input class="form-check-input student-promo-check me-2" type="checkbox" name="child_ids[]" value="${student.id}" checked>
                            <div>
                                <strong>${escapeHtml(student.name)}</strong>
                                <div class="small text-muted">Wali: ${escapeHtml(student.parent_name || '-')}</div>
                            </div>
                        `;
                        studentList.appendChild(item);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                studentList.innerHTML = '<div class="alert alert-danger">Gagal memuat data siswa.</div>';
            });
    });

    // Handle Check All
    checkAll.addEventListener('change', function() {
        const checks = document.querySelectorAll('.student-promo-check');
        checks.forEach(c => c.checked = this.checked);
    });

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function disableSameOption(selectElement, valueToDisable) {
        Array.from(selectElement.options).forEach(opt => {
            opt.disabled = (opt.value === valueToDisable && valueToDisable !== "");
        });
    }
});
</script>
