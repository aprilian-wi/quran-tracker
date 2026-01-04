<?php
// src/Views/admin/update_progress.php
global $pdo;
require_once __DIR__ . '/../../Models/Child.php';
require_once __DIR__ . '/../../Models/Progress.php';
require_once __DIR__ . '/../../Models/Quran.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$child_id = $_GET['child_id'] ?? 0;
$class_id = $_GET['class_id'] ?? 0;

if ($child_id && !is_numeric($child_id)) {
    setFlash('danger', 'Invalid child.');
    redirect('dashboard');
}

if ($class_id && !is_numeric($class_id)) {
    setFlash('danger', 'Invalid class.');
    redirect('dashboard');
}

if (!$child_id && !$class_id) {
    setFlash('danger', 'Child or class ID required.');
    redirect('dashboard');
}

$childModel = new Child($pdo);
$role = $_SESSION['role'] ?? '';

if ($child_id) {
    $child = $childModel->find($child_id, $_SESSION['user_id'], $role);
    if (!$child) {
        setFlash('danger', 'Access denied or child not found.');
        redirect('dashboard');
    }
} elseif ($class_id) {
    // For class-based access, show child selection for admin/superadmin
    $children = $childModel->getByClass($class_id);
    if (empty($children)) {
        setFlash('danger', 'No children in this class.');
        redirect('admin/classes');
    }
    include __DIR__ . '/../layouts/main.php';
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-people"></i> Pilih Anak untuk Memperbarui Kemajuan</h3>
        <a href="?page=admin/classes" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="row">
        <?php foreach ($children as $child): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= h($child['name']) ?></h5>
                        <p class="card-text">Wali: <?= h($child['parent_name']) ?></p>
                        <a href="?page=admin/update_progress&child_id=<?= $child['id'] ?>" class="btn btn-primary">
                            Perbarui Kemajuan
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-3">
        <a href="?page=admin/classes" class="btn btn-secondary">Kembali ke Kelas</a>
    </div>
    <?php
    exit;
}

$quranModel = new Quran($pdo);
$juzList = $quranModel->getAllJuz();

include __DIR__ . '/../layouts/main.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-journal-text"></i> Perbarui Kemajuan untuk <?= h($child['name']) ?></h3>
    <a href="<?= BASE_URL ?>public/index.php?page=admin/list_children" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=update_progress">
            <?= csrfInput() ?>
            <input type="hidden" name="child_id" value="<?= $child_id ?>">
            <input type="hidden" name="updated_by" value="<?= $_SESSION['user_id'] ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Juz</label>
                    <select name="juz" id="juz" class="form-select" required>
                        <option value="">Pilih Juz</option>
                        <?php foreach ($juzList as $juz): ?>
                            <option value="<?= $juz ?>"><?= $juz ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Surat</label>
                    <select name="surah" id="surah" class="form-select" required disabled>
                        <option value="">Pilih Surat</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ayat</label>
                    <input type="number" name="verse" id="verse" class="form-control" min="1" required disabled>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="in_progress">Murajaah</option>
                        <option value="memorized" selected>Menghafal</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Tambahkan catatan tambahan tentang pembaruan kemajuan ini..."></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2"></i> Simpan Kemajuan
                </button>
                <a href="<?= BASE_URL ?>public/index.php?page=admin/list_children" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
// Dynamic Surah & Verse loading
document.getElementById('juz').addEventListener('change', function() {
    const juz = this.value;
    const surahSelect = document.getElementById('surah');
    const verseInput = document.getElementById('verse');

    surahSelect.innerHTML = '<option value="">Memuat...</option>';
    surahSelect.disabled = true;
    verseInput.disabled = true;

    if (!juz) return;

    fetch(`?page=get_surahs&juz=${juz}&_=${Date.now()}`)
        .then(r => r.json())
        .then(data => {
            surahSelect.innerHTML = '<option value="">Pilih Surat</option>';
            data.forEach(s => {
                const opt = new Option(`${s.surah_number}. ${s.surah_name_ar} (${s.surah_name_en})`, s.surah_number);
                surahSelect.add(opt);
            });
            surahSelect.disabled = false;
        });
});

document.getElementById('surah').addEventListener('change', function() {
    const surah = this.value;
    const verseInput = document.getElementById('verse');
    verseInput.disabled = true;

    if (!surah) return;

    fetch(`?page=get_verse_count&surah=${surah}&_=${Date.now()}`)
        .then(r => r.json())
        .then(data => {
            verseInput.max = data.full_verses;
            verseInput.placeholder = `1–${data.full_verses}`;
            verseInput.disabled = false;
        });
});
</script>

<!-- Progress History -->
<?php
$progressModel = new Progress($pdo);
$history = $progressModel->getHistory($child_id);
if ($history):
    // Collect unique updated_by_names for filter
    $uniqueUpdatedBy = array_unique(array_column($history, 'updated_by_name'));
    sort($uniqueUpdatedBy);
