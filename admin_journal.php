<?php
// admin_journal.php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/config.php';
require_admin();

$action = $_POST['action'] ?? '';
$message = '';
$error = '';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } else {
        $journalId = (int)($_POST['journal_id'] ?? 0);

        if ($action === 'approve' && $journalId > 0) {
            $stmt = $pdo->prepare('UPDATE journals SET is_approved = 1 WHERE id = ?');
            $stmt->execute([$journalId]);
            $message = 'Journal approved.';
        } elseif ($action === 'delete' && $journalId > 0) {
            $stmt = $pdo->prepare('DELETE FROM journals WHERE id = ?');
            $stmt->execute([$journalId]);
            $message = 'Journal deleted.';
        } elseif ($action === 'update' && $journalId > 0) {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $photoUrl = trim($_POST['photo_url'] ?? '');
            $videoUrl = trim($_POST['video_url'] ?? '');
            $isApproved = isset($_POST['is_approved']) ? 1 : 0;

            if ($title === '' || $content === '') {
                $error = 'Title and content are required.';
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE journals
                     SET title = ?, content = ?, photo_url = ?, video_url = ?, is_approved = ?
                     WHERE id = ?'
                );
                $stmt->execute([
                    $title,
                    $content,
                    $photoUrl !== '' ? $photoUrl : null,
                    $videoUrl !== '' ? $videoUrl : null,
                    $isApproved,
                    $journalId
                ]);
                $message = 'Journal updated.';
            }
        }
    }
}

// Fetch journals
$stmt = $pdo->query(
    'SELECT j.id, j.title, j.content, j.photo_url, j.video_url, j.is_approved,
            j.created_at,
            c.name AS city_name, co.name AS country_name
     FROM journals j
     JOIN cities c ON c.id = j.city_id
     JOIN countries co ON co.id = c.country_id
     ORDER BY j.created_at DESC'
);
$journals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Journals - Alpaca Travels</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles.css?v21">
  <style>
    .journal-admin-card {
      border: 1px solid #e0d4c4;
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 12px;
      background: #fffaf2;
    }
    .journal-admin-header {
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:6px;
    }
    .badge {
      display:inline-block;
      padding:2px 8px;
      border-radius:999px;
      font-size:0.8rem;
    }
    .badge-approved { background:#c8e6c9; color:#1b5e20; }
    .badge-pending { background:#ffe0b2; color:#e65100; }
  </style>
</head>
<body>
  <main class="main-content">
    <h1>Admin Journals</h1>
    <p>Logged in as <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> (admin) |
       <a href="logout.php">Logout</a></p>

    <?php if ($message): ?>
      <p style="color:#2e7d32;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p style="color:#b00020;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php foreach ($journals as $j): ?>
      <div class="journal-admin-card">
        <div class="journal-admin-header">
          <strong>
            <?php echo htmlspecialchars($j['title'], ENT_QUOTES, 'UTF-8'); ?>
            (<?php echo htmlspecialchars($j['city_name'] . ', ' . $j['country_name'], ENT_QUOTES, 'UTF-8'); ?>)
          </strong>
          <span class="badge <?php echo $j['is_approved'] ? 'badge-approved' : 'badge-pending'; ?>">
            <?php echo $j['is_approved'] ? 'Approved' : 'Pending'; ?>
          </span>
        </div>
        <div style="font-size:0.85rem;color:#7a6451;margin-bottom:4px;">
          Created: <?php echo htmlspecialchars($j['created_at'], ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <form method="post" action="admin_journal.php">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="journal_id" value="<?php echo (int)$j['id']; ?>">

          <div>
            <label>Title</label><br>
            <input type="text" name="title" style="width:100%;"
              value="<?php echo htmlspecialchars($j['title'], ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div style="margin-top:6px;">
            <label>Content</label><br>
            <textarea name="content" rows="4" style="width:100%;"><?php
              echo htmlspecialchars($j['content'], ENT_QUOTES, 'UTF-8');
            ?></textarea>
          </div>

          <div style="margin-top:6px;">
            <label>Photo URL</label><br>
            <input type="url" name="photo_url" style="width:100%;"
              value="<?php echo htmlspecialchars($j['photo_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div style="margin-top:6px;">
            <label>Video URL</label><br>
            <input type="url" name="video_url" style="width:100%;"
              value="<?php echo htmlspecialchars($j['video_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div style="margin-top:6px;">
            <label>
              <input type="checkbox" name="is_approved" <?php echo $j['is_approved'] ? 'checked' : ''; ?>>
              Approved
            </label>
          </div>

          <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
            <button type="submit" name="action" value="update">Save Changes</button>
            <?php if (!$j['is_approved']): ?>
              <button type="submit" name="action" value="approve">Approve</button>
            <?php endif; ?>
            <button type="submit" name="action" value="delete"
                    onclick="return confirm('Delete this journal?');"
                    style="background:#b00020;color:#fff;border:none;padding:6px 12px;border-radius:4px;">
              Delete
            </button>
          </div>
        </form>
      </div>
    <?php endforeach; ?>
  </main>
</body>
</html>
