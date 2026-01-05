<?php
// src/Views/admin/create_video.php
requireLayer('admin');
require_once '../src/Models/VideoCategory.php';

$categoryModel = new VideoCategory($pdo);
$categories = $categoryModel->getAll();
?>

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="<?= BASE_URL ?>public/index.php?page=admin/videos" class="mr-4 text-gray-500 hover:text-gray-700">
            <span class="material-icons-round text-2xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Video Baru</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="<?= BASE_URL ?>public/index.php?page=admin/store_video" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <!-- YouTube URL Input with Auto-Fetch logic (simulated for now or JS helper) -->
            <div>
                <label for="youtube_url" class="block text-sm font-medium text-gray-700">Link YouTube</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="text" name="youtube_url" id="youtube_url" required
                           class="focus:ring-primary focus:border-primary block w-full pl-3 pr-10 sm:text-sm border-gray-300 rounded-md" 
                           placeholder="https://youtu.be/..."
                           onchange="extractYoutubeId(this.value)">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="material-icons-round text-gray-400">link</span>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Paste link YouTube lengkap, ID akan diambil otomatis.</p>
                <input type="hidden" name="youtube_id" id="youtube_id">
            </div>

            <!-- Preview Container -->
            <div id="video-preview" class="hidden aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                <!-- iframe injected by JS -->
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                     <label for="title" class="block text-sm font-medium text-gray-700">Judul Video</label>
                     <input type="text" name="title" id="title" required
                        class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category_id" name="category_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= h($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                 <div>
                     <label for="duration" class="block text-sm font-medium text-gray-700">Durasi (MM:SS)</label>
                     <input type="text" name="duration" id="duration" placeholder="05:30"
                        class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div>
                 <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                 <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-primary focus:border-primary mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/videos" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Simpan Video
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function extractYoutubeId(url) {
    if (!url) return;
    
    // RegEx to partial YouTube ID
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);

    if (match && match[2].length === 11) {
        const id = match[2];
        document.getElementById('youtube_id').value = id;
        
        // Show Preview
        const preview = document.getElementById('video-preview');
        preview.classList.remove('hidden');
        preview.innerHTML = `<iframe class="w-full h-full" src="https://www.youtube.com/embed/${id}" frameborder="0" allowfullscreen></iframe>`;
        
        // Optional: Can use YouTube API key to fetch title/desc automatically if user provides key
    } else {
        alert('Link YouTube tidak valid!');
        document.getElementById('video-preview').classList.add('hidden');
    }
}
</script>
