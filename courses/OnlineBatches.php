<?php
require_once '../config.php';
session_start();
$pdo = getPDO();
$studentid = isset($_SESSION['studentid']) ? $_SESSION['studentid'] : null;
$enrollSuccess = false;
$enrollError = '';

// Handle enrollment POST
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batchid']) && $studentid && isset($_POST['payment_reference'])
) {
    $batchid = intval($_POST['batchid']);
    $payment_reference = trim($_POST['payment_reference']);
    // Get course price for this batch
    $stmt = $pdo->prepare('SELECT c.price FROM livebatches b JOIN course c ON b.courseid = c.courseid WHERE b.Id = ?');
    $stmt->execute([$batchid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $amount = $row ? $row['price'] : 0;
    // Call stored procedure to enroll after payment
    $stmt = $pdo->prepare('CALL enroll_student_after_payment(?, ?, ?, ?)');
    if ($stmt->execute([$studentid, $batchid, $amount, $payment_reference])) {
        $enrollSuccess = true;
    } else {
        $enrollError = 'Could not enroll. Please try again.';
    }
}

// Fetch all active batches (enddate >= today)
$batches = [];
try {
    $stmt = $pdo->query('SELECT b.Id, b.mode, b.courseid, b.startdate, b.enddate, c.coursename, c.price FROM livebatches b JOIN course c ON b.courseid = c.courseid WHERE b.enddate >= CURDATE() ORDER BY b.courseid, b.startdate');
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $batches = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Batches</title>
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
      .agreement-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.35); justify-content: center; align-items: center; }
      .agreement-content { background: #fff; color: #222; border-radius: 12px; max-width: 98vw; width: 370px; padding: 28px 24px 18px 24px; box-shadow: 0 8px 32px #0002; position: relative; }
      .agreement-content h2 { color: var(--acc); margin-top: 0; }
      .agreement-content label { font-size: 0.98rem; }
      .agreement-content button { margin-top: 16px; }
    </style>
</head>
<body>
  <div class="container">
    <h1>Online Batches</h1>
    <?php if ($enrollSuccess): ?>
      <div style="color:green;font-weight:600;margin-bottom:18px;">Enrollment successful! You will be contacted soon.</div>
    <?php elseif ($enrollError): ?>
      <div style="color:#c00;font-weight:600;margin-bottom:18px;"><?= htmlspecialchars($enrollError) ?></div>
    <?php endif; ?>
    <table class="batch-table">
      <thead>
        <tr>
          <th>Course</th>
          <th>Mode</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($batches as $batch): ?>
          <tr>
            <td><?= htmlspecialchars($batch['coursename']) ?></td>
            <td><?= htmlspecialchars($batch['mode']) ?></td>
            <td><?= htmlspecialchars($batch['startdate']) ?></td>
            <td><?= htmlspecialchars($batch['enddate']) ?></td>
            <td>₹<?= number_format($batch['price'], 2) ?></td>
            <td>
              <?php if (!$studentid): ?>
                <a href="../users/login.php" class="enroll-btn">Login to Enroll</a>
              <?php else: ?>
                <button class="enroll-btn" data-batchid="<?= $batch['Id'] ?>" data-coursename="<?= htmlspecialchars($batch['coursename']) ?>" data-price="<?= $batch['price'] ?>">Enroll</button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <a href="../default.php" class="btn">Back to Home</a>
  </div>
  <footer style="padding:32px 0 24px 0; color:var(--muted); border-top:1px solid rgba(255,255,255,.08); text-align:center; margin-top:40px;">
    <div style="display:flex;justify-content:center;gap:18px;align-items:center;flex-wrap:wrap;">
      <a href="https://www.facebook.com/kmitdevops/" target="_blank" rel="noopener" title="Facebook" style="display:inline-flex;align-items:center;"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" alt="Facebook" style="width:22px;height:22px;filter:invert(0.5) sepia(1) hue-rotate(180deg);margin-right:6px;"/>Facebook</a>
      <a href="https://instagram.com/" target="_blank" rel="noopener" title="Instagram" style="display:inline-flex;align-items:center;"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/instagram.svg" alt="Instagram" style="width:22px;height:22px;filter:invert(0.5) sepia(1) hue-rotate(290deg);margin-right:6px;"/>Instagram</a>
      <a href="https://wa.me/9739299502" target="_blank" rel="noopener" title="WhatsApp" style="display:inline-flex;align-items:center;"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/whatsapp.svg" alt="WhatsApp" style="width:22px;height:22px;filter:invert(0.5) sepia(1) hue-rotate(90deg);margin-right:6px;"/>WhatsApp</a>
      <a href="https://twitter.com/" target="_blank" rel="noopener" title="Twitter" style="display:inline-flex;align-items:center;"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/twitter.svg" alt="Twitter" style="width:22px;height:22px;filter:invert(0.5) sepia(1) hue-rotate(180deg);margin-right:6px;"/>Twitter</a>
    </div>
    <div style="margin-top:12px;font-size:13px;">&copy; <?= date('Y') ?> KMIT Solutions Services</div>
  </footer>
  <!-- Agreement Modal -->
  <div class="agreement-modal" id="agreementModal">
    <div class="agreement-content">
      <h2>Enrollment Agreement</h2>
      <form id="agreementForm" method="POST">
        <input type="hidden" name="batchid" id="modalBatchId" value="" />
        <div style="margin-bottom:12px;">
          <label><input type="checkbox" id="agreeCheck" required /> I agree to the terms and conditions of joining this batch.</label>
        </div>
        <div style="margin-bottom:12px;">
          <div id="payAmount" style="font-weight:600;color:var(--acc);margin-bottom:6px;"></div>
          <input type="text" name="payment_reference" id="paymentReference" placeholder="Enter Payment Reference" required style="width:100%;padding:7px 8px;border-radius:6px;border:1px solid #ccc;" />
          <a href="https://razorpay.me/@kmitsolutionsservices" target="_blank" class="btn primary" style="width:100%;text-align:center;margin-top:8px;">Pay Now</a>
        </div>
        <!-- <button type="submit" class="btn primary" style="width:100%;">Confirm Enrollment</button> -->
      </form>
      <button onclick="closeModal()" class="btn" style="width:100%;margin-top:8px;background:#eee;color:#0078d4;">Cancel</button>
    </div>
  </div>
  <script>
    // Modal logic
    const batchPrices = {};
    <?php foreach ($batches as $batch): ?>
      batchPrices[<?= $batch['Id'] ?>] = <?= json_encode($batch['price']) ?>;
    <?php endforeach; ?>
    function closeModal() {
      document.getElementById('agreementModal').style.display = 'none';
    }
    document.querySelectorAll('.enroll-btn[data-batchid]').forEach(function(btn) {
      btn.onclick = function() {
        document.getElementById('agreementModal').style.display = 'flex';
        document.getElementById('modalBatchId').value = btn.getAttribute('data-batchid');
        var price = batchPrices[btn.getAttribute('data-batchid')];
        document.getElementById('payAmount').textContent = 'Amount to Pay: ₹' + parseFloat(price).toFixed(2);
      };
    });
    document.getElementById('agreementForm').onsubmit = function(e) {
      if (!document.getElementById('agreeCheck').checked) {
        alert('You must agree to the terms.');
        e.preventDefault();
        return false;
      }
      if (!document.getElementById('paymentReference').value.trim()) {
        alert('Please enter your payment reference after payment.');
        e.preventDefault();
        return false;
      }
    };
    document.getElementById('agreementModal').onclick = function(e) {
      if (e.target === this) closeModal();
    };
  </script>
</body>
</html>
