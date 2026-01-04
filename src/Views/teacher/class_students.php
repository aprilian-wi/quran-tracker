<?php
// src/Views/teacher/class_students.php
global $pdo;
require_once __DIR__ . '/../../Controllers/TeacherController.php';
require_once __DIR__ . '/../../Models/Progress.php';
require_once __DIR__ . '/../../Helpers/functions.php';
require_once __DIR__ . '/../../Models/Class.php';
require_once __DIR__ . '/../../Models/Child.php';

$class_id = $_GET['class_id'] ?? 0;
if (!$class_id || !is_numeric($class_id)) {
    setFlash('danger', 'Invalid class. Please specify a class_id parameter.');
    redirect('dashboard');
}

$controller = new TeacherController($pdo);
$students = $controller->classStudents($class_id);

$classModel = new ClassModel($pdo);
// $isOwner logic removed as the feature to assign students is removed from this view.
$class = $classModel->getWithTeachers($class_id); // Fetch class info for display

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">school</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Kelas <?= h($class['name'] ?? 'Undefined') ?></h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage students and their progress</p>
        </div>
    </div>
    
    <div class="flex gap-2">
        <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
            <span class="material-icons-round text-lg">arrow_back</span>
            Kembali
        </a>
    </div>
</div>

<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
         <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
            <span class="material-icons-round text-slate-500">people</span>
            Daftar Siswa
         </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-800/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Orang Tua/Wali</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-96">Perbarui Kemajuan</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-card-dark divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                <span class="material-icons-round text-4xl mb-2">school</span>
                                <p class="text-base font-medium">Belum ada siswa di kelas ini</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-900 dark:text-white"><?= h($student['name']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                <?= h($student['parent_name'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center gap-2">
                                     <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress&child_id=<?= $student['id'] ?>" class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 rounded-lg text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors decoration-0">
                                        Tahfidz
                                    </a>
                                    <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_books&child_id=<?= $student['id'] ?>" class="inline-flex items-center px-3 py-1.5 border border-amber-200 dark:border-amber-800 rounded-lg text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors decoration-0">
                                        Tahsin
                                    </a>
                                    <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_hadiths&child_id=<?= $student['id'] ?>" class="inline-flex items-center px-3 py-1.5 border border-cyan-200 dark:border-cyan-800 rounded-lg text-xs font-medium text-cyan-700 dark:text-cyan-300 bg-cyan-50 dark:bg-cyan-900/20 hover:bg-cyan-100 dark:hover:bg-cyan-900/40 transition-colors decoration-0">
                                        Hadits
                                    </a>
                                    <a href="<?= BASE_URL ?>public/index.php?page=teacher/update_progress_prayers&child_id=<?= $student['id'] ?>" class="inline-flex items-center px-3 py-1.5 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors decoration-0">
                                        Doa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>