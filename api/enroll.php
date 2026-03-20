<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /CMS/student/catalog.php');
    exit;
}

$db       = getDB();
$courseId = (int)($_POST['course_id'] ?? 0);
$studentId = (int)$_SESSION['user_id'];

if (!$courseId) {
    setFlash('error', 'Invalid course.');
    header('Location: /CMS/student/catalog.php');
    exit;
}

// Check course exists
$course = $db->prepare('SELECT id, title FROM courses WHERE id = ? AND status = "active"');
$course->execute([$courseId]);
$course = $course->fetch();
if (!$course) {
    setFlash('error', 'Course not found or inactive.');
    header('Location: /CMS/student/catalog.php');
    exit;
}

// Check duplicate
$check = $db->prepare('SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?');
$check->execute([$studentId, $courseId]);
if ($check->fetch()) {
    setFlash('error', 'You are already enrolled in "' . htmlspecialchars($course['title']) . '".');
    header('Location: /CMS/student/catalog.php');
    exit;
}

// Enroll
$stmt = $db->prepare('INSERT INTO enrollments (student_id, course_id) VALUES (?,?)');
$stmt->execute([$studentId, $courseId]);
setFlash('success', 'Successfully enrolled in "' . htmlspecialchars($course['title']) . '"!');
header('Location: /CMS/student/dashboard.php');
exit;
