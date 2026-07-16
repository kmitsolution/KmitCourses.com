<?php
// AWS Solution Architect Professional (PSAA) Course Page
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
  <title>AWS Solution Architect Professional (SAP) | KMIT Courses</title>
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
        <h1>AWS Solution Architect Professional (SAP)</h1>
        <p class="muted"><strong>Live Instructor-Led Training</strong> (<strong>40 hours</strong>) designed to prepare you for <strong>AWS Certified Solutions Architect - Professional</strong> certification and complex cloud architectures.</p>
      </div>
      <div class="course-meta">
        <span class="badge">Total Hours: 40</span>
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

          <h3>Section 1: Introduction to AWS and Core Concepts</h3>
          <ul>
            <li>AWS Global Infrastructure (Regions, Availability Zones)</li>
            <li>AWS Core Services Overview: EC2, S3, RDS, VPC, IAM</li>
            <li>AWS Well-Architected Framework</li>
            <li>Cost Optimization Best Practices</li>
          </ul>

          <h3>Section 2: Compute Services</h3>
          <ul>
            <li>EC2 (Elastic Compute Cloud) - Instances, AMIs, Security Groups</li>
            <li>Auto Scaling Groups</li>
            <li>Elastic Load Balancing (ELB)</li>
            <li>Lambda (Serverless Computing)</li>
          </ul>

          <h3>Section 3: Storage Services</h3>
          <ul>
            <li>S3 (Simple Storage Service) - Buckets, Objects, Storage Classes</li>
            <li>EBS (Elastic Block Store) - Volumes, Snapshots</li>
            <li>EFS (Elastic File System)</li>
            <li>Glacier and Lifecycle Management</li>
          </ul>

          <h3>Section 4: Networking and Security</h3>
          <ul>
            <li>VPC (Virtual Private Cloud) - Subnets, Route Tables</li>
            <li>Security Groups and Network ACLs</li>
            <li>IAM (Identity and Access Management) - Users, Roles, Policies</li>
            <li>AWS Config and CloudTrail</li>
          </ul>

          <h3>Section 5: Database Services</h3>
          <ul>
            <li>RDS (Relational Database Service) - MySQL, PostgreSQL, etc.</li>
            <li>DynamoDB (NoSQL Database)</li>
            <li>Aurora</li>
            <li>ElastiCache (Redis/Memcached)</li>
          </ul>

          <h3>Section 6: Monitoring and Management</h3>
          <ul>
            <li>CloudWatch - Metrics, Alarms, Logs</li>
            <li>CloudFormation (Infrastructure as Code)</li>
            <li>Exam Preparation Tips and Scenario Walkthroughs</li>
          </ul>

          <h3>Certification & Exam Preparation</h3>
          <ul>
            <li><strong>Certificate:</strong> AWS Certified Solutions Architect - Professional</li>
            <li><strong>Exam Registration:</strong> <a href="https://aws.amazon.com/certification/certified-solutions-architect-professional/" target="_blank" style="color: #5ab0ff;">Register for AWS SAP Exam</a></li>
            <li><strong>Reference Material:</strong> <a href="https://github.com/kmitsolution/DevOps-Mastering/tree/main/AWS-Architect" target="_blank" style="color: #5ab0ff;">GitHub Repository</a></li>
            <li>Advanced Architecture Patterns and Best Practices</li>
          </ul>
        </section>
      </div>

      <div class="video-sidebar">
        <h2>Demo Videos</h2>
        <div style="display: flex; flex-direction: column; gap: 15px;">
          <iframe src="https://www.youtube.com/embed/videoseries?list=PLQ-AYnT0fM5j53h4z5jmLyrSqBpPOzNrG" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/wgDauLE0_O8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/SDUvAx63s9o" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/d9YmAb4ool0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <iframe src="https://www.youtube.com/embed/TR17nPJpGCk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </div>

    <p class="note">Note: The "Pay Now" button links to Razorpay payment gateway. Please contact us or call +91-9739299502 for any inquiries.</p>
  </div>
</body>
</html>
