<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
require_once 'db.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'countries':
        getCountries($pdo);
        break;
    case 'cities':
        getCities($pdo);
        break;
    case 'journals':
        getJournals($pdo);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getCountries($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
}

function getCities($pdo) {
    $countryId = $_GET['country_id'] ?? 0;
    if (!ctype_digit((string)$countryId)) {
        echo json_encode([]);
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT id, name, summary
         FROM cities
         WHERE country_id = ?
         ORDER BY name"
    );
    $stmt->execute([$countryId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
}

function getJournals($pdo) {
    $cityId = $_GET['city_id'] ?? 0;
    if (!ctype_digit((string)$cityId)) {
        echo json_encode([]);
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT id, title, content, photo_url, video_url, created_at
         FROM journals
         WHERE city_id = ?
           AND is_approved = 1
         ORDER BY created_at DESC"
    );
    $stmt->execute([$cityId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        $stmt = $pdo->prepare(
            "SELECT id, title, content, photo_url, video_url, created_at
             FROM journals
             WHERE city_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$cityId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($rows);
}
