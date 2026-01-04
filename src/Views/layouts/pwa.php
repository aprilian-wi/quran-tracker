<?php
// src/Views/layouts/pwa.php
require_once __DIR__ . '/../../Helpers/functions.php';
requireLogin();

$flash = getFlash();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#15803d">
    <title>SDIT Baitusalam - Quran Tracker</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Poppins:wght@500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
<body class="bg-background-light dark:bg-background-dark font-body text-text-main-light dark:text-text-main-dark transition-colors duration-300 min-h-screen flex flex-col relative">

<!-- Header -->
<header class="bg-primary shadow-md sticky top-0 z-50">
    <div class="max-w-md mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex flex-col">
            <h1 class="font-display font-bold text-white text-lg tracking-wide">SDIT Baitusalam</h1>
            <span class="text-green-100 text-xs font-medium">Quran Tracker App</span>
        </div>
        <div class="flex items-center space-x-1">
            <button class="text-white hover:bg-white/20 p-2.5 rounded-full transition-colors flex items-center justify-center relative">
                <span class="material-icons-round">notifications</span>
                <!-- Notification Dot (Dynamic logic needed later) -->
                <!-- <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-400 rounded-full ring-2 ring-primary"></span> -->
            </button>
            <a href="<?= BASE_URL ?>public/index.php?page=logout" class="text-white hover:bg-white/20 p-2.5 rounded-full transition-colors flex items-center justify-center">
                <span class="material-icons-round">logout</span>
            </a>
        </div>
    </div>
</header>

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
function __render_pwa_footer() {
?>
</main>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-surface-light dark:bg-surface-dark shadow-nav z-50 pb-safe border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-md mx-auto px-6 h-16 flex items-center justify-between">
        <!-- Home (Dynamic) -->
        <?php
        $homeLink = 'parent/my_children';
        if (hasRole('teacher')) {
            $homeLink = 'teacher/class_students';
        }
        $isHome = ($_GET['page'] ?? '') == $homeLink;
        ?>
        <a class="flex flex-col items-center justify-center <?= $isHome ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors" href="<?= BASE_URL ?>public/index.php?page=<?= $homeLink ?>&mode=pwa">
            <span class="material-icons-round text-2xl group-active:scale-90 transition-transform">home</span>
            <span class="text-[10px] font-medium tracking-wide">Home</span>
        </a>
        
        <!-- Quran -->
        <a class="flex flex-col items-center justify-center <?= strpos($_GET['page'] ?? '', 'quran') !== false ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors" href="<?= BASE_URL ?>public/index.php?page=quran/surah_list&mode=pwa">
            <span class="material-icons-round text-2xl group-active:scale-90 transition-transform">menu_book</span>
            <span class="text-[10px] font-medium tracking-wide">Quran</span>
        </a>
        
        <!-- Doa -->
        <a class="flex flex-col items-center justify-center <?= strpos($_GET['page'] ?? '', 'short_prayers') !== false ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors" href="<?= BASE_URL ?>public/index.php?page=shared/list_short_prayers&mode=pwa">
            <span class="material-icons-round text-2xl group-active:scale-90 transition-transform">volunteer_activism</span>
            <span class="text-[10px] font-medium tracking-wide">Doa</span>
        </a>
        
        <!-- Hadits -->
        <a class="flex flex-col items-center justify-center <?= strpos($_GET['page'] ?? '', 'hadiths') !== false ? 'text-primary dark:text-green-400' : 'text-text-sub-light dark:text-text-sub-dark hover:text-primary dark:hover:text-green-400' ?> space-y-1 w-16 group transition-colors" href="<?= BASE_URL ?>public/index.php?page=shared/list_hadiths&mode=pwa">
            <span class="material-icons-round text-2xl group-active:scale-90 transition-transform">format_quote</span>
            <span class="text-[10px] font-medium tracking-wide">Hadits</span>
        </a>
    </div>
</nav>

<script>
    // Check system preference on load
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
    }
</script>
</body>
</html>
<?php
}
register_shutdown_function('__render_pwa_footer');
?>
