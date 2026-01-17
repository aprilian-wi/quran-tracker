<?php
// src/Actions/store_school_action.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/create_school');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid CSRF token.');
    redirect('admin/create_school');
}

$schoolName = trim($_POST['school_name'] ?? '');
$adminName = trim($_POST['admin_name'] ?? '');
$adminPhone = trim($_POST['admin_phone'] ?? '');
$adminPass = $_POST['admin_password'] ?? '';

// Address Details
$address = trim($_POST['address'] ?? '');
$provinsi = trim($_POST['provinsi'] ?? '');
$kabupaten = trim($_POST['kabupaten'] ?? '');
$kecamatan = trim($_POST['kecamatan'] ?? '');
$kelurahan = trim($_POST['kelurahan'] ?? '');
$rt_rw = trim($_POST['rt_rw'] ?? '');
$kode_pos = trim($_POST['kode_pos'] ?? '');

if (empty($schoolName) || empty($adminName) || empty($adminPhone) || empty($adminPass)) {
    setFlash('danger', 'All fields are required.');
    redirect('admin/create_school');
}

$schoolData = [
    'name' => $schoolName,
    'address' => $address,
    'provinsi' => $provinsi,
    'kabupaten' => $kabupaten,
    'kecamatan' => $kecamatan,
    'kelurahan' => $kelurahan,
    'rt_rw' => $rt_rw,
    'kode_pos' => $kode_pos
];

require_once __DIR__ . '/../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$result = $controller->createSchool($schoolData, $adminName, $adminPhone, $adminPass);

if ($result['success']) {
    setFlash('success', $result['message']);
    redirect('admin/schools');
} else {
    setFlash('danger', 'Error: ' . $result['message']);
    redirect('admin/create_school');
}
