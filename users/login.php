<?php
require_once '../config.php';
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        $pdo = getPDO();
        // Use a stored procedure to get username and hashed password (case-insensitive)
        $stmt = $pdo->prepare('CALL get_user_by_credentials(?, NULL)');
        $stmt->execute([strtolower($username)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if ($result && isset($result['username']) && isset($result['password']) && password_verify($password, $result['password'])) {
            // Get studentid for session using stored procedure
            $idStmt = $pdo->prepare('CALL get_studentid_by_username(?)');
            $idStmt->execute([strtolower($username)]);
            $user1 = $idStmt->fetch(PDO::FETCH_ASSOC);
            $idStmt->closeCursor();
            $_SESSION['studentid'] = $user1['studentid'];
            $_SESSION['username'] = $result['username'];
            // Get roles using stored procedure
            $roleStmt = $pdo->prepare('CALL get_studentid_roles(?)');
            $roleStmt->execute([$user1['studentid']]);
            $_SESSION['roles'] = [];
            while ($row = $roleStmt->fetch(PDO::FETCH_ASSOC)) {
                if (isset($row['rolename'])) {
                    $_SESSION['roles'][] = $row['rolename'];
                }
            }
            $roleStmt->closeCursor();
            // Check if user is admin using stored procedure, store in session
            $isAdmin = false;
            try {
                $adminPdo = getPDO();
                $adminStmt = $adminPdo->prepare('CALL is_student_admin(?)');
                $adminStmt->execute([$_SESSION['username']]);
                $isAdmin = $adminStmt->fetch() ? true : false;
                $adminStmt->closeCursor();
            } catch (Exception $e) {
                $isAdmin = false;
            }
            $_SESSION['is_admin'] = $isAdmin;
            header('Location: ../default.php');
            exit;
        } else {
            $message = 'Invalid username or password.';
        }
    } else {
        $message = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
      body { font-family: 'Inter', Arial, sans-serif; background: #f8f9fa; }
      .auth-container { max-width: 400px; margin: 48px auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 32px #0002; padding: 32px 28px; }
      h2 { color: #0078d4; margin-bottom: 18px; }
      label { display: block; margin-bottom: 10px; font-weight: 600; color: #222; }
      input[type=text], input[type=password] {
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
    <h2>Login</h2>
    <?php if ($message) echo '<div class="error">'.$message.'</div>'; ?>
    <form method="post" autocomplete="off">
        <label>Username: <input type="text" name="username" required></label>
        <label>Password: <input type="password" name="password" required></label>
        <button type="submit">Login</button>
    </form>
    <p style="margin-top:18px;color:#111;"><a href="forgot_password.php" style="color:#0078d4;">Forgot Password?</a></p>
    <p style="color:#111;">Don't have an account? <a href="signup.php">Signup</a></p>
    <a href="../default.php" style="display:inline-block;margin-top:10px;background:#eee;color:#0078d4;padding:7px 18px;border-radius:6px;text-decoration:none;font-weight:600;">Back to Home</a>
  </div>
</body>
</html>
