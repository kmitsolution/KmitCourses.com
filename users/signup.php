<?php
require_once '../config.php';

// Sanitize and validate input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($username === '' || $password === '' || $email === '' || $mobile === '' || $firstname === '' || $lastname === '') {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if (!preg_match('/^[0-9]{10,15}$/', $mobile)) {
        $errors[] = 'Invalid mobile number.';
    }
    // Password complexity: min 8 chars, 1 upper, 1 lower, 1 digit, 1 special
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d]).{8,}$/', $password)) {
        $errors[] = 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.';
    }
    if (!$errors) {
        try {
            $pdo = getPDO();
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            // Store username in lowercase for case-insensitive login
            $stmt = $pdo->prepare('CALL add_student(?, ?, ?, ?, ?, ?)');
            $stmt->execute([strtolower($username), $hashed, $email, $mobile, $firstname, $lastname]);
            // Get the new studentid using stored procedure
            $stmt->closeCursor();
            $idStmt = $pdo->prepare('CALL get_studentid_by_username(?)');
            $idStmt->execute([strtolower($username)]);
            $studentid = $idStmt->fetchColumn();
            $idStmt->closeCursor();
            // Assign default Reader role
            $roleStmt = $pdo->prepare('SELECT roleid FROM role WHERE rolename = ?');
            $roleStmt->execute(['Reader']);
            $roleid = $roleStmt->fetchColumn();
            if ($roleid && $studentid) {
                $pdo->prepare('INSERT INTO student_role (studentid, roleid) VALUES (?, ?)')->execute([$studentid, $roleid]);
            }
            if (isset($_GET['from']) && $_GET['from'] === 'userlist') {
                echo '<script>window.close();window.opener.location.reload();</script>';
            } else {
                $redirect = $_GET['redirect'] ?? '';
                $loginUrl = 'login.php';
                if ($redirect) {
                    $loginUrl .= '?redirect=' . urlencode($redirect);
                }
                echo '<script>alert("Signup successful! You can now login."); window.location.href = "' . $loginUrl . '";</script>';
            }
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = 'Username or email already exists.';
            } else {
                $errors[] = 'Error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
      body { font-family: 'Inter', Arial, sans-serif; background: #f8f9fa; }
      .auth-container { max-width: 400px; margin: 48px auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 32px #0002; padding: 32px 28px; }
      h2 { color: #0078d4; margin-bottom: 18px; }
      label { display: block; margin-bottom: 10px; font-weight: 600; color: #222; }
      input[type=text], input[type=password], input[type=email] {
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
    <h2>Signup</h2>
    <?php if ($errors) echo '<div class="error">'.implode('<br>', $errors).'</div>'; ?>
    <form method="post" autocomplete="off" id="signupForm" onsubmit="return validateSignupForm();">
        <label>First Name: <input type="text" name="firstname" id="firstname" required value="<?= htmlspecialchars($firstname) ?>"></label>
        <label>Last Name: <input type="text" name="lastname" id="lastname" required value="<?= htmlspecialchars($lastname) ?>"></label>
        <label>Username: <input type="text" name="username" id="username" required value="<?= htmlspecialchars($username) ?>"></label>
        <label>Password: <input type="password" name="password" id="password" required></label>
        <label>Email: <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email) ?>"></label>
        <label>Mobile: <input type="text" name="mobile" id="mobile" required value="<?= htmlspecialchars($mobile) ?>"></label>
        <div id="formError" class="error" style="display:none;"></div>
        <button type="submit">Signup</button>
    </form>
    <script>
    function validateSignupForm() {
        var firstname = document.getElementById('firstname').value.trim();
        var lastname = document.getElementById('lastname').value.trim();
        var username = document.getElementById('username').value.trim();
        var password = document.getElementById('password').value;
        var email = document.getElementById('email').value.trim();
        var mobile = document.getElementById('mobile').value.trim();
        var error = '';
        if (!firstname || !lastname || !username || !password || !email || !mobile) {
            error = 'All fields are required.';
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            error = 'Invalid email address.';
        } else if (!/^[0-9]{10,15}$/.test(mobile)) {
            error = 'Invalid mobile number.';
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d]).{8,}$/.test(password)) {
            error = 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.';
        }
        if (error) {
            var errDiv = document.getElementById('formError');
            errDiv.textContent = error;
            errDiv.style.display = 'block';
            return false;
        }
        return true;
    }
    </script>
    <p style="margin-top:18px;color:#111;">Already have an account? <a href="login.php" style="color:#111;">Login</a></p>
    <a href="../default.php" style="display:inline-block;margin-top:10px;background:#eee;color:#0078d4;padding:7px 18px;border-radius:6px;text-decoration:none;font-weight:600;">Back to Home</a>
  </div>
</body>