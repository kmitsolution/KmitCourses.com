<?php
// Use config.php for database connection (PDO)
require_once __DIR__ . '/../../config.php';
$pdo = getPDO();

// Fetch categories for dropdown
$categories = [];
$catStmt = $pdo->prepare("SELECT categoryid, categoryname FROM course_category ORDER BY categoryname");
$catStmt->execute();
while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) $categories[] = $row;
$catStmt->closeCursor();

// Handle add course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coursename = $_POST['coursename'] ?? '';
    $image = '';
    // Only allow gallery selection
    if (!empty($_POST['select_gallery_image'])) {
        $image = basename($_POST['select_gallery_image']);
    }
    $description = $_POST['description'] ?? '';
    $page_slug = $_POST['page_slug'] ?? '';
    $price = $_POST['price'] ?? '0.00';
    $type = $_POST['type'] ?? 'FREE';
    $categoryid = $_POST['categoryid'] ?? '';
    if ($coursename && $categoryid && $type) {
        try {
            // Add course
            $stmt = $pdo->prepare("CALL AddCourse(?, ?, ?, ?, ?, ?)");
            $stmt->execute([$coursename, $image, $description, $page_slug, $price, $type]);
            $stmt->closeCursor();
            // Get courseid
            $stmt2 = $pdo->prepare("CALL GetCourseIdByName(?, @courseid)");
            $stmt2->execute([$coursename]);
            $stmt2->closeCursor();
            $result = $pdo->query("SELECT @courseid AS courseid");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $courseid = $row['courseid'];
            // Link course to category
            $stmt3 = $pdo->prepare("CALL AddCourseToCategory(?, ?)");
            $stmt3->execute([$courseid, $categoryid]);
            $stmt3->closeCursor();
            header('Location: courseadmin.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill all required fields.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body style="background:#fff; color:#081326;">
<div class="container">
    <h2 style="color:#081326;">Add Course</h2>
    <?php if (!empty($error)) echo '<div class="muted" style="color:#c00;">'.$error.'</div>'; ?>
    <form method="post" enctype="multipart/form-data" class="card" style="max-width:500px;background:#fff;color:#081326;">
        <div style="margin-bottom:18px;">
            <label style="font-weight:600;color:#081326;">Category:<br></label>
            <div style="display:flex;align-items:center;gap:10px;">
                <select name="categoryid" required style="width:100%;margin-bottom:0;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['categoryid'] ?>"><?= htmlspecialchars($cat['categoryname']) ?></option>
                    <?php endforeach; ?>
                </select>
                <a href="addcategory.php" style="color:#0078d4;text-decoration:underline;font-weight:600;white-space:nowrap;">Add New Category</a>
            </div>
        </div>
        <label style="font-weight:600;color:#081326;">Course Name:<br>
            <input type="text" name="coursename" required style="width:100%;margin-bottom:12px;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;">
        </label><br>
        <label style="font-weight:600;color:#081326;">Type:<br>
            <select name="type" required style="width:100%;margin-bottom:12px;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;">
                <option value="FREE">FREE</option>
                <option value="PAID">PAID</option>
            </select>
        </label><br>
        <label style="font-weight:600;color:#081326;">Select Course Image from Gallery:<br>
            <button type="button" onclick="window.open('../../gallery/gallery.php?select=1', 'gallery', 'width=900,height=600');" class="btn" style="margin-top:8px;margin-bottom:12px;">Open Media Gallery</button>
            <input type="hidden" name="select_gallery_image" id="selectedGalleryImage">
            <div id="selectedImagePreview" style="margin-top:8px;"></div>
            <div id="selectedImageName" style="margin-top:4px;color:#222;font-size:0.97em;"></div>
        </label><br>
        <label style="font-weight:600;color:#081326;">Description:<br>
            <textarea name="description" style="width:100%;margin-bottom:12px;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;"></textarea>
        </label><br>
        <label style="font-weight:600;color:#081326;">Page Slug:<br>
            <input type="text" name="page_slug" style="width:100%;margin-bottom:12px;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;">
        </label><br>
        <label style="font-weight:600;color:#081326;">Price:<br>
            <input type="number" step="0.01" name="price" value="0.00" style="width:100%;margin-bottom:12px;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;">
        </label><br>
        <button type="submit" class="btn primary" style="margin-top:10px;">Add Course</button>
        <a href="courseadmin.php" class="btn" style="margin-left:8px;">Cancel</a>
    </form>
</div>
<script>
// Listen for messages from gallery window
window.addEventListener('message', function(event) {
    if (event.origin !== window.location.origin) return;
    if (event.data && event.data.selectedImage) {
        document.getElementById('selectedGalleryImage').value = event.data.selectedImage;
        document.getElementById('selectedImagePreview').innerHTML = '<img src="../' + event.data.selectedImage + '" style="max-width:120px;max-height:90px;border-radius:8px;object-fit:cover;">';
        var fname = event.data.selectedImage.split('/').pop();
        document.getElementById('selectedImageName').textContent = 'Image Name: ' + fname;
    }
});
</script>
</body>
</html>
