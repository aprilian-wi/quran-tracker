<?php
// src/Views/admin/promote_class.php
// This view handles mass promotion/migration of students from one class to another.
global $pdo;

// Fetch all classes for the dropdowns
$stmt = $pdo->prepare("SELECT id, name FROM classes WHERE school_id = ? ORDER BY name");
$stmt->execute([$_SESSION['school_id']]);
$classes = $stmt->fetchAll();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-teal-600 dark:text-teal-400">
            <span class="material-icons-round text-2xl">upgrade</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Promosi / Pindah Kelas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pindahkan siswa antar kelas secara massal</p>
        </div>
    </div>
    
    <a href="<?= BASE_URL ?>public/index.php?page=admin/classes" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
        <span class="material-icons-round text-lg mr-2">arrow_back</span>
        Kembali ke Kelas
    </a>
</div>

<div class="space-y-6">
    <!-- Info Alert -->
     <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-3">
        <span class="material-icons-round text-blue-500 mt-0.5">info</span>
        <div>
            <p class="text-sm text-blue-800 dark:text-blue-300">
                Gunakan fitur ini untuk memindahkan siswa dari satu kelas ke kelas lain secara massal (misalnya saat kenaikan kelas atau restrukturisasi kelas).
            </p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=promote_class_action" id="promoteForm">
            <?= csrfInput() ?>
            
            <!-- Class Selection -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center mb-8">
                <!-- Source Class -->
                <div class="md:col-span-5">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dari Kelas (Sumber)</label>
                    <select id="source_class" name="source_class_id" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500 shadow-sm" required>
                        <option value="">-- Pilih Kelas Asal --</option>
                        <option value="-1" class="font-bold text-amber-600 dark:text-amber-500">⚠️ Siswa Belum Ada Kelas (Unassigned)</option>
                        <option disabled>------------------------</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Arrow Icon -->
                <div class="md:col-span-2 flex justify-center py-2 md:py-0 md:mt-6 text-slate-400 dark:text-slate-500">
                    <span class="material-icons-round text-3xl md:rotate-0 rotate-90">arrow_forward</span>
                </div>
                
                <!-- Target Class -->
                <div class="md:col-span-5">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ke Kelas (Tujuan)</label>
                    <select id="target_class" name="target_class_id" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500 shadow-sm" required>
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Student List Selection Area -->
            <div id="student_selection_area" class="hidden animate-fade-in">
                <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Pilih Siswa untuk Dipindahkan</h3>
                    
                    <div class="flex items-center justify-between mb-4 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center h-5">
                            <input id="checkAllPromotion" type="checkbox" class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-slate-300 rounded" checked>
                            <label for="checkAllPromotion" class="ml-2 block text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                                Pilih Semua
                            </label>
                        </div>
                        <span id="studentCountBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400">
                            0 Siswa
                        </span>
                    </div>

                    <!-- Scrollable List -->
                    <div id="student_list" class="space-y-2 max-h-96 overflow-y-auto pr-2 costume-scrollbar mb-6">
                        <!-- Content loaded via JS -->
                        <div class="text-center py-12">
                             <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-teal-500 border-r-transparent"></div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Memuat data siswa...</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors" onclick="return confirm('Apakah Anda yakin ingin memindahkan siswa yang dipilih?')">
                            <span class="material-icons-round text-lg mr-2">sync_alt</span>
                            Proses Perpindahan
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div id="empty_state" class="text-center py-12 bg-slate-50 dark:bg-slate-800/50 rounded-lg border-2 border-dashed border-slate-200 dark:border-slate-700">
                <span class="material-icons-round text-slate-400 text-4xl mb-2">checklist</span>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Silakan pilih "Kelas Asal" terlebih dahulu untuk melihat daftar siswa.</p>
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
            studentArea.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        // Show loading state
        studentArea.classList.remove('hidden');
        emptyState.classList.add('hidden');
        studentList.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-teal-500 border-r-transparent"></div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Memuat data siswa...</p>
            </div>
        `;

        // Fetch students via AJAX
        fetch(`<?= BASE_URL ?>public/index.php?page=api/get_class_students&class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                studentList.innerHTML = '';
                countBadge.textContent = `${data.length} Siswa`;
                
                if (data.length === 0) {
                    studentList.innerHTML = `
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 text-center">
                            <p class="text-sm text-amber-700 dark:text-amber-400 font-medium">Tidak ada siswa di kelas ini.</p>
                        </div>
                    `;
                } else {
                    data.forEach(student => {
                        const item = document.createElement('label');
                        item.className = 'flex items-center space-x-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer group bg-white dark:bg-slate-800/30';
                        item.innerHTML = `
                            <input class="form-check-input student-promo-check h-5 w-5 text-teal-600 focus:ring-teal-500 border-slate-300 rounded cursor-pointer" type="checkbox" name="child_ids[]" value="${student.id}" checked>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">${escapeHtml(student.name)}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                    <span class="material-icons-round text-[10px]">person</span> 
                                    Wali: ${escapeHtml(student.parent_name || '-')}
                                </p>
                            </div>
                        `;
                        studentList.appendChild(item);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                studentList.innerHTML = `
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-center">
                         <p class="text-sm text-red-700 dark:text-red-400 font-medium">Gagal memuat data siswa.</p>
                    </div>
                `;
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
            if (opt.value === valueToDisable && valueToDisable !== "") {
                opt.disabled = true;
                opt.classList.add('text-slate-300', 'dark:text-slate-600');
            } else {
                opt.disabled = false;
                opt.classList.remove('text-slate-300', 'dark:text-slate-600');
            }
        });
    }
});
</script>
