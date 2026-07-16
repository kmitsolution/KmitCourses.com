<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['studentid'])) {
    header('Location: users/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$courseid = $_GET['courseid'] ?? null;
$price = 0;
$courseLabel = 'Course';
if ($courseid) {
    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT price, coursename FROM course WHERE courseid = ?');
        $stmt->execute([$courseid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $price = $result['price'] ?? 0;
            $courseLabel = $result['coursename'] ?? 'Course';
        }
    } catch (Exception $e) {
        $price = 0;
    }
}
if ($price <= 0) {
    $price = 0; // Contact for pricing
}
$amountInPaise = $price * 100; // Razorpay amount in paise
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment - <?= htmlspecialchars($courseLabel) ?></title>
  <link rel="stylesheet" href="assets/style.css" />
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <style>
    body { background: #0b1020; color: #e8edff; }
    .page { max-width: 600px; margin: 72px auto; padding: 28px 18px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); border-radius: 18px; }
    h1 { margin-top: 0; }
    .btn { width: 100%; max-width: 240px; display: inline-block; text-align: center; background: #0078d4; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; }
    .info { margin: 18px 0; color: rgba(255,255,255,.75); }
  </style>
</head>
<body>
  <div class="page">
    <h1>Pay for <?= htmlspecialchars($courseLabel) ?></h1>
    <?php if ($price > 0): ?>
    <ul>
      <li><strong>Fees:</strong> ₹<?= number_format($price, 0) ?></li>
    </ul>
    <button id="rzp-button1" class="btn">Pay Now</button>
    <script>
    var options = {
        "key": "YOUR_RAZORPAY_KEY_ID", // Replace with your Razorpay Key ID
        "amount": "<?= $amountInPaise ?>", // Amount in paise
        "currency": "INR",
        "name": "KMIT Solutions Services",
        "description": "Payment for <?= htmlspecialchars($courseLabel) ?>",
        "image": "assets/images/Original.png",
        "handler": function (response){
            alert('Payment successful! Payment ID: ' + response.razorpay_payment_id);
            // Here you can redirect or handle success
            window.location.href = 'default.php'; // Redirect after payment
        },
        "prefill": {
            "name": "", // Can add user name if available
            "email": "",
            "contact": ""
        },
        "theme": {
            "color": "#0078d4"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
    </script>
    <?php else: ?>
    <p class="info">Price not available. Please contact us for pricing.</p>
    <a href="mailto:info@kmitcourses.com" class="btn">Contact Us</a>
    <?php endif; ?>
    <p class="info" style="margin-top:18px;"><a href="courses/DevOps/devops.php" style="color:#5ab0ff;">Back to course details</a></p>
  </div>
</body>
</html>
