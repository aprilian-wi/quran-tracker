<?php
// src/Views/teacher/class_students_pwa.php
// Expected: $students, $class_id
?>
<section class="mb-6 pt-2 px-1 flex items-center space-x-3">
    <a href="<?= BASE_URL ?>public/index.php?page=dashboard&mode=pwa" class="text-text-main-light dark:text-white hover:text-primary transition-colors">
        <span class="material-icons-round text-2xl">arrow_back</span>
    </a>
    <div class="flex flex-col">
        <h2 class="text-xl font-display font-bold text-text-main-light dark:text-white">Daftar Siswa</h2>
        <?php if (!empty($class)): ?>
            <p class="text-xs text-primary dark:text-green-400 font-medium">Kelas <?= h($class['name']) ?></p>
        <?php endif; ?>
    </div>
</section>

<?php if (empty($students)): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center space-y-4">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-400">
            <span class="material-icons-round text-3xl">people_outline</span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada siswa di kelas ini.</p>
    </div>
<?php else: ?>
    <section class="space-y-4 pb-20">
        <?php foreach ($students as $student): ?>
        <div class="bg-surface-light dark:bg-surface-dark rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                    <?= strtoupper(substr($student['name'], 0, 2)) ?>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 dark:text-white text-sm"><?= h($student['name']) ?></h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= h($student['parent_name']) ?></p>
                </div>
            </div>
            
            <button onclick="openActionSheet(<?= $student['id'] ?>, '<?= h($student['name']) ?>')" class="bg-primary/10 hover:bg-primary/20 text-primary rounded-xl px-4 py-2 text-xs font-bold transition-colors">
                Update Progress
            </button>
        </div>
        <?php endforeach; ?>
    </section>

    <!-- Bottom Sheet Modal -->
    <div id="actionSheetBackdrop" class="fixed inset-0 bg-black/50 z-[60] hidden opacity-0 transition-opacity duration-300" onclick="closeActionSheet()"></div>
    <div id="actionSheet" class="fixed bottom-0 left-0 right-0 bg-white dark:bg-surface-dark rounded-t-3xl p-6 z-[70] transform translate-y-full transition-transform duration-300 shadow-[0_-5px_30px_rgba(0,0,0,0.1)] pb-12">
        <div class="w-12 h-1 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-6"></div>
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-xs text-text-sub-light dark:text-text-sub-dark">Update Progress untuk</p>
                <h3 id="sheetStudentName" class="text-lg font-display font-bold text-text-main-light dark:text-white">Student Name</h3>
            </div>
            <button onclick="closeActionSheet()" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500">
                <span class="material-icons-round text-lg">close</span>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <a id="linkTahfidz" href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-md active:scale-95 transition-all">
                <span class="material-icons-round text-3xl mb-1">mic</span>
                <span class="font-bold text-sm">Tahfidz</span>
            </a>
            <a id="linkTahsin" href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-yellow-400 hover:bg-yellow-500 text-yellow-900 shadow-md active:scale-95 transition-all">
                <span class="material-icons-round text-3xl mb-1">auto_stories</span>
                <span class="font-bold text-sm">Tahsin</span>
            </a>
            <a id="linkDoa" href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-cyan-400 hover:bg-cyan-500 text-cyan-900 shadow-md active:scale-95 transition-all">
                <span class="material-icons-round text-3xl mb-1">waving_hand</span>
                <span class="font-bold text-sm">Doa</span>
            </a>
            <a id="linkHadits" href="#" class="flex flex-col items-center justify-center p-4 rounded-xl bg-rose-500 hover:bg-rose-600 text-white shadow-md active:scale-95 transition-all">
                <span class="material-icons-round text-3xl mb-1">format_quote</span>
                <span class="font-bold text-sm">Hadits</span>
            </a>
        </div>
    </div>

    <script>
    function openActionSheet(id, name) {
        document.getElementById('sheetStudentName').innerText = name;
        
        // Update links with mode=pwa and class_id
        const base = '<?= BASE_URL ?>public/index.php?page=';
        const classParam = '<?= isset($class_id) ? "&class_id=$class_id" : "" ?>';
        
        document.getElementById('linkTahfidz').href = `${base}teacher/update_progress&child_id=${id}&mode=pwa${classParam}`;
        document.getElementById('linkTahsin').href = `${base}teacher/update_progress_books&child_id=${id}&mode=pwa${classParam}`;
        document.getElementById('linkDoa').href = `${base}teacher/update_progress_prayers&child_id=${id}&mode=pwa${classParam}`;
        document.getElementById('linkHadits').href = `${base}teacher/update_progress_hadiths&child_id=${id}&mode=pwa${classParam}`;

        // Show
        const backdrop = document.getElementById('actionSheetBackdrop');
        const sheet = document.getElementById('actionSheet');
        
        backdrop.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            sheet.classList.remove('translate-y-full');
        });
    }

    function closeActionSheet() {
        const backdrop = document.getElementById('actionSheetBackdrop');
        const sheet = document.getElementById('actionSheet');
        
        backdrop.classList.add('opacity-0');
        sheet.classList.add('translate-y-full');
        
        setTimeout(() => {
            backdrop.classList.add('hidden');
        }, 300);
    }
    </script>
<?php endif; ?>
