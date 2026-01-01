<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="assets/images/Original.png" />
    <title>KMIT Courses — Learn Anywhere, Anytime</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/style.css" />
    <style>
      .header-social img { width:20px; height:20px; display:block; }
      /* Ensure navbar lays out in a single row and the search form can shrink
         so the social icons remain on the same line and are right-aligned */
      #mainNav { display:flex; align-items:center; gap:10px; }
      #mainNav form { flex:1 1 200px; min-width:0; }
      .header-social { margin-left:12px; display:inline-flex; align-items:center; gap:10px; }
      @media (max-width: 700px) {
        .header-social { margin-left:8px !important; }
      }
      .nav-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 2rem;
        color: #0078d4;
        cursor: pointer;
        margin-left: 10px;
        z-index: 1100;
      }
      @media (max-width: 900px) {
        .container.nav {
          flex-wrap: wrap;
        }
        .container.nav nav {
          flex-direction: column;
          align-items: flex-start;
          width: 100vw;
          display: none;
          background: #fff;
          position: absolute;
          top: 72px;
          left: 0;
          z-index: 1001;
          box-shadow: 0 8px 32px #0002;
          border-radius: 0 0 16px 16px;
        }
        .container.nav nav.open {
          display: flex;
        }
        .nav-toggle {
          display: block;
        }
        .container.nav > .brand {
          min-width: 0;
        }
        .container.nav nav a,
        .container.nav nav button {
          color: #222 !important;
          background: none !important;
          border: none !important;
          border-radius: 0 !important;
          width: 100%;
          text-align: left;
          padding: 16px 24px;
          font-size: 1.1rem;
        }
        .container.nav nav .btn.primary {
          color: #0078d4 !important;
        }
      }
      .sidebar-category {
        position: relative;
        margin-bottom: 8px;
      }
      .leftcat {
        font-weight: 600;
        color: #222;
        font-size: 0.93rem;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        padding: 7px 10px 7px 6px;
        border-radius: 7px;
        background: linear-gradient(90deg, #e5e7eb 80%, #dbeafe 100%);
        box-shadow: 0 1px 4px #00214711;
        transition: background 0.18s, box-shadow 0.18s, color 0.18s;
        position: relative;
        z-index: 1;
      }
      .leftcat .arrow {
        font-size: 1rem;
        margin-right: 7px;
        transition: transform 0.18s;
      }
      .sidebar-category.open .leftcat .arrow {
        transform: rotate(90deg);
      }
      .leftcat:hover, .sidebar-category.open .leftcat {
        background: linear-gradient(90deg, #dbeafe 80%, #e0e7ff 100%);
        color: #002147;
        box-shadow: 0 2px 8px #00214722;
      }
      .leftsubmenu {
        display: none;
        position: absolute;
        left: 100%;
        top: 0;
        min-width: 170px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 18px #00214722;
        margin: 0 0 7px 0;
        padding: 10px 0 10px 0;
        list-style: none;
        z-index: 10;
        animation: fadeInMenu 0.22s;
      }
      .sidebar-category.open .leftsubmenu {
        display: block;
      }
      .leftsubmenu li {
        margin: 0;
        padding: 0;
      }
      .leftsubmenu a {
        display: block;
        color: #0078d4;
        text-decoration: none;
        font-size: 0.97rem;
        padding: 7px 18px 7px 18px;
        border-radius: 5px;
        transition: background 0.15s, color 0.15s;
      }
      .leftsubmenu a:hover {
        background: #e0e7ff;
        color: #002147;
      }
      @keyframes fadeInMenu {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      /* Fancy scrollbar for sidebar */
      aside::-webkit-scrollbar {
        width: 7px;
        background: #e5e7eb;
      }
      aside::-webkit-scrollbar-thumb {
        background: #b6c6e2;
        border-radius: 6px;
      }
      @media (max-width: 700px) {
        aside {
          min-width: 0 !important;
          width: 100vw !important;
          max-width: 100vw !important;
          position: relative;
          z-index: 1002;
        }
        .leftsubmenu {
          left: 0;
          top: 100%;
          min-width: 140px;
          box-shadow: 0 4px 18px #00214722;
        }
      }
    </style>
  </head>
  <body style="background:#fff;">
    <div style="width:100vw;background:#002147;color:#fff;font-size:15px;padding:0;text-align:left;letter-spacing:0.5px;z-index:1200;position:relative;min-height:64px;display:flex;align-items:center;">
      <span style="display:flex;align-items:center;vertical-align:middle;gap:18px;min-height:64px;">
        
        <img src="assets/images/Original.png" alt="KMIT Solutions Services" style="height:64px;width:auto;object-fit:contain;display:block;" />
        
        <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' style='vertical-align:middle;margin-right:8px;fill:#5ab0ff;' viewBox='0 0 24 24'><path d='M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C10.07 21 3 13.93 3 5a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.24 1.01l-2.2 2.2z'/></svg>
        <span style="font-weight:600;letter-spacing:1px;font-size:1.08em;">Call us:</span>
        <a href="tel:+919739299502" style="color:#5ab0ff;font-weight:700;text-decoration:none;margin-left:8px;font-size:1.08em;">+91-9739299502</a>
        <span style="margin-left:12px;font-weight:700;font-size:1.08em;letter-spacing:1px;">KMIT Courses | KMIT Solutions Services</span>
        <span class="header-social" style="margin-left:auto;display:inline-flex;align-items:center;gap:10px;">
              <a href="https://www.facebook.com/kmitdevops/" target="_blank" rel="noopener" title="Facebook" style="display:inline-flex;align-items:center;color:#0078d4;text-decoration:none;">
                <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" alt="Facebook" style="width:20px;height:20px;filter:invert(1) brightness(1.2);" />
              </a>
              <a href="https://instagram.com/" target="_blank" rel="noopener" title="Instagram" style="display:inline-flex;align-items:center;color:#0078d4;text-decoration:none;">
                <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/instagram.svg" alt="Instagram" style="width:20px;height:20px;filter:invert(1) brightness(1.2);" />
              </a>
              <a href="https://wa.me/9739299502" target="_blank" rel="noopener" title="WhatsApp" style="display:inline-flex;align-items:center;color:#0078d4;text-decoration:none;">
                <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/whatsapp.svg" alt="WhatsApp" style="width:20px;height:20px;filter:invert(1) brightness(1.2);" />
              </a>
            </span>
      </span>
    </div>
    <header>
      <div class="container nav" style="align-items: center; flex-wrap: wrap; gap: 12px; background: #e5e7eb; width: 100vw; max-width: 100vw; min-height:48px; height:52px;">
        <?php
        // Move session_start to very top of file to avoid headers already sent warning
        ?>
        <div style="display: flex; align-items: center; width: 100%; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
          <!-- Hamburger menu for mobile -->
          <button class="nav-toggle" id="navToggle" aria-label="Open menu" style="display:none;background:none;border:none;padding:8px 12px;cursor:pointer;">
            <span style="display:block;width:28px;height:3px;background:#222;margin:5px 0 0 0;border-radius:2px;"></span>
            <span style="display:block;width:28px;height:3px;background:#222;margin:5px 0 0 0;border-radius:2px;"></span>
            <span style="display:block;width:28px;height:3px;background:#222;margin:5px 0 0 0;border-radius:2px;"></span>
          </button>
          <nav id="mainNav" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#e5e7eb;">
            <form id="courseSearchForm" action="#courses" method="get" style="display:flex;align-items:center;gap:0;position:relative;min-width:180px;">
              <input type="text" id="courseSearchInput" name="q" placeholder="Search courses..." style="height:36px;padding:0 38px 0 12px;border:1.5px solid #e0e0e0;border-radius:7px 0 0 7px;font-size:1rem;outline:none;width:100%;" />
              <button type="submit" style="height:36px;width:38px;border:none;background:#0078d4;border-radius:0 7px 7px 0;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" viewBox="0 0 24 24"><path d="M21.71 20.29l-3.4-3.39A8,8,0,1,0,18,19.59l3.39,3.4a1,1,0,0,0,1.41-1.41ZM5,11A6,6,0,1,1,11,17,6,6,0,0,1,5,11Z"/></svg>
              </button>
            </form>
            <?php if (!empty($_SESSION['username'])): ?>
              <div class="user-dropdown" style="position:relative;display:inline-block;">
                <button id="userDropdownBtn" class="btn primary" style="min-width:110px;background:#002147;color:#fff;font-size:1rem;display:flex;align-items:center;gap:7px;">
                  <span style="font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" viewBox="0 0 24 24" style="margin-right:3px;"><circle cx="12" cy="8" r="4"/><path d="M12 14c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z"/></svg>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                  </span>
                  <svg width="14" height="14" style="margin-left:3px;fill:#fff;" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                </button>
                <div id="userDropdownMenu" style="display:none;position:absolute;right:0;top:110%;background:#fff;min-width:160px;box-shadow:0 4px 18px #00214722;border-radius:8px;padding:8px 0;z-index:2000;">
                  <a href="mycourses.php" style="display:block;padding:10px 18px;color:#0078d4;text-decoration:none;font-size:1rem;border-radius:5px;transition:background 0.15s;">My Courses</a>
                  <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="users/admin.php" style="display:block;padding:10px 18px;color:#0078d4;text-decoration:none;font-size:1rem;border-radius:5px;transition:background 0.15s;">Admin Panel</a>
                  <?php endif; ?>
                  <a href="users/logout.php" style="display:block;padding:10px 18px;color:#d32f2f;text-decoration:none;font-size:1rem;border-radius:5px;transition:background 0.15s;">Logout</a>
                </div>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  var btn = document.getElementById('userDropdownBtn');
                  var menu = document.getElementById('userDropdownMenu');
                  if (btn && menu) {
                    btn.addEventListener('click', function(e) {
                      e.stopPropagation();
                      menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
                    });
                    document.addEventListener('click', function(e) {
                      if (!btn.contains(e.target) && !menu.contains(e.target)) {
                        menu.style.display = 'none';
                      }
                    });
                  }
                });
              </script>
            <?php else: ?>
              <a href="users/login.php" class="btn primary" style="min-width:110px;background:#002147;color:#fff;font-size:1rem;">Login / Signup</a>
            <?php endif; ?>
            <button id="contactBtn" class="btn" type="button" style="min-width:110px;color:#0078d4;background:#e5e7eb;border:1.5px solid #e0e0e0;font-size:1rem;">Contact Us</button>
            <a href="courses/OnlineBatches.php" class="btn" style="min-width:110px;color:#0078d4;background:#e5e7eb;border:1.5px solid #e0e0e0;font-size:1rem;">Live Classes</a>

          </nav>
          <!-- Contact Us Modal (unchanged) -->
          <div id="contactModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.35); justify-content:center; align-items:center;">
            <div style="background:#fff; border-radius:14px; max-width: 98vw; width: 370px; padding: 28px 24px 18px 24px; box-shadow:0 8px 32px #0002; position:relative;">
              <button id="closeContactModal" style="position:absolute; right:12px; top:10px; background:none; border:none; font-size:1.5rem; color:#888; cursor:pointer;">&times;</button>
              <h2 style="margin:0 0 18px 0; font-size:1.3rem; color:#0078d4;">Contact Us</h2>
              <form id="contactForm" action="contact.php" method="POST" autocomplete="off">
                <div style="margin-bottom:12px;">
                  <label for="cname" style="font-size:0.98rem;color:#222;">Name</label><br/>
                  <input type="text" id="cname" name="name" required style="width:100%;padding:7px 8px;border-radius:6px;border:1px solid #ccc;">
                </div>
                <div style="margin-bottom:12px;">
                  <label for="cemail" style="font-size:0.98rem;color:#222;">Email</label><br/>
                  <input type="email" id="cemail" name="email" required style="width:100%;padding:7px 8px;border-radius:6px;border:1px solid #ccc;">
                </div>
                <div style="margin-bottom:12px;">
                  <label for="cphone" style="font-size:0.98rem;color:#222;">Phone</label><br/>
                  <input type="text" id="cphone" name="phone" required pattern="[0-9]{10,15}" style="width:100%;padding:7px 8px;border-radius:6px;border:1px solid #ccc;">
                </div>
                <div style="margin-bottom:16px;">
                  <label for="ccourse" style="font-size:0.98rem;color:#222;">Course</label><br/>
                  <?php
                  // Dynamically populate courses from DB using stored procedure
                  require_once __DIR__ . '/config.php';
                  $courseOptions = '';
                  try {
                    $pdo = getPDO();
                    $stmt = $pdo->query('CALL get_all_courses()');
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $courseOptions .= '<option value="' . htmlspecialchars($row['courseid']) . '">' . htmlspecialchars($row['coursename']) . '</option>';
                    }
                    $stmt->closeCursor(); // Required after CALL in MySQL
                  } catch (Exception $e) {
                    $courseOptions = '<option value="">Could not load courses</option>';
                  }
                  ?>
                  <select id="ccourse" name="courseid" required style="width:100%;padding:7px 8px;border-radius:6px;border:1px solid #ccc;">
                    <option value="">Select Course</option>
                    <?php echo $courseOptions; ?>
                  </select>
                </div>
                <button type="submit" class="btn primary" style="width:100%;margin-top:4px;">Submit</button>
              </form>
            </div>
          </div>
        </div>
    </header>

    <!-- Left Panel: All Courses -->
    <div style="display:flex;">
      <aside style="width:170px;min-width:140px;max-width:200px;background:#e5e7eb;border-right:1.5px solid #e0e0e0;padding:0 0 20px 0;min-height:80vh;">
        <div style="background:#002147;color:#fff;padding:12px 12px 10px 12px;border-radius:0 0 10px 0;">
          <h2 style="font-size:0.98rem;font-weight:700;margin:0;color:#fff;letter-spacing:0.3px;">All Courses</h2>
        </div>
        <div style="padding:0 0 0 0;">
        <?php
        require_once __DIR__ . '/config.php';
        $pdo = getPDO();
        $categories = [];
        try {
          $catStmt = $pdo->query('CALL get_all_categories()');
          $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
          $catStmt->closeCursor();
        } catch (Exception $e) {
          $categories = [];
        }
        foreach ($categories as $cat) {
          // Fetch courses for this category
          try {
            $stmt = $pdo->prepare('CALL get_courses_by_category(?)');
            $stmt->execute([$cat['categoryid']]);
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
          } catch (Exception $e) {
            $courses = [];
          }
          // Only show arrow and submenu if there are courses
          $hasCourses = !empty($courses);
          echo '<div class="sidebar-category">';
          echo '<div class="leftcat">' . htmlspecialchars($cat['categoryname']);
          if ($hasCourses) {
            echo '<span class="arrow" style="margin-left:auto;font-size:1.1em;">&gt;</span>';
          }
          echo '</div>';
          if ($hasCourses) {
            echo '<ul class="leftsubmenu" style="min-width:170px;max-width:200px;width:170px;">';
            foreach ($courses as $course) {
              $img = isset($course['image']) && $course['image'] ? (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/assets/images/' . htmlspecialchars($course['image']) : 'assets/images/' . htmlspecialchars($course['image'])) : (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/assets/images/placeholder.jpeg' : 'assets/images/placeholder.jpeg');
              $page = isset($course['page_slug']) ? ltrim($course['page_slug'], '/') : '#';
              $pageUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' . $page : $page;
              echo '<li><a href="' . htmlspecialchars($pageUrl) . '" style="display:flex;align-items:center;gap:7px;min-height:26px;font-weight:400;">'
                . '<img src="' . $img . '" alt="' . htmlspecialchars($course['coursename']) . '" style="width:22px;height:22px;object-fit:cover;border-radius:4px;box-shadow:0 1px 4px #0001;background:#fff;">'
                . '<span style="font-weight:400;">' . htmlspecialchars($course['coursename']) . '</span>'
                . '</a></li>';
            }
            echo '</ul>';
          }
          echo '</div>';
        }
        ?>
        <script>
        // Sidebar submenu open/close logic for better usability
        document.addEventListener('DOMContentLoaded', function() {
          var cats = document.querySelectorAll('.sidebar-category');
          cats.forEach(function(cat) {
            var catBtn = cat.querySelector('.leftcat');
            var submenu = cat.querySelector('.leftsubmenu');
            if (!catBtn || !submenu) return;
            // Open submenu on hover or focus
            catBtn.addEventListener('mouseenter', function() {
              cat.classList.add('open');
            });
            catBtn.addEventListener('focus', function() {
              cat.classList.add('open');
            });
            // Keep open if submenu hovered
            submenu.addEventListener('mouseenter', function() {
              cat.classList.add('open');
            });
            // Close submenu when mouse leaves both
            cat.addEventListener('mouseleave', function() {
              cat.classList.remove('open');
            });
            // Also close on blur (for keyboard nav)
            catBtn.addEventListener('blur', function() {
              setTimeout(function() { cat.classList.remove('open'); }, 120);
            });
          });
        });
        </script>
        </div>
      </aside>
      <main style="flex:1;min-height:80vh;">
        <iframe id="courseContentFrame" src="" style="width:100%;height:80vh;border:none;display:none;background:#fff;box-shadow:0 2px 12px #00214711;border-radius:10px;margin:0;padding:0;"></iframe>
        <div id="mainContentPlaceholder" style="width:100%;height:80vh;display:flex;align-items:center;justify-content:center;color:#888;font-size:1.1rem;">Main content goes here</div>
      </main>
    </div>
    <script>
    // Sidebar submenu open/close logic for better usability
    document.addEventListener('DOMContentLoaded', function() {
      var cats = document.querySelectorAll('.sidebar-category');
      cats.forEach(function(cat) {
        var catBtn = cat.querySelector('.leftcat');
        var submenu = cat.querySelector('.leftsubmenu');
        if (!catBtn || !submenu) return;
        // Open submenu on hover or focus
        catBtn.addEventListener('mouseenter', function() {
          cat.classList.add('open');
        });
        catBtn.addEventListener('focus', function() {
          cat.classList.add('open');
        });
        // Keep open if submenu hovered
        submenu.addEventListener('mouseenter', function() {
          cat.classList.add('open');
        });
        // Close submenu when mouse leaves both
        cat.addEventListener('mouseleave', function() {
          cat.classList.remove('open');
        });
        // Also close on blur (for keyboard nav)
        catBtn.addEventListener('blur', function() {
          setTimeout(function() { cat.classList.remove('open'); }, 120);
        });
      });

      // Intercept submenu link clicks to load in iframe
      var mainFrame = document.getElementById('courseContentFrame');
      var placeholder = document.getElementById('mainContentPlaceholder');
      document.querySelectorAll('.leftsubmenu a').forEach(function(link) {
        link.addEventListener('click', function(e) {
          var url = this.getAttribute('href');
          if (url && url !== '#') {
            e.preventDefault();
            mainFrame.style.display = 'block';
            mainFrame.src = url;
            if (placeholder) placeholder.style.display = 'none';
          }
        });
      });
    });
    </script>
  </body>
</html>
