<?php
// Simple gallery page for images and videos in assets/images and assets/videos
$imagesDir = __DIR__ . '/../assets/images';
$videosDir = __DIR__ . '/../assets/videos';
$images = [];
$videos = [];
if (is_dir($imagesDir)) {
    foreach (scandir($imagesDir) as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $images[] = '../assets/images/' . $file;
        }
    }
}
if (is_dir($videosDir)) {
    foreach (scandir($videosDir) as $file) {
        if (preg_match('/\.(mp4|webm|ogg)$/i', $file)) {
            $videos[] = '../assets/videos/' . $file;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Media Gallery</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-top: 32px; }
        .gallery-item { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px #00214711; padding: 16px; text-align: center; }
        .gallery-item img { max-width: 120px; max-height: 90px; border-radius: 8px; object-fit: cover; }
        .gallery-item video { max-width: 180px; max-height: 120px; border-radius: 8px; object-fit: cover; }
        .gallery-title { font-size: 1.3em; color: #081326; margin-bottom: 18px; text-align: center; }
    </style>
</head>
<body style="background:#f8f9fa; color:#081326;">
<div class="container">
    <h2 class="gallery-title">Media Gallery</h2>
    <form method="post" enctype="multipart/form-data" style="margin-bottom:24px;max-width:400px;">
        <label style="font-weight:600;color:#081326;">Upload Image:<br>
            <input type="file" name="gallery_image" accept="image/*" style="width:100%;margin-bottom:12px;color:#081326;background:#f3f4f6;border:1px solid #ccc;padding:7px 10px;border-radius:6px;">
        </label><br>
        <button type="submit" name="upload_gallery_image" class="btn primary" style="margin-top:8px;">Upload to Gallery</button>
    </form>
    <?php
    // Handle gallery image upload
    $imagesDir = __DIR__ . '/../assets/images';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_gallery_image'])) {
        if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === UPLOAD_ERR_OK) {
            $imgTmp = $_FILES['gallery_image']['tmp_name'];
            $imgName = basename($_FILES['gallery_image']['name']);
            $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (in_array($imgExt, $allowed)) {
                $targetPath = $imagesDir . '/' . $imgName;
                if (move_uploaded_file($imgTmp, $targetPath)) {
                    echo '<div style="color:#0078d4;font-weight:600;margin-bottom:12px;">Image uploaded to gallery.</div>';
                } else {
                    echo '<div style="color:#c00;font-weight:600;margin-bottom:12px;">Image upload failed.</div>';
                }
            } else {
                echo '<div style="color:#c00;font-weight:600;margin-bottom:12px;">Invalid image type.</div>';
            }
        }
    }
    ?>
    <div class="gallery-grid">
        <?php foreach ($images as $img): ?>
            <div class="gallery-item">
                <img src="<?= $img ?>" alt="Image">
                <?php if (isset($_GET['select']) && $_GET['select'] == '1'): ?>
                    <button type="button" onclick="window.opener.postMessage({selectedImage: '<?= $img ?>'}, window.opener.location.origin); window.close();" style="margin-top:8px;display:block;width:100%;background:#0078d4;color:#fff;border:none;padding:7px 0;border-radius:6px;font-weight:600;">Select</button>
                    <div style="font-size:0.95em;color:#222;margin-top:4px;word-break:break-all;">Filename: <?= basename($img) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php foreach ($videos as $vid): ?>
            <div class="gallery-item"><video src="<?= $vid ?>" controls></video></div>
        <?php endforeach; ?>
        <?php if (empty($images) && empty($videos)): ?>
            <div class="gallery-item" style="grid-column:1/-1;color:#c00;">No media found.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
