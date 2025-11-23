<?php
// src/Controllers/AuthController.php
class AuthController {
    public function login() {
        require_once __DIR__ . '/../Auth/Login.php';
    }

    public function logout() {
        require_once __DIR__ . '/../Auth/Logout.php';
    }
}