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
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['is_admin'])) {
  header('Location: ../users/login.php');
  exit;
}
require_once __DIR__ . '/../../config.php';
$pdo = getPDO();
// Handle delete via GET (delete icon)
if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $courseid = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare('CALL DeleteCourse(?)');
        $stmt->execute([$courseid]);
        $stmt->closeCursor();
        header('Location: courseadmin.php');
        exit;
    } catch (Exception $e) {
        $message = 'Error deleting course: ' . $e->getMessage();
    }
}

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $coursename = trim($_POST['coursename'] ?? '');
    $categoryid = intval($_POST['categoryid'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $page_slug = trim($_POST['page_slug'] ?? '');
    $price = $_POST['price'] !== '' ? floatval($_POST['price']) : null;
    $type = $_POST['type'] ?? 'PAID';
    $courseid = intval($_POST['courseid'] ?? 0);

    try {
      if ($action === 'add') {
        $stmt = $pdo->prepare('CALL AddCourse(?,?,?,?,?,?)');
        $stmt->execute([$coursename, $image, $description, $page_slug, $price, $type]);
        $stmt->closeCursor();
        // Link to category
        $stmt2 = $pdo->prepare('CALL GetCourseIdByName(?, @cid)');
        $stmt2->execute([$coursename]);
        $stmt2->closeCursor();
        $cid = $pdo->query('SELECT @cid')->fetchColumn();
        $stmt3 = $pdo->prepare('CALL AddCourseToCategory(?, ?)');
        $stmt3->execute([$cid, $categoryid]);
        $stmt3->closeCursor();
        $message = 'Course added successfully.';
      } elseif ($action === 'update' && $courseid) {
        $stmt = $pdo->prepare('CALL UpdateCourse(?,?,?,?,?,?,?)');
        $stmt->execute([$courseid, $coursename, $image, $description, $page_slug, $price, $type]);
        $stmt->closeCursor();
        $message = 'Course updated successfully.';
      } elseif ($action === 'delete' && $courseid) {
        $stmt = $pdo->prepare('CALL DeleteCourse(?)');
        $stmt->execute([$courseid]);
        $stmt->closeCursor();
        $message = 'Course deleted.';
      }
    } catch (Exception $e) {
      $message = 'Error: ' . $e->getMessage();
    }
  }
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
// Fetch courses
$courses = [];
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;
$searchType = isset($_POST['search_type']) ? $_POST['search_type'] : '';
$searchValue = isset($_POST['search_value']) ? trim($_POST['search_value']) : '';
try {
  $pdo = getPDO();
  $allStmt = $pdo->query('CALL ListCoursesWithCategories()');
  $allCourses = $allStmt->fetchAll(PDO::FETCH_ASSOC);
  $allStmt->closeCursor();
  // Filter in PHP like userlist.php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $searchType && $searchValue) {
    $searchValueLower = mb_strtolower($searchValue);
    if ($searchType === 'category') {
      $allCourses = array_filter($allCourses, function($course) use ($searchValueLower) {
        return mb_strpos(mb_strtolower($course['categoryname']), $searchValueLower) !== false;
      });
    } elseif ($searchType === 'coursename') {
      $allCourses = array_filter($allCourses, function($course) use ($searchValueLower) {
        return mb_strpos(mb_strtolower($course['coursename']), $searchValueLower) !== false;
      });
    }
    $allCourses = array_values($allCourses); // reindex
  }
  $totalCourses = count($allCourses);
  $totalPages = ceil($totalCourses / $perPage);
  $courses = array_slice($allCourses, $offset, $perPage);
} catch (Exception $e) {
  $courses = [];
  $totalPages = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course Admin</title>
  <link rel="stylesheet" href="../../assets/style.css">
  <style>
    .course-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
    .course-table th, .course-table td { border: 1px solid #e0e0e0; padding: 10px 8px; text-align: left; color: #fff; }
    .course-table th { background: #f3f4f6; color: #081326; }
    .course-table tr.selected { background: #e6f2ff; }
    .actions button { margin-right: 8px; }
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
    .form-section { background:#f8fafc; border-radius:12px; padding:24px; margin-bottom:32px; box-shadow:0 2px 12px #00214711; }
    .form-section h2 { margin-top:0; }
    .form-group { margin-bottom:16px; }
    .form-group label { font-weight:600; display:block; margin-bottom:6px; }
    .form-group input, .form-group select, .form-group textarea { width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; font-size:1rem; }
    .form-actions { display:flex; gap:12px; }
    .btn { padding:8px 14px; border-radius:7px; border:none; font-weight:600; cursor:pointer; }
    .btn.primary { background:#0078d4; color:#fff; }
    .btn.danger { background:#d32f2f; color:#fff; }
  </style>
</head>
<body>
<div class="container">
  <div style="margin-bottom:8px;">
    <h2 style="color:white;margin:0 0 18px 0;white-space:nowrap;font-size:1.5em;text-align:center;width:100%;">Course List</h2>
    <form method="post" class="search-bar" id="courseSearchForm" style="margin:0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <label for="search_type" style="font-size:0.96em;">Search by:</label>
      <select name="search_type" id="search_type" required style="font-size:0.96em;min-width:80px;">
        <option value="">Select</option>
        <option value="category" <?= isset($_POST['search_type']) && $_POST['search_type']==='category'?'selected':'' ?>>Category</option>
        <option value="coursename" <?= isset($_POST['search_type']) && $_POST['search_type']==='coursename'?'selected':'' ?>>Course Name</option>
      </select>
      <input type="text" name="search_value" id="search_value" value="<?= isset($_POST['search_value']) ? htmlspecialchars($_POST['search_value']) : '' ?>" required placeholder="Enter value..." style="font-size:0.96em;max-width:120px;">
      <button type="submit" style="font-size:0.96em;padding:5px 10px;cursor:pointer;display:none;">Search</button>
      <button type="button" onclick="window.location.href='addcourse.php';" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#28a745;color:#fff;border:none;font-weight:600;font-size:0.96em;cursor:pointer;">Add Course</button>
      <button type="button" onclick="window.location.reload();" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#0078d4;color:#fff;border:none;font-weight:600;font-size:0.96em;cursor:pointer;">Refresh</button>
      <?php if ($totalPages > 1): ?>
        <div class="pagination" style="display:flex;gap:2px;align-items:center;margin-left:12px;justify-content:center;">
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
    <form method="post" id="courseForm">
    <table class="course-table" style="table-layout:fixed;width:100%;">
      <thead>
        <tr>
          <th style="width:160px;">Name</th>
          <th style="width:120px;">Category</th>
          <th style="width:80px;">Type</th>
          <th style="width:80px;">Price</th>
          <th style="width:140px;">Image</th>
          <th style="width:120px;">Page Slug</th>
          <th style="width:110px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($courses as $course): ?>
          <tr>
            <td style="word-break:break-word;"><?php echo htmlspecialchars($course['coursename']); ?></td>
            <td style="word-break:break-word;"><?php echo htmlspecialchars($course['categoryname']); ?></td>
            <td><?php echo htmlspecialchars($course['Type']); ?></td>
            <td><?php echo htmlspecialchars($course['price']); ?></td>
            <td style="word-break:break-word;"><?php echo htmlspecialchars($course['image']); ?></td>
            <td style="word-break:break-word;"><?php echo htmlspecialchars($course['page_slug']); ?></td>
            <td class="actions" style="min-width:110px;max-width:110px;gap:8px;align-items:center;">
              <a href="courseadmin.php?delete=<?php echo urlencode($course['courseid']); ?>" onclick="return confirm('Delete this course?');" title="Delete" class="action-icon delete-icon"><span>🗑️</span></a>
              <a href="updatecourse.php?courseid=<?php echo urlencode($course['courseid']); ?>" title="Update" class="action-icon update-icon"><span>✏️</span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </form>
    <!-- Pagination shown after refresh button -->
  </div>
</div>
  <script>
    function fillForm(course) {
      document.getElementById('courseid').value = course.courseid;
      document.getElementById('coursename').value = course.coursename;
      document.getElementById('image').value = course.image;
      document.getElementById('description').value = course.description;
      document.getElementById('page_slug').value = course.page_slug;
      document.getElementById('price').value = course.price;
      document.getElementById('type').value = course.Type;
      // Set category
      fetch('get_category_for_course.php?courseid=' + course.courseid)
        .then(response => response.json())
        .then(data => {
          document.getElementById('categoryid').value = data.categoryid;
        });
    }
    function resetForm() {
      document.getElementById('courseid').value = '';
      document.getElementById('coursename').value = '';
      document.getElementById('image').value = '';
      document.getElementById('description').value = '';
      document.getElementById('page_slug').value = '';
      document.getElementById('price').value = '';
      document.getElementById('type').value = 'PAID';
      document.getElementById('categoryid').value = '';
    }
    document.getElementById('search_value').addEventListener('input', function() {
      // Use AJAX to submit search without page refresh
      var form = document.getElementById('courseSearchForm');
      var formData = new FormData(form);
      var xhr = new XMLHttpRequest();
      xhr.open('POST', window.location.pathname, true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          var parser = new DOMParser();
          var doc = parser.parseFromString(xhr.responseText, 'text/html');
          var newTable = doc.querySelector('.course-table');
          var oldTable = document.querySelector('.course-table');
          if (newTable && oldTable) {
            oldTable.parentNode.replaceChild(newTable, oldTable);
          }
        }
      };
      xhr.send(formData);
    });
    // Instant search/filter like userlist.php
    document.getElementById('search_value').addEventListener('input', function() {
      var filter = this.value.toLowerCase();
      var searchType = document.getElementById('search_type').value;
      var rows = document.querySelectorAll('.course-table tbody tr');
      rows.forEach(function(row) {
        var nameCell = row.querySelector('td');
        var categoryCell = row.querySelectorAll('td')[1];
        var match = false;
        if (searchType === 'coursename' && nameCell) {
          match = nameCell.textContent.toLowerCase().indexOf(filter) !== -1;
        } else if (searchType === 'category' && categoryCell) {
          match = categoryCell.textContent.toLowerCase().indexOf(filter) !== -1;
        } else if (!searchType) {
          match = true;
        }
        row.style.display = match ? '' : 'none';
      });
    });
  </script>
</body>
</html>
