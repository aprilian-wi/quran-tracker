<?php
// src/Views/admin/video_categories.php
requireLayer('admin');
require_once '../src/Models/VideoCategory.php';
$categoryModel = new VideoCategory($pdo);
$categories = $categoryModel->getAll();
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/videos" class="mr-4 text-gray-500 hover:text-gray-700">
                <span class="material-icons-round text-2xl">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Kategori Video</h1>
        </div>
        <a href="<?= BASE_URL ?>public/index.php?page=admin/create_video_category" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
            <span class="material-icons-round mr-2 text-lg">add</span>
            Tambah Kategori
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden rounded-md border border-gray-200">
        <ul class="divide-y divide-gray-200">
            <?php foreach ($categories as $cat): ?>
                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <span class="material-icons-round"><?= h($cat['icon']) ?></span>
                            </span>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900"><?= h($cat['name']) ?></h3>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="<?= BASE_URL ?>public/index.php?page=admin/edit_video_category&id=<?= $cat['id'] ?>" class="text-gray-400 hover:text-primary p-2">
                                <span class="material-icons-round">edit</span>
                            </a>
                            <a href="<?= BASE_URL ?>public/index.php?page=admin/delete_video_category&id=<?= $cat['id'] ?>" onclick="return confirm('Hapus kategori ini? Semua video didalamnya juga akan terhapus!')" class="text-gray-400 hover:text-red-500 p-2">
                                <span class="material-icons-round">delete</span>
                            </a>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            
            <?php if (empty($categories)): ?>
                <li class="px-4 py-8 text-center text-gray-500">
                    Belum ada kategori.
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
