<?php
// src/Auth/Logout.php
require_once __DIR__ . '/../Helpers/functions.php';

if (isLoggedIn()) {
    session_destroy();
}

setFlash('info', 'You have been logged out.');
redirect('login');