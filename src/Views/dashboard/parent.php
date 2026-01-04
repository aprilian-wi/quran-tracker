<?php
// src/Views/dashboard/parent.php
require_once __DIR__ . '/../../Controllers/DashboardController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new DashboardController($pdo);
$data = $controller->index();

// Layout Decision
if (isLoggedIn()) {
    include __DIR__ . '/../layouts/admin.php';
} else {
    include __DIR__ . '/../layouts/main.php';
}
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">family_restroom</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Anak Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pilih anak untuk melihat atau memperbarui hafalan</p>
        </div>
    </div>
</div>

<?php if (empty($data['children'])): ?>
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 flex items-start gap-4">
        <span class="material-icons-round text-blue-500 text-2xl mt-1">info</span>
        <div>
            <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-1">Belum ada data anak</h3>
            <p class="text-sm text-blue-600 dark:text-blue-400">
                Belum ada anak yang terdaftar pada akun Anda. Silakan hubungi pihak sekolah atau administrator.
            </p>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($data['children'] as $child): ?>
            <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow flex flex-col h-full">
                <!-- Child Header & Photo -->
                <div class="p-6 pb-0 flex flex-col items-center">
                    <div class="relative group cursor-pointer mb-4" onclick="openPhotoModal('<?= $child['id'] ?>')">
                        <div class="w-28 h-28 rounded-full border-4 border-slate-50 dark:border-slate-700 shadow-sm overflow-hidden bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <?php if (!empty($child['photo'])): ?>
                                <img src="<?= BASE_URL ?>public/uploads/children_photos/<?= htmlspecialchars($child['photo']) ?>" alt="<?= h($child['name']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="material-icons-round text-5xl text-slate-300 dark:text-slate-600">person</span>
                            <?php endif; ?>
                        </div>
                        <div class="absolute bottom-0 right-0 bg-white dark:bg-slate-700 rounded-full p-1.5 shadow-md border border-slate-100 dark:border-slate-600 group-hover:bg-primary group-hover:border-primary transition-colors">
                            <span class="material-icons-round text-slate-400 dark:text-slate-300 text-lg group-hover:text-white">edit</span>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white text-center mb-1"><?= h($child['name']) ?></h3>
                    <?php if ($child['class_name']): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 mb-4">
                            <?= h($child['class_name']) ?>
                        </span>
                    <?php else: ?>
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400 mb-4">
                            Belum Ada Kelas
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Notifications -->
                <div class="px-6 mb-4 flex-grow">
                     <?php if (!empty($child['notifications'])): ?>
                        <div class="space-y-2 max-h-40 overflow-y-auto pr-1 costume-scrollbar">
                            <?php foreach ($child['notifications'] as $notification): ?>
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-lg p-3 relative" data-notification-id="<?= $notification['id'] ?>">
                                    <div class="flex gap-2 items-start">
                                        <span class="material-icons-round text-blue-500 text-sm mt-0.5">notifications</span>
                                        <div class="text-xs text-blue-800 dark:text-blue-300">
                                            <p class="font-medium mb-0.5"><?= h($notification['message']) ?></p>
                                            <p class="text-blue-600 dark:text-blue-400 opacity-80">
                                                Oleh <?= h($notification['updated_by_name']) ?> • <?= date('d M', strtotime($notification['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" class="absolute top-1 right-1 text-blue-400 hover:text-blue-600 dark:hover:text-blue-200 p-1 btn-close-notification">
                                        <span class="material-icons-round text-sm">close</span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 border-t border-dashed border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-400">Belum ada pemberitahuan baru.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 p-4">
                    <div class="grid grid-cols-2 gap-2">
                         <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress&child_id=<?= $child['id'] ?>" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-200 transition-colors shadow-sm">
                            <span class="material-icons-round text-lg">menu_book</span>
                            Tahfidz
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_books&child_id=<?= $child['id'] ?>" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-200 transition-colors shadow-sm">
                            <span class="material-icons-round text-lg">auto_stories</span>
                            Tahsin
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_prayers&child_id=<?= $child['id'] ?>" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-teal-600 dark:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/20 hover:border-teal-200 transition-colors shadow-sm">
                            <span class="material-icons-round text-lg">volunteer_activism</span>
                            Doa
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_hadiths&child_id=<?= $child['id'] ?>" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:border-orange-200 transition-colors shadow-sm">
                            <span class="material-icons-round text-lg">format_quote</span>
                            Hadits
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Photo Upload Modal (Tailwind) -->
<div id="photoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="closePhotoModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-card-dark rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="childPhotoForm" action="<?= BASE_URL ?>public/index.php?page=parent/upload_photo" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="child_id" id="modalChildId">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" id="modal-title">
                        Upload Foto Anak
                    </h3>
                </div>
                
                <div class="px-6 py-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Pilih file foto
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-lg hover:border-emerald-500 dark:hover:border-emerald-500 transition-colors cursor-pointer" onclick="document.getElementById('photoInput').click()">
                        <div class="space-y-1 text-center">
                            <span class="material-icons-round text-slate-400 text-4xl">cloud_upload</span>
                            <div class="flex text-sm text-slate-600 dark:text-slate-400">
                                <label for="photoInput" class="relative cursor-pointer bg-transparent rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="photoInput" name="photo" type="file" class="sr-only" accept="image/*" required onchange="updateFileName(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">
                                PNG, JPG, GIF up to 5MB
                            </p>
                            <p id="fileName" class="text-sm text-emerald-600 font-medium mt-2 hidden"></p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-row-reverse gap-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm transition-colors">
                        Upload
                    </button>
                    <button type="button" class="mt-3 w-full sm:w-auto mt-0 inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-card-dark text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:text-sm transition-colors" onclick="closePhotoModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Logic
const modal = document.getElementById('photoModal');
const modalChildId = document.getElementById('modalChildId');
const fileNameDisplay = document.getElementById('fileName');

function openPhotoModal(childId) {
    modalChildId.value = childId;
    modal.classList.remove('hidden');
    // Reset file input display
    document.getElementById('childPhotoForm').reset();
    fileNameDisplay.classList.add('hidden');
    fileNameDisplay.textContent = '';
}

function closePhotoModal() {
    modal.classList.add('hidden');
}

function updateFileName(input) {
    if (input.files && input.files[0]) {
        fileNameDisplay.textContent = input.files[0].name;
        fileNameDisplay.classList.remove('hidden');
    }
}

// Form Submission with fetch
document.getElementById('childPhotoForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Uploading...';
    submitBtn.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Upload failed'));
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error uploading photo');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
});

// Dismiss Notifications
document.addEventListener('click', function(event) {
    if (event.target.closest('.btn-close-notification')) {
        const btn = event.target.closest('.btn-close-notification');
        const alertElement = btn.closest('div[data-notification-id]');
        const notificationId = alertElement.getAttribute('data-notification-id');

        if (notificationId) {
            // Optimistically remove
            alertElement.style.opacity = '0';
            setTimeout(() => alertElement.remove(), 300);

            fetch('<?= BASE_URL ?>public/index.php?page=mark_notification_viewed', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: 'notification_id=' + encodeURIComponent(notificationId)
            });
        }
    }
});
</script>
