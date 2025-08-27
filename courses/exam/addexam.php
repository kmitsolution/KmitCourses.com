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
// Edit mode logic
$editMode = false;
$editExam = null;
if (isset($_GET['edit']) && $_GET['edit'] !== '') {
  $editMode = true;
  $exam_id = intval($_GET['edit']);
  try {
    // Fetch exam name from exams table
    $stmt = $pdo->prepare('SELECT e.exam_id, e.exam_name, ec.course_id, ec.level_id FROM exams e JOIN exam_courses ec ON e.exam_id = ec.exam_id WHERE e.exam_id = ?');
    $stmt->execute([$exam_id]);
    $editExam = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    // If coming from POST, override with posted values for sticky form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $editExam['exam_name'] = $_POST['exam_name'] ?? $editExam['exam_name'];
      $editExam['course_id'] = $_POST['course_id'] ?? $editExam['course_id'];
      $editExam['level_id'] = $_POST['level_id'] ?? $editExam['level_id'];
    }
  } catch (Exception $e) {
    $editExam = null;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $exam_name = trim($_POST['exam_name'] ?? '');
  $course_id = intval($_POST['course_id'] ?? 0);
  $level_id = intval($_POST['level_id'] ?? 0);
  if ($_POST['action'] === 'add') {
    if ($exam_name && $course_id && $level_id) {
      try {
        $stmt = $pdo->prepare('CALL add_exam_with_course(?,?,?)');
        $stmt->execute([$exam_name, $course_id, $level_id]);
        $stmt->closeCursor();
        $message = 'Exam added successfully.';
        echo '<script>window.location.href="exam.php";</script>';
        exit;
      } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
      }
    } else {
      $message = 'Please fill all fields.';
    }
  } elseif ($_POST['action'] === 'update' && isset($_POST['exam_id'])) {
    $exam_id = intval($_POST['exam_id']);
    if ($exam_id && $exam_name && $course_id && $level_id) {
      try {
        $stmt = $pdo->prepare('CALL update_exam_with_course(?,?,?,?)');
        $stmt->execute([$exam_id, $exam_name, $course_id, $level_id]);
        $stmt->closeCursor();
        $message = 'Exam updated successfully.';
        echo '<script>window.location.href="exam.php";</script>';
        exit;
      } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
      }
    } else {
      $message = 'Please fill all fields.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Exam</title>
  <link rel="stylesheet" href="../../assets/style.css">
  <style>
    .form-section { background:#f8fafc; border-radius:12px; padding:24px; margin-bottom:32px; box-shadow:0 2px 12px #00214711; max-width:500px; margin:auto; }
    .form-section h2 { margin-top:0; color:#002147; }
    .form-group { margin-bottom:16px; }
    .form-group label { font-weight:600; display:block; margin-bottom:6px; color:#002147; }
    .form-group input, .form-group select { width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; font-size:1rem; color:#002147; }
    .form-actions { display:flex; gap:12px; justify-content:flex-end; }
    .btn { padding:8px 14px; border-radius:7px; border:none; font-weight:600; cursor:pointer; }
    .btn.primary { background:#0078d4; color:#fff; }
  </style>
</head>
<body>
<div class="container">
  <div class="form-section">
    <h2><?= $editMode ? 'Update Exam' : 'Add Exam' ?></h2>
    <?php if ($message) echo '<div style="color:#0078d4;font-weight:600;margin-bottom:12px;">'.htmlspecialchars($message).'</div>'; ?>
    <form method="post" action="addexam.php<?= $editMode && $editExam ? '?edit=' . urlencode($editExam['exam_id']) : '' ?>">
      <?php if ($editMode && $editExam): ?>
        <input type="hidden" name="exam_id" value="<?= htmlspecialchars($editExam['exam_id']) ?>">
      <?php endif; ?>
      <div class="form-group">
        <label for="exam_name">Exam Name:</label>
        <input type="text" name="exam_name" id="exam_name" required value="<?= $editMode && $editExam ? htmlspecialchars($editExam['exam_name']) : '' ?>">
      </div>
      <div class="form-group">
        <label for="course_id">Course:</label>
        <select name="course_id" id="course_id" required>
          <option value="">Select Course</option>
          <?php foreach ($coursesList as $course): ?>
            <option value="<?= $course['courseid'] ?>" <?= $editMode && $editExam && $editExam['course_id'] == $course['courseid'] ? 'selected' : '' ?>><?= htmlspecialchars($course['coursename']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="level_id">Level:</label>
        <select name="level_id" id="level_id" required>
          <option value="">Select Level</option>
          <?php foreach ($levelsList as $level): ?>
            <option value="<?= $level['level_id'] ?>" <?= $editMode && $editExam && $editExam['level_id'] == $level['level_id'] ? 'selected' : '' ?>><?= htmlspecialchars($level['level_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-actions">
        <button type="submit" name="action" value="<?= $editMode ? 'update' : 'add' ?>" class="btn primary"><?= $editMode ? 'Update Exam' : 'Add Exam' ?></button>
        <a href="exam.php" class="btn" style="background:#eee;color:#222;">Back to List</a>
      </div>
    </form>
  </div>
</div>
<?php if ($editMode && $editExam && !empty($editExam['exam_name'])): ?>
<script>
  window.onload = function() {
    alert('Exam Name: <?= addslashes($editExam['exam_name']) ?>');
  };
</script>
<?php endif; ?>
</body>
</html>
