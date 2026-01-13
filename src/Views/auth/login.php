<?php
// src/Views/auth/login.php
if (isLoggedIn()) {
    redirect('dashboard');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10b981">
    <title>Masuk | Quran Tracker</title>
    <link rel="manifest" href="<?= BASE_URL ?>public/manifest.json">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/assets/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#10b981', // emerald-500
                        primaryDark: '#059669', // emerald-600
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100 slide-up">
        <!-- Header -->
        <div class="bg-emerald-600 p-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-emerald-700 opacity-90"></div>
            <div class="relative z-10">
                <div
                    class="bg-white/20 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-inner ring-4 ring-white/10">
                    <img src="<?= BASE_URL ?>public/assets/logo_quran_tracker.png" alt="Logo"
                        class="w-20 h-20 object-contain drop-shadow-md">
                </div>
                <h1 class="text-2xl font-bold text-white mb-1 tracking-tight">Quran Tracker</h1>
                <p class="text-emerald-100 text-sm font-medium">Menjaga Cahaya Al-Qur'an di Hati Si Kecil</p>
            </div>

            <!-- Decorative circles -->
            <div class="absolute -top-12 -left-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Body -->
        <div class="p-8">
            <?php
            $flash = getFlash();
            if ($flash):
                $alertColor = $flash['type'] === 'danger' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                $icon = $flash['type'] === 'danger' ? 'error_outline' : 'check_circle';
                ?>
                <div class="mb-6 p-4 rounded-xl border <?= $alertColor ?> flex items-start gap-3 animate-fade-in">
                    <span class="material-icons-round text-xl shrink-0"><?= $icon ?></span>
                    <span class="text-sm font-medium"><?= h($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <?php
            // Preserve 'mode' parameter for PWA
            $mode = $_GET['mode'] ?? '';
            $actionUrl = BASE_URL . "public/index.php?page=login" . ($mode ? "&mode=" . h($mode) : "");
            ?>
            <form method="POST" action="<?= $actionUrl ?>" class="space-y-6">
                <?= csrfInput() ?>
                <?php if ($mode): ?>
                    <input type="hidden" name="mode" value="<?= h($mode) ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">No. HP <span
                            class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                            <span class="material-icons-round text-xl">smartphone</span>
                        </span>
                        <input type="tel" name="phone" required autofocus
                            class="block w-full rounded-xl border-slate-300 pl-10 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-3 transition-all hover:border-slate-400 shadow-sm"
                            placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">Kata Sandi <span
                            class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                            <span class="material-icons-round text-xl">lock</span>
                        </span>
                        <input type="password" name="password" required
                            class="block w-full rounded-xl border-slate-300 pl-10 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm py-3 transition-all hover:border-slate-400 shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-emerald-200 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all hover:-translate-y-0.5 active:translate-y-0 active:shadow-md">
                        Masuk Aplikasi
                        <span class="material-icons-round ml-2 text-lg">arrow_forward</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center">
                <p class="text-xs text-slate-400 font-medium tracking-wide">© <?= date('Y') ?> Quran Tracker. All rights
                    reserved.</p>
            </div>
        </div>
    </div>

    <style>
        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <!-- Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= BASE_URL ?>public/service-worker.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(error => {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }

        // PWA Detection & Form Injection
        document.addEventListener('DOMContentLoaded', () => {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            const urlParams = new URLSearchParams(window.location.search);
            const hasModePwa = urlParams.get('mode') === 'pwa';

            if (isStandalone || hasModePwa) {
                // Ensure hidden input exists and set value
                let modeInput = document.querySelector('input[name="mode"]');
                if (!modeInput) {
                    modeInput = document.createElement('input');
                    modeInput.type = 'hidden';
                    modeInput.name = 'mode';
                    document.querySelector('form').appendChild(modeInput);
                }
                modeInput.value = 'pwa';

                // Append to Action URL if missing
                const form = document.querySelector('form');
                const action = new URL(form.action);
                if (!action.searchParams.has('mode') || action.searchParams.get('mode') !== 'pwa') {
                    action.searchParams.set('mode', 'pwa');
                    form.action = action.toString();
                }
            }
        });
    </script>
</body>

</html>