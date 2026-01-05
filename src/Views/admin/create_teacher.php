<?php
// src/Views/admin/create_teacher.php
require_once __DIR__ . '/../../Helpers/functions.php';
include __DIR__ . '/../layouts/admin.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 text-primary">
            <span class="material-icons-round text-2xl">person_add</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tambah Data Guru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Masukan informasi detail guru baru</p>
        </div>
    </div>
    
    <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
        <span class="material-icons-round text-lg mr-2">arrow_back</span>
        Kembali
    </a>
</div>

<div class="max-w-2xl bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <form method="POST" action="<?= BASE_URL ?>public/index.php?page=create_teacher">
        <?= csrfInput() ?>
        
        <div class="space-y-6">
            <!-- Name Input -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-icons-round text-slate-400 text-xl">badge</span>
                    </span>
                    <input type="text" name="name" class="block w-full pl-10 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-primary focus:border-primary shadow-sm" placeholder="Contoh: Ahmad S.Pd" required>
                </div>
            </div>

            <!-- Email Input -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-icons-round text-slate-400 text-xl">email</span>
                    </span>
                    <input type="email" name="email" class="block w-full pl-10 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-primary focus:border-primary shadow-sm" placeholder="email@contoh.com" required>
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-icons-round text-slate-400 text-xl">lock</span>
                    </span>
                    <input type="password" name="password" class="block w-full pl-10 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-primary focus:border-primary shadow-sm" placeholder="Minimal 6 karakter" required minlength="6">
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Password default untuk guru baru.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-card-dark border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
                Batal
            </a>
            <button type="submit" class="flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors shadow-sm">
                <span class="material-icons-round text-lg mr-2">save</span>
                Simpan Data
            </button>
        </div>
    </form>
</div>
