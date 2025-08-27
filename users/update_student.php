<?php
require_once '../config.php';

// Check if username is provided
if (!isset($_GET['username']) || empty($_GET['username'])) {
    header('Location: userlist.php');
    exit;
}

$username = $_GET['username'];
$pdo = getPDO();

// Fetch student details using stored procedure

// Get studentid by username
$stmt = $pdo->prepare('CALL get_studentid_by_username(?)');
$stmt->execute([$username]);
$studentidRow = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
if (!$studentidRow || !isset($studentidRow['studentid'])) {
    echo '<script>alert("Student not found.");window.location.href="userlist.php";</script>';
    exit;
}
$studentid = $studentidRow['studentid'];

// Now get student details by studentid
$stmt = $pdo->prepare('SELECT * FROM student WHERE studentid = ?');
$stmt->execute([$studentid]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
if (!$student) {
    echo '<script>alert("Student not found.");window.location.href="userlist.php";</script>';
    exit;
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    // Optionally, add validation here
    // Password is not updated here, so pass NULL for in_password
    $updateStmt = $pdo->prepare('CALL update_student_by_username(?, ?, ?, ?, ?, ?)');
    $updateStmt->execute([$username, null, $email, $mobile, $firstname, $lastname]);
    $updateStmt->closeCursor();
    echo '<script>alert("Student updated successfully.");window.location.href="userlist.php";</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        .update-form { max-width: 400px; margin: 40px auto; background: #f9f9f9; padding: 28px 32px; border-radius: 10px; box-shadow: 0 2px 8px #e0e0e0; }
        .update-form label { display: block; margin-bottom: 6px; color: #222; font-weight: 600; }
        .update-form input { width: 100%; padding: 8px 10px; margin-bottom: 18px; border-radius: 6px; border: 1px solid #ccc; }
        .update-form button { padding: 9px 22px; border-radius: 6px; background: #0078d4; color: #fff; border: none; font-weight: 700; }
        .update-form a { margin-left: 16px; color: #0078d4; text-decoration: underline; }
    </style>
</head>
<body>
<div class="update-form">
    <h2>Update Student</h2>
    <form method="post">
        <label>Username</label>
        <input type="text" value="<?= htmlspecialchars($student['username']) ?>" disabled>
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($student['firstname']) ?>" required>
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($student['lastname']) ?>" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($student['email']) ?>" required>
        <label for="mobile">Mobile</label>
        <input type="text" name="mobile" id="mobile" value="<?= htmlspecialchars($student['mobile']) ?>" required>
        <button type="submit">Update</button>
        <a href="userlist.php">Cancel</a>
    </form>
</div>
</body>
</html>
