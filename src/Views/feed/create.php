<?php
// src/Views/feed/create.php
?>
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <a href="<?= BASE_URL ?>public/index.php?page=feed/index&mode=pwa"
            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <span class="material-icons-round">arrow_back</span>
        </a>
        <h2 class="text-lg font-bold">Bagikan Momen</h2>
    </div>

    <form action="<?= BASE_URL ?>public/index.php?page=feed/action/create" method="POST" enctype="multipart/form-data"
        class="space-y-6">
        <?= csrfInput() ?>

        <!-- Media Upload Area -->
        <div class="relative group">
            <label for="media-upload"
                class="flex flex-col items-center justify-center w-full aspect-video border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl cursor-pointer hover:border-primary transition-colors bg-gray-50 dark:bg-surface-dark overflow-hidden">
                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-gray-400" id="upload-placeholder">
                    <span class="material-icons-round text-4xl mb-2">add_photo_alternate</span>
                    <p class="text-xs text-center">Tap untuk upload Foto/Video</p>
                </div>
                <img id="image-preview" class="absolute inset-0 w-full h-full object-cover hidden" />
                <video id="video-preview" class="absolute inset-0 w-full h-full object-cover hidden" controls></video>
                <input id="media-upload" name="media" type="file" class="hidden" accept="image/*,video/*"
                    onchange="previewMedia(this)" />
            </label>
        </div>

        <!-- Caption -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Caption</label>
            <textarea name="caption" rows="4"
                class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-surface-dark focus:ring-primary focus:border-primary text-sm shadow-sm"
                placeholder="Ceritakan tentang momen ini..."></textarea>
        </div>

        <!-- Info Dialog -->
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl flex items-start space-x-3">
            <span class="material-icons-round text-blue-500 text-lg mt-0.5">info</span>
            <p class="text-xs text-blue-600 dark:text-blue-300 leading-relaxed">
                Postingan ini akan hilang secara otomatis setelah 24 jam.
                <br>
                Maksimum ukuran file: <strong><?= ini_get('upload_max_filesize') ?></strong>.
            </p>
        </div>

        <!-- Submit -->
        <button type="submit"
            class="w-full py-3.5 bg-primary text-white rounded-xl font-semibold shadow-lg shadow-green-200 dark:shadow-none hover:bg-primary-dark transition-all active:scale-[0.98]">
            Bagikan
        </button>
    </form>
</div>

<script>
    function previewMedia(input) {
        const file = input.files[0];
        if (!file) return;

        const placeholder = document.getElementById('upload-placeholder');
        const imgPreview = document.getElementById('image-preview');
        const vidPreview = document.getElementById('video-preview');

        const objectUrl = URL.createObjectURL(file);

        placeholder.classList.add('hidden');
        imgPreview.classList.add('hidden');
        vidPreview.classList.add('hidden');

        if (file.type.startsWith('image/')) {
            imgPreview.src = objectUrl;
            imgPreview.classList.remove('hidden');
        } else if (file.type.startsWith('video/')) {
            vidPreview.src = objectUrl;
            vidPreview.classList.remove('hidden');
        }
    }
</script>