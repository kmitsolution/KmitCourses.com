<?php
// Mastering DevOps Course Page
require_once '../../config.php';
session_start();
$courseid = $_GET['courseid'] ?? null;
$price = 'Contact for pricing';
if ($courseid) {
    try {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT price FROM course WHERE courseid = ?');
        $stmt->execute([$courseid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['price']) && $result['price'] !== null) {
            $price = '₹' . number_format($result['price'], 0);
        }
    } catch (Exception $e) {
        $price = 'Contact for pricing';
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mastering DevOps | KMIT Courses</title>
  <link rel="stylesheet" href="../../assets/style.css" />
  <style>
    body { background: #0b1020; color: #e8edff; }
    .course-page { max-width: 980px; margin: 0 auto; padding: 32px 20px; }
    .course-header { display: grid; gap: 18px; margin-bottom: 28px; }
    .course-header h1 { margin: 0; font-size: 2.5rem; }
    .course-meta { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .badge { background: rgba(90,176,255,.15); border: 1px solid rgba(90,176,255,.3); color: #e8edff; }
    .content-layout { display: flex; gap: 28px; align-items: flex-start; }
    .main-content { flex: 1; }
    .video-sidebar { flex: 0 0 420px; }
    .video-sidebar h2 { margin-top: 0; }
    .video-sidebar iframe { width: 100%; height: 180px; border-radius: 8px; }
    @media (max-width: 768px) {
      .content-layout { flex-direction: column; }
      .video-sidebar { flex: none; order: -1; }
    }
    .topics { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 14px; padding: 22px; margin-bottom: 28px; }
    .topics h2 { margin-top: 0; }
    .topics h3 { margin-top: 20px; margin-bottom: 10px; color: #5ab0ff; }
    .topics ul { margin: 0; padding-left: 18px; }
    .topics li { margin-bottom: 10px; }
    .note { margin-top: 18px; color: rgba(255,255,255,.7); font-size: 0.95rem; }
  </style>
</head>
<body>
  <div class="course-page">
    <header class="course-header">
      <div>
        <h1>Mastering DevOps</h1>
        <p class="muted"><strong>Live Instructor-Led Training</strong> (<strong>35 hours</strong>) designed to master DevOps practices and tools for efficient software delivery.</p>
        <p class="muted" style="margin-top: 8px; font-style: italic;">Create CI/CD pipelines using Jenkins • Automate deployments with Docker & Kubernetes • Master infrastructure as code</p>
      </div>
      <div class="course-meta">
        <span class="badge">Total Hours: 35</span>
        <span class="badge">Pay: 
<?php if (strpos($price, '₹') === 0): ?>
  <?php if (isset($_SESSION['studentid'])): ?>
    <a href="https://razorpay.me/@kmitsolutionsservices" target="_blank" style="color:#5ab0ff; text-decoration:underline;">Pay <?php echo htmlspecialchars($price); ?></a>
  <?php else: ?>
    <a href="#" onclick="alert('Please signup or login first'); return false;" style="color:#5ab0ff; text-decoration:underline;">Pay <?php echo htmlspecialchars($price); ?></a>
  <?php endif; ?>
<?php else: ?>
  <?php echo htmlspecialchars($price); ?>
<?php endif; ?>
        </span>
        <span class="badge">Mode: Live Online Class</span>
      </div>
    </header>

    <div class="content-layout">
      <div class="main-content">
        <section class="topics">
          <h2>Course Content</h2>

          <h3>Section 1: DevOps Fundamentals & SDLC</h3>
          <ul>
            <li>Understanding Software Development Life Cycle (SDLC)</li>
            <li>DevOps Principles and Culture</li>
            <li>Agile vs Waterfall Methodologies</li>
            <li>Continuous Integration and Continuous Deployment (CI/CD)</li>
          </ul>

          <h3>Section 2: Version Control with Git & GitHub</h3>
          <ul>
            <li>Git Fundamentals - Commits, Branches, Merging</li>
            <li>GitHub - Pull Requests, Issues, Actions</li>
            <li>Branching Strategies and Git Flow</li>
            <li>Collaborative Development Workflows</li>
          </ul>

          <h3>Section 3: Build Automation with Maven</h3>
          <ul>
            <li>Maven Build Lifecycle and Phases</li>
            <li>POM Configuration and Dependencies</li>
            <li>Multi-module Projects</li>
            <li>Maven Plugins and Custom Builds</li>
          </ul>

          <h3>Section 4: Artifact Management with JFrog Artifactory</h3>
          <ul>
            <li>Artifactory Setup and Configuration</li>
            <li>Repository Types and Management</li>
            <li>Artifact Storage and Versioning</li>
            <li>Integration with CI/CD Pipelines</li>
          </ul>

          <h3>Section 5: Containerization with Docker</h3>
          <ul>
            <li>Docker Fundamentals - Images, Containers, Dockerfile</li>
            <li>Docker Compose for Multi-container Applications</li>
            <li>Docker Networking and Volumes</li>
            <li>Docker Registry and Image Management</li>
          </ul>

          <h3>Section 6: Orchestration with Kubernetes</h3>
          <ul>
            <li>Kubernetes Architecture and Components</li>
            <li>Pods, Services, and Deployments</li>
            <li>ConfigMaps, Secrets, and Persistent Volumes</li>
            <li>Kubernetes Networking and Service Mesh</li>
          </ul>

          <h3>Section 7: Configuration Management with Ansible</h3>
          <ul>
            <li>Ansible Playbooks and Roles</li>
            <li>Inventory Management and Variables</li>
            <li>Ansible Tower/AWX for Enterprise</li>
            <li>Integration with Cloud Platforms</li>
          </ul>

          <h3>Section 8: CI/CD with Jenkins</h3>
          <ul>
            <li>Jenkins Installation and Configuration</li>
            <li>Creating CI/CD Pipelines using Jenkins</li>
            <li>Jenkins Plugins and Integrations</li>
            <li>Pipeline as Code with Jenkinsfile</li>
          </ul>

          <h3>Reference Materials & Resources</h3>
          <ul>
            <li><strong>Reference Material:</strong> <a href="https://github.com/kmitsolution/DevOps-Mastering/tree/main/On-Prem-DevOps" target="_blank" style="color: #5ab0ff;">GitHub Repository</a></li>
            <li>Hands-on Labs and Real-world Projects</li>
            <li>Industry Best Practices and Case Studies</li>
          </ul>
        </section>
      </div>

      <div class="video-sidebar">
        <h2>Demo Videos</h2>
        <div style="display: flex; flex-direction: column; gap: 15px;">
          <iframe src="https://www.youtube.com/embed/qM9qCnnl0cU" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/Kat8Kscvqdc" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/HwP3SKRX9pg" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/jvUT5E_okWY" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </div>

    <p class="note">Note: The "Pay Now" button links to Razorpay payment gateway. Please contact us or call +91-9739299502 for any inquiries.</p>
  </div>
</body>
</html>