?>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Riwayat Kemajuan</h5>
        <div class="mt-3 d-flex gap-2 align-items-center">
            <label for="statusFilter" class="form-label mb-0">Filter Berdasarkan Status:</label>
            <select id="statusFilter" class="form-select" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="Menghafal">Menghafal</option>
                <option value="Murajaah">Murajaah</option>
            </select>
            <label for="updatedByFilter" class="form-label mb-0">Filter Berdasarkan Pemperbarui:</label>
            <select id="updatedByFilter" class="form-select" style="max-width: 200px;">
                <option value="">Semua Pengguna</option>
                <?php foreach ($uniqueUpdatedBy as $name): ?>
                    <option value="<?= h($name) ?>"><?= h($name) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="ms-auto">
                <button id="exportBtn" class="btn btn-success btn-sm">
                    <i class="bi bi-download"></i> Unduh Excel
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table id="progressHistoryTable" class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Surat</th>
                        <th>Ayat</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Diperbarui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $entry): ?>
                        <?php
                        $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                                      ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                        ?>
                        <tr class="history-item" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                            <td><?= date('M j, Y g:i A', strtotime($entry['updated_at'])) ?></td>
                            <td><?= h($entry['surah_name_ar']) ?> (<?= h($entry['surah_name_en']) ?>)</td>
                            <td><?= $entry['verse'] ?></td>
                            <td>
                                <span class="badge bg-<?=
                                    $entry['status'] === 'memorized' ? 'success' :
                                    ($entry['status'] === 'in_progress' ? 'warning' : 'info')
                                ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td><?= h($entry['note'] ?? '') ?></td>
                            <td><?= h($entry['updated_by_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            <?php foreach ($history as $entry): ?>
                <?php
                $statusText = $entry['status'] === 'memorized' ? 'Menghafal' :
                              ($entry['status'] === 'in_progress' ? 'Murajaah' : ucfirst($entry['status']));
                $badgeClass = $entry['status'] === 'memorized' ? 'success' :
                              ($entry['status'] === 'in_progress' ? 'warning' : 'info');
                ?>
                <div class="card-body border-bottom history-item" data-status="<?= h($statusText) ?>" data-updated-by="<?= h($entry['updated_by_name']) ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong><?= h($entry['surah_name_ar']) ?> (<?= h($entry['surah_name_en']) ?>)</strong>
                            <div class="text-muted small">Ayat: <?= $entry['verse'] ?></div>
                        </div>
                        <span class="badge bg-<?= $badgeClass ?>"><?= $statusText ?></span>
                    </div>
                    <?php if (!empty($entry['note'])): ?>
                        <div class="alert alert-light p-2 mb-2 small text-muted fst-italic">
                            <i class="bi bi-sticky"></i> <?= h($entry['note']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center text-muted small">
                        <span><i class="bi bi-person"></i> <?= h($entry['updated_by_name']) ?></span>
                        <span><?= date('M j, Y g:i A', strtotime($entry['updated_at'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Progress History filters
const statusFilter = document.getElementById('statusFilter');
const updatedByFilter = document.getElementById('updatedByFilter');

function filterHistory() {
    const selectedStatus = statusFilter.value;
    const selectedUpdatedBy = updatedByFilter.value;
    // Select both table rows and mobile cards
    const rows = document.querySelectorAll('.history-item');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const rowUpdatedBy = row.getAttribute('data-updated-by');
        const statusMatch = selectedStatus === '' || rowStatus === selectedStatus;
        const updatedByMatch = selectedUpdatedBy === '' || rowUpdatedBy === selectedUpdatedBy;

        if (statusMatch && updatedByMatch) {
            // For table rows, display depends on parent (table-row), but standard is empty to reset
            // For div cards, separate display logic logic or just '' which usually works (block or table-row)
            // Ideally explicit:
            if (row.tagName === 'TR') {
                row.style.display = '';
            } else {
                row.style.display = 'block';
            }
        } else {
            row.style.display = 'none';
        }
    });
}

statusFilter.addEventListener('change', filterHistory);
updatedByFilter.addEventListener('change', filterHistory);

// Export functionality
document.getElementById('exportBtn').addEventListener('click', function() {
    const statusFilter = document.getElementById('statusFilter').value;
    const updatedByFilter = document.getElementById('updatedByFilter').value;

    let url = `?page=export_quran_progress_excel&child_id=<?= $child_id ?>`;
    if (statusFilter) url += `&status=${encodeURIComponent(statusFilter)}`;
    if (updatedByFilter) url += `&updated_by=${encodeURIComponent(updatedByFilter)}`;

    window.location.href = url;
});
</script>
<?php endif; ?>
