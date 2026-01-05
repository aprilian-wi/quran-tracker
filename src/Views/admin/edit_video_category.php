<?php
// src/Views/admin/edit_video_category.php
requireLayer('admin');
require_once '../src/Models/VideoCategory.php';

$id = $_GET['id'] ?? 0;
$categoryModel = new VideoCategory($pdo);
$category = $categoryModel->find($id);

if (!$category) {
    setFlash('danger', 'Kategori tidak ditemukan.');
    redirect('admin/video_categories');
}
?>

<div class="max-w-md mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="<?= BASE_URL ?>public/index.php?page=admin/video_categories" class="mr-4 text-gray-500 hover:text-gray-700">
            <span class="material-icons-round text-2xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Kategori</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="<?= BASE_URL ?>public/index.php?page=admin/update_video_category" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input type="text" name="name" id="name" required value="<?= h($category['name']) ?>"
                       class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Material Icons Name)</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input type="text" name="icon" id="icon" required value="<?= h($category['icon']) ?>"
                           onkeyup="document.getElementById('icon-preview').innerText = this.value"
                           class="flex-1 focus:ring-primary focus:border-primary block w-full min-w-0 rounded-none rounded-l-md sm:text-sm border-gray-300">
                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        <span class="material-icons-round" id="icon-preview"><?= h($category['icon']) ?></span>
                    </span>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
