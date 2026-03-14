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
            <a href="quizzies.php" class="btn" style="min-width:110px;color:#0078d4;background:#e5e7eb;border:1.5px solid #e0e0e0;font-size:1rem;">Quizzies</a>

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
        <div id="mainContentPlaceholder" style="width:100%;min-height:80vh;display:block;background:#fff;">
          <!-- Landing Page Start -->
          <section class="landing-hero" style="padding:48px 24px;background:linear-gradient(180deg,#f8fbff 0%,#ffffff 100%);">
            <div class="container-landing" style="max-width:1100px;margin:0 auto;display:flex;align-items:center;gap:28px;flex-wrap:wrap;">
              <div class="hero-left" style="flex:1 1 420px;min-width:260px;">
                <img src="assets/images/Original.png" alt="KMIT Solutions Services" style="height:78px;width:auto;object-fit:contain;margin-bottom:18px;display:block;" />
                <h1 style="margin:0 0 12px 0;font-size:2rem;color:#002147;line-height:1.06;font-weight:800;">Empowering Careers Through IT Excellence</h1>
                <p style="margin:0 0 18px 0;color:#334155;font-size:1.05rem;">KMIT Solutions Services delivers industry-relevant IT training for students and working professionals — hands-on labs, expert instructors, and career-focused learning.</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;">
                  <a href="#courses" class="btn primary" style="background:#0078d4;color:#fff;padding:12px 18px;border-radius:8px;text-decoration:none;font-weight:700;box-shadow:0 6px 18px rgba(2,41,88,0.08);">Explore Courses</a>
                  <a href="#contact" class="btn" style="background:transparent;border:2px solid #0078d4;color:#0078d4;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:700;">Enroll Now</a>
                </div>
              </div>
              <div class="hero-right" style="flex:0 0 380px;min-width:260px;display:flex;align-items:center;justify-content:center;">
                <div style="width:320px;height:220px;border-radius:14px;background:linear-gradient(135deg,#fff 0%,#eaf6ff 100%);box-shadow:0 10px 30px rgba(2,41,88,0.06);display:flex;flex-direction:column;gap:8px;padding:18px;">
                  <div style="font-weight:700;color:#002147;">Featured Programs</div>
                  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px;">
                    <span class="pill" style="background:#fff;padding:8px 12px;border-radius:999px;border:1px solid #e6eef8;color:#065f9e;font-weight:700;font-size:0.9rem;">DevOps</span>
                    <span class="pill" style="background:#fff;padding:8px 12px;border-radius:999px;border:1px solid #e6eef8;color:#065f9e;font-weight:700;font-size:0.9rem;">AWS</span>
                    <span class="pill" style="background:#fff;padding:8px 12px;border-radius:999px;border:1px solid #e6eef8;color:#065f9e;font-weight:700;font-size:0.9rem;">Azure</span>
                    <span class="pill" style="background:#fff;padding:8px 12px;border-radius:999px;border:1px solid #e6eef8;color:#065f9e;font-weight:700;font-size:0.9rem;">Python</span>
                  </div>
                  <div style="margin-top:auto;display:flex;gap:8px;align-items:center;justify-content:space-between;">
                    <div style="font-size:0.95rem;color:#0f172a;font-weight:700;">Hands-on Labs</div>
                    <div style="font-size:0.9rem;color:#334155;">100+ hrs</div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section id="courses" class="landing-courses" style="padding:36px 18px;">
            <div style="max-width:1100px;margin:0 auto;">
              <h2 style="color:#002147;margin:0 0 12px 0;font-size:1.4rem;">Our Courses</h2>
              <p style="color:#475569;margin:0 0 18px 0;">Industry-focused tracks designed to make you job-ready.</p>
              <div class="courses-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <!-- Course cards -->
                <article class="card" style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 8px 22px rgba(2,41,88,0.04);display:flex;flex-direction:column;gap:12px;transition:transform .18s ease,box-shadow .18s ease;">
                  <div style="display:flex;align-items:center;gap:12px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="#0078d4" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3 6 6 .5-4.5 3.6L18 20l-6-3-6 3 .5-7.9L2 8.5 8 8 12 2z"/></svg><h3 style="margin:0;font-size:1.05rem;color:#002147;">DevOps</h3></div>
                  <p style="margin:0;color:#475569;">CI/CD, containerization, monitoring, and automation for production-ready pipelines.</p>
                  <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;"><span style="color:#065f9e;font-weight:700;">Beginner → Advanced</span><a href="#contact" style="color:#0078d4;text-decoration:none;font-weight:700;">Enroll</a></div>
                </article>

                <article class="card" style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 8px 22px rgba(2,41,88,0.04);display:flex;flex-direction:column;gap:12px;transition:transform .18s ease,box-shadow .18s ease;">
                  <div style="display:flex;align-items:center;gap:12px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="#0078d4" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L2 7l10 5 10-5-10-5zm0 7l10 5v7l-10-5-10 5v-7l10-5z"/></svg><h3 style="margin:0;font-size:1.05rem;color:#002147;">AWS</h3></div>
                  <p style="margin:0;color:#475569;">Cloud architecture, compute & serverless services, and practical deployment strategies.</p>
                  <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;"><span style="color:#065f9e;font-weight:700;">Certification Prep</span><a href="#contact" style="color:#0078d4;text-decoration:none;font-weight:700;">Enroll</a></div>
                </article>

                <article class="card" style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 8px 22px rgba(2,41,88,0.04);display:flex;flex-direction:column;gap:12px;transition:transform .18s ease,box-shadow .18s ease;">
                  <div style="display:flex;align-items:center;gap:12px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="#0078d4" xmlns="http://www.w3.org/2000/svg"><path d="M3 3h18v4H3V3zm0 7h18v11H3V10z"/></svg><h3 style="margin:0;font-size:1.05rem;color:#002147;">Azure</h3></div>
                  <p style="margin:0;color:#475569;">Microsoft cloud services, identity, networking, and platform services for enterprises.</p>
                  <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;"><span style="color:#065f9e;font-weight:700;">Hands-on Labs</span><a href="#contact" style="color:#0078d4;text-decoration:none;font-weight:700;">Enroll</a></div>
                </article>

                <article class="card" style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 8px 22px rgba(2,41,88,0.04);display:flex;flex-direction:column;gap:12px;transition:transform .18s ease,box-shadow .18s ease;">
                  <div style="display:flex;align-items:center;gap:12px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="#0078d4" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zM6 20v-1c0-2.8 2.2-5 5-5h2c2.8 0 5 2.2 5 5v1H6z"/></svg><h3 style="margin:0;font-size:1.05rem;color:#002147;">Python</h3></div>
                  <p style="margin:0;color:#475569;">Core programming, automation, data manipulation, and scripting for cloud use-cases.</p>
                  <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;"><span style="color:#065f9e;font-weight:700;">Project Work</span><a href="#contact" style="color:#0078d4;text-decoration:none;font-weight:700;">Enroll</a></div>
                </article>

                <article class="card" style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 8px 22px rgba(2,41,88,0.04);display:flex;flex-direction:column;gap:12px;transition:transform .18s ease,box-shadow .18s ease;">
                  <div style="display:flex;align-items:center;gap:12px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="#0078d4" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg><h3 style="margin:0;font-size:1.05rem;color:#002147;">Machine Learning</h3></div>
                  <p style="margin:0;color:#475569;">Foundations of ML, model building, evaluation, and deployment on cloud platforms.</p>
                  <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;"><span style="color:#065f9e;font-weight:700;">Capstone Project</span><a href="#contact" style="color:#0078d4;text-decoration:none;font-weight:700;">Enroll</a></div>
                </article>
              </div>
            </div>
          </section>

          <section class="landing-why" style="background:#f8fbff;padding:34px 18px;border-top:1px solid #eef4fb;">
            <div style="max-width:1100px;margin:0 auto;display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start;">
              <div style="flex:1 1 420px;min-width:260px;">
                <h2 style="margin:0 0 10px 0;color:#002147;font-size:1.25rem;">Why Choose KMIT Solutions Services</h2>
                <ul style="margin:0;padding:0 0 0 18px;color:#334155;">
                  <li style="margin-bottom:10px;">Industry experts with real-world experience</li>
                  <li style="margin-bottom:10px;">Hands-on projects and labs</li>
                  <li style="margin-bottom:10px;">Career guidance and interview prep</li>
                  <li style="margin-bottom:10px;">Certification support and exam readiness</li>
                </ul>
              </div>
              <div style="flex:1 1 300px;min-width:260px;">
                <h3 style="margin:0 0 8px 0;color:#002147;font-size:1.05rem;">Testimonials</h3>
                <blockquote style="margin:0;padding:12px;border-radius:10px;background:#fff;box-shadow:0 8px 20px rgba(2,41,88,0.03);color:#334155;">“The trainers at KMIT helped me transition to a cloud engineering role — practical and focused.” <br/><small style="color:#64748b;">— Anshul, Cloud Engineer</small></blockquote>
              </div>
            </div>
          </section>

          <section id="about" style="padding:28px 18px;">
            <div style="max-width:1100px;margin:0 auto;">
              <h2 style="color:#002147;margin:0 0 8px 0;font-size:1.2rem;">About Us</h2>
              <p style="color:#475569;margin:0 0 6px 0;">KMIT Solutions Services is a professional IT training institute delivering practical, job-ready courses in Cloud, DevOps, Programming, and Machine Learning. We focus on real-world scenarios and projects to prepare students for industry roles.</p>
            </div>
          </section>

          <section id="contact" style="padding:28px 18px;background:#ffffff;border-top:1px solid #eef4fb;">
            <div style="max-width:700px;margin:0 auto;">
              <h2 style="color:#002147;margin:0 0 12px 0;font-size:1.2rem;">Contact Us</h2>
              <form id="landingContactForm" style="display:grid;gap:10px;"> 
                <input type="text" id="contactName" name="name" placeholder="Your name" required style="padding:10px;border-radius:8px;border:1px solid #e6eef8;">
                <input type="email" id="contactEmail" name="email" placeholder="Your email" required style="padding:10px;border-radius:8px;border:1px solid #e6eef8;">
                <textarea id="contactMessage" name="message" placeholder="Message" rows="4" required style="padding:10px;border-radius:8px;border:1px solid #e6eef8;"></textarea>
                <div style="display:flex;gap:10px;">
                  <button type="submit" class="btn primary" style="background:#0078d4;color:#fff;padding:10px 16px;border-radius:8px;border:none;">Send Message</button>
                  <button type="button" id="contactReset" class="btn" style="background:transparent;border:1.5px solid #e0e0e0;padding:10px 16px;border-radius:8px;">Reset</button>
                </div>
                <div id="contactMsg" role="status" style="color:#065f9e;font-weight:700;display:none;padding-top:6px;">Message sent — we will contact you soon.</div>
              </form>
            </div>
          </section>

          <footer style="padding:18px;background:#002147;color:#fff;margin-top:12px;">
            <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
              <div style="font-weight:700;">KMIT Solutions Services</div>
              <div style="display:flex;gap:10px;align-items:center;">
                <a href="https://www.facebook.com/kmitdevops/" target="_blank" rel="noopener" style="color:#fff;text-decoration:none;">Facebook</a>
                <a href="https://instagram.com/" target="_blank" rel="noopener" style="color:#fff;text-decoration:none;">Instagram</a>
                <a href="https://wa.me/9739299502" target="_blank" rel="noopener" style="color:#fff;text-decoration:none;">WhatsApp</a>
              </div>
              <div style="width:100%;text-align:center;color:#a8c8e6;font-size:0.95rem;margin-top:8px;">© <?php echo date('Y'); ?> KMIT Solutions Services. All rights reserved.</div>
            </div>
          </footer>
          <!-- Landing Page End -->
        </div>
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
    <script>
    // Landing page interactions: smooth scroll and contact form handling
    document.addEventListener('DOMContentLoaded', function() {
      // Smooth scroll for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
          var href = this.getAttribute('href');
          if (href && href.startsWith('#')) {
            var target = document.querySelector(href);
            if (target) {
              e.preventDefault();
              target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }
        });
      });

      // Contact form handling (client-side only)
      var form = document.getElementById('landingContactForm');
      if (form) {
        var msg = document.getElementById('contactMsg');
        var resetBtn = document.getElementById('contactReset');
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          // Basic validation
          var name = document.getElementById('contactName').value.trim();
          var email = document.getElementById('contactEmail').value.trim();
          var message = document.getElementById('contactMessage').value.trim();
          if (!name || !email || !message) {
            alert('Please fill all required fields.');
            return;
          }
          // Simulate send (implement server call later)
          form.reset();
          if (msg) { msg.style.display = 'block'; }
          setTimeout(function() { if (msg) { msg.style.display = 'none'; } }, 6000);
        });
        if (resetBtn) {
          resetBtn.addEventListener('click', function() { form.reset(); });
        }
      }
    });
    </script>
  </body>
</html>
