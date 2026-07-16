<?php
session_start();
$loggedIn = !empty($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : '';
$csvPath = __DIR__ . '/data/courses.csv';
$courses = [];
if (file_exists($csvPath) && ($h = @fopen($csvPath, 'r')) !== false) {
  $headers = fgetcsv($h);
  while (($row = fgetcsv($h)) !== false) {
    if (count($row) !== count($headers)) continue;
    $r = array_combine($headers, $row);
    // Clean price by removing spaces
    $price = isset($r['price']) ? str_replace(' ', '', $r['price']) : '₹0';
    $courses[] = [
      'id' => $r['id'],
      'title' => $r['course_name'],
      'description' => 'Professional training course designed to enhance your skills',
      'price' => $price,
      'link' => $r['url'],
      'thumbnail' => $r['thumbnail'],
      'validity' => $r['validity'],
      'category' => $r['category'] ?? 'Other'
    ];
  }
  fclose($h);
}

// Fallback hard-coded courses if CSV not available or empty
if (empty($courses)) {
  $courses = [
    ['id'=>1,'title' => 'DevOps Bootcamp', 'description' => 'Build CI/CD pipelines with Docker, Kubernetes, and GitOps. Master containerization and orchestration.', 'price' => '₹15,999', 'link' => 'courses/DevOps/devops.php', 'thumbnail' => 'assets/images/devops.png', 'validity'=>'6 months','category'=>'DevOps'],
    ['id'=>2,'title' => 'AWS Cloud Training', 'description' => 'Learn AWS core services, deployment, and certification prep. Get hands-on with EC2, S3, and IAM.', 'price' => '₹12,999', 'link' => 'courses/Aws/AwsDevOps.php', 'thumbnail' => 'assets/images/aws.png', 'validity'=>'6 months','category'=>'AWS'],
    ['id'=>3,'title' => 'Azure Fundamentals', 'description' => 'Hands-on Azure cloud skills for infrastructure and security. Deploy and manage cloud resources.', 'price' => '₹12,499', 'link' => 'courses/Azure/AzureFundamentals.php', 'thumbnail' => 'assets/images/azure.png', 'validity'=>'6 months','category'=>'Azure'],
    ['id'=>4,'title' => 'Python Programming', 'description' => 'Master Python for automation, scripting, and data projects. From basics to advanced concepts.', 'price' => '₹8,999', 'link' => 'courses/OnlineBatches.php', 'thumbnail' => 'assets/images/python.png', 'validity'=>'3 months','category'=>'Programming'],
    ['id'=>5,'title' => 'Machine Learning', 'description' => 'Learn ML workflows, training, and deployment basics. Build intelligent applications with AI.', 'price' => '₹18,999', 'link' => 'courses/OnlineBatches.php', 'thumbnail' => 'assets/images/ml.png', 'validity'=>'6 months','category'=>'ML'],
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>KMIT Courses — Excellence in IT Training</title>
    <link rel="icon" type="image/png" href="assets/images/Original.png" />
    <style>
      :root { 
        --primary: #0066cc;
        --primary-dark: #004499;
        --primary-light: #e6f2ff;
        --secondary: #ff6b6b;
        --accent: #00d4aa;
        --text-dark: #1a1a2e;
        --text-light: #666666;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --border: #e2e8f0;
        font-family: 'Segoe UI', Trebuchet MS, system-ui, -apple-system, BlinkMacSystemFont, sans-serif; 
        color: var(--text-dark); 
        background: var(--bg-light); 
      }
      * { box-sizing: border-box; }
      body { margin: 0; padding: 0; background: var(--bg-light); }
      a { color: inherit; text-decoration: none; }
      
      /* Top Bar */
      .topbar { 
        display: flex; align-items: center; justify-content: space-between; gap: 16px; 
        background: linear-gradient(135deg, #0a2463 0%, #247ba0 100%); 
        color: #fff; padding: 14px 20px; flex-wrap: wrap; 
      }
      .brand { display: flex; align-items: center; gap: 12px; }
      .brand img { height: 45px; width: auto; }
      .contact-info { display: flex; flex-wrap: wrap; gap: 16px; align-items: center; font-size: 0.95rem; }
      .contact-info span { display: inline-flex; align-items: center; gap: 6px; }
      .contact-info a { color: #b0e0e6; font-weight: 600; transition: color 0.3s; }
      .contact-info a:hover { color: #fff; }
      
      /* Header & Navigation */
      header { 
        background: var(--bg-white); 
        box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
        position: sticky; top: 0; z-index: 100; 
      }
      .navbar { 
        display: flex; align-items: center; justify-content: space-between; gap: 16px; 
        max-width: 1400px; padding: 18px 30px; margin: 0 auto; flex-wrap: wrap; 
      }
      .nav-brand { font-weight: 800; font-size: 1.3rem; color: var(--primary-dark); }
      .nav-links { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
      .nav-links a, .nav-links button { 
        background: transparent; border: none; padding: 10px 14px; 
        border-radius: 8px; font-weight: 600; color: var(--text-dark); 
        cursor: pointer; transition: all 0.3s;
      }
      .nav-links a:hover, .nav-links button:hover { 
        background: var(--primary-light); 
        color: var(--primary);
      }
      .btn-primary { 
        background: linear-gradient(135deg, var(--primary), #0052a3); 
        color: #fff; border: none; padding: 10px 18px;
        border-radius: 8px; font-weight: 700; cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
      }
      .btn-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.3);
      }
      
      /* Hero Section */
      .hero { 
        padding: 80px 30px 60px; text-align: center;
        background: linear-gradient(135deg, #f8fafc 0%, #e6f2ff 100%);
        border-bottom: 2px solid var(--primary-light);
      }
      .hero h1 { 
        margin: 0 auto 16px; max-width: 900px; 
        font-size: clamp(2.5rem, 5vw, 3.8rem); 
        line-height: 1.1; 
        color: var(--primary-dark);
        font-weight: 800;
        letter-spacing: -0.5px;
      }
      .hero p { 
        margin: 0 auto 32px; max-width: 800px; 
        color: var(--text-light); font-size: 1.05rem; 
        line-height: 1.8; 
      }
      .hero-actions { 
        display: flex; justify-content: center; 
        flex-wrap: wrap; gap: 16px; 
      }
      .hero-actions a { 
        padding: 16px 28px; border-radius: 10px; 
        font-weight: 700; transition: all 0.3s;
        font-size: 1rem;
      }
      .hero-actions a.btn-primary {
        box-shadow: 0 6px 20px rgba(0, 102, 204, 0.25);
      }
      
      /* Main */
      .main { max-width: 1400px; margin: 0 auto; padding: 50px 30px; }
      
      /* Section Headers */
      .section-header {
        margin-bottom: 40px;
      }
      .section-header h2 { 
        margin: 0 0 12px; 
        font-size: 2rem; 
        color: var(--text-dark);
        font-weight: 800;
      }
      .section-header p {
        margin: 0;
        color: var(--text-light);
        font-size: 1.05rem;
      }
      
      /* Filters & Search */
      .filters-bar {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 32px;
        padding: 20px;
        background: var(--bg-white);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      }
      .filter-group {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
      }
      .filter-group label {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 0.95rem;
      }
      .filter-group select, .filter-group input {
        padding: 10px 14px;
        border-radius: 8px;
        border: 2px solid var(--border);
        background: var(--bg-white);
        color: var(--text-dark);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
      }
      .filter-group select:hover, .filter-group input:hover {
        border-color: var(--primary);
      }
      .filter-group select:focus, .filter-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
      }
      
      /* Course Grid */
      .tiles { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
        gap: 24px; 
        margin-bottom: 40px;
      }
      
      /* Course Card */
      .tile { 
        background: var(--bg-white); 
        border-radius: 16px; 
        overflow: hidden;
        border: 1px solid var(--border);
        display: flex; 
        flex-direction: column; 
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        position: relative;
      }
      .tile:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 16px 40px rgba(0, 102, 204, 0.15);
        border-color: var(--primary-light);
      }
      
      .tile-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: linear-gradient(135deg, #e6f2ff, #f0f9ff);
      }
      
      .tile-content {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 12px;
      }
      
      .tile-category {
        display: inline-block;
        width: fit-content;
        padding: 6px 12px;
        border-radius: 20px;
        background: var(--primary-light);
        color: var(--primary);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      
      .tile-title { 
        font-size: 1.2rem; 
        margin: 0 0 8px 0; 
        color: var(--text-dark);
        font-weight: 800;
        line-height: 1.3;
      }
      
      .tile-description { 
        margin: 0; 
        color: var(--text-light); 
        line-height: 1.6; 
        font-size: 0.95rem;
        flex: 1;
      }
      
      .tile-meta {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 12px 0;
        border-top: 1px solid var(--border);
        font-size: 0.85rem;
        color: var(--text-light);
      }
      
      .tile-footer { 
        margin-top: 12px;
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        gap: 12px;
      }
      
      .tile-price { 
        font-weight: 800; 
        color: var(--primary); 
        font-size: 1.3rem; 
      }
      
      .tile-link { 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        padding: 10px 16px; 
        border-radius: 8px; 
        background: linear-gradient(135deg, var(--primary), #0052a3); 
        color: #fff; 
        font-weight: 700; 
        font-size: 0.9rem;
        transition: all 0.3s;
        white-space: nowrap;
      }
      .tile-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.3);
      }

      /* Info Cards */
      .info-cards { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 24px; 
        margin-top: 24px;
      }
      .info-card { 
        background: var(--bg-white); 
        border-radius: 16px; 
        padding: 28px; 
        border: 1px solid var(--border);
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      }
      .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        border-color: var(--primary-light);
      }
      .info-card h3 { 
        margin: 0 0 12px; 
        font-size: 1.1rem; 
        color: var(--text-dark);
        font-weight: 800;
      }
      .info-card p { 
        margin: 0; 
        color: var(--text-light); 
        line-height: 1.7;
      }

      /* Footer */
      .footer { 
        background: linear-gradient(135deg, #0a2463 0%, #247ba0 100%);
        color: #fff; 
        padding: 30px 30px; 
        text-align: center;
        margin-top: 60px;
      }
      .footer p { margin: 0; opacity: 0.9; }

      /* Modal */
      .modal-backdrop { 
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 200;
      }
      .modal { 
        background: var(--bg-white);
        border-radius: 16px;
        max-width: 900px;
        width: 100%;
        box-shadow: 0 30px 80px rgba(0,0,0,0.25);
        overflow: hidden;
        display: flex;
        gap: 24px;
        padding: 28px;
      }
      .modal img{
        width: 320px;
        height: 220px;
        object-fit: cover;
        border-radius: 12px;
      }
      .modal .content{
        flex: 1;
      }
      .modal h3{
        margin: 0 0 12px;
        font-size: 1.4rem;
        color: var(--text-dark);
        font-weight: 800;
      }
      .close-btn{
        background: transparent;
        border: 0;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-light);
        float: right;
        transition: color 0.3s;
      }
      .close-btn:hover {
        color: var(--text-dark);
      }
      
      /* Responsive */
      @media (max-width: 768px) {
        .navbar { padding: 16px 20px; }
        .hero { padding: 60px 20px 40px; }
        .hero h1 { font-size: 2rem; }
        .main { padding: 30px 20px; }
        .modal { flex-direction: column; gap: 16px; padding: 20px; }
        .modal img { width: 100%; height: 200px; }
        .filters-bar { flex-direction: column; align-items: stretch; }
        .filter-group select, .filter-group input { width: 100%; }
      }
      .footer p { margin: 0; color: #cbd5e1; }
      @media (max-width: 720px) { .navbar { flex-direction: column; align-items: stretch; } .contact-info { justify-content: center; } }
    </style>
  </head>
  <body>
      <?php if ($loggedIn): ?>
      <div class="controls">
            <div class="controls-left">
              <div class="control">
                <label for="category-filter" style="font-weight:600;color:#102a43;margin-right:8px;">Category</label>
                <select id="category-filter" style="border:0;background:transparent;outline:none;">
                  <option value="all">All categories</option>
                </select>
              </div>
              <div class="control">
                <label for="course-select" style="font-weight:600;color:#102a43;margin-right:8px;">Course</label>
                <select id="course-select" style="border:0;background:transparent;outline:none;min-width:220px;">
                  <option value="all">All courses</option>
                </select>
              </div>
              <div class="control">
                <input id="search-box" class="search-input" placeholder="Search courses..." aria-label="Search courses">
              </div>
            </div>
            <div>
              <span class="pill">Professional IT Courses • Career-focused</span>
            </div>
          </div>
          <?php else: ?>
            <a href="users/login.php" class="btn-primary">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </header>
    <main class="main">
      <section class="hero">
        <h1>Ready to launch your career with practical IT courses?</h1>
        <p>Choose from curated training programs in DevOps, AWS, Azure, Python, and Machine Learning — with real project experience and expert support.</p>
        <div class="hero-actions">
          <a href="#courses" class="btn-primary">View Courses</a>
          <a href="#contact" style="border: 1.5px solid #0078d4; color: #0078d4;">Request Information</a>
        </div>
      </section>
      <section id="courses">
        <div class="section-heading">
          <h2>Course Tiles</h2>
          <p style="margin:0;color:#64748b;">Click any course to visit its details.</p>
          <div style="display:flex;gap:12px;align-items:center;margin-top:12px;flex-wrap:wrap;">
            <div style="display:flex;gap:8px;align-items:center;">
              <label for="category-filter" style="font-weight:600;color:#102a43;">Category:</label>
              <select id="category-filter" style="padding:8px;border-radius:8px;border:1px solid #d1e3f0;min-width:200px;">
                <option value="all">All categories</option>
              </select>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
              <label for="course-select" style="font-weight:600;color:#102a43;">Course:</label>
              <select id="course-select" style="padding:8px;border-radius:8px;border:1px solid #d1e3f0;min-width:300px;">
                <option value="all">All courses</option>
              </select>
            </div>
          </div>
        </div>

        <div id="selected-course" style="display:none;margin-top:18px;">
          <article class="tile" style="display:flex;gap:18px;align-items:center;">
            <img id="selected-thumbnail" src="assets/images/Original.png" alt="thumbnail" style="width:140px;height:100px;object-fit:cover;border-radius:12px;">
            <div>
              <h3 id="selected-title" style="margin:0;font-size:1.2rem;color:#002147;">Course Title</h3>
              <p id="selected-validity" style="margin:6px 0;color:#475569;"></p>
              <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
                <div id="selected-price" style="font-weight:700;color:#0078d4;font-size:1rem;"></div>
                <a id="selected-link" class="tile-link" href="#">View Course</a>
              </div>
            </div>
          </article>
        </div>

        <!-- Modal for course details -->
        <div id="modal-backdrop" class="modal-backdrop" role="dialog" aria-modal="true">
          <div class="modal" role="document">
            <div style="flex:0 0 340px">
              <img id="modal-thumb" src="assets/images/Original.png" alt="course image">
            </div>
            <div class="content">
              <button id="modal-close" class="close-btn" aria-label="Close">✕</button>
              <h3 id="modal-title">Title</h3>
              <div class="meta"><span id="modal-category" class="category-pill"></span><span id="modal-validity" style="margin-left:8px;color:#64748b"></span></div>
              <p id="modal-desc" style="margin-top:12px;color:#334155"></p>
              <div style="margin-top:18px;display:flex;gap:12px;align-items:center">
                <div id="modal-price" style="font-weight:800;color:#0078d4;font-size:1.2rem"></div>
                <a id="modal-enroll" class="tile-link" href="#">Open Course</a>
              </div>
            </div>
          </div>
        </div>

        <div class="tiles" id="course-tiles">
          <?php foreach ($courses as $course): ?>
            <article class="tile" data-category="<?php echo htmlspecialchars($course['category'] ?? ''); ?>" data-id="<?php echo htmlspecialchars($course['id'] ?? $course['title']); ?>">
              <div>
                <img src="<?php echo htmlspecialchars($course['thumbnail'] ?? 'assets/images/Original.png'); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width:100%;height:140px;object-fit:cover;border-radius:12px;margin-bottom:12px;">
                <h3 class="tile-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                <p class="tile-description"><?php echo htmlspecialchars($course['description']); ?></p>
              </div>
              <div class="tile-footer">
                <span class="tile-price"><?php echo htmlspecialchars($course['price']); ?></span>
                <a href="<?php echo htmlspecialchars($course['link']); ?>" class="tile-link">View Course</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
      <section id="why-us" style="margin-top:40px;">
        <div class="section-heading">
          <h2>Why choose KMIT Solutions Services?</h2>
        </div>
        <div class="info-cards">
          <div class="info-card"><h3>Practical Learning</h3><p>Courses are built around real use cases, lab work, and job-focused outcomes.</p></div>
          <div class="info-card"><h3>Expert Mentors</h3><p>Learn from trainers with industry experience and certification-ready guidance.</p></div>
          <div class="info-card"><h3>Career Support</h3><p>Get help with interview preparation, resume guidance, and live training support.</p></div>
        </div>
      </section>
      <?php if ($loggedIn): ?>
      <section id="my-courses" style="margin-top:40px;">
        <div class="section-heading">
          <h2>My Courses</h2>
          <p style="margin:0;color:#64748b;">Welcome back, <?php echo htmlspecialchars($username); ?>. Here are the courses you can access.</p>
        </div>
        <div class="info-cards">
          <div class="info-card"><h3>Enrolled Courses</h3><p>Your enrolled courses will appear here once you start learning.</p></div>
          <div class="info-card"><h3>Progress Tracker</h3><p>Check course progress, upcoming lessons, and next steps from your dashboard.</p></div>
        </div>
      </section>
      <?php endif; ?>
      <section id="contact" style="margin-top:40px;">
        <div class="section-heading">
          <h2>Contact Us</h2>
          <p style="margin:0;color:#64748b;">Have questions? Send us a message or call directly.</p>
        </div>
        <div class="info-cards">
          <div class="info-card"><h3>Phone</h3><p><a href="tel:+918792217562" style="color:#0078d4;">+91-8792217562</a></p></div>
          <div class="info-card"><h3>Email</h3><p><a href="mailto:support@gmail.com" style="color:#0078d4;">support@gmail.com</a></p></div>
        </div>
      </section>
    </main>
    <footer class="footer">
      <p>© <?php echo date('Y'); ?> KMIT Solutions Services. All rights reserved.</p>
    </footer>
    <script>
      const coursesData = <?php echo json_encode($courses, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
      (function(){
          const categoryFilter = document.getElementById('category-filter');
          const courseSelect = document.getElementById('course-select');
          const tilesWrap = document.getElementById('course-tiles');
          const selectedCard = document.getElementById('selected-course');
          const selThumb = document.getElementById('selected-thumbnail');
          const selTitle = document.getElementById('selected-title');
          const selPrice = document.getElementById('selected-price');
          const selValidity = document.getElementById('selected-validity');
          const selLink = document.getElementById('selected-link');

        // populate category dropdown and course dropdown
        const cats = Array.from(new Set(coursesData.map(c => c.category || '').filter(Boolean))).sort();
        cats.forEach(cat => {
          const o = document.createElement('option'); o.value = cat; o.textContent = cat; categoryFilter.appendChild(o);
        });

        function populateCourseSelect(category){
          // clear options
          while (courseSelect.options.length>0) courseSelect.remove(0);
          const optAll = document.createElement('option'); optAll.value='all'; optAll.textContent='All courses'; courseSelect.appendChild(optAll);
          const list = (category==='all') ? coursesData : coursesData.filter(c => c.category===category);
          list.forEach(c => {
            const o = document.createElement('option'); o.value = String(c.id ?? c.title); o.textContent = c.title + (c.category?(' — '+c.category):''); courseSelect.appendChild(o);
          });
          courseSelect.value = 'all';
        }
        // initial populate
        populateCourseSelect('all');

        // show selected course in featured card (returns course object)
        function showCourseById(id){
          const c = coursesData.find(x => String(x.id)===String(id) || x.title===id);
          if (!c) { selectedCard.style.display='none'; return null; }
          selThumb.src = c.thumbnail || 'assets/images/Original.png';
          selTitle.textContent = c.title;
          selPrice.textContent = c.price || '';
          selValidity.textContent = c.validity ? ('Validity: '+c.validity) : '';
          selLink.href = c.link || '#';
          selectedCard.style.display = '';
          return c;
        }

        // modal elements
        const modalBackdrop = document.getElementById('modal-backdrop');
        const modalThumb = document.getElementById('modal-thumb');
        const modalTitle = document.getElementById('modal-title');
        const modalDesc = document.getElementById('modal-desc');
        const modalPrice = document.getElementById('modal-price');
        const modalEnroll = document.getElementById('modal-enroll');
        const modalCategory = document.getElementById('modal-category');
        const modalValidity = document.getElementById('modal-validity');
        const modalClose = document.getElementById('modal-close');

        function openModalForCourse(c){
          if (!c) return;
          modalThumb.src = c.thumbnail || 'assets/images/Original.png';
          modalTitle.textContent = c.title;
          modalDesc.textContent = c.description || c.validity || '';
          modalPrice.textContent = c.price || '';
          modalEnroll.href = c.link || '#';
          modalCategory.textContent = c.category || '';
          modalValidity.textContent = c.validity ? ('Validity: '+c.validity) : '';
          modalBackdrop.style.display = 'flex';
          modalClose.focus();
        }
        modalClose.addEventListener('click', ()=> modalBackdrop.style.display='none');
        modalBackdrop.addEventListener('click', (e)=>{ if (e.target===modalBackdrop) modalBackdrop.style.display='none'; });
        document.addEventListener('keydown', (e)=>{ if (e.key==='Escape') modalBackdrop.style.display='none'; });

        // search box filtering
        const searchBox = document.getElementById('search-box');
        function filterTiles(){
          const cat = categoryFilter.value || 'all';
          const q = (searchBox.value || '').toLowerCase();
          const tiles = tilesWrap.querySelectorAll('.tile');
          tiles.forEach(t => {
            const title = (t.querySelector('.tile-title')?.textContent||'').toLowerCase();
            const category = t.getAttribute('data-category')||'';
            const show = (cat==='all' || category===cat) && (!q || title.indexOf(q) !== -1);
            t.style.display = show ? '' : 'none';
            if (!show) t.style.boxShadow='';
          });
        }

        categoryFilter.addEventListener('change', function(){
          populateCourseSelect(this.value);
          filterTiles();
          selectedCard.style.display = 'none';
        });

        courseSelect.addEventListener('change', function(){
          const v = this.value;
          if (v==='all') { selectedCard.style.display='none'; return; }
          const selected = showCourseById(v);
          const tiles = tilesWrap.querySelectorAll('.tile');
          tiles.forEach(t => t.style.boxShadow='');
          const sel = tilesWrap.querySelector('[data-id="'+v+'"]');
          if (sel) sel.style.boxShadow='0 18px 40px rgba(0,33,71,0.08)';
          openModalForCourse(selected);
        });

        searchBox.addEventListener('input', ()=> filterTiles());

        // clicking a tile selects it and opens modal
        tilesWrap.addEventListener('click', function(e){
          const tile = e.target.closest('.tile'); if (!tile) return;
          const id = tile.getAttribute('data-id'); if (!id) return;
          const cat = tile.getAttribute('data-category') || '';
          if (cat) categoryFilter.value = cat; else categoryFilter.value = 'all';
          populateCourseSelect(cat || 'all');
          courseSelect.value = id;
          const selected = showCourseById(id);
          openModalForCourse(selected);
          selectedCard.scrollIntoView({behavior:'smooth',block:'center'});
        });
      })();
    </script>
  </body>
</html>
