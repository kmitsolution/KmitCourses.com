<?php
require_once '../config.php';
// Fetch all categories
$pdo = getPDO();
$categories = [];
try {
    $catStmt = $pdo->query('CALL get_all_categories()');
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
    $catStmt->closeCursor();
} catch (Exception $e) {
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Courses</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
      /* Match main site theme (uses variables from assets/style.css) */
      body { font-family: 'Inter', Arial, sans-serif; background: var(--bg); color: var(--text); }
      .container { max-width: 1200px; margin: 0 auto; padding: 36px 20px; }
      .section-header { margin-bottom: 18px; }
      .category-title { font-size: 1.25rem; color: var(--acc); margin: 28px 0 12px 0; font-weight:700; cursor:pointer; display:flex; align-items:center; user-select:none; }
      .category-title .cat-arrow { margin-right: 10px; font-size: 1.2rem; transition: transform 0.2s; }
      .category-title.expanded .cat-arrow { transform: rotate(90deg); }
      .category-row { display: none; gap: 18px; overflow-x: auto; padding-bottom: 8px; -webkit-overflow-scrolling: touch; }
      .category-row.expanded { display: flex; }
      .category-row::-webkit-scrollbar { height: 8px; }
      .category-row::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 6px; }

      /* Compact course cards */
      .course.card { background: var(--card); border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.45); padding: 12px; width: 220px; min-width: 220px; display: flex; flex-direction: column; align-items: flex-start; color: var(--text); cursor: pointer; transition: box-shadow 0.2s, transform 0.2s; position: relative; }
      .course.card img { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; background: #fff; margin-bottom: 10px; }
      .course.card .title { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 6px; }
      .course.card .desc, .course.card .actions { display: none; }
      .course.card.expanded { z-index: 2; box-shadow: 0 12px 32px #0008, 0 0 0 2px var(--acc); transform: scale(1.08); }
      .course.card.expanded .desc, .course.card.expanded .actions { display: block; }
      .course.card .desc { font-size: 0.95rem; color: var(--muted); margin-bottom: 8px; margin-top: 6px; }
      .course.card .actions { margin-top: 8px; }
      .course.card .btn { margin-right: 8px; }
      .course.card .expand-arrow { position: absolute; top: 10px; right: 10px; font-size: 1.2rem; color: var(--acc); transition: transform 0.2s; }
      .course.card.expanded .expand-arrow { transform: rotate(90deg); }

      a.back-home { display:inline-block;margin-top:32px;background:transparent;color:var(--acc);padding:7px 18px;border-radius:6px;text-decoration:none;font-weight:600;border:1px solid rgba(255,255,255,0.06); }

      @media (max-width: 700px) {
        .container { padding: 18px 12px; }
        .course.card { width: 190px; min-width: 190px; }
        .course.card img { height: 100px; }
      }
    </style>
</head>
<body>
  <div class="container">
    <h1>All Courses</h1>
    <?php
    $cardId = 0;
    $catIdx = 0;
    foreach ($categories as $cat) {
        $catId = 'cat_' . $cat['categoryid'];
        $expanded = $catIdx === 0 ? 'expanded' : '';
        echo '<div class="category-title ' . $expanded . '" data-cat="' . $catId . '"><span class="cat-arrow">&#9654;</span>' . htmlspecialchars($cat['categoryname']) . '</div>';
        // Fetch courses for this category
        try {
            $stmt = $pdo->prepare('CALL get_courses_by_category(?)');
            $stmt->execute([$cat['categoryid']]);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (Exception $e) {
            $courses = [];
        }
        echo '<div class="category-row ' . $expanded . '" id="' . $catId . '">';
        if ($courses) {
            foreach ($courses as $course) {
                $img = isset($course['image']) && $course['image'] ? (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/assets/images/' . htmlspecialchars($course['image']) : '../assets/images/' . htmlspecialchars($course['image'])) : (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/assets/images/placeholder.jpeg' : '../assets/images/placeholder.jpeg');
                $page = isset($course['page_slug']) ? ltrim($course['page_slug'], '/') : '#';
                $desc = !empty($course['description']) ? htmlspecialchars($course['description']) : '';
                $price = isset($course['price']) && $course['price'] !== null ? number_format($course['price'], 2) : null;
                $thisId = 'coursecard_' . (++$cardId);
                // Use BASE_URL from config.php for the course link
                $pageUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' . $page : $page;
                echo '<div class="course card" id="' . $thisId . '" tabindex="0">';
                echo '<img src="' . $img . '" alt="' . htmlspecialchars($course['coursename']) . '" />';
                echo '<div class="title">' . htmlspecialchars($course['coursename']) . '</div>';
                if ($price !== null) {
                    echo '<div class="price" style="font-weight:600;color:var(--acc);margin-bottom:4px;">₹' . $price . '</div>';
                }
                echo '<span class="expand-arrow">&#9654;</span>';
                if ($desc) {
                    echo '<div class="desc">' . $desc . '</div>';
                }
                echo '<div class="actions">';
                echo '<a class="btn primary" href="' . $pageUrl . '">View</a>';
                echo '<a class="btn" style="background:var(--acc);color:#fff;font-weight:600;" href="#" onclick="alert(\'Buy Now coming soon!\');return false;">Buy Now</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div style="color:var(--muted);margin-bottom:18px;">No courses in this category.</div>';
        }
        echo '</div>';
        $catIdx++;
    }
    ?>
    <a class="back-home" href="../../default.php">Back to Home</a>
  </div>
  <script>
    // Expand/collapse logic for categories: only one open at a time
    document.addEventListener('DOMContentLoaded', function() {
      const catTitles = document.querySelectorAll('.category-title');
      let openCat = document.querySelector('.category-title.expanded');
      catTitles.forEach(title => {
        title.addEventListener('click', function(e) {
          const catId = title.getAttribute('data-cat');
          const row = document.getElementById(catId);
          // Collapse all
          document.querySelectorAll('.category-title').forEach(t => t.classList.remove('expanded'));
          document.querySelectorAll('.category-row').forEach(r => r.classList.remove('expanded'));
          // Expand this
          title.classList.add('expanded');
          if (row) row.classList.add('expanded');
        });
        title.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            title.click();
            e.preventDefault();
          }
        });
      });
      // Expand/collapse logic for course cards (only one open at a time per category)
      document.querySelectorAll('.category-row').forEach(function(row) {
        let openCard = null;
        row.querySelectorAll('.course.card').forEach(function(card) {
          card.addEventListener('click', function(e) {
            if (openCard && openCard !== card) {
              openCard.classList.remove('expanded');
            }
            if (card.classList.contains('expanded')) {
              card.classList.remove('expanded');
              openCard = null;
            } else {
              card.classList.add('expanded');
              openCard = card;
            }
            e.stopPropagation();
          });
          card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
              card.click();
              e.preventDefault();
            }
          });
        });
      });
      // Optional: collapse all cards on click outside
      document.body.addEventListener('click', function(e) {
        if (!e.target.closest('.course.card')) {
          document.querySelectorAll('.course.card.expanded').forEach(function(card) {
            card.classList.remove('expanded');
          });
        }
      }, true);
    });
  </script>
</body>
</html>
