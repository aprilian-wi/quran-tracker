<?php
// src/Actions/update_progress_action.php
global $pdo;
require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/Progress.php';
require_once __DIR__ . '/../Models/Child.php';
require_once __DIR__ . '/../Models/Quran.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Invalid request.');
    redirect('dashboard');
}

if (!validateCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid security token.');
    redirect('dashboard');
}

$child_id = (int)($_POST['child_id'] ?? 0);
$juz = (int)($_POST['juz'] ?? 0);
$surah = (int)($_POST['surah'] ?? 0);
$verse = (int)($_POST['verse'] ?? 0);
$status = $_POST['status'] ?? '';
$note = trim($_POST['note'] ?? '');
$updated_by = (int)($_POST['updated_by'] ?? 0);

if (!$child_id || !$juz || !$surah || !$verse || !in_array($status, ['in_progress', 'memorized']) || $updated_by !== $_SESSION['user_id']) {
    setFlash('danger', 'Invalid data.');
    redirect('dashboard');
}

// Verify child access
$childModel = new Child($pdo);
$child = $childModel->find($child_id, $_SESSION['user_id'], $_SESSION['role']);
if (!$child) {
    setFlash('danger', 'Access denied.');
    redirect('dashboard');
}

// Validate verse count
$quranModel = new Quran($pdo);
$max_verse = $quranModel->getVerseCount($surah);
if ($verse < 1 || $verse > $max_verse) {
    setFlash('danger', "Verse must be between 1 and $max_verse.");
    redirect('dashboard');
}

$progressModel = new Progress($pdo);
$success = $progressModel->update($child_id, $juz, $surah, $verse, $status, $updated_by, $note);

if ($success) {
    setFlash('success', "Progress updated for {$child['name']}.");
    if ($_SESSION['role'] === 'parent') {
        $redirectPage = 'page=parent/update_progress&child_id=' . $child_id;
    } else {
        $redirectPage = 'page=teacher/update_progress&child_id=' . $child_id;
    }
} else {
    setFlash('danger', 'Failed to update progress.');
    if ($_SESSION['role'] === 'parent') {
        $redirectPage = 'page=parent/update_progress&child_id=' . $child_id;
    } else {
        $redirectPage = 'page=teacher/update_progress&child_id=' . $child_id;
    }
}
redirect($redirectPage);
