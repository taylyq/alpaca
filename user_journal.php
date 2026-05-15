<?php
// user_journal.php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/config.php';
require_login();

$message = '';
$error = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// Load countries
$countries = $pdo->query('SELECT id, name FROM countries ORDER BY name')->fetchAll();

$selectedCountry = (int)($_POST['country_id'] ?? 0);
$cities = [];

if ($selectedCountry > 0) {
    $stmt = $pdo->prepare('SELECT id, name FROM cities WHERE country_id = ? ORDER BY name');
    $stmt->execute([$selectedCountry]);
    $cities = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_journal'])) {
    $countryId = (int)($_POST['country_id'] ?? 0);
    $cityId = (int)($_POST['city_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $photoUrl = trim($_POST['photo_url'] ?? '');
    $videoUrl = trim($_POST['video_url'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } elseif ($countryId <= 0 || $cityId <= 0 || $title === '' || $content === '') {
        $error = 'Country, city, title, and content are required.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM cities WHERE id = ? AND country_id = ?');
        $stmt->execute([$cityId, $countryId]);
        if (!$stmt->fetchColumn()) {
            $error = 'Invalid city for selected country.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO journals (city_id, title, content, photo_url, video_url, is_approved)
                 VALUES (?, ?, ?, ?, ?, 0)'
            );
            $stmt->execute([
                $cityId,
                $title,
                $content,
                $photoUrl !== '' ? $photoUrl : null,
                $videoUrl !== '' ? $videoUrl : null
            ]);
            $message = 'Journal submitted! It will appear after an admin approves it.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Journals - Alpaca Travels</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles.css?v21">
</head>
<body>
  <main class="main-content">
    <h1>Submit a Journal</h1>
    <p>Logged in as <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> |
       <a href="logout.php">Logout</a></p>

    <?php if ($message): ?>
      <p style="color:#2e7d32;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p style="color:#b00020;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="post" action="user_journal.php">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

      <div>
        <label>Country</label><br>
        <select name="country_id" required onchange="this.form.submit()">
          <option value="">Select country</option>
          <?php foreach ($countries as $country): ?>
            <option value="<?php echo (int)$country['id']; ?>"
              <?php echo $selectedCountry === (int)$country['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($country['name'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="margin-top:8px;">
        <label>City</label><br>
        <select name="city_id" required>
          <option value="">Select city</option>
          <?php foreach ($cities as $city): ?>
            <option value="<?php echo (int)$city['id']; ?>">
              <?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="margin-top:8px;">
        <label>Title</label><br>
        <input type="text" name="title" required style="width:100%;">
      </div>

      <div style="margin-top:8px;">
        <label>Content</label><br>
        <textarea name="content" rows="6" required style="width:100%;"></textarea>
      </div>

      <div style="margin-top:8px;">
        <label>Photo URL (optional)</label><br>
        <input type="url" name="photo_url" style="width:100%;">
      </div>

      <div style="margin-top:8px;">
        <label>Video URL (optional)</label><br>
        <input type="url" name="video_url" style="width:100%;">
      </div>

      <div style="margin-top:12px;">
        <button type="submit" name="submit_journal">Submit for Approval</button>
      </div>
    </form>
  </main>
</body>
</html>
