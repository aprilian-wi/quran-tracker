<?php
// src/Views/layouts/pwa.php
require_once __DIR__ . '/../../Helpers/functions.php';
requireLogin();

$flash = getFlash();
$user = currentUser();
$schoolName = isset($_SESSION['school_name']) ? $_SESSION['school_name'] : 'SDIT Baitusalam';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="theme-color" content="#15803d">
    <title><?= h($schoolName) ?> - Quran Tracker</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= BASE_URL ?>public/manifest.json">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>public/assets/logo_pwa.png">


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Poppins:wght@500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#15803d", // Green-700
                        "primary-dark": "#14532d", // Green-900
                        "background-light": "#f3f4f6", // Gray-100
                        "background-dark": "#121212", // Very dark gray
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e1e1e",
                        "text-main-light": "#1f2937", // Gray-800
                        "text-main-dark": "#f3f4f6", // Gray-100
                        "text-sub-light": "#6b7280", // Gray-500
                        "text-sub-dark": "#9ca3af", // Gray-400
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                        body: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        'xl': '0.75rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.1)',
                        'nav': '0 -4px 25px -5px rgba(0, 0, 0, 0.1)',
                    }
                },
            },
        };
    </script>
    <style>
        body {
            min-height: max(884px, 100dvh);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Hide scrollbar for clean UI */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-body text-text-main-light dark:text-text-main-dark transition-colors duration-300 min-h-screen flex flex-col relative">

    <!-- Header -->
    <header class="bg-primary shadow-md sticky top-0 z-50">
        <div class="max-w-md mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex flex-col">
                <h1 class="font-display font-bold text-white text-lg tracking-wide"><?= h($schoolName) ?></h1>
                <span class="text-green-100 text-xs font-medium">Quran Tracker App</span>
            </div>
            <div class="flex items-center space-x-1">
                <button id="pwa-install-btn"
                    class="hidden text-white hover:bg-white/20 p-2.5 rounded-full transition-colors flex items-center justify-center mr-1"
                    aria-label="Install App">
                    <span class="material-icons-round text-xl">download</span>
                </button>
                <a href="<?= BASE_URL ?>public/index.php?page=notifications/index&mode=pwa"
                    class="text-white hover:bg-white/20 p-2.5 rounded-full transition-colors flex items-center justify-center relative">
                    <span class="material-icons-round">notifications</span>
                    <!-- Notification Dot -->
                    <span id="notif-badge"
                        class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-primary scale-0 transition-transform duration-300"></span>
                </a>
                <a href="<?= BASE_URL ?>public/index.php?page=logout"
                    class="text-white hover:bg-white/20 p-2.5 rounded-full transition-colors flex items-center justify-center">
                    <span class="material-icons-round">logout</span>
                </a>
            </div>
        </div>
    </header>

    <script>
        // PWA Install Prompt Logic
        let deferredPrompt;
        const installBtn = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            installBtn.classList.remove('hidden');
        });

        installBtn.addEventListener('click', (e) => {
            // Hide our user interface that shows our A2HS button
            installBtn.classList.add('hidden');
            // Show the prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    // console.log('User accepted the A2HS prompt');
                } else {
                    // console.log('User dismissed the A2HS prompt');
                }
                deferredPrompt = null;
            });
        });
    </script>

    <!-- Main Content -->
    <main class="flex-grow max-w-md mx-auto w-full px-4 py-6 space-y-8 pb-28">
        <?php if ($flash): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded shadow-sm" role="alert">
                <p><?= h($flash['message']) ?></p>
            </div>
        <?php endif; ?>

        <?php
        // Content injected by view files
        ?>
        <?php
        // Defer footer/nav to end
        function __render_pwa_footer()
        {
            ?>
        </main>

        <!-- Bottom Navigation -->
        <nav
            class="fixed bottom-0 left-0 right-0 bg-surface-light dark:bg-surface-dark shadow-nav z-50 pb-safe border-t border-gray-200 dark:border-gray-800">
            <div class="max-w-md mx-auto px-6 h-16 flex items-center justify-between">
                <!-- Home (Dynamic) -->
                <?php
                $homeLink = 'parent/my_children';
                if (hasRole('teacher')) {
                    $homeLink = 'dashboard';
                }
                $isHome = ($_GET['page'] ?? '') == $homeLink;
                ?>
                <a class="flex flex-col items-center justify-center <?= $isHome ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors"
                    href="<?= BASE_URL ?>public/index.php?page=<?= $homeLink ?>&mode=pwa">
                    <span class="material-icons-round text-2xl group-active:scale-90 transition-transform">home</span>
                    <span class="text-[10px] font-medium tracking-wide">Home</span>
                </a>

                <!-- Pustaka Menu Trigger -->
                <?php
                $isPustaka = strpos($_GET['page'] ?? '', 'quran') !== false ||
                    strpos($_GET['page'] ?? '', 'short_prayers') !== false ||
                    strpos($_GET['page'] ?? '', 'hadiths') !== false;
                ?>
                <button x-data x-on:click="$dispatch('open-pustaka-menu')"
                    class="flex flex-col items-center justify-center <?= $isPustaka ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors">
                    <span
                        class="material-icons-round text-2xl group-active:scale-90 transition-transform">auto_stories</span>
                    <span class="text-[10px] font-medium tracking-wide">Pustaka</span>
                </button>

                <!-- Feed -->
                <a class="flex flex-col items-center justify-center <?= strpos($_GET['page'] ?? '', 'feed') !== false ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors"
                    href="<?= BASE_URL ?>public/index.php?page=feed/index&mode=pwa">
                    <span
                        class="material-icons-round text-2xl group-active:scale-90 transition-transform">dynamic_feed</span>
                    <span class="text-[10px] font-medium tracking-wide">Feed</span>
                </a>

                <!-- Video -->
                <a class="flex flex-col items-center justify-center <?= strpos($_GET['page'] ?? '', 'videos') !== false ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors"
                    href="<?= BASE_URL ?>public/index.php?page=videos/index&mode=pwa">
                    <span
                        class="material-icons-round text-2xl group-active:scale-90 transition-transform">play_circle</span>
                    <span class="text-[10px] font-medium tracking-wide">Video</span>
                </a>
            </div>
        </nav>

        <!-- Pustaka Bottom Sheet (Alpine.js) -->
        <div x-data="{ open: false }" x-show="open" x-on:open-pustaka-menu.window="open = true"
            x-on:keydown.escape.window="open = false" style="display: none;"
            class="fixed inset-0 z-[60] flex items-end justify-center pointer-events-none">

            <!-- Backdrop -->
            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm pointer-events-auto">
            </div>

            <!-- Bottom Sheet -->
            <div x-show="open" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="bg-surface-light dark:bg-surface-dark w-full max-w-md rounded-t-2xl shadow-card pointer-events-auto pb-safe relative">

                <!-- Handle -->
                <div class="flex justify-center pt-3 pb-1" @click="open = false">
                    <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                </div>

                <!-- Header -->
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center space-x-3">
                        <span
                            class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-primary dark:text-green-400">
                            <span class="material-icons-round">auto_stories</span>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-text-main-light dark:text-text-main-dark">Pustaka Islami</h3>
                            <p class="text-xs text-text-sub-light dark:text-text-sub-dark">Sumber bacaan & amalan harian</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Grid -->
                <div class="grid grid-cols-3 gap-4 p-6">
                    <!-- Quran -->
                    <a href="<?= BASE_URL ?>public/index.php?page=quran/surah_list&mode=pwa"
                        class="flex flex-col items-center space-y-2 group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 dark:from-emerald-900/40 dark:to-teal-900/20 flex items-center justify-center shadow-sm group-active:scale-95 transition-transform border border-emerald-100 dark:border-emerald-900/50">
                            <span
                                class="material-icons-round text-3xl text-emerald-600 dark:text-emerald-400">menu_book</span>
                        </div>
                        <span
                            class="text-xs font-medium text-text-main-light dark:text-text-main-dark text-center">Al-Quran</span>
                    </a>

                    <!-- Doa -->
                    <a href="<?= BASE_URL ?>public/index.php?page=shared/list_short_prayers&mode=pwa"
                        class="flex flex-col items-center space-y-2 group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-50 dark:from-blue-900/40 dark:to-indigo-900/20 flex items-center justify-center shadow-sm group-active:scale-95 transition-transform border border-blue-100 dark:border-blue-900/50">
                            <span
                                class="material-icons-round text-3xl text-blue-600 dark:text-blue-400">volunteer_activism</span>
                        </div>
                        <span
                            class="text-xs font-medium text-text-main-light dark:text-text-main-dark text-center">Doa-Doa</span>
                    </a>

                    <!-- Hadits -->
                    <a href="<?= BASE_URL ?>public/index.php?page=shared/list_hadiths&mode=pwa"
                        class="flex flex-col items-center space-y-2 group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 dark:from-amber-900/40 dark:to-orange-900/20 flex items-center justify-center shadow-sm group-active:scale-95 transition-transform border border-amber-100 dark:border-amber-900/50">
                            <span
                                class="material-icons-round text-3xl text-amber-600 dark:text-amber-400">format_quote</span>
                        </div>
                        <span
                            class="text-xs font-medium text-text-main-light dark:text-text-main-dark text-center">Hadits</span>
                    </a>
                </div>

                <!-- Close Button (Mobile Friendly adjustment for spacing) -->
                <div class="h-4"></div>
            </div>
        </div>

        </script>
        <script>
            // System theme check
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }

            // Register Service Worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('<?= BASE_URL ?>public/sw.js')
                        .then(registration => {
                            // console.log('SW Registered: ', registration);
                        })
                        .catch(err => {
                            // console.log('SW Registration Failed: ', err);
                        });
                });
            }

            // Request Notification Permission
            function requestNotificationPermission() {
                if ('Notification' in window && Notification.permission !== 'granted') {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            // console.log('Notification permission granted.');
                        }
                    });
                }
            }

            // Previous count to detect NEW notifications
            let previousCount = 0;

            // Notification Polling with Local Notification Trigger
            function checkNotifications() {
                fetch('<?= BASE_URL ?>public/index.php?page=api/get_unread_count', { credentials: 'include' })
                    .then(r => r.json())
                    .then(data => {
                        const badge = document.getElementById('notif-badge');

                        // Update Badge
                        if (data.count > 0) {
                            badge.classList.remove('scale-0');
                            badge.classList.add('scale-100');

                            // Trigger Local Notification if count increased
                            if (data.count > previousCount && 'Notification' in window && Notification.permission === 'granted') {
                                // Create system notification
                                new Notification('Quran Tracker', {
                                    body: `Anda memiliki ${data.count} notifikasi baru.`,
                                    icon: '/icon-192.png', // Ensure this exists or use placeholder
                                    tag: 'new-update' // Prevents spamming stack
                                });
                            }
                        } else {
                            badge.classList.remove('scale-100');
                            badge.classList.add('scale-0');
                        }

                        previousCount = data.count; // Sync count
                    })
                    .catch(console.error);
            }

            // Check on load and every 60s
            document.addEventListener('DOMContentLoaded', () => {
                requestNotificationPermission();
                checkNotifications();
                setInterval(checkNotifications, 60000);
            });
        </script>
    </body>

    </html>
    <?php
        }
        register_shutdown_function('__render_pwa_footer');
        ?>