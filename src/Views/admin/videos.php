<?php
// src/Views/admin/videos.php
requireLayer('admin');
require_once '../src/Models/Video.php';
$videoModel = new Video($pdo);
$videos = $videoModel->getAll();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">

        <div class="flex items-center">
            <a href="<?= BASE_URL ?>public/index.php?page=dashboard" class="mr-4 text-gray-500 hover:text-gray-700">
                <span class="material-icons-round text-2xl">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Video Edukasi</h1>
                <p class="mt-1 text-sm text-gray-500">Daftar video pembelajaran untuk siswa.</p>
            </div>
        </div>
        <div class="flex space-x-3">
             <a href="<?= BASE_URL ?>public/index.php?page=admin/video_categories" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <span class="material-icons-round mr-2 text-lg">category</span>
                Kategori
            </a>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/create_video" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <span class="material-icons-round mr-2 text-lg">add</span>
                Tambah Video
            </a>
        </div>
    </div>

    <!-- Videos Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($videos as $video): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Thumbnail -->
                <div class="aspect-w-16 aspect-h-9 bg-gray-100 relative group">
                    <img src="https://img.youtube.com/vi/<?= h($video['youtube_id']) ?>/mqdefault.jpg" alt="<?= h($video['title']) ?>" class="object-cover w-full h-48">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center">
                        <span class="material-icons-round text-white text-4xl opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-lg">play_circle</span>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mb-2">
                                <?= h($video['category_name']) ?>
                            </span>
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-2" title="<?= h($video['title']) ?>">
                                <?= h($video['title']) ?>
                            </h3>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-600">
                                <span class="material-icons-round">more_vert</span>
                            </button>
                            <!-- Dropdown -->
                            <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 py-1 border border-gray-100">
                                <a href="<?= BASE_URL ?>public/index.php?page=admin/edit_video&id=<?= $video['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Edit</a>
                                <a href="<?= BASE_URL ?>public/index.php?page=admin/delete_video&id=<?= $video['id'] ?>" onclick="return confirm('Hapus video ini?')" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Hapus</a>
                            </div>
                        </div>
                    </div>
                    
                    <p class="mt-2 text-sm text-gray-500 line-clamp-3">
                        <?= h($video['description']) ?>
                    </p>
                    
                    <div class="mt-4 flex items-center justify-between text-xs text-gray-400 border-t pt-3">
                        <div class="flex items-center">
                            <span class="material-icons-round text-sm mr-1">schedule</span>
                            <?= h($video['duration']) ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($videos)): ?>
            <div class="col-span-full py-12 text-center">
                <span class="material-icons-round text-gray-300 text-6xl">movie</span>
                <p class="mt-2 text-gray-500">Belum ada video edukasi yang ditambahkan.</p>
                <a href="<?= BASE_URL ?>public/index.php?page=admin/create_video" class="mt-4 inline-flex items-center text-primary hover:text-primary-dark">
                    Tambah video pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
