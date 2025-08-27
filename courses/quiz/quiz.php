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
    $quiz_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare('CALL delete_exam_by_id(?)');
        $stmt->execute([$quiz_id]);
        $stmt->closeCursor();
        header('Location: exam.php');
        exit;
    } catch (Exception $e) {
        $message = 'Error deleting exam: ' . $e->getMessage();
    }
}

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $quiz_id = intval($_POST['quiz_id'] ?? 0);
    $quiz_name = trim($_POST['quiz_name'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $level_id = intval($_POST['level_id'] ?? 0);
    try {
      if ($action === 'add') {
        $stmt = $pdo->prepare('CALL add_exam_with_course(?,?,?)');
        $stmt->execute([$quiz_name, $course_id, $level_id]);
        $stmt->closeCursor();
        $message = 'Exam added successfully.';
      } elseif ($action === 'update' && $quiz_id) {
        $stmt = $pdo->prepare('CALL update_exam_with_course(?,?,?,?)');
        $stmt->execute([$quiz_id, $quiz_name, $course_id, $level_id]);
        $stmt->closeCursor();
        $message = 'Exam updated successfully.';
      } elseif ($action === 'delete' && $quiz_id) {
        $stmt = $pdo->prepare('CALL delete_exam_by_id(?)');
        $stmt->execute([$quiz_id]);
        $stmt->closeCursor();
        $message = 'Exam deleted.';
      }
    } catch (Exception $e) {
      $message = 'Error: ' . $e->getMessage();
    }
  }
}
// Fetch courses
$coursesList = [];
try {
  $courseStmt = $pdo->query('SELECT courseid, coursename FROM course');
  $coursesList = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
  $courseStmt->closeCursor();
} catch (Exception $e) {
  $coursesList = [];
}
// Fetch levels
$levelsList = [];
try {
  $levelStmt = $pdo->query('SELECT level_id, level_name FROM levels');
  $levelsList = $levelStmt->fetchAll(PDO::FETCH_ASSOC);
  $levelStmt->closeCursor();
} catch (Exception $e) {
  $levelsList = [];
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
  $allStmt = $pdo->query('CALL get_all_exams_with_course_and_level()');
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
    <h2 style="color:white;margin:0 0 18px 0;white-space:nowrap;font-size:1.5em;text-align:center;width:100%;">Exam List</h2>
    <form method="post" class="search-bar" id="courseSearchForm" style="margin:0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <label for="search_type" style="font-size:0.96em;">Search by:</label>
      <select name="search_type" id="search_type" required style="font-size:0.96em;min-width:80px;">
        <option value="">Select</option>
        <option value="Exam" selected>Exam</option>
        <option value="coursename" <?= isset($_POST['search_type']) && $_POST['search_type']==='coursename'?'selected':'' ?>>Course Name</option>
      </select>
      <input type="text" name="search_value" id="search_value" value="<?= isset($_POST['search_value']) ? htmlspecialchars($_POST['search_value']) : '' ?>" placeholder="Enter value..." style="font-size:0.96em;max-width:120px;">
      <button type="submit" style="font-size:0.96em;padding:5px 10px;cursor:pointer;display:none;">Search</button>
      <button type="button" onclick="window.location.href='addexam.php';" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#28a745;color:#fff;border:none;font-weight:600;font-size:0.96em;cursor:pointer;">Add Exam</button>
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
          <th style="width:160px;">Exam Name</th>
          <th style="width:120px;">Course Name</th>
          <th style="width:80px;">Level</th>
          <th style="width:110px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($courses as $course): ?>
          <tr>
            <td style="word-break:break-word;"><?php echo htmlspecialchars($course['quiz_name']); ?></td>
            <td style="word-break:break-word;"><?php echo htmlspecialchars($course['coursename']); ?></td>
            <td><?php echo htmlspecialchars($course['level_name']); ?></td>
            <td class="actions" style="min-width:110px;max-width:110px;gap:8px;align-items:center;">
              <a href="exam.php?delete=<?php echo urlencode($course['quiz_id']); ?>" onclick="return confirm('Delete this exam?');" title="Delete" class="action-icon delete-icon"><span>🗑️</span></a>
              <a href="#" onclick="openExamEdit(<?php echo htmlspecialchars(json_encode($course)); ?>);return false;" title="Update" class="action-icon update-icon"><span>✏️</span></a>
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
    function openExamEdit(course) {
      // Open addexam.php in edit mode with quiz_id
      window.location.href = 'addexam.php?edit=' + encodeURIComponent(course.quiz_id);
    }
    document.getElementById('search_value').addEventListener('input', function(e) {
      e.preventDefault(); // Prevent form submission
      var filter = this.value.toLowerCase();
      var searchType = document.getElementById('search_type').value;
      var rows = document.querySelectorAll('.course-table tbody tr');
      rows.forEach(function(row) {
        var examNameCell = row.querySelectorAll('td')[0]; // Exam Name
        var courseNameCell = row.querySelectorAll('td')[1]; // Course Name
        var match = false;
        if (searchType.toLowerCase() === 'exam' && examNameCell) {
          match = examNameCell.textContent.toLowerCase().indexOf(filter) !== -1;
        } else if (searchType.toLowerCase() === 'coursename' && courseNameCell) {
          match = courseNameCell.textContent.toLowerCase().indexOf(filter) !== -1;
        } else if (!searchType) {
          match = true;
        } else {
          match = false;
        }
        row.style.display = match ? '' : 'none';
      });
    });
  </script>
</body>
</html>
