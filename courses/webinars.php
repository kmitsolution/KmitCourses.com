<?php
require_once '../config.php';
session_start();
$pdo = getPDO();
$studentid = isset($_SESSION['studentid']) ? $_SESSION['studentid'] : null;

// Fetch all upcoming webinars
$webinars = [];
try {
    $stmt = $pdo->query('CALL get_upcoming_webinars()');
    $webinars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (Exception $e) {
    $webinars = [];
}

// Fetch all webinarids student is enrolled in
$enrolledWebinars = [];
if ($studentid) {
    $stmt = $pdo->prepare('SELECT webinarid FROM student_webinar WHERE studentid = ?');
    $stmt->execute([$studentid]);
    $enrolledWebinars = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'webinarid');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webinars</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
      body { background: var(--bg); color: var(--text); font-family: Inter, Arial, sans-serif; }
      .container { max-width: 900px; margin: 0 auto; padding: 32px 12px; }
      .batch-table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
      .batch-table th, .batch-table td { padding: 10px 8px; border-bottom: 1px solid #2223; text-align: left; }
      .batch-table th { background: var(--card); color: var(--acc); font-size: 1.05rem; }
      .batch-table tr:nth-child(even) { background: rgba(255,255,255,0.01); }
      .enroll-btn { background: var(--acc); color: #fff; border: none; border-radius: 7px; padding: 7px 16px; font-weight: 600; cursor: pointer; }
      .enroll-btn:disabled { background: #888; cursor: not-allowed; }
      .modal-auth-container { max-width: 400px; margin: 48px auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 32px #0002; padding: 32px 28px; }
      .modal-auth-container h2 { color: #0078d4; margin-bottom: 18px; }
      .modal-auth-container label { display: block; margin-bottom: 10px; font-weight: 600; color: #222; }
      .modal-auth-container input[type=text], .modal-auth-container input[type=email] {
        width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 16px;
      }
      .modal-auth-container button { width: 100%; background: #0078d4; color: #fff; border: none; border-radius: 6px; padding: 10px 0; font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 8px; }
      .modal-auth-container .error { color: #c00; margin-bottom: 12px; }
      .modal-auth-container .success { color: green; font-weight: 600; margin-bottom: 18px; }

      /* Enrollment Modal Styles */
      .modal-bg { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.35); justify-content: center; align-items: center; }
      .modal-auth-container { max-width: 400px; margin: 0 auto; background: #fff; border-radius: 14px; box-shadow: 0 8px 32px #0002; padding: 32px 28px; }
      .modal-auth-container h2 { color: #0078d4; margin-bottom: 18px; }
      .modal-auth-container label { display: block; margin-bottom: 10px; font-weight: 600; color: #222; }
      .modal-auth-container input[type=text], .modal-auth-container input[type=email] {
        width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 16px;
      }
      .modal-auth-container button { width: 100%; background: #0078d4; color: #fff; border: none; border-radius: 6px; padding: 10px 0; font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 8px; }
      .modal-auth-container .error { color: #c00; margin-bottom: 12px; }
      .modal-auth-container .success { color: green; font-weight: 600; margin-bottom: 18px; }
    </style>
</head>
<body>
  <div class="container">
    <h1>Upcoming Webinars</h1>
    <table class="batch-table">
      <thead>
        <tr>
          <th>Course ID</th>
          <th>Subtopic</th>
          <th>Date</th>
          <th>Meeting Link</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($webinars as $webinar): ?>
          <tr>
            <td><?= htmlspecialchars($webinar['courseid']) ?></td>
            <td><?= htmlspecialchars($webinar['subtopic']) ?></td>
            <td><?= htmlspecialchars($webinar['webinar_date']) ?></td>
            <td><a href="<?= htmlspecialchars($webinar['meeting_link']) ?>" target="_blank">Join</a></td>
            <td><?= $webinar['is_paid'] ? 'Paid' : 'Free' ?></td>
            <td><?= $webinar['is_paid'] ? '₹' . number_format($webinar['amount'], 2) : '-' ?></td>
            <td>
              <?php if (in_array($webinar['webinarid'], $enrolledWebinars)): ?>
                <button class="enroll-btn" disabled>Enrolled</button>
              <?php else: ?>
                <a href="enroll_webinar.php?webinarid=<?= $webinar['webinarid'] ?>" class="enroll-btn primary" style="background:#0078d4;color:#fff;font-weight:700;">Enroll</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <a href="../default.php" class="btn">Back to Home</a>
  </div>

  <!-- Enrollment Modal -->
  <div class="modal-bg" id="enrollModal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);justify-content:center;align-items:center;">
    <div class="modal-auth-container">
      <h2>Enroll in Webinar</h2>
      <form id="enrollForm" method="POST">
        <input type="hidden" name="webinarid" id="modalWebinarId" value="" />
        <label>First Name *</label>
        <input type="text" name="firstname" id="modalFirstName" required value="<?= htmlspecialchars($_SESSION['firstname'] ?? '') ?>" <?= isset($_SESSION['firstname']) ? 'readonly' : '' ?> />
        <label>Last Name</label>
        <input type="text" name="lastname" id="modalLastName" value="<?= htmlspecialchars($_SESSION['lastname'] ?? '') ?>" <?= isset($_SESSION['lastname']) ? 'readonly' : '' ?> />
        <label>Email *</label>
        <input type="email" name="email" id="modalEmail" required value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" <?= isset($_SESSION['email']) ? 'readonly' : '' ?> />
        <label>Phone *</label>
        <input type="text" name="phone" id="modalPhone" required value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" <?= isset($_SESSION['phone']) ? 'readonly' : '' ?> />
        <button type="submit" class="btn primary">Submit</button>
        <button type="button" onclick="closeEnrollModal()" class="btn" style="margin-top:8px;background:#eee;color:#0078d4;">Cancel</button>
      </form>
    </div>
  </div>
  <script>
  function openEnrollModal(webinarId, subtopic) {
    document.getElementById('enrollModal').style.display = 'flex';
    document.getElementById('modalWebinarId').value = webinarId;
    <?php if (!isset($_SESSION['firstname'])): ?>
      document.getElementById('modalFirstName').value = '';
    <?php endif; ?>
    <?php if (!isset($_SESSION['lastname'])): ?>
      document.getElementById('modalLastName').value = '';
    <?php endif; ?>
    <?php if (!isset($_SESSION['email'])): ?>
      document.getElementById('modalEmail').value = '';
    <?php endif; ?>
    <?php if (!isset($_SESSION['phone'])): ?>
      document.getElementById('modalPhone').value = '';
    <?php endif; ?>
  }
  function closeEnrollModal() {
    document.getElementById('enrollModal').style.display = 'none';
  }
  window.onclick = function(event) {
    var modal = document.getElementById('enrollModal');
    if (event.target === modal) { modal.style.display = 'none'; }
  }
  </script>
  <?php
  // Handle direct POST enrollment for logged-in users
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['webinarid']) && $studentid) {
      $webinarid = intval($_POST['webinarid']);
      // Only enroll if not already enrolled
      $stmt = $pdo->prepare('SELECT COUNT(*) FROM student_webinar WHERE studentid = ? AND webinarid = ?');
      $stmt->execute([$studentid, $webinarid]);
      $already = $stmt->fetchColumn();
      if (!$already) {
          $stmt = $pdo->prepare('INSERT INTO student_webinar (studentid, webinarid) VALUES (?, ?)');
          $stmt->execute([$studentid, $webinarid]);
          echo '<script>alert("Enrollment successful!");window.location.href="webinars.php";</script>';
          exit;
      } else {
          echo '<script>alert("You are already enrolled in this webinar.");</script>';
      }
  }
  ?>
</body>
</html>
