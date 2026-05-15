<?php
// login.php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: admin_journal.php');
                } else {
                    header('Location: user_journal.php');
                }
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Throwable $e) {
            $error = 'Login error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Alpaca Travels</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles.css?v21">
</head>
<body>
  <main class="main-content">
    <h1>Login</h1>
    <?php if ($error): ?>
      <p style="color:#b00020;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <form method="post" action="login.php" autocomplete="off">
      <div>
        <label>Username</label><br>
        <input type="text" name="username" required>
      </div>
      <div style="margin-top:8px;">
        <label>Password</label><br>
        <input type="password" name="password" required>
      </div>
      <div style="margin-top:12px;">
        <button type="submit">Login</button>
      </div>
    </form>
  </main>
</body>
</html>
