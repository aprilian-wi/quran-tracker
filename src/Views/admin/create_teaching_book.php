<?php
// src/Views/admin/create_teaching_book.php
global $pdo;
require_once __DIR__ . '/../../Controllers/AdminController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

$controller = new AdminController($pdo);

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">menu_book</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Buku Ajar Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Add new teaching book volume</p>
        </div>
    </div>
    <a href="?page=admin/teaching_books" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Formulir Buku Ajar</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="?page=admin/store_teaching_book" class="space-y-6">
                <?= csrfInput() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Volume Number (Jilid) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="volume_number" min="1" required 
                               class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="e.g. 1">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Nomor jilid buku (1, 2, 3, dst)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Total Halaman <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_pages" min="1" required 
                               class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               placeholder="e.g. 40">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total halaman dalam jilid ini</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Judul Buku <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required 
                           class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Jilid 1">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Judul deskriptif untuk buku ajar</p>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-700 mt-6">
                    <a href="?page=admin/teaching_books" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors decoration-0">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <span class="material-icons-round text-lg mr-2">save</span>
                        Simpan Buku
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
