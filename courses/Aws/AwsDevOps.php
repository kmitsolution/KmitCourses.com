<?php
// AWS DevOps Engineer Professional Course Page
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
  <title>AWS DevOps Engineer Professional | KMIT Courses</title>
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
        <h1>AWS DevOps Engineer Professional</h1>
        <p class="muted"><strong>Live Instructor-Led Training</strong> (<strong>40 hours</strong>) designed to prepare you for <strong>AWS Certified DevOps Engineer - Professional</strong> certification and advanced DevOps practices on AWS.</p>
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

          <h3>Section 1: DevOps Fundamentals & AWS Core Services</h3>
          <ul>
            <li>DevOps Principles and Culture</li>
            <li>AWS Global Infrastructure and Core Services</li>
            <li>IAM, VPC, EC2, S3, RDS Fundamentals</li>
            <li>AWS Well-Architected Framework for DevOps</li>
          </ul>

          <h3>Section 2: CI/CD with AWS Developer Tools</h3>
          <ul>
            <li>CodeCommit - Source Control</li>
            <li>CodeBuild - Build and Test Automation</li>
            <li>CodeDeploy - Application Deployment</li>
            <li>CodePipeline - CI/CD Orchestration</li>
            <li>AWS CodeStar - Project Management</li>
          </ul>

          <h3>Section 3: Infrastructure as Code</h3>
          <ul>
            <li>CloudFormation Templates and Stacks</li>
            <li>CloudFormation Best Practices</li>
            <li>Change Sets and Stack Updates</li>
            <li>Custom Resources and Macros</li>
          </ul>

          <h3>Section 4: Configuration Management</h3>
          <ul>
            <li>AWS Systems Manager (SSM)</li>
            <li>Parameter Store and Documents</li>
            <li>OpsWorks and OpsWorks for Chef</li>
            <li>Configuration Compliance</li>
          </ul>

          <h3>Section 5: Monitoring & Logging</h3>
          <ul>
            <li>CloudWatch Metrics, Alarms, and Dashboards</li>
            <li>CloudWatch Logs and Log Insights</li>
            <li>X-Ray for Application Tracing</li>
            <li>CloudTrail for API Auditing</li>
          </ul>

          <h3>Section 6: Security & Compliance</h3>
          <ul>
            <li>AWS Security Best Practices</li>
            <li>AWS Config Rules and Compliance</li>
            <li>AWS Inspector and Security Assessments</li>
            <li>Secrets Manager and Certificate Manager</li>
          </ul>

          <h3>Section 7: High Availability & Disaster Recovery</h3>
          <ul>
            <li>Multi-Region Deployments</li>
            <li>Auto Scaling and Load Balancing</li>
            <li>Backup and Recovery Strategies</li>
            <li>Route 53 for DNS and Failover</li>
          </ul>

          <h3>Certification & Exam Preparation</h3>
          <ul>
            <li><strong>Certificate:</strong> AWS Certified DevOps Engineer - Professional</li>
            <li><strong>Exam Registration:</strong> <a href="https://aws.amazon.com/certification/certified-devops-engineer-professional/" target="_blank" style="color: #5ab0ff;">Register for AWS DevOps Pro Exam</a></li>
            <li><strong>Reference Material:</strong> <a href="https://github.com/kmitsolution/DevOps-Mastering/tree/main/AWS-DevOps" target="_blank" style="color: #5ab0ff;">GitHub Repository</a></li>
            <li>Exam Tips and Practice Scenarios</li>
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
