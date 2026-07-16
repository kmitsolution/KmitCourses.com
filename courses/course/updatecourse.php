<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['is_admin'])) {
  header('Location: ../users/login.php');
  exit;
}
require_once __DIR__ . '/../../config.php';
$pdo = getPDO();
$message = '';
$courseid = isset($_GET['courseid']) ? intval($_GET['courseid']) : 0;
if (!$courseid) {
  header('Location: courseadmin.php');
  exit;
}
// Fetch course details
$course = null;
try {
  $stmt = $pdo->prepare('SELECT c.*, cc.categoryid FROM course c LEFT JOIN course_category_link cl ON c.courseid = cl.courseid LEFT JOIN course_category cc ON cl.categoryid = cc.categoryid WHERE c.courseid = ?');
  $stmt->execute([$courseid]);
  $course = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmt->closeCursor();
} catch (Exception $e) {
  $course = null;
}
if (!$course) {
  header('Location: courseadmin.php');
  exit;
}
// Fetch categories
$categories = [];
try {
  $catStmt = $pdo->query('SELECT categoryid, categoryname FROM course_category');
  $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
  $catStmt->closeCursor();
} catch (Exception $e) {
  $categories = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
  $coursename = trim($_POST['coursename'] ?? '');
  $categoryid = intval($_POST['categoryid'] ?? 0);
  $image = trim($_POST['image'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $page_slug = trim($_POST['page_slug'] ?? '');
  $price = $_POST['price'] !== '' ? floatval($_POST['price']) : null;
  $type = $_POST['type'] ?? 'PAID';
  try {
    $stmt = $pdo->prepare('CALL UpdateCourse(?,?,?,?,?,?,?)');
    $stmt->execute([$courseid, $coursename, $image, $description, $page_slug, $price, $type]);
    $stmt->closeCursor();
    // Update category link
    $stmt2 = $pdo->prepare('DELETE FROM course_category_link WHERE courseid = ?');
    $stmt2->execute([$courseid]);
    $stmt2->closeCursor();
    if ($categoryid) {
      $stmt3 = $pdo->prepare('INSERT INTO course_category_link (courseid, categoryid) VALUES (?, ?)');
      $stmt3->execute([$courseid, $categoryid]);
      $stmt3->closeCursor();
    }
    $message = 'Course updated successfully.';
    // Refresh course data
    header('Location: courseadmin.php');
    exit;
  } catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Course</title>
  <link rel="stylesheet" href="../../assets/style.css">
  <style>
    .form-section { background:#f8fafc; border-radius:12px; padding:24px; margin-bottom:32px; box-shadow:0 2px 12px #00214711; }
    .form-section h2 { margin-top:0; color:#002147; }
    .form-group { margin-bottom:16px; }
    .form-group label { font-weight:600; display:block; margin-bottom:6px; color:#002147; }
    .form-group input, .form-group select, .form-group textarea { width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; font-size:1rem; color:#002147; }
    .form-actions { display:flex; gap:12px; justify-content:flex-end; }
    .btn { padding:8px 14px; border-radius:7px; border:none; font-weight:600; cursor:pointer; }
    .btn.primary { background:#0078d4; color:#fff; }
    .btn.danger { background:#d32f2f; color:#fff; }
    .btn.secondary { background:#6c757d; color:#fff; }
  </style>
  <script>
    function openGallery() {
      const galleryWindow = window.open('../../gallery/gallery.php?select=1', 'gallery', 'width=800,height=600,scrollbars=yes,resizable=yes');
      window.addEventListener('message', function(event) {
        if (event.origin !== window.location.origin) return;
        if (event.data.selectedImage) {
          const imagePath = event.data.selectedImage;
          const filename = imagePath.split('/').pop();
          document.getElementById('image').value = filename;
          galleryWindow.close();
        }
      });
    }
  </script>
</head>
<body>
<div class="container">
  <div class="form-section" style="max-width:600px;margin:auto;">
    <h2 style="color:#081326;">Update Course</h2>
    <?php if ($message) echo '<div style="color:#0078d4;font-weight:600;margin-bottom:12px;">'.htmlspecialchars($message).'</div>'; ?>
    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="coursename">Course Name:</label>
        <input type="text" name="coursename" id="coursename" value="<?= htmlspecialchars($course['coursename']) ?>" required>
      </div>
      <div class="form-group">
        <label for="categoryid">Category:</label>
        <select name="categoryid" id="categoryid" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['categoryid'] ?>" <?= $cat['categoryid'] == $course['categoryid'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['categoryname']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="type">Type:</label>
        <select name="type" id="type" required>
          <option value="PAID" <?= $course['type'] === 'PAID' ? 'selected' : '' ?>>PAID</option>
          <option value="FREE" <?= $course['type'] === 'FREE' ? 'selected' : '' ?>>FREE</option>
        </select>
      </div>
      <div class="form-group">
        <label for="image">Image:</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="text" name="image" id="image" value="<?= htmlspecialchars($course['image']) ?>" readonly style="flex:1;">
          <button type="button" onclick="openGallery()" class="btn primary" style="padding:6px 12px;font-size:0.9rem;">Select from Gallery</button>
        </div>
        <?php if ($course['image']): ?>
          <div style="margin-top:8px;">
            <img src="../assets/images/<?= htmlspecialchars($course['image']) ?>" alt="Current image" style="max-width:100px;max-height:75px;border-radius:4px;object-fit:cover;">
          </div>
        <?php endif; ?>
      </div>
      <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($course['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="page_slug">Page Slug:</label>
        <input type="text" name="page_slug" id="page_slug" value="<?= htmlspecialchars($course['page_slug']) ?>">
      </div>
      <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($course['price']) ?>">
      </div>
      <div class="form-actions">
        <a href="courseadmin.php" class="btn secondary" style="text-decoration:none;">Cancel</a>
        <button type="submit" name="action" value="update" class="btn primary">Update</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
