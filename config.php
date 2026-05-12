<?php
// config.php
declare(strict_types=1);

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_start();

// Use the same DB connection as api.php
require_once __DIR__ . '/db.php'; // db.php must define $pdo

if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    echo 'Database connection error.';
    exit;
}

function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo 'Forbidden.';
        exit;
    }
}
