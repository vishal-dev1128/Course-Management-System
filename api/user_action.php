<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$page       = max(1, (int)($_POST['page'] ?? 1));
$search     = sanitize($_POST['search'] ?? '');
$roleFilter = sanitize($_POST['role_filter'] ?? '');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /CMS/admin/users.php');
    exit;
}

// DELETE or BULK DELETE
if (isset($_POST['delete_id']) || isset($_POST['bulk_delete_ids'])) {
    $singleId = (int)($_POST['delete_id'] ?? 0);
    $bulkIds  = sanitize($_POST['bulk_delete_ids'] ?? '');

    $idsToDelete = [];
    if ($singleId > 0) {
        $idsToDelete[] = $singleId;
    } elseif (!empty($bulkIds)) {
        $parts = explode(',', $bulkIds);
        foreach ($parts as $p) {
            $pid = (int)trim($p);
            if ($pid > 0) $idsToDelete[] = $pid;
        }
    }

    if (!empty($idsToDelete)) {
        $currentUserId = (int)$_SESSION['user_id'];
        
        // Prevent deleting own account
        $ownKey = array_search($currentUserId, $idsToDelete);
        if ($ownKey !== false) {
            unset($idsToDelete[$ownKey]);
            if (empty($idsToDelete) && $singleId > 0) {
                setFlash('error', 'You cannot delete your own account.');
                header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . "#usersTable");
                exit;
            }
        }

        if (!empty($idsToDelete)) {
            $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
            $stmt = $db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
            $stmt->execute(array_values($idsToDelete));
            
            if (count($idsToDelete) > 1) {
                setFlash('success', count($idsToDelete) . ' users deleted successfully.' . ($ownKey !== false ? ' Your own account was skipped.' : ''));
            } else {
                setFlash('success', 'User deleted successfully.' . ($ownKey !== false ? ' Your own account was skipped.' : ''));
            }
        } elseif ($ownKey !== false && empty($idsToDelete) && empty($singleId)) {
             setFlash('error', 'You cannot delete your own account.');
        }
    }
    
    header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . "#usersTable");
    exit;
}

// ADD or EDIT
$userId   = (int)($_POST['user_id'] ?? 0);
$name     = sanitize($_POST['name'] ?? '');
$email    = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = sanitize($_POST['role'] ?? 'student');
$status   = sanitize($_POST['status'] ?? 'active');

if (empty($name) || empty($email)) {
    setFlash('error', 'Name and email are required.');
    header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . ($userId > 0 ? "#user-$userId" : "#usersTable"));
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Invalid email address.');
    header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . ($userId > 0 ? "#user-$userId" : "#usersTable"));
    exit;
}
if (!in_array($role, ['admin','instructor','student'])) $role = 'student';
if (!in_array($status, ['active','inactive'])) $status = 'active';

// Check email uniqueness
$checkStmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
$checkStmt->execute([$email, $userId]);
if ($checkStmt->fetch()) {
    setFlash('error', 'This email is already registered.');
    header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . ($userId > 0 ? "#user-$userId" : "#usersTable"));
    exit;
}

if ($userId > 0) {
    // Edit
    if (!empty($password) && strlen($password) >= 6) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET name=?, email=?, password=?, role=?, status=? WHERE id=?');
        $stmt->execute([$name, $email, $hashed, $role, $status, $userId]);
    } else {
        $stmt = $db->prepare('UPDATE users SET name=?, email=?, role=?, status=? WHERE id=?');
        $stmt->execute([$name, $email, $role, $status, $userId]);
    }
    setFlash('success', 'User updated successfully.');
} else {
    // Add
    if (empty($password) || strlen($password) < 6) {
        setFlash('error', 'Password must be at least 6 characters.');
        header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . ($userId > 0 ? "#user-$userId" : "#usersTable"));
        exit;
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt   = $db->prepare('INSERT INTO users (name, email, password, role, status) VALUES (?,?,?,?,?)');
    $stmt->execute([$name, $email, $hashed, $role, $status]);
    setFlash('success', 'User created successfully.');
}

header("Location: /CMS/admin/users.php?page=$page&search=" . urlencode($search) . "&role=" . urlencode($roleFilter) . ($userId > 0 ? "#user-$userId" : "#usersTable"));
exit;
