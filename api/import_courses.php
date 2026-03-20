<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /CMS/admin/courses.php');
    exit;
}

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    setFlash('error', 'Please upload a valid CSV file.');
    header('Location: /CMS/admin/courses.php');
    exit;
}

$file = $_FILES['csv_file'];
$fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($fileExt !== 'csv' && $file['type'] !== 'text/csv') {
    setFlash('error', 'Invalid file format. Only CSV files are allowed.');
    header('Location: /CMS/admin/courses.php');
    exit;
}

$handle = fopen($file['tmp_name'], 'r');
if (!$handle) {
    setFlash('error', 'Failed to open the uploaded file.');
    header('Location: /CMS/admin/courses.php');
    exit;
}

// Read header row
$header = fgetcsv($handle);
if (!$header) {
    setFlash('error', 'The CSV file is empty or improperly formatted.');
    fclose($handle);
    header('Location: /CMS/admin/courses.php');
    exit;
}

// Normalize header
$header = array_map(function($col) {
    return strtolower(trim($col));
}, $header);

// Expected columns for courses
// Columns: title, description, category, academic_year, instructor_id, status
$requiredColumns = ['title', 'description', 'category', 'academic_year', 'instructor_id', 'status'];
$colIndexes = [];
foreach ($requiredColumns as $col) {
    $idx = array_search($col, $header);
    if ($idx === false) {
        setFlash('error', "Missing required column in CSV header: '$col'. Required columns are: " . implode(', ', $requiredColumns));
        fclose($handle);
        header('Location: /CMS/admin/courses.php');
        exit;
    }
    $colIndexes[$col] = $idx;
}

$successCount = 0;
$errorCount = 0;
$validYears = ['First Year', 'Second Year', 'Third Year', 'Fourth Year'];

$insertStmt = $db->prepare('INSERT INTO courses (title, description, category, academic_year, instructor_id, status) VALUES (?,?,?,?,?,?)');
$checkUserStmt = $db->prepare('SELECT id FROM users WHERE id = ? AND role = "instructor"');

$rowIndex = 1;
while (($data = fgetcsv($handle)) !== false) {
    $rowIndex++;
    // Skip empty rows
    if (empty(array_filter($data))) continue;
    
    // Ensure row has enough columns
    if (count($data) <= max($colIndexes)) {
        $errorCount++;
        continue;
    }

    $title       = sanitize($data[$colIndexes['title']] ?? '');
    $description = sanitize($data[$colIndexes['description']] ?? '');
    $category    = sanitize($data[$colIndexes['category']] ?? '');
    $year        = sanitize($data[$colIndexes['academic_year']] ?? '');
    $instructor  = sanitize($data[$colIndexes['instructor_id']] ?? '');
    $status      = sanitize($data[$colIndexes['status']] ?? '');

    // Title is required
    if (empty($title)) {
        $errorCount++;
        continue;
    }

    // Validate Academic Year
    if (!in_array($year, $validYears)) {
        $year = 'First Year'; // Default fallback
    }

    // Validate Status
    if (!in_array($status, ['active', 'draft'])) {
        $status = 'draft';
    }

    // Validate Instructor ID (optional, but must be valid if provided)
    $instrId = null;
    if (!empty($instructor) && is_numeric($instructor)) {
        $checkUserStmt->execute([(int)$instructor]);
        if ($checkUserStmt->fetch()) {
            $instrId = (int)$instructor;
        }
    }

    try {
        $insertStmt->execute([$title, $description, $category, $year, $instrId, $status]);
        $successCount++;
    } catch (PDOException $e) {
        $errorCount++;
    }
}

fclose($handle);

$msg = "Import Summary: $successCount courses added.";
if ($errorCount > 0) $msg .= " $errorCount errors/invalid rows.";

setFlash('success', $msg);
header('Location: /CMS/admin/courses.php');
exit;
