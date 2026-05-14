<?php
$localConfig = __DIR__ . '/db.local.php';

if (is_readable($localConfig)) {
    $config = require $localConfig;
} else {
    $config = [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ];
}

$host = $config['host'] ?? 'localhost';
$db = $config['name'] ?? '';
$user = $config['user'] ?? '';
$pass = $config['pass'] ?? '';
$charset = $config['charset'] ?? 'utf8mb4';

if ($db === '' || $user === '') {
    http_response_code(500);
    echo json_encode(['error' => 'Database configuration is missing']);
    exit;
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
