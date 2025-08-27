<?php
require_once '../config.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT studentid FROM student WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // In real app, send email with reset link/token. Here, just show a message.
            $message = 'A password reset link would be sent to your email (demo only).';
        } else {
            $message = 'No account found with that email.';
        }
    } else {
        $message = 'Please enter your email.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
      body { font-family: 'Inter', Arial, sans-serif; background: #f8f9fa; }
      .auth-container { max-width: 400px; margin: 48px auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 32px #0002; padding: 32px 28px; }
      h2 { color: #0078d4; margin-bottom: 18px; }
      label { display: block; margin-bottom: 10px; font-weight: 600; color: #222; }
      input[type=email] {
        width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 16px;
      }
      button { width: 100%; background: #0078d4; color: #fff; border: none; border-radius: 6px; padding: 10px 0; font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 8px; }
      a { color: #0078d4; text-decoration: none; }
      .error { color: #c00; margin-bottom: 12px; }
    </style>
</head>
<body>
  <div class="auth-container">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
      <img src="../assets/images/Original.png" alt="KMIT Solutions Services Logo" style="width:38px;height:48px;object-fit:contain;border-radius:8px;box-shadow:0 2px 8px #0001;background:#fff;">
      <span style="font-size:1.15rem;font-weight:700;color:#0078d4;">KMIT Solutions Services</span>
    </div>
    <h2>Forgot Password</h2>
    <?php if ($message) echo '<div class="error">'.$message.'</div>'; ?>
    <form method="post" autocomplete="off">
        <label>Email: <input type="email" name="email" required></label>
        <button type="submit">Send Reset Link</button>
    </form>
    <a href="login.php" style="display:inline-block;margin-top:18px;color:#0078d4;">Back to Login</a>
    <a href="../default.php" style="display:inline-block;margin-top:10px;background:#eee;color:#0078d4;padding:7px 18px;border-radius:6px;text-decoration:none;font-weight:600;">Back to Home</a>
  </div>
</body>
</html>
