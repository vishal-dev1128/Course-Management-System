<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /CMS/auth/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: /CMS/auth/login.php');
        exit;
    }
}

function requireAnyRole(array $roles) {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $roles)) {
        header('Location: /CMS/auth/login.php');
        exit;
    }
}

function currentUser() {
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'name' => $_SESSION['user_name'] ?? '',
        'role' => $_SESSION['user_role'] ?? '',
        'email'=> $_SESSION['user_email']?? '',
    ];
}

function redirectByRole($role) {
    switch ($role) {
        case 'admin':      header('Location: /CMS/admin/dashboard.php'); break;
        case 'instructor': header('Location: /CMS/instructor/dashboard.php'); break;
        case 'student':    header('Location: /CMS/student/dashboard.php'); break;
        default:           header('Location: /CMS/auth/login.php');
    }
    exit;
}

function sanitize($value) {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
