<?php
// src/Views/layouts/admin.php
require_once __DIR__ . '/../../Helpers/functions.php';
requireLogin();

$user = currentUser();
$schoolName = isset($_SESSION['school_name']) ? $_SESSION['school_name'] : 'SDIT Baitusalam';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="<?= h($_SESSION['csrf_token']) ?>">
    <title>Quran Tracker</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>public/assets/favicon.png" type="image/x-icon">
    <link rel="manifest" href="<?= BASE_URL ?>public/manifest.json">
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#15803d", // Green-700
                        "primary-dark": "#14532d", // Green-900
                        primaryDark: "#14532d", // Alias
                        "background-light": "#f3f4f6", // Gray-100
                        "background-dark": "#121212", // Very dark gray
                        "card-light": "#ffffff",
                        "card-dark": "#1e1e1e", // Surface dark
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e1e1e",
                        "text-main-light": "#1f2937", // Gray-800
                        "text-main-dark": "#f3f4f6", // Gray-100
                        "text-sub-light": "#6b7280", // Gray-500
                        "text-sub-dark": "#9ca3af", // Gray-400
                    },
                    fontFamily: {
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                        body: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 min-h-screen flex flex-col">
    <nav class="bg-primary shadow-lg dark:bg-primary-dark sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <a href="<?= BASE_URL ?>public/index.php?page=dashboard"
                        class="flex items-center gap-3 decoration-0 no-underline">
                        <div
                            class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center text-white backdrop-blur-sm">
                            <span class="material-icons-round text-xl">mosque</span>
                        </div>
                        <span
                            class="text-white font-bold text-lg tracking-wide hover:text-white/90 transition-colors"><?= h($schoolName) ?></span>
                    </a>
                </div>

                <!-- Navbar Menu for Teacher/Parent -->
                <?php if (hasRole('parent') || hasRole('teacher')): ?>
                    <div class="hidden md:flex items-center gap-1 ml-6 mr-auto">
                        <!-- Al-Qur'an Dropdown -->
                        <div class="relative group">
                            <button
                                class="flex items-center gap-1 text-white/90 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm font-medium focus:outline-none"
                                onclick="document.getElementById('quran-menu').classList.toggle('hidden')">
                                Al-Qur'an
                                <span class="material-icons-round text-lg">expand_more</span>
                            </button>
                            <div id="quran-menu"
                                class="hidden absolute left-0 mt-1 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <a href="<?= BASE_URL ?>public/index.php?page=quran/surah_list"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Daftar
                                    Surah</a>
                                <a href="<?= BASE_URL ?>public/index.php?page=quran/bookmarks"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Penanda</a>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>public/index.php?page=shared/list_short_prayers"
                            class="text-white/90 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm font-medium decoration-0">Doa-doa
                            Pendek</a>
                        <a href="<?= BASE_URL ?>public/index.php?page=shared/list_hadiths"
                            class="text-white/90 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm font-medium decoration-0">Hadits</a>
                    </div>
                <?php endif; ?>

                <div class="flex items-center gap-4">
                    <button
                        class="p-2 rounded-full text-white/80 hover:bg-white/10 hover:text-white transition-colors focus:outline-none"
                        onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-icons-round dark:hidden">dark_mode</span>
                        <span class="material-icons-round hidden dark:block">light_mode</span>
                    </button>

                    <div class="relative ml-3">
                        <div class="flex items-center gap-2 text-white/90 hover:text-white cursor-pointer px-3 py-1.5 rounded-lg hover:bg-white/10 transition-colors"
                            onclick="document.getElementById('user-menu').classList.toggle('hidden')">
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-200 text-emerald-800 flex items-center justify-center font-bold text-sm">
                                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <span class="font-medium text-sm hidden sm:block"><?= h($user['name'] ?? 'User') ?></span>
                            <span class="material-icons-round text-sm">expand_more</span>
                        </div>
                        <!-- Dropdown -->
                        <div id="user-menu"
                            class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <a href="<?= BASE_URL ?>public/index.php?page=logout"
                                class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                                Keluar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <?php
        function __render_admin_footer()
        {
            ?>
        </main>
        <footer class="mt-auto py-6 text-center border-t border-slate-200 dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-500">
                © <?= date('Y') ?> Quran Tracker. All rights reserved.
            </p>
        </footer>

        <script>
            // Close dropdown when clicking outside
            // Close dropdowns when clicking outside
            window.addEventListener('click', function (e) {
                // User Menu
                const userMenu = document.getElementById('user-menu');
                const userButton = userMenu.previousElementSibling; // Assuming button is immediately before
                if (!userMenu.contains(e.target) && !userButton.contains(e.target) && !userMenu.classList.contains('hidden')) {
                    userMenu.classList.add('hidden');
                }

                // Quran Menu (if exists)
                const quranMenu = document.getElementById('quran-menu');
                if (quranMenu) {
                    const quranButton = quranMenu.previousElementSibling;
                    if (!quranMenu.contains(e.target) && !quranButton.contains(e.target) && !quranMenu.classList.contains('hidden')) {
                        quranMenu.classList.add('hidden');
                    }
                }
            });
        </script>

    </body>

    </html>
    <?php
        }
        register_shutdown_function('__render_admin_footer');
        ?>