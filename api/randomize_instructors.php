<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();

try {
    // Get all active instructor IDs
    $stmt = $db->query("SELECT id FROM users WHERE role = 'instructor' AND status = 'active'");
    $instructors = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($instructors)) {
        setFlash('error', 'No active instructors found to assign.');
        header('Location: /CMS/admin/courses.php');
        exit;
    }

    // Get all course IDs
    $courses = $db->query("SELECT id FROM courses")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($courses)) {
        setFlash('error', 'No courses found to randomize.');
        header('Location: /CMS/admin/courses.php');
        exit;
    }

    $db->beginTransaction();
    $updateStmt = $db->prepare("UPDATE courses SET instructor_id = ? WHERE id = ?");
    
    foreach ($courses as $courseId) {
        $randomInstructorId = $instructors[array_rand($instructors)];
        $updateStmt->execute([$randomInstructorId, $courseId]);
    }
    
    $db->commit();
    setFlash('success', 'All courses have been randomly assigned to ' . count($instructors) . ' instructors.');
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    setFlash('error', 'An error occurred: ' . $e->getMessage());
}

header('Location: /CMS/admin/courses.php#coursesTable');
exit;
