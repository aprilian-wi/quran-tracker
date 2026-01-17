<?php
// src/Views/dashboard/superadmin.php
require_once __DIR__ . '/../../Controllers/DashboardController.php';
require_once __DIR__ . '/../../Helpers/functions.php';

requireLayer('admin');

$controller = new DashboardController($pdo);
$data = $controller->index();
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Dashboard
                Superadmin</h2>
            <p class="mt-1 text-sm text-gray-500">Selamat datang kembali, kelola aplikasi Quran Tracker dari sini.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Schools -->
        <div
            class="bg-white overflow-hidden shadow rounded-lg px-4 py-5 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Sekolah</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900"><?= $data['total_schools'] ?></dd>
            <div class="mt-2">
                <span class="text-teal-600 text-sm font-medium">Registered Tenants</span>
            </div>
        </div>
        <!-- Teachers -->
        <div
            class="bg-white overflow-hidden shadow rounded-lg px-4 py-5 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Guru</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900"><?= $data['total_teachers'] ?></dd>
            <div class="mt-2">
                <span class="text-primary text-sm font-medium">Active Teachers</span>
            </div>
        </div>
        <!-- Parents -->
        <div
            class="bg-white overflow-hidden shadow rounded-lg px-4 py-5 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Wali Siswa</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900"><?= $data['total_parents'] ?></dd>
            <div class="mt-2">
                <span class="text-blue-600 text-sm font-medium">Registered Parents</span>
            </div>
        </div>
        <!-- Children -->
        <div
            class="bg-white overflow-hidden shadow rounded-lg px-4 py-5 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Siswa</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900"><?= $data['total_children'] ?></dd>
            <div class="mt-2">
                <span class="text-purple-600 text-sm font-medium">Learning Quran</span>
            </div>
        </div>
        <!-- Classes -->
        <div
            class="bg-white overflow-hidden shadow rounded-lg px-4 py-5 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Kelas</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900"><?= $data['total_classes'] ?></dd>
            <div class="mt-2">
                <span class="text-orange-600 text-sm font-medium">Active Classes</span>
            </div>
        </div>
    </div>

    <!-- Management Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- School Management -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-2">
                    <span class="material-icons-round text-blue-600">domain</span>
                    <h3 class="text-lg font-medium text-gray-900">Manajemen Sekolah</h3>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/schools"
                    class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                    <div
                        class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 group-hover:bg-white group-hover:text-blue-700 transition-colors">
                        <span class="material-icons-round">school</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">Data Sekolah</div>
                        <div class="text-xs text-gray-500">Kelola daftar sekolah</div>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>public/index.php?page=admin/create_school"
                    class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-200 transition-colors group">
                    <div
                        class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 group-hover:bg-white group-hover:text-blue-700 transition-colors">
                        <span class="material-icons-round">add_business</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">Tambah Sekolah</div>
                        <div class="text-xs text-gray-500">Registrasi sekolah baru</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Video Education (New) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-2">
                    <span class="material-icons-round text-red-600">play_circle_filled</span>
                    <h3 class="text-lg font-medium text-gray-900">Video Edukasi</h3>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="<?= BASE_URL ?>public/index.php?page=admin/videos"
                    class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 transition-colors group">
                    <div
                        class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 group-hover:bg-white group-hover:text-red-700 transition-colors">
                        <span class="material-icons-round">movie</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">Kelola Video</div>
                        <div class="text-xs text-gray-500">Daftar video pembelajaran</div>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>public/index.php?page=admin/video_categories"
                    class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-red-50 hover:border-red-200 transition-colors group">
                    <div
                        class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 group-hover:bg-white group-hover:text-red-700 transition-colors">
                        <span class="material-icons-round">category</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">Kategori Video</div>
                        <div class="text-xs text-gray-500">Atur kategori video</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- User Management -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <span class="material-icons-round text-emerald-600">people</span>
                <h3 class="text-lg font-medium text-gray-900">Manajemen User</h3>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <a href="<?= BASE_URL ?>public/index.php?page=admin/teachers"
                class="flex items-center p-4 rounded-xl border border-gray-200 hover:bg-emerald-50 hover:border-emerald-200 transition-all group shadow-sm hover:shadow-md">
                <div
                    class="flex-shrink-0 h-12 w-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-white group-hover:text-emerald-700 transition-colors">
                    <span class="material-icons-round text-2xl">supervisor_account</span>
                </div>
                <div class="ml-4">
                    <div class="text-base font-semibold text-gray-900">Guru</div>
                    <div class="text-sm text-gray-500">Kelola data guru</div>
                </div>
            </a>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/parents"
                class="flex items-center p-4 rounded-xl border border-gray-200 hover:bg-emerald-50 hover:border-emerald-200 transition-all group shadow-sm hover:shadow-md">
                <div
                    class="flex-shrink-0 h-12 w-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-white group-hover:text-emerald-700 transition-colors">
                    <span class="material-icons-round text-2xl">family_restroom</span>
                </div>
                <div class="ml-4">
                    <div class="text-base font-semibold text-gray-900">Wali Siswa</div>
                    <div class="text-sm text-gray-500">Kelola data wali</div>
                </div>
            </a>
            <a href="<?= BASE_URL ?>public/index.php?page=admin/list_children"
                class="flex items-center p-4 rounded-xl border border-gray-200 hover:bg-emerald-50 hover:border-emerald-200 transition-all group shadow-sm hover:shadow-md">
                <div
                    class="flex-shrink-0 h-12 w-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-white group-hover:text-emerald-700 transition-colors">
                    <span class="material-icons-round text-2xl">face</span>
                </div>
                <div class="ml-4">
                    <div class="text-base font-semibold text-gray-900">Siswa</div>
                    <div class="text-sm text-gray-500">Data seluruh siswa</div>
                </div>
            </a>
        </div>
    </div>
</div>