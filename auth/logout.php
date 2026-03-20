<?php
require_once '../config/session.php';
session_destroy();
header('Location: /CMS/auth/login.php');
exit;
