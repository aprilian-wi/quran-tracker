<?php
require 'config/database.php';

$teacher_id = 10;

echo "=== DEBUG TEACHER PROFILE ===\n\n";

// Check if teacher exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'teacher'");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

if ($teacher) {
    echo "✓ Teacher found: " . $teacher['name'] . "\n\n";
} else {
    echo "✗ Teacher not found\n\n";
}

// Check classes assigned
$stmt = $pdo->prepare("SELECT c.* FROM classes c WHERE c.teacher_id = ? ORDER BY c.name");
$stmt->execute([$teacher_id]);
$classes = $stmt->fetchAll();

echo "Classes assigned to teacher ID {$teacher_id}: " . count($classes) . "\n";

if (count($classes) > 0) {
    foreach ($classes as $class) {
        echo "\n- " . $class['name'] . " (ID: " . $class['id'] . ")\n";
        
        // Count students
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM children WHERE class_id = ?");
        $stmt->execute([$class['id']]);
        $student_count = $stmt->fetch()['count'];
        echo "  Students: $student_count\n";
    }
} else {
    echo "\n⚠ No classes assigned to this teacher\n";
}

// Show all teachers for reference
echo "\n\n=== ALL TEACHERS ===\n";
$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'teacher'");
$all_teachers = $stmt->fetchAll();
foreach ($all_teachers as $t) {
    echo $t['id'] . " - " . $t['name'] . " (" . $t['email'] . ")\n";
}
?>
