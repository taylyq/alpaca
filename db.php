<?php
$envPaths = [
    dirname(__DIR__) . '/.env',
    __DIR__ . '/.env',
];

$localConfigPaths = [
    dirname(__DIR__) . '/db.local.php',
    __DIR__ . '/db.local.php',
];

$env = loadEnv($envPaths);
$config = null;

foreach ($localConfigPaths as $localConfig) {
    if (is_readable($localConfig)) {
        $config = require $localConfig;
        break;
    }
}

if (!is_array($config)) {
    $config = [
        'host' => envValue('DB_HOST', $env, 'localhost'),
        'name' => envValue('DB_NAME', $env, ''),
        'user' => envValue('DB_USER', $env, ''),
        'pass' => envValue('DB_PASS', $env, ''),
        'charset' => envValue('DB_CHARSET', $env, 'utf8mb4'),
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

function loadEnv(array $paths): array
{
    foreach ($paths as $path) {
        if (!is_readable($path)) {
            continue;
        }

        $values = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return [];
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);

            if ($key === '') {
                continue;
            }

            $values[$key] = trim(trim($value), "\"'");
        }

        return $values;
    }

    return [];
}

function envValue(string $key, array $env, string $default = ''): string
{
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    return isset($env[$key]) && $env[$key] !== ''
        ? trim($env[$key], "\"'")
        : $default;
}
