<?php
// src/Views/dashboard/school_admin.php
$pageTitle = 'Dashboard School Admin';
require_once __DIR__ . '/../../Controllers/DashboardController.php';

$controller = new DashboardController($pdo);
$data = $controller->index(); 

include __DIR__ . '/../layouts/admin.php';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Sekolah</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400">Selamat Datang, <?= h($_SESSION['user_name']) ?></p>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Siswa -->
    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Siswa</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white"><?= $data['total_children'] ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <span class="material-icons-round text-2xl">child_care</span>
        </div>
    </div>

    <!-- Total Guru -->
    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Guru</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white"><?= $data['total_teachers'] ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400">
            <span class="material-icons-round text-2xl">supervisor_account</span>
        </div>
    </div>

    <!-- Total Kelas -->
    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Kelas</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white"><?= $data['total_classes'] ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
            <span class="material-icons-round text-2xl">school</span>
        </div>
    </div>

    <!-- Total Orang Tua -->
    <div class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total Orang Tua</p>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white"><?= $data['total_parents'] ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
            <span class="material-icons-round text-2xl">family_restroom</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Administration Section -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="material-icons-round text-slate-400">admin_panel_settings</span>
            Administrasi
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="index.php?page=admin/users" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-emerald-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 transition-colors">
                    <span class="material-icons-round text-slate-500 dark:text-slate-400 group-hover:text-emerald-500">manage_accounts</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Pengguna</span>
            </a>

            <a href="index.php?page=admin/classes" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-emerald-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 transition-colors">
                    <span class="material-icons-round text-slate-500 dark:text-slate-400 group-hover:text-emerald-500">class</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Kelas</span>
            </a>

            <a href="index.php?page=admin/teachers" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-emerald-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 transition-colors">
                    <span class="material-icons-round text-slate-500 dark:text-slate-400 group-hover:text-emerald-500">co_present</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Guru</span>
            </a>

            <a href="index.php?page=admin/parents" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-emerald-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 transition-colors">
                    <span class="material-icons-round text-slate-500 dark:text-slate-400 group-hover:text-emerald-500">escalator_warning</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Orang Tua</span>
            </a>

            <a href="index.php?page=admin/list_children" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-emerald-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/20 transition-colors">
                    <span class="material-icons-round text-slate-500 dark:text-slate-400 group-hover:text-emerald-500">face</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Siswa</span>
            </a>
        </div>
    </div>

    <!-- Content Management Section -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="material-icons-round text-slate-400">library_books</span>
            Manajemen Materi
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="index.php?page=admin/teaching_books" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-purple-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                    <span class="material-icons-round text-purple-500 dark:text-purple-400">menu_book</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">Buku Tahsin</span>
            </a>

            <a href="index.php?page=admin/manage_short_prayers" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-teal-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center group-hover:bg-teal-100 dark:group-hover:bg-teal-900/40 transition-colors">
                    <span class="material-icons-round text-teal-500 dark:text-teal-400">auto_stories</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400">Doa Pendek</span>
            </a>

            <a href="index.php?page=admin/manage_hadiths" class="bg-white dark:bg-card-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center justify-center gap-3 hover:shadow-md hover:border-orange-500 transition-all group text-center h-full">
                <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center group-hover:bg-orange-100 dark:group-hover:bg-orange-900/40 transition-colors">
                    <span class="material-icons-round text-orange-500 dark:text-orange-400">format_quote</span>
                </div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-orange-600 dark:group-hover:text-orange-400">Hadits</span>
            </a>
        </div>
    </div>
</div>
