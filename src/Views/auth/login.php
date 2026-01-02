<?php
// src/Views/auth/login.php
if (isLoggedIn()) {
    redirect('dashboard');
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Quran Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .login-header {
            background: #28a745;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-weight: 700;
        }
        .login-body {
            padding: 2rem;
            background: white;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-3"><img src="<?= BASE_URL ?>public/assets/logo_quran_tracker.png" alt="Logo" style="width: 200px; height: 200px; object-fit: cover;"></div>
                <div class="login-card">
                    <div class="login-header">
                        <h2>Sekolah Anak Bangsa</h2>
                        <p class="mb-0">TK - KOBER - DAYCARE</p>
                    </div>
                    <div class="login-body">
                        <?php
                        $flash = getFlash();
                        if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
                                <?= h($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= BASE_URL ?>public/index.php?page=login">
                            <?= csrfInput() ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>