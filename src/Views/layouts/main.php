<?php
// src/Views/layouts/main.php
require_once __DIR__ . '/../../Helpers/functions.php';
requireLogin();

$flash = getFlash();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= h($_SESSION['csrf_token']) ?>">
    <title>Quran Tracker</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Amiri and Tajawal (Arabic-friendly) -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>public/index.php?page=dashboard">
            
            <?php
            if (!hasRole('superadmin') && isset($_SESSION['school_id'])) {
                if (isset($_SESSION['school_name'])) {
                    echo h($_SESSION['school_name']);
                } else {
                    // Fallback: Fetch school name if not in session yet
                    global $pdo;
                    $stmt = $pdo->prepare("SELECT name FROM schools WHERE id = ?");
                    $stmt->execute([$_SESSION['school_id']]);
                    $school = $stmt->fetch();
                    $sName = $school ? $school['name'] : 'Quran Tracker';
                    $_SESSION['school_name'] = $sName; // Cache it
                    echo h($sName);
                }
            } else {
                echo 'Quran Tracker';
            }
            ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (hasRole('parent')): ?>
                    <li class="nav-item">
                        <!-- Removed My Children menu item as per request -->
                        <!-- <a class="nav-link" href="<?= BASE_URL ?>public/index.php?page=parent/my_children">Anak Saya</a> -->
                    </li>
                <?php endif; ?>

                <?php if (hasRole('parent') || hasRole('teacher')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Al-Qur'an</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>public/index.php?page=quran/surah_list">Daftar Surah</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>public/index.php?page=quran/search">Pencarian</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>public/index.php?page=quran/bookmarks">Penanda</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>public/index.php?page=shared/list_short_prayers">Doa-doa Pendek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>public/index.php?page=shared/list_hadiths">Hadits</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= $user ? h($user['name']) : 'Pengguna' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>public/index.php?page=logout">Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= h($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Main Content -->
<div class="container mt-4 main-content">
    <?php
    // Content injected by including view files
    // NOTE: do NOT close this container here. Views are included after this file
    // and should render inside the .main-content. The closing tag is printed
    // in the shutdown function so footer appears after the view content.
    ?>
<?php
// Defer footer and scripts to the end of the request so views that include this layout
// at the top can still output their content inside the main-content container.
function __render_footer_and_scripts() {
    ?>
    </div> <!-- /.main-content -->

    <!-- Footer -->
    <footer class="bg-white border-top py-4">
        <div class="container text-center text-muted">
            <small>&copy; <?= date('Y') ?> Quran Tracker. All rights reserved.</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>public/assets/js/script.js"></script>
    </body>
    </html>
    <?php
}

register_shutdown_function('__render_footer_and_scripts');
// When the view finishes rendering, PHP will call the shutdown function which
// prints the footer and scripts after all view output.
