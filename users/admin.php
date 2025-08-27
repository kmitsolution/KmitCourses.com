<?php
session_start();
require_once '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['username'])) {
    header('Location: ../default.php');
    exit;
}

$isAdmin = false;
try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('CALL is_student_admin(?)');
    $stmt->execute([$_SESSION['username']]);
    $isAdmin = $stmt->fetch() ? true : false;
    $stmt->closeCursor();
} catch (Exception $e) {
    $isAdmin = false;
}
if (!$isAdmin) {
    echo '<h2>Access Denied</h2><p>You are not an admin.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        body { display: flex; min-height: 100vh; margin: 0; }
        .sidebar { width: 220px; background: #f3f4f6; padding: 32px 0 0 0; box-shadow: 2px 0 12px #0001; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar li { margin: 0; }
        .sidebar a { display: block; padding: 16px 28px; color: #0078d4; font-weight: 600; text-decoration: none; border-left: 4px solid transparent; transition: background 0.2s, border 0.2s; }
        .sidebar a.active, .sidebar a:hover { background: #e6f2ff; border-left: 4px solid #0078d4; color: #005fa3; }
        .main-content { flex: 1; padding: 32px; background: #fff; }
        iframe { width: 100%; height: 80vh; border: none; border-radius: 10px; background: #fff; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <ul style="margin-bottom:18px;">
            <li>
                <a href="../default.php" style="background:#0078d4;color:#fff;border-radius:6px;margin:12px 18px 18px 18px;padding:7px 16px;display:block;font-weight:600;font-size:0.98em;text-decoration:none;">&larr; Back to Home</a>
            </li>
            <li><a href="#" id="menu-users" class="active">List All Users</a></li>
            <li>
                <button id="coursesCollapseBtn" style="width:100%;background:linear-gradient(90deg,#e5e7eb 80%,#dbeafe 100%);border:none;outline:none;font-size:1.08rem;font-weight:600;color:#222;padding:14px 18px;text-align:left;cursor:pointer;border-radius:0;transition:background 0.18s,color 0.18s;display:flex;align-items:center;gap:8px;">Courses <span id="coursesArrow" style="margin-left:auto;transition:transform 0.18s;">&#9654;</span></button>
                <div id="coursesSubmenu" style="display:none;background:#fff;border-radius:0 0 8px 8px;box-shadow:0 4px 18px #00214722;">
                    <a href="../courses/OnlineBatches.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Live Classes</a>
                    <a href="../courses/course/courseadmin.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Video Courses</a>
                    <a href="../courses/SelfPaced.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Self Paced</a>
                    <a href="../courses/quiz/quiz.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Quizzes</a>
                    <a href="../courses/category/categorylist.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Category</a>
                    <a href="../courses/levels/levellist.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Exam Levels</a>
                    <a href="../courses/exam/exam.php" class="course-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Exams</a>
                </div>
            </li>
            <li>
                <button id="galleryCollapseBtn" style="width:100%;background:linear-gradient(90deg,#e5e7eb 80%,#dbeafe 100%);border:none;outline:none;font-size:1.08rem;font-weight:600;color:#222;padding:14px 18px;text-align:left;cursor:pointer;border-radius:0;transition:background 0.18s,color 0.18s;display:flex;align-items:center;gap:8px;">Gallery <span id="galleryArrow" style="margin-left:auto;transition:transform 0.18s;">&#9654;</span></button>
                <div id="gallerySubmenu" style="display:none;background:#fff;border-radius:0 0 8px 8px;box-shadow:0 4px 18px #00214722;">
                    <a href="../gallery/gallery.php" class="gallery-link" style="display:block;padding:12px 28px;color:#0078d4;text-decoration:none;font-size:1rem;">Add Media</a>
                </div>
            </li>
            <!-- Add more admin menu items here -->
        </ul>
    </nav>
    <div class="main-content">
        <iframe id="adminFrame" src="userlist.php"></iframe>
    </div>
    <script>
    document.getElementById('menu-users').onclick = function(e) {
        e.preventDefault();
        document.getElementById('adminFrame').src = 'userlist.php';
        this.classList.add('active');
    };
    // Collapse/expand Courses menu
    var collapseBtn = document.getElementById('coursesCollapseBtn');
    var submenu = document.getElementById('coursesSubmenu');
    var arrow = document.getElementById('coursesArrow');
    collapseBtn.addEventListener('click', function() {
        var isOpen = submenu.style.display === 'block';
        submenu.style.display = isOpen ? 'none' : 'block';
        arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
    });
    // Load course pages in iframe
    document.querySelectorAll('.course-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('adminFrame').src = this.getAttribute('href');
        });
    });
    // Collapse/expand Gallery menu
    var galleryCollapseBtn = document.getElementById('galleryCollapseBtn');
    var gallerySubmenu = document.getElementById('gallerySubmenu');
    var galleryArrow = document.getElementById('galleryArrow');
    galleryCollapseBtn.addEventListener('click', function() {
        var isOpen = gallerySubmenu.style.display === 'block';
        gallerySubmenu.style.display = isOpen ? 'none' : 'block';
        galleryArrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
    });
    // Load gallery page in iframe
    document.querySelectorAll('.gallery-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('adminFrame').src = this.getAttribute('href');
        });
    });
    // Collapse/expand Category menu
    var categoryCollapseBtn = document.getElementById('categoryCollapseBtn');
    var categorySubmenu = document.getElementById('categorySubmenu');
    var categoryArrow = document.getElementById('categoryArrow');
    categoryCollapseBtn.addEventListener('click', function() {
        var isOpen = categorySubmenu.style.display === 'block';
        categorySubmenu.style.display = isOpen ? 'none' : 'block';
        categoryArrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
    });
    // Load category page in iframe
    document.querySelectorAll('.category-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('adminFrame').src = this.getAttribute('href');
        });
    });
    </script>
</body>
</html>
