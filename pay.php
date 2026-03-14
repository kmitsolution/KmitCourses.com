<?php
// pay.php - simple placeholder payment page
$course = $_GET['course'] ?? 'unknown';
$courseLabel = 'Course';
if ($course === 'aws-saa') {
    $courseLabel = 'AWS Solution Architect Associate (SAA)';
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment - <?= htmlspecialchars($courseLabel) ?></title>
  <link rel="stylesheet" href="assets/style.css" />
  <style>
    body { background: #0b1020; color: #e8edff; }
    .page { max-width: 600px; margin: 72px auto; padding: 28px 18px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); border-radius: 18px; }
    h1 { margin-top: 0; }
    .btn { width: 100%; max-width: 240px; display: inline-block; text-align: center; }
    .info { margin: 18px 0; color: rgba(255,255,255,.75); }
  </style>
</head>
<body>
  <div class="page">
    <h1>Pay for <?= htmlspecialchars($courseLabel) ?></h1>
    <p class="info">This is a placeholder payment page. In a real deployment, integrate with your payment gateway (Razorpay, PayPal, Stripe, etc.) and redirect the user to complete the transaction.</p>
    <ul>
      <li><strong>Total Hours:</strong> 32</li>
      <li><strong>Fees:</strong> ₹7,080</li>
    </ul>
    <p class="info">After completing the payment, please send a message to <a href="mailto:info@kmitcourses.com">info@kmitcourses.com</a> or call <a href="tel:+919739299502">+91-9739299502</a> with your receipt.</p>
    <a href="mailto:info@kmitcourses.com?subject=Payment%20Completed%20for%20<?= urlencode($courseLabel) ?>" class="btn primary">Notify Us After Payment</a>
    <p class="info" style="margin-top:18px;"><a href="courses/Aws/SAA.php" style="color:#5ab0ff;">Back to course details</a></p>
  </div>
</body>
</html>
