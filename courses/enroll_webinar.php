<?php
require_once '../config.php';
$pdo = getPDO();

$success = false;
$error = '';

// Fetch webinar info if webinarid is present
$webinarInfo = null;
if (isset($_GET['webinarid']) || isset($_POST['webinarid'])) {
    $wid = intval($_GET['webinarid'] ?? $_POST['webinarid']);
    if ($wid) {
        $stmt = $pdo->prepare('SELECT w.*, c.coursename FROM webinars w JOIN course c ON w.courseid = c.courseid WHERE w.webinarid = ?');
        $stmt->execute([$wid]);
        $webinarInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $webinarid = isset($_POST['webinarid']) ? intval($_POST['webinarid']) : 0;
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
if ($webinarid && $firstname && $email && $mobile) {
    try {
        // Ensure the stored procedure and table use 'mobile' column, not 'phone'
        $stmt = $pdo->prepare('CALL enroll_student_to_webinar(?, ?, ?, ?, ?)');
        $stmt->execute([$firstname, $lastname, $email, $mobile, $webinarid]);
        $success = true;
    } catch (Exception $e) {
        // If error mentions 'phone', clarify to use 'mobile' in DB/procedure
        if (strpos($e->getMessage(), 'phone') !== false) {
            $error = "Database error: Please update your 'student' table and 'enroll_student_to_webinar' procedure to use 'mobile' instead of 'phone'.";
        } else {
            $error = 'Could not enroll: ' . $e->getMessage();
        }
    }
} else {
    $error = 'Please fill all required fields.';
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Webinar</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
      body { font-family: 'Inter', Arial, sans-serif; background: #f8f9fa; }
      .auth-container { max-width: 400px; margin: 48px auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 32px #0002; padding: 32px 28px; }
      h2 { color: #0078d4; margin-bottom: 18px; }
      label { display: block; margin-bottom: 10px; font-weight: 600; color: #222; }
      input[type=text], input[type=email] {
        width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 16px;
      }
      button { width: 100%; background: #0078d4; color: #fff; border: none; border-radius: 6px; padding: 10px 0; font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 8px; }
      a { color: #0078d4; text-decoration: none; }
      .error { color: #c00; margin-bottom: 12px; }
      .success { color: green; font-weight: 600; margin-bottom: 18px; }
    </style>
</head>
<body>
  <div class="auth-container">
    <h2>Enroll in Webinar</h2>
    <?php if ($success): ?>
      <div class="success">Enrollment successful! Check your email for details.</div>
    <?php elseif ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($webinarInfo): ?>
      <div style="background:#f8f9fa;padding:16px 14px 10px 14px;border-radius:10px;margin-bottom:18px;box-shadow:0 2px 8px #0001;">
        <div style="font-size:1.08rem;font-weight:700;color:#0078d4;">Webinar: <?= htmlspecialchars($webinarInfo['subtopic']) ?></div>
        <div style="font-size:0.98rem;color:#444;margin-top:2px;">Course: <?= htmlspecialchars($webinarInfo['coursename']) ?></div>
        <div style="font-size:0.98rem;color:#444;margin-top:2px;">Date: <?= htmlspecialchars($webinarInfo['webinar_date']) ?></div>
        <div style="font-size:0.98rem;color:#444;margin-top:2px;">Type: <?= $webinarInfo['is_paid'] ? 'Paid (₹'.number_format($webinarInfo['amount'],2).')' : 'Free' ?></div>
      </div>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="webinarid" value="<?= htmlspecialchars($_POST['webinarid'] ?? $_GET['webinarid'] ?? '') ?>">
      <label>First Name *</label>
      <input type="text" name="firstname" required value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
      <label>Last Name</label>
      <input type="text" name="lastname" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
      <label>Email *</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <label>Mobile *</label>
      <input type="text" name="mobile" required value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
      <button type="submit" class="btn primary">Submit</button>
    </form>
    <a href="webinars.php" class="btn" style="margin-top:18px;">Back to Webinars</a>
  </div>
</body>
</html>
