<?php
// src/Views/admin/edit_class.php
global $pdo;
require_once __DIR__ . '/../../Models/Class.php';
require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Child.php';

$class_id = (int)($_GET['class_id'] ?? 0);
if (!$class_id) {
    setFlash('danger', 'Invalid class.');
    redirect('admin/classes');
}

$classModel = new ClassModel($pdo);
$class = $classModel->getWithTeachers($class_id);

if (!$class) {
    setFlash('danger', 'Class not found.');
    redirect('admin/classes');
}

$students = $classModel->getStudents($class_id);

// Get all teachers for the dropdown
$userModel = new User($pdo);
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'teacher' AND school_id = ? ORDER BY name");
$stmt->execute([$_SESSION['school_id']]);
$allTeachers = $stmt->fetchAll();

// Filter out already assigned teachers
$assigned_teacher_ids = array_column($class['teachers'], 'id');
$available_teachers = array_filter($allTeachers, function($t) use ($assigned_teacher_ids) {
    return !in_array($t['id'], $assigned_teacher_ids);
});

// Get unassigned children
$childModel = new Child($pdo);
$unassignedChildren = $childModel->getUnassignedChildren();

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">edit_note</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Kelas: <?= h($class['name']) ?></h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Management data kelas dan siswa</p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>public/index.php?page=admin/classes" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Left Column -->
    <div class="space-y-6">
        <!-- Edit Class Name -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-3 bg-blue-600 flex items-center gap-2 text-white">
                <span class="material-icons-round">badge</span>
                <h3 class="font-semibold">Nama Kelas</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class" class="flex flex-col sm:flex-row gap-3">
                    <?= csrfInput() ?>
                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                    <input type="hidden" name="action" value="update_name">
                    
                    <input class="flex-1 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm" placeholder="Nama Kelas" type="text" name="name" value="<?= h($class['name']) ?>" required/>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition-colors flex items-center justify-center gap-2">
                        <span class="material-icons-round text-lg">check_circle</span>
                        Perbarui
                    </button>
                </form>
            </div>
        </div>

        <!-- Manage Teachers -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-3 bg-cyan-500 flex items-center gap-2 text-white">
                <span class="material-icons-round">supervisor_account</span>
                <h3 class="font-semibold">Guru yang Ditugaskan (<?= count($class['teachers']) ?>)</h3>
            </div>
            <div class="p-6 space-y-3">
                <!-- Add Teacher Form -->
                <?php if (count($available_teachers) > 0): ?>
                    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class" class="flex gap-3 mb-4">
                        <?= csrfInput() ?>
                        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                        <input type="hidden" name="action" value="add_teacher">
                        <select name="teacher_id" class="flex-1 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-cyan-500 focus:border-cyan-500 shadow-sm" required>
                            <option value="">Pilih Guru...</option>
                            <?php foreach ($available_teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= h($teacher['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="px-3 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium shadow-sm transition-colors flex items-center gap-1">
                            <span class="material-icons-round">add</span>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (count($class['teachers']) > 0): ?>
                    <?php foreach ($class['teachers'] as $teacher): ?>
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-3 sm:p-4 flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-300 flex items-center justify-center font-bold text-xs uppercase">
                                    <?= strtoupper(substr($teacher['name'], 0, 1)) ?>
                                </div>
                                <span class="font-medium text-slate-900 dark:text-white"><?= h($teacher['name']) ?></span>
                            </div>
                            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class" onsubmit="return confirm('Hapus guru ini?')">
                                <?= csrfInput() ?>
                                <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                                <input type="hidden" name="action" value="remove_teacher">
                                <button type="submit" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-medium rounded-md transition-colors flex items-center gap-1">
                                    <span class="material-icons-round text-sm">delete</span>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-slate-500 dark:text-slate-400 italic">Tidak ada guru yang ditugaskan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Students -->
    <div class="flex flex-col h-full space-y-6">
        <!-- Assigned Students -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full" style="max-height: 500px">
            <div class="px-6 py-3 bg-amber-400 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-2">
                    <span class="material-icons-round">groups</span>
                    <h3 class="font-semibold text-slate-900">Siswa (<?= count($students) ?>)</h3>
                </div>
                <?php if (count($students) > 0): ?>
                    <button type="submit" form="bulkRemoveForm" class="px-3 py-1 bg-rose-500 hover:bg-rose-600 text-white text-xs font-medium rounded shadow-sm transition-colors flex items-center gap-1" onclick="return confirm('Hapus siswa terpilih dari kelas ini?')">
                        <span class="material-icons-round text-sm">delete_sweep</span>
                        Hapus Terpilih
                    </button>
                <?php endif; ?>
            </div>
            <div class="p-6 flex-grow overflow-y-auto">
                <form id="bulkRemoveForm" method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                    <?= csrfInput() ?>
                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                    <input type="hidden" name="action" value="bulk_remove_students">
                    
                    <?php if (count($students) > 0): ?>
                        <div class="mb-4 flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <input class="rounded border-slate-300 text-amber-500 focus:ring-amber-500 w-4 h-4 cursor-pointer" type="checkbox" id="checkAllStudents"/>
                            <label for="checkAllStudents" class="text-sm text-slate-600 dark:text-slate-400 font-medium cursor-pointer">Pilih Semua</label>
                        </div>
                        <div class="space-y-3">
                            <?php foreach ($students as $student): ?>
                                <label class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-3 flex items-start gap-3 shadow-sm hover:border-amber-400 transition-colors group cursor-pointer">
                                    <input class="mt-1 rounded border-slate-300 text-amber-500 focus:ring-amber-500 w-4 h-4 cursor-pointer student-checkbox" type="checkbox" name="child_ids[]" value="<?= $student['id'] ?>"/>
                                    <div class="flex-1">
                                        <div class="font-semibold text-slate-900 dark:text-white text-sm group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors"><?= h($student['name']) ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Wali: <?= h($student['parent_name'] ?? 'N/A') ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400 italic text-center py-4">Tidak ada siswa yang ditugaskan</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Assign Students -->
         <?php if (count($unassignedChildren) > 0): ?>
            <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col" style="max-height: 400px">
                <div class="px-6 py-3 bg-emerald-600 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <span class="material-icons-round">person_add</span>
                        <h3 class="font-semibold">Tetapkan Siswa</h3>
                    </div>
                    <button type="submit" form="bulkAssignForm" class="px-3 py-1 bg-white text-emerald-700 hover:bg-emerald-50 text-xs font-bold rounded shadow-sm transition-colors flex items-center gap-1">
                        <span class="material-icons-round text-sm">add</span>
                        Tambah Terpilih
                    </button>
                </div>
                <div class="p-6 flex-grow overflow-y-auto">
                    <form id="bulkAssignForm" method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                        <?= csrfInput() ?>
                        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                        <input type="hidden" name="action" value="bulk_assign_students">
                        
                        <div class="mb-4 flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <input class="rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-4 h-4 cursor-pointer" type="checkbox" id="checkAllUnassigned"/>
                            <label for="checkAllUnassigned" class="text-sm text-slate-600 dark:text-slate-400 font-medium cursor-pointer">Pilih Semua</label>
                        </div>
                        
                        <div class="space-y-3">
                            <?php foreach ($unassignedChildren as $child): ?>
                                <label class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-3 flex items-start gap-3 shadow-sm hover:border-emerald-400 transition-colors group cursor-pointer">
                                    <input class="mt-1 rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-4 h-4 cursor-pointer unassigned-checkbox" type="checkbox" name="child_ids[]" value="<?= $child['id'] ?>"/>
                                    <div class="flex-1">
                                        <div class="font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors"><?= h($child['name']) ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Wali: <?= h($child['parent_name'] ?? 'N/A') ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </div>
         <?php endif; ?>
    </div>
</div>

<!-- Danger Zone -->
<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">
    <div class="px-6 py-3 bg-rose-600 flex items-center gap-2 text-white">
        <span class="material-icons-round">warning</span>
        <h3 class="font-semibold">Zona Bahaya</h3>
    </div>
    <div class="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <p class="text-rose-600 dark:text-rose-400 text-sm font-medium">
                Peringatan: Menghapus kelas ini akan menghapus semua asosiasi tetapi tidak akan menghapus siswa.
            </p>
            <button onclick="document.getElementById('deleteClassModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium shadow-sm transition-colors flex items-center justify-center gap-2 whitespace-nowrap">
                <span class="material-icons-round text-lg">delete_forever</span>
                Hapus Kelas
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteClassModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('deleteClassModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-card-dark rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=edit_class">
                <?= csrfInput() ?>
                <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                <input type="hidden" name="action" value="delete_class">
                
                <div class="bg-white dark:bg-card-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-red-600 dark:text-red-400">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" id="modal-title">
                                Konfirmasi Penghapusan
                            </h3>
                            <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                <p>Apakah Anda yakin ingin menghapus kelas "<strong><?= h($class['name']) ?></strong>"?</p>
                                <p class="mt-1 text-red-600 dark:text-red-400">Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                            <div class="mt-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="confirm" value="yes" required class="rounded border-slate-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-slate-600 dark:text-slate-300">Ya, saya ingin menghapus kelas ini</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus Secara Permanen
                    </button>
                    <button type="button" onclick="document.getElementById('deleteClassModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Check all checkboxes helper
    function setupCheckAll(masterCheckboxId, itemClass) {
        const master = document.getElementById(masterCheckboxId);
        if(!master) return;
        
        master.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.' + itemClass);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    setupCheckAll('checkAllStudents', 'student-checkbox');
    setupCheckAll('checkAllUnassigned', 'unassigned-checkbox');
</script>
