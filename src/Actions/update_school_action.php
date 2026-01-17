<?php
// src/Actions/update_school_action.php

checkCSRFToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/schools');
}

$id = $_POST['id'] ?? 0;
$data = [];
if (isset($_POST['name']))
    $data['name'] = trim($_POST['name']);
if (isset($_POST['address']))
    $data['address'] = trim($_POST['address']);
if (isset($_POST['slug']))
    $data['slug'] = trim($_POST['slug']);
if (isset($_POST['microsite_html']))
    $data['microsite_html'] = $_POST['microsite_html'];

// Address Details
$fields = ['provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'rt_rw', 'kode_pos'];
foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        $data[$field] = trim($_POST[$field]);
    }
}

require_once __DIR__ . '/../Controllers/SystemAdminController.php';
$controller = new SystemAdminController($pdo);
$result = $controller->updateSchool($id, $data);

if ($result) {
    setFlash('success', 'School updated successfully.');
    redirect('admin/edit_school', ['id' => $id]);
} else {
    setFlash('danger', 'Failed to update school.');
    redirect('admin/edit_school', ['id' => $id]);
}
