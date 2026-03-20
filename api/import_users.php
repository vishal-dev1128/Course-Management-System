<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /CMS/admin/users.php');
    exit;
}

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    setFlash('error', 'Please upload a valid CSV file.');
    header('Location: /CMS/admin/users.php');
    exit;
}

$file = $_FILES['csv_file'];
$fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($fileExt !== 'csv' && $file['type'] !== 'text/csv') {
    setFlash('error', 'Invalid file format. Only CSV files are allowed.');
    header('Location: /CMS/admin/users.php');
    exit;
}

$handle = fopen($file['tmp_name'], 'r');
if (!$handle) {
    setFlash('error', 'Failed to open the uploaded file.');
    header('Location: /CMS/admin/users.php');
    exit;
}

// Read header row
$header = fgetcsv($handle);
if (!$header) {
    setFlash('error', 'The CSV file is empty or improperly formatted.');
    fclose($handle);
    header('Location: /CMS/admin/users.php');
    exit;
}

// Normalize header
$header = array_map(function($col) {
    return strtolower(trim($col));
}, $header);

// Expect: name, email, password, role, status
$requiredColumns = ['name', 'email', 'password', 'role', 'status'];
$colIndexes = [];
foreach ($requiredColumns as $col) {
    $idx = array_search($col, $header);
    if ($idx === false) {
        setFlash('error', "Missing required column in CSV header: '$col'. Required columns are: " . implode(', ', $requiredColumns));
        fclose($handle);
        header('Location: /CMS/admin/users.php');
        exit;
    }
    $colIndexes[$col] = $idx;
}

$successCount = 0;
$skipCount = 0;
$errorCount = 0;

$checkStmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$insertStmt = $db->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?,?,?,?,?)');

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

    $name     = sanitize($data[$colIndexes['name']] ?? '');
    $email    = sanitize($data[$colIndexes['email']] ?? '');
    $password = trim($data[$colIndexes['password']] ?? '');
    $role     = sanitize($data[$colIndexes['role']] ?? '');
    $status   = sanitize($data[$colIndexes['status']] ?? '');

    // Validate required
    if (empty($name) || empty($email)) {
        $errorCount++;
        continue;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorCount++;
        continue;
    }

    // Default fallbacks
    if (!in_array($role, ['admin','instructor','student'])) $role = 'student';
    if (!in_array($status, ['active','inactive'])) $status = 'active';

    // Password fallback (generate random if empty)
    if (empty($password)) {
        $password = bin2hex(random_bytes(4)); // 8 chars default
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        $skipCount++;
        continue;
    }

    // Insert user
    try {
        $insertStmt->execute([$name, $email, $hashed, $role, $status]);
        $successCount++;
    } catch (PDOException $e) {
        $errorCount++;
    }
}

fclose($handle);

$msg = "Import Summary: $successCount users added.";
if ($skipCount > 0) $msg .= " $skipCount skipped (already exist).";
if ($errorCount > 0) $msg .= " $errorCount errors/invalid rows.";

setFlash('success', $msg);
header('Location: /CMS/admin/users.php');
exit;
