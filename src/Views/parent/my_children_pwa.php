<?php
// src/Views/parent/my_children_pwa.php
// Assumes $children is available from the controller
?>

<section>
    <div class="flex items-center space-x-2 mb-4 px-1">
        <span class="material-icons-round text-primary dark:text-green-400">face</span>
        <h2 class="text-lg font-display font-bold text-text-main-light dark:text-white">Anak Saya</h2>
    </div>

    <?php if (empty($children)): ?>
        <div
            class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 border border-gray-100 dark:border-gray-800 text-center">
            <p class="text-text-sub-light dark:text-text-sub-dark">Tidak ada anak ditemukan.</p>
        </div>
    <?php else: ?>
        <?php foreach ($children as $child): ?>
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-2xl shadow-card p-6 border border-gray-100 dark:border-gray-800 relative overflow-hidden mb-6">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -mr-10 -mt-10 z-0"></div>

                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-display font-bold text-gray-900 dark:text-white capitalize">
                                <?= h($child['name']) ?></h3>
                            <p class="text-xs text-text-sub-light dark:text-text-sub-dark mt-1">Siswa Aktif</p>
                        </div>
                        <?php if (!empty($child['class_name'])): ?>
                            <span class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                <?= h($child['class_name']) ?>
                            </span>
                        <?php else: ?>
                            <span class="bg-gray-400 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                Belum Ada Kelas
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col items-center mb-6">
                        <div class="relative group">
                            <!-- Photo Container - Triggers Preview -->
                            <div class="w-28 h-28 rounded-full border-4 border-white dark:border-gray-700 shadow-md overflow-hidden bg-gray-200 transition-transform active:scale-95 hover:shadow-lg cursor-pointer"
                                onclick="viewPhoto('<?= !empty($child['photo']) ? BASE_URL . 'public/uploads/children_photos/' . h($child['photo']) : '' ?>')">
                                <?php if (!empty($child['photo'])): ?>
                                    <img alt="Foto profil <?= h($child['name']) ?>" class="w-full h-full object-cover"
                                        src="<?= BASE_URL ?>public/uploads/children_photos/<?= h($child['photo']) ?>" />
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <span class="material-icons-round text-6xl">person</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Edit Button - Triggers Upload Modal -->
                            <button onclick="openPhotoModal(<?= $child['id'] ?>)"
                                class="absolute bottom-1 right-1 w-8 h-8 flex items-center justify-center bg-white/30 dark:bg-gray-800/30 backdrop-blur-sm text-gray-800 dark:text-white rounded-full shadow-sm hover:bg-white dark:hover:bg-gray-700 hover:shadow-md transition-all z-10">
                                <span class="material-icons-round text-base">photo_camera</span>
                            </button>
                        </div>

                        <div class="mt-4 text-center">
                            <p
                                class="text-sm text-text-sub-light dark:text-text-sub-dark italic bg-gray-50 dark:bg-gray-800/50 px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-800">
                                "Semangat belajar Al-Qur'an!"
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress&child_id=<?= $child['id'] ?>&mode=pwa"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">mic</span> Tahfidz
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_books&child_id=<?= $child['id'] ?>&mode=pwa"
                            class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">auto_stories</span> Tahsin
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_prayers&child_id=<?= $child['id'] ?>&mode=pwa"
                            class="bg-cyan-400 hover:bg-cyan-500 text-cyan-900 font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">waving_hand</span> Doa
                        </a>
                        <a href="<?= BASE_URL ?>public/index.php?page=parent/update_progress_hadiths&child_id=<?= $child['id'] ?>&mode=pwa"
                            class="bg-rose-500 hover:bg-rose-600 text-white font-medium py-2.5 px-2 rounded-xl text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-1">
                            <span class="material-icons-round text-sm">format_quote</span> Hadits
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<!-- Photo Preview Modal (Lightbox) -->
<div id="photoPreviewModal"
    class="fixed inset-0 z-[110] hidden bg-black bg-opacity-95 flex items-center justify-center p-4 transition-opacity duration-300"
    onclick="closePreview()">
    <div class="relative w-full max-w-3xl transform transition-transform duration-300 scale-100">
        <img id="previewImage" src="" class="max-w-full max-h-[85vh] mx-auto rounded-lg shadow-2xl object-contain"
            onclick="event.stopPropagation()">
        <button class="absolute -top-12 right-0 text-white p-2 rounded-full hover:bg-white/10 transition-colors"
            onclick="closePreview()">
            <span class="material-icons-round text-3xl">close</span>
        </button>
    </div>
