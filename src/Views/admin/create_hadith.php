<?php
// src/Views/admin/create_hadith.php
require_once __DIR__ . '/../../Helpers/functions.php';

include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">post_add</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Hadits Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Add new hadith to the collection</p>
        </div>
    </div>
    <a href="?page=admin/manage_hadiths" class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 text-sm font-medium transition-all shadow-sm hover:shadow decoration-0">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Formulir Hadits</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="<?= BASE_URL ?>public/index.php?page=admin/save_hadith" class="space-y-6">
                <?= csrfInput() ?>

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Title (Judul) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required 
                           class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                           placeholder="Contoh: Hadits Tentang Niat">
                </div>

                <div>
                    <label for="arabic_text" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Arabic Text (Teks Arab) <span class="text-red-500">*</span>
                    </label>
                    <textarea id="arabic_text" name="arabic_text" rows="4" required dir="rtl"
                              class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-amiri text-lg"
                              placeholder="Masukkan teks Arab di sini..."></textarea>
                </div>

                <div>
                    <label for="translation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Translation (Terjemahan)
                    </label>
                    <textarea id="translation" name="translation" rows="4"
                              class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                              placeholder="Masukkan terjemahan bahasa Indonesia..."></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-700 mt-6">
                    <a href="?page=admin/manage_hadiths" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors decoration-0">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <span class="material-icons-round text-lg mr-2">save</span>
                        Simpan Hadits
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
