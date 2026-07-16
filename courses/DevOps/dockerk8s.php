<?php
// Docker & Kubernetes Course Page
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
  <title>Docker & Kubernetes Mastery | KMIT Courses</title>
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
        <h1>Docker & Kubernetes Mastery</h1>
        <p class="muted"><strong>Live Instructor-Led Training</strong> (<strong>30 hours</strong>) designed to master containerization with Docker and orchestration with Kubernetes.</p>
        <p class="muted" style="margin-top: 8px; font-style: italic;">Build scalable applications • Master container orchestration • Prepare for CKAD & CKA certifications</p>
      </div>
      <div class="course-meta">
        <span class="badge">Total Hours: 30</span>
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

          <h3>Section 1: Docker Introduction</h3>
          <ul>
            <li>What is Containerization and Why Docker?</li>
            <li>Docker Architecture and Components</li>
            <li>Installing Docker on Different Platforms</li>
            <li>Docker Desktop and Docker Engine</li>
            <li>Docker Hub and Container Registries</li>
          </ul>

          <h3>Section 2: Docker Images</h3>
          <ul>
            <li>Understanding Docker Images</li>
            <li>Creating Custom Images</li>
            <li>Image Layers and Optimization</li>
            <li>Tagging and Versioning Images</li>
            <li>Image Security Best Practices</li>
          </ul>

          <h3>Section 3: Docker Containers</h3>
          <ul>
            <li>Running Containers from Images</li>
            <li>Container Lifecycle Management</li>
            <li>Container Resource Limits</li>
            <li>Container Logging and Monitoring</li>
            <li>Container Debugging Techniques</li>
          </ul>

          <h3>Section 4: Docker File</h3>
          <ul>
            <li>Dockerfile Syntax and Instructions</li>
            <li>Best Practices for Writing Dockerfiles</li>
            <li>Multi-stage Builds</li>
            <li>Dockerfile Security Considerations</li>
            <li>Building Efficient Images</li>
          </ul>

          <h3>Section 5: Docker Compose</h3>
          <ul>
            <li>Introduction to Docker Compose</li>
            <li>Compose File Structure (docker-compose.yml)</li>
            <li>Multi-container Applications</li>
            <li>Networking Between Containers</li>
            <li>Environment Variables and Configuration</li>
          </ul>

          <h3>Section 6: Docker Networking</h3>
          <ul>
            <li>Docker Network Types (Bridge, Host, Overlay)</li>
            <li>Custom Network Creation</li>
            <li>Container Communication</li>
            <li>Network Security and Isolation</li>
            <li>Service Discovery</li>
          </ul>

          <h3>Section 7: Docker Swarm</h3>
          <ul>
            <li>Introduction to Docker Swarm</li>
            <li>Creating and Managing Swarm Clusters</li>
            <li>Services and Stacks</li>
            <li>Load Balancing and Scaling</li>
            <li>Swarm Security and Best Practices</li>
          </ul>

          <h3>Section 8: Kubernetes Fundamentals</h3>
          <ul>
            <li>Kubernetes Architecture Overview</li>
            <li>Pods, Nodes, and Clusters</li>
            <li>Kubectl Command Line Tool</li>
            <li>Kubernetes Objects and Resources</li>
            <li>Namespaces and Context Management</li>
          </ul>

          <h3>Section 9: Kubernetes Workloads</h3>
          <ul>
            <li>Deployments and ReplicaSets</li>
            <li>Services and Ingress</li>
            <li>ConfigMaps and Secrets</li>
            <li>Persistent Volumes and Claims</li>
            <li>Jobs and CronJobs</li>
          </ul>

          <h3>Section 10: Kubernetes Advanced Topics</h3>
          <ul>
            <li>Helm Package Manager</li>
            <li>Kubernetes Security</li>
            <li>Monitoring and Logging</li>
            <li>Cluster Administration</li>
            <li>Troubleshooting Kubernetes</li>
          </ul>

          <h3>Kubernetes Certifications</h3>
          <ul>
            <li><strong>CKA (Certified Kubernetes Administrator):</strong> Validates skills to perform Kubernetes cluster administration tasks</li>
            <li><strong>CKAD (Certified Kubernetes Application Developer):</strong> Focuses on designing, building, and deploying cloud-native applications</li>
            <li><strong>Exam Registration:</strong> <a href="https://training.linuxfoundation.org/certification/certified-kubernetes-administrator-cka/" target="_blank" style="color: #5ab0ff;">Register for CKA</a> | <a href="https://training.linuxfoundation.org/certification/certified-kubernetes-application-developer-ckad/" target="_blank" style="color: #5ab0ff;">Register for CKAD</a></li>
            <li><strong>Reference Material:</strong> <a href="https://github.com/kmitsolution/DevOps-Mastering/tree/main/On-Prem-DevOps" target="_blank" style="color: #5ab0ff;">GitHub Repository</a></li>
            <li>Hands-on Labs and Practice Exams</li>
          </ul>
        </section>
      </div>

      <div class="video-sidebar">
        <h2>Demo Videos</h2>
        <div style="display: flex; flex-direction: column; gap: 15px;">
          <iframe src="https://www.youtube.com/embed/ozd22LKP8HU" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/jvUT5E_okWY" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/jF4KEuTTBDM" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/OqVGz_RKUUk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/lp3tIhj_eyI" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </div>

    <p class="note">Note: The "Pay Now" button links to Razorpay payment gateway. Please contact us or call +91-9739299502 for any inquiries.</p>
  </div>
</body>
</html>
