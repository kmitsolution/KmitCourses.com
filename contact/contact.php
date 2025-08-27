<?php
// contact.php: Handles Contact Us form submission
require_once __DIR__ . '/config.php';

// Sanitize and validate input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$courseid = intval($_POST['courseid'] ?? 0);

$errors = [];
if ($name === '' || $email === '' || $phone === '' || !$courseid) {
    $errors[] = 'All fields are required.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}
if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
    $errors[] = 'Invalid phone number.';
}

if ($errors) {
    echo '<script>alert("' . implode(' ', $errors) . '"); window.history.back();</script>';
    exit;
}

// Check if course exists and insert using stored procedure
try {
    $pdo = getPDO();
    // Check if course exists using a stored procedure
    $stmt = $pdo->prepare('CALL get_course_by_id(?)');
    $stmt->execute([$courseid]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    if (!$course) {
        echo '<script>alert("Invalid course selected."); window.history.back();</script>';
        exit;
    }

    // Insert submission using stored procedure
    $stmt = $pdo->prepare('CALL add_contact_submission(?, ?, ?, ?)');
    if ($stmt->execute([$name, $email, $phone, $courseid])) {
        echo '<script>alert("Thank you! Your details have been submitted."); window.close(); window.location.href = document.referrer || "/";</script>';
    } else {
        echo '<script>alert("Submission failed. Please try again later."); window.history.back();</script>';
    }
    $stmt->closeCursor();
} catch (Exception $e) {
    echo '<script>alert("Database error: ' . htmlspecialchars($e->getMessage()) . '"); window.history.back();</script>';
}
?>
