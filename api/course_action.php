<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$page   = max(1, (int)($_POST['page'] ?? 1));
$search = sanitize($_POST['search'] ?? '');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /CMS/admin/courses.php');
    exit;
}

// DELETE
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $db->prepare('DELETE FROM courses WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Course deleted successfully.');
    header("Location: /CMS/admin/courses.php?page=$page&search=" . urlencode($search) . "#coursesTable");
    exit;
}

// ADD or EDIT
$courseId    = (int)($_POST['course_id'] ?? 0);
$title       = sanitize($_POST['title'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$category    = sanitize($_POST['category'] ?? '');
$year        = sanitize($_POST['academic_year'] ?? 'First Year');
$instrId     = !empty($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : null;
$status      = in_array($_POST['status'] ?? '', ['active','draft']) ? $_POST['status'] : 'active';

if (empty($title)) {
    setFlash('error', 'Course title is required.');
    header("Location: /CMS/admin/courses.php?page=$page&search=" . urlencode($search) . ($courseId > 0 ? "#course-$courseId" : "#coursesTable"));
    exit;
}

$validYears = ['First Year','Second Year','Third Year','Fourth Year'];
if (!in_array($year, $validYears)) $year = 'First Year';

$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/courses/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', basename($_FILES['image']['name']));
    $targetPath = $uploadDir . $filename;
    
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/courses/' . $filename;
        }
    } else {
        setFlash('error', 'Only JPG, JPEG, PNG, WEBP & GIF files are allowed.');
        header("Location: /CMS/admin/courses.php?page=$page&search=" . urlencode($search) . ($courseId > 0 ? "#course-$courseId" : "#coursesTable"));
        exit;
    }
}

if ($courseId > 0) {
    // Edit
    if ($imagePath) {
        $stmt = $db->prepare('UPDATE courses SET title=?, description=?, category=?, academic_year=?, instructor_id=?, status=?, image=? WHERE id=?');
        $stmt->execute([$title, $description, $category, $year, $instrId, $status, $imagePath, $courseId]);
    } else {
        $stmt = $db->prepare('UPDATE courses SET title=?, description=?, category=?, academic_year=?, instructor_id=?, status=? WHERE id=?');
        $stmt->execute([$title, $description, $category, $year, $instrId, $status, $courseId]);
    }
    setFlash('success', 'Course updated successfully.');
} else {
    // Add
    $stmt = $db->prepare('INSERT INTO courses (title, description, category, academic_year, instructor_id, status, image) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$title, $description, $category, $year, $instrId, $status, $imagePath]);
    setFlash('success', 'Course added successfully.');
}

header("Location: /CMS/admin/courses.php?page=$page&search=" . urlencode($search) . ($courseId > 0 ? "#course-$courseId" : "#coursesTable"));
exit;
