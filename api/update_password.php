<?php
require_once '../config/db.php';
require_once '../config/session.php';

header('Content-Type: application/json');

// Only authenticated students (or any logged-in role) can change their own password
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

$currentPassword = $_POST['current_password'] ?? '';
$newPassword     = $_POST['new_password']     ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// --- Validate inputs ---
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (strlen($newPassword) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
    exit;
}

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit;
    }

    if (password_verify($newPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'New password must be different from the current one.']);
        exit;
    }

    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
    $update->execute([$hashed, (int)$_SESSION['user_id']]);

    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully!']);

} catch (Exception $e) {
    error_log('[update_password] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An internal error occurred. Please try again.']);
}