</div>

<!-- PWA Upload Photo Modal (Tailwind) -->
<div id="pwaPhotoModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"
        onclick="closePhotoModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark text-left shadow-xl transition-all sm:my-8 w-full max-w-sm border border-gray-100 dark:border-gray-700">
                <div class="bg-white dark:bg-surface-dark px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-display font-semibold leading-6 text-gray-900 dark:text-white"
                                id="modal-title">Upload Foto Anak</h3>
                            <div class="mt-2">
                                <form id="pwaPhotoForm"
                                    action="<?= BASE_URL ?>public/index.php?page=parent/upload_photo" method="POST"
                                    enctype="multipart/form-data" class="space-y-4">
                                    <input type="hidden" name="child_id" id="pwaModalChildId">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                    <div class="mt-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih
                                            Foto</label>
                                        <div class="flex items-center justify-center w-full">
                                            <label for="dropzone-file"
                                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <span
                                                        class="material-icons-round text-gray-400 mb-2">cloud_upload</span>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400"><span
                                                            class="font-semibold">Klik untuk upload</span></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, GIF
                                                        (Max 5MB)</p>
                                                </div>
                                                <input id="dropzone-file" name="photo" type="file" class="hidden"
                                                    accept="image/*" required onchange="handleFileSelect(this)" />
                                            </label>
                                        </div>
                                        <p id="fileNameDisplay"
                                            class="text-xs text-center text-gray-500 mt-2 min-h-[1rem]"></p>
                                    </div>

                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                                        <button type="submit"
                                            class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:ml-3 sm:w-auto transition-colors">Upload</button>
                                        <button type="button"
                                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto transition-colors"
                                            onclick="closePhotoModal()">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewPhoto(url) {
        if (!url) return;
        const modal = document.getElementById('photoPreviewModal');
        const img = document.getElementById('previewImage');
        img.src = url;
        modal.classList.remove('hidden');
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }

    function closePreview() {
        const modal = document.getElementById('photoPreviewModal');
        modal.classList.add('hidden');
        setTimeout(() => {
            document.getElementById('previewImage').src = '';
        }, 300); // Wait for transition if we added one, currently mostly instant hide but good practice
        document.body.style.overflow = '';
    }

    function openPhotoModal(childId) {
        document.getElementById('pwaModalChildId').value = childId;
        document.getElementById('pwaPhotoModal').classList.remove('hidden');
        document.getElementById('fileNameDisplay').textContent = '';
        document.getElementById('dropzone-file').value = '';
    }
    // ... rest of script ...

    function closePhotoModal() {
        document.getElementById('pwaPhotoModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const fileName = input.files[0] ? input.files[0].name : '';
        document.getElementById('fileNameDisplay').textContent = fileName;
    }

    document.getElementById('pwaPhotoForm').addEventListener('submit', async function (event) {
        event.preventDefault();

        // Disable button to prevent double submit
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Uploading...';

        const form = event.currentTarget;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            // Check if response is JSON (it might be HTML error page if route missing)
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await response.text();
                console.error("Non-JSON Response:", text);
                throw new Error(`Server Error (${response.status}): Response is not JSON`);
            }

            const data = await response.json();

            if (data.success) {
                // Success
                closePhotoModal();
                window.location.reload();
            } else {
                alert('Error: ' + (data.error || 'Upload failed'));
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            console.error('Upload Error:', error);
            alert('Gagal: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
</script>