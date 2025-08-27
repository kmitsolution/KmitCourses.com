<?php
require_once '../config.php';

// Handle search
$searchResults = [];
$searchType = '';
$searchValue = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_type'], $_POST['search_value'])) {
    $searchType = $_POST['search_type'];
    $searchValue = trim($_POST['search_value']);
    $pdo = getPDO();
    if ($searchType === 'mobile') {
        $stmt = $pdo->prepare('CALL get_student_by_mobile(?)');
        $stmt->execute([$searchValue]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
    } elseif ($searchType === 'email') {
        $stmt = $pdo->prepare('CALL get_student_by_email(?)');
        $stmt->execute([$searchValue]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
    } elseif ($searchType === 'username') {
        $stmt = $pdo->prepare('CALL get_student_by_username(?)');
        $stmt->execute([$searchValue]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
    }
} else {
// Pagination (using stored procedures only)
$pdo = getPDO();
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;
// Get total count using stored procedure
$countStmt = $pdo->prepare('CALL list_all_students()');
$countStmt->execute();
$allStudents = $countStmt->fetchAll(PDO::FETCH_ASSOC);
$countStmt->closeCursor();
$totalStudents = count($allStudents);
$totalPages = ceil($totalStudents / $perPage);
// Get paginated students from the result set
$searchResults = $allStudents;
}

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $username = $_GET['delete'];
    $pdo = getPDO();
    // Check if user is admin
    $adminCheck = $pdo->prepare('CALL is_student_admin(?)');
    $adminCheck->execute([$username]);
    $isAdmin = $adminCheck->fetch() ? true : false;
    $adminCheck->closeCursor();
    if ($isAdmin) {
        echo "<script>alert('Admin users cannot be deleted.');window.location.href='userlist.php';</script>";
        exit;
    } else {
        $stmt = $pdo->prepare('CALL delete_student_by_username(?)');
        $stmt->execute([$username]);
        $stmt->closeCursor();
        header('Location: userlist.php');
        exit;
    }
}

// Handle update (redirect to a separate update page or modal, not implemented here)
// ...

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        .student-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        .student-table th, .student-table td { border: 1px solid #e0e0e0; padding: 10px 8px; text-align: left; color: #fff; }
        .student-table th { background: #f3f4f6; }
        .student-table tr.selected { background: #e6f2ff; }
        .actions button { margin-right: 8px; }
        .search-bar { margin: 24px 0 0 0; display: flex; gap: 12px; align-items: center; }
        .search-bar select, .search-bar input { padding: 7px 10px; border-radius: 6px; border: 1px solid #ccc; }
        .search-bar button { padding: 7px 18px; border-radius: 6px; background: #0078d4; color: #fff; border: none; font-weight: 600; cursor: pointer; }
        .action-icon {
            transition: background 0.2s, color 0.2s;
            border-radius: 6px;
            padding: 4px 7px;
            display: inline-block;
            cursor: pointer;
        }
        .action-icon:hover {
            background: #e6f2ff;
            color: #0078d4 !important;
            text-decoration: none;
            cursor: pointer;
        }
        .delete-icon:hover {
            background: #ffeaea;
            color: #c00 !important;
        }
        .update-icon:hover {
            background: #e6f2ff;
            color: #0078d4 !important;
        }
        .reset-icon:hover {
            background: #f3f4f6;
            color: #0078d4 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div style="margin-bottom:8px;">
      <h2 style="color:white;margin:0 0 18px 0;white-space:nowrap;font-size:1.5em;text-align:center;width:100%;">Student List</h2>
      <form method="post" class="search-bar" style="margin:0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
          <label for="search_type" style="font-size:0.96em;">Search by:</label>
          <select name="search_type" id="search_type" required style="font-size:0.96em;min-width:80px;">
              <option value="">Select</option>
              <option value="mobile" <?= $searchType==='mobile'?'selected':'' ?>>Mobile</option>
              <option value="email" <?= $searchType==='email'?'selected':'' ?>>Email</option>
              <option value="username" <?= $searchType==='username'?'selected':'' ?>>Username</option>
          </select>
          <input type="text" name="search_value" id="search_value" value="<?= htmlspecialchars($searchValue) ?>" required placeholder="Enter value..." oninput="filterTable()" style="font-size:0.96em;max-width:120px;">
          <button type="button" onclick="filterTable()" style="font-size:0.96em;padding:5px 10px;">Search</button>
          <button type="button" onclick="window.open('signup.php?from=userlist', '_blank');" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#28a745;color:#fff;border:none;font-weight:600;font-size:0.96em;">Add User</button>
          <button type="button" onclick="window.location.reload();" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#0078d4;color:#fff;border:none;font-weight:600;font-size:0.96em;">Refresh</button>
          <?php if ($totalPages > 1): ?>
            <div class="pagination" style="display:flex;gap:2px;align-items:center;margin-left:10px;">
                <?php
                $firstDisabled = $page == 1 ? 'pointer-events:none;opacity:0.5;' : '';
                $prevDisabled = $page == 1 ? 'pointer-events:none;opacity:0.5;' : '';
                $nextDisabled = $page == $totalPages ? 'pointer-events:none;opacity:0.5;' : '';
                $lastDisabled = $page == $totalPages ? 'pointer-events:none;opacity:0.5;' : '';
                ?>
                <a href="?page=1" title="First Page" style="padding:1px 4px;border-radius:4px;background:#f3f4f6;color:#0078d4;text-decoration:none;font-size:0.92em;<?= $firstDisabled ?>">&#171;</a>
                <a href="?page=<?= max(1, $page-1) ?>" title="Previous Page" style="padding:1px 4px;border-radius:4px;background:#f3f4f6;color:#0078d4;text-decoration:none;font-size:0.92em;<?= $prevDisabled ?>">&#60;</a>
                <span style="padding:1px 4px;font-weight:700;color:#0078d4;font-size:0.92em;">Page <?= $page ?> of <?= $totalPages ?></span>
                <a href="?page=<?= min($totalPages, $page+1) ?>" title="Next Page" style="padding:1px 4px;border-radius:4px;background:#f3f4f6;color:#0078d4;text-decoration:none;font-size:0.92em;<?= $nextDisabled ?>">&#62;</a>
                <a href="?page=<?= $totalPages ?>" title="Last Page" style="padding:1px 4px;border-radius:4px;background:#f3f4f6;color:#0078d4;text-decoration:none;font-size:0.92em;<?= $lastDisabled ?>">&#187;</a>
            </div>
          <?php endif; ?>
      </form>
    </div>
    <form method="post" id="studentForm">
    <table class="student-table">
        <thead>
            <tr>
                <th style="color:#081326"></th>
                <th style="color:#081326">Username</th>
                <th style="color:#081326">First Name</th>
                <th style="color:#081326">Last Name</th>
                <th style="color:#081326">Email</th>
                <th style="color:#081326">Mobile</th>
                <th style="color:#081326">Date of Joining</th>
                <th style="color:#081326">Actions</th>
            </tr>
        </thead>
        <tbody id="studentTableBody">
        <?php foreach ($searchResults as $i => $row):
            $pageNum = floor($i / $perPage) + 1;
        ?>
            <tr class="student-row" data-page="<?= $pageNum ?>" data-username="<?= htmlspecialchars(strtolower($row['username'])) ?>" data-email="<?= htmlspecialchars(strtolower($row['email'])) ?>" data-mobile="<?= htmlspecialchars(strtolower($row['mobile'])) ?>" style="display:none;">
                <td><input type="radio" name="selected_student" value="<?= htmlspecialchars($row['username']) ?>"></td>
                <td class="username"><?= htmlspecialchars($row['username']) ?></td>
                <td class="firstname"><?= htmlspecialchars($row['firstname']) ?></td>
                <td class="lastname"><?= htmlspecialchars($row['lastname']) ?></td>
                <td class="email"><?= htmlspecialchars($row['email']) ?></td>
                <td class="mobile"><?= htmlspecialchars($row['mobile']) ?></td>
                <td class="date_of_joining"><?= htmlspecialchars($row['date_of_joining']) ?></td>
                <td class="actions" style="min-width:100px;display:flex;gap:8px;align-items:center;">
                    <a href="userlist.php?delete=<?= urlencode($row['username']) ?>" onclick="return confirm('Delete this student?');" title="Delete" class="action-icon delete-icon"><span>🗑️</span></a>
                    <a href="update_student.php?username=<?= urlencode($row['username']) ?>" title="Update" class="action-icon update-icon"><span>✏️</span></a>
                    <a href="#" onclick="openResetPasswordModal('<?= htmlspecialchars($row['username']) ?>');return false;" title="Reset Password" class="action-icon reset-icon"><span>🔑</span></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </form>
</div>

<!-- Password Reset Modal -->
<div id="resetPasswordModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:32px 28px 18px 28px;border-radius:10px;max-width:350px;width:100%;margin:120px auto 0 auto;position:relative;">
    <h3 style="margin-top:0;color:#111;">Reset Password</h3>
    <form id="resetPasswordForm" method="post" autocomplete="off" onsubmit="return submitResetPassword();">
      <input type="hidden" name="reset_username" id="reset_username" value="">
      <label for="new_password" style="color:#222;font-weight:600;">New Password</label>
      <input type="password" name="new_password" id="new_password" required style="width:100%;padding:8px 10px;margin-bottom:18px;border-radius:6px;border:1px solid #ccc;">
      <button type="submit" style="padding:9px 22px;border-radius:6px;background:#0078d4;color:#fff;border:none;font-weight:700;">Update</button>
      <button type="button" onclick="closeResetPasswordModal();" style="margin-left:12px;padding:9px 22px;border-radius:6px;background:#eee;color:#222;border:none;font-weight:600;">Cancel</button>
      <div id="resetPasswordMsg" style="margin-top:12px;color:#c00;font-size:0.98em;"></div>
    </form>
  </div>
</div>

<script>
function showPage(page) {
  var rows = document.querySelectorAll('.student-row');
  for (var i = 0; i < rows.length; i++) {
    rows[i].style.display = (rows[i].getAttribute('data-page') == page) ? '' : 'none';
  }
}

function filterTable() {
  var input = document.getElementById('search_value').value.toLowerCase();
  var type = document.getElementById('search_type').value;
  var rows = document.querySelectorAll('.student-row');
  var pagDiv = document.querySelector('.pagination');
  var searching = input.length > 0;
  var count = 0;
  for (var i = 0; i < rows.length; i++) {
    var show = false;
    if (searching) {
      if (type === 'mobile') {
        var val = rows[i].getAttribute('data-mobile');
        if (val && val.indexOf(input) !== -1) show = true;
      } else if (type === 'email') {
        var val = rows[i].getAttribute('data-email');
        if (val && val.indexOf(input) !== -1) show = true;
      } else if (type === 'username') {
        var val = rows[i].getAttribute('data-username');
        if (val && val.indexOf(input) !== -1) show = true;
      } else {
        show = true;
      }
      rows[i].style.display = show ? '' : 'none';
      if (show) count++;
    } else {
      // Not searching, show only current page
      var page = <?= $page ?>;
      rows[i].style.display = (rows[i].getAttribute('data-page') == page) ? '' : 'none';
    }
  }
  if (pagDiv) {
    pagDiv.style.display = searching ? 'none' : '';
  }
}

// On page load, show only current page
window.onload = function() {
  filterTable();
}
function openResetPasswordModal(username) {
  document.getElementById('reset_username').value = username;
  document.getElementById('new_password').value = '';
  document.getElementById('resetPasswordMsg').innerText = '';
  document.getElementById('resetPasswordModal').style.display = 'flex';
}
function closeResetPasswordModal() {
  document.getElementById('resetPasswordModal').style.display = 'none';
}
function submitResetPassword() {
  var username = document.getElementById('reset_username').value;
  var newPassword = document.getElementById('new_password').value;
  if (!newPassword) {
    document.getElementById('resetPasswordMsg').innerText = 'Password required.';
    return false;
  }
  // Password complexity check (same as signup)
  var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d]).{8,}$/;
  if (!regex.test(newPassword)) {
    document.getElementById('resetPasswordMsg').innerText = 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.';
    return false;
  }
  // AJAX request to reset password
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'userlist.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      var msg = xhr.responseText;
      if (msg.indexOf('success') !== -1) {
        document.getElementById('resetPasswordMsg').style.color = '#090';
        document.getElementById('resetPasswordMsg').innerText = 'Password updated successfully!';
        setTimeout(closeResetPasswordModal, 1200);
      } else {
        document.getElementById('resetPasswordMsg').style.color = '#c00';
        document.getElementById('resetPasswordMsg').innerText = msg;
      }
    }
  };
  xhr.send('action=reset_password&reset_username=' + encodeURIComponent(username) + '&new_password=' + encodeURIComponent(newPassword));
  return false;
}
</script>

<?php
// Handle password reset AJAX
if (isset($_POST['action']) && $_POST['action'] === 'reset_password' && isset($_POST['reset_username'], $_POST['new_password'])) {
    $resetUsername = $_POST['reset_username'];
    $newPassword = $_POST['new_password'];
    // Use same hash as signup
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $pdo = getPDO();
    $stmt = $pdo->prepare('CALL update_student_password(?, ?, ?)');
    $stmt->execute([$resetUsername, '', $hashed]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    if ($result && isset($result['message'])) {
        if (stripos($result['message'], 'success') !== false) {
            echo 'success';
        } else {
            echo $result['message'];
        }
    } else {
        echo 'Unknown error.';
    }
    exit;
}
?>
