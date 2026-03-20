<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

$db        = getDB();
$studentId = (int)$_SESSION['user_id'];
$courseId  = (int)($_GET['course_id'] ?? 0);

if (!$courseId) {
    header('Location: /CMS/student/my_courses.php');
    exit;
}

// Get course details
$course = $db->prepare(
    'SELECT c.*, u.name AS instructor_name
     FROM courses c
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE c.id = ?'
);
$course->execute([$courseId]);
$course = $course->fetch();

if (!$course) {
    header('Location: /CMS/student/my_courses.php');
    exit;
}

// Verify enrollment and 100% completion
$enrollment = $db->prepare('SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?');
$enrollment->execute([$studentId, $courseId]);
$enrollment = $enrollment->fetch();

if (!$enrollment || (int)($enrollment['progress'] ?? 0) < 100) {
    setFlash('error', 'Complete the course to earn your certificate.');
    header('Location: /CMS/student/learning.php?course_id=' . $courseId);
    exit;
}

// Get student info
$student = $db->prepare('SELECT name, email FROM users WHERE id = ?');
$student->execute([$studentId]);
$student = $student->fetch();

// Calculate total duration
$totalMins = $db->prepare('SELECT COALESCE(SUM(duration_minutes),0) as total FROM lessons WHERE course_id=? AND status="active"');
$totalMins->execute([$courseId]);
$totalMins = (int)$totalMins->fetchColumn();
$hours = floor($totalMins / 60);
$mins  = $totalMins % 60;
$durationLabel = $hours > 0 ? $hours . 'h ' . $mins . 'm' : $mins . ' min';

// Lesson count
$lessonCount = $db->prepare('SELECT COUNT(*) FROM lessons WHERE course_id=? AND status="active"');
$lessonCount->execute([$courseId]);
$lessonCount = (int)$lessonCount->fetchColumn();

// Completion date
$completedOn = $db->prepare('SELECT MAX(lp.completed_at) FROM lesson_progress lp JOIN lessons l ON lp.lesson_id=l.id WHERE lp.student_id=? AND l.course_id=? AND lp.completed=1');
$completedOn->execute([$studentId, $courseId]);
$completedOn = $completedOn->fetchColumn();
$completionDate = $completedOn ? date('F d, Y', strtotime($completedOn)) : date('F d, Y');

// Certificate ID
$certId = strtoupper(substr(md5($studentId . '-' . $courseId . '-cert'), 0, 12));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Certificate — <?= htmlspecialchars($course['title']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  background: #0f172a;
  font-family: 'Inter', sans-serif;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px 20px;
}

/* ===== ACTIONS BAR ===== */
.actions-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 1000px;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
}
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 24px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  border: none;
  transition: all .2s ease;
  font-family: 'Inter', sans-serif;
}
.btn-back  { background: #1e293b; color: #94a3b8; }
.btn-back:hover { background: #334155; color: #fff; }

.btn-print { 
  background: linear-gradient(135deg, #1e3b8a, #7c3aed);
  color: #fff; 
  box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}
.btn-print:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4); }

.btn-share { background: transparent; color: #60a5fa; border: 1px solid #1e3b8a; }
.btn-share:hover { background: rgba(30, 59, 138, 0.2); }

/* ===== MODERN LUXURY CERTIFICATE WRAPPER ===== */
.certificate-container {
  width: 100%;
  max-width: 1000px;
  background: #fff;
  position: relative;
  border-radius: 8px;
  box-shadow: 0 30px 60px rgba(0,0,0,0.4), 0 0 100px rgba(124, 58, 237, 0.15);
  overflow: hidden;
}

/* Inner frame line */
.certificate-container::after {
  content: '';
  position: absolute;
  top: 15px; left: 15px; right: 15px; bottom: 15px;
  border: 1px solid rgba(15, 23, 42, 0.08);
  pointer-events: none;
  z-index: 10;
  border-radius: 4px;
}

/* Top Branding Banner */
.cert-hero-band {
  background: linear-gradient(135deg, #1e3b8a 0%, #3b82f6 50%, #7c3aed 100%);
  padding: 50px 60px 70px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  position: relative;
  overflow: hidden;
}

/* Wavy bottom edge for the top banner */
.cert-hero-band::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 100%;
  height: 48px;
  background: white;
  clip-path: ellipse(60% 100% at 50% 100%);
}

.cert-logo {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  z-index: 2;
}
.cert-logo-icon {
  width: 54px; height: 54px;
  background: rgba(255,255,255,0.15);
  border-radius: 12px;
  border: 2px solid rgba(255,255,255,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  color: #fff;
}
.cert-logo-text { color: #fff; }
.cert-logo-title { font-size: 24px; font-weight: 900; letter-spacing: -0.5px; font-family: 'Montserrat', sans-serif;}
.cert-logo-sub { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; opacity: 0.8; }

.cert-badge {
  background: rgba(255,255,255,0.1);
  border: 2px solid rgba(255,255,255,0.25);
  border-radius: 50%;
  width: 80px; height: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
  z-index: 2;
}
.cert-badge-icon { font-size: 28px; line-height: 1; }
.cert-badge-text { font-size: 9px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; font-family: 'Montserrat', sans-serif;}

/* ===== BODY CONTENT ===== */
.cert-body {
  text-align: center;
  padding: 30px 80px 40px;
  position: relative;
  background: #fff;
}

/* Watermark */
.cert-body::before {
  content: 'EDUMANAGE';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-family: 'Montserrat', sans-serif;
  font-size: 90px;
  font-weight: 900;
  color: rgba(15, 23, 42, 0.02);
  pointer-events: none;
  z-index: 0;
  letter-spacing: 5px;
}

.intro-text {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 3px;
  margin-bottom: 30px;
  position: relative;
  z-index: 2;
  font-family: 'Montserrat', sans-serif;
}

.presentation {
  font-family: 'Playfair Display', serif;
  font-style: italic;
  font-size: 18px;
  color: #475569;
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
}

.student-name {
  font-family: 'Dancing Script', cursive;
  font-size: 68px;
  font-weight: 700;
  background: linear-gradient(135deg, #1e3b8a, #7c3aed);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.2;
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
  display: inline-block;
  padding: 0 40px;
}
.student-name::after {
  content: '';
  position: absolute;
  bottom: 5px; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(124, 58, 237, 0.4), transparent);
}

.completion {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  margin-bottom: 15px;
  position: relative;
  z-index: 2;
}

.course-title {
  font-family: 'Playfair Display', serif;
  font-size: 32px;
  font-weight: 900;
  color: #0f172a;
  margin-bottom: 12px;
  position: relative;
  z-index: 2;
  line-height: 1.3;
}

.course-meta {
  display: inline-block;
  background: #f8fafc;
  color: #3b82f6;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  padding: 8px 20px;
  border-radius: 99px;
  border: 1px solid #e2e8f0;
  margin-bottom: 40px;
  position: relative;
  z-index: 2;
  font-family: 'Montserrat', sans-serif;
}

/* Stats */
.stats-grid {
  display: flex;
  justify-content: center;
  gap: 0;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
  padding: 20px 0;
  margin-bottom: 40px;
  position: relative;
  z-index: 2;
}
.stat-box { flex: 1; border-right: 1px solid #f1f5f9; }
.stat-box:last-child { border-right: none; }
.stat-value { font-size: 24px; font-weight: 900; color: #1e3b8a; font-family: 'Montserrat', sans-serif; }
.stat-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

/* Signatures */
.signatures-area {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  position: relative;
  z-index: 2;
  padding: 0 40px;
}

.sig-block { width: 220px; text-align: center; }
.sig-img {
  font-family: 'Dancing Script', cursive;
  font-size: 32px;
  color: #1e3b8a;
  margin-bottom: 5px;
  height: 40px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.sig-line { border-bottom: 2px solid #e2e8f0; margin: 8px 0; }
.sig-name { font-size: 13px; font-weight: 700; color: #0f172a; font-family: 'Montserrat', sans-serif; }
.sig-role { font-size: 10px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 2px; }

/* Modern Seal Ribbon */
.modern-seal {
  width: 90px; height: 90px;
  background: linear-gradient(135deg, #1e3b8a, #7c3aed);
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 10px 25px rgba(30, 59, 138, 0.3);
  position: relative;
}
.modern-seal::after {
  content: '';
  position: absolute;
  top: 4px; left: 4px; right: 4px; bottom: 4px;
  border: 1px dashed rgba(255,255,255,0.4);
  border-radius: 50%;
}
.modern-seal-check { font-size: 24px; margin-bottom: 2px; }
.modern-seal-text { font-size: 9px; font-weight: 800; letter-spacing: 1px; font-family: 'Montserrat', sans-serif; }

/* ===== FOOTER STRIP ===== */
.cert-footer-strip {
  background: #f8fafc;
  padding: 16px 40px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-family: 'Montserrat', sans-serif;
}
.strip-id { font-size: 11px; font-weight: 700; color: #64748b; }
.strip-id span { color: #0f172a; font-family: monospace; font-size: 13px; }
.strip-date { font-size: 11px; font-weight: 600; color: #64748b; }

/* ===== PRINT STYLES ===== */
@media print {
  @page { size: landscape; margin: 0; }
  body { background: #fff; padding: 0; width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; }
  .actions-bar, .info-cards { display: none !important; }
  .certificate-container { box-shadow: none; border: none; max-width: none; width: 1050px; zoom: 95%; }
  /* Ensure backgrounds print */
  * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; print-color-adjust: exact !important; }
}

/* Confetti */
.confetti-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; }
.cp { position: absolute; width: 10px; height: 10px; opacity: 0; border-radius: 2px; }
@keyframes fall { 
  0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
  100% { transform: translateY(110vh) rotate(720deg); opacity: 0; } 
}
</style>
</head>
<body>

<!-- Confetti Container -->
<div class="confetti-container" id="confettiContainer"></div>

<!-- Actions Bar -->
<div class="actions-bar">
  <div style="display:flex;gap:12px;">
    <a href="/CMS/student/learning.php?course_id=<?= $courseId ?>" class="btn btn-back">
      ← Back
    </a>
    <a href="/CMS/student/my_courses.php" class="btn btn-back" style="background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.2); color: #60a5fa;">
      My Courses
    </a>
  </div>
  <div style="display:flex;gap:12px;align-items:center;">
    <button onclick="shareCert()" class="btn btn-share">
      <span style="font-size:16px;line-height:1;">🔗</span> Share Link
    </button>
    <button onclick="window.print()" class="btn btn-print">
      <span style="font-size:16px;line-height:1;">🖨️</span> Save as PDF / Print
    </button>
  </div>
</div>

<!-- LUXURY CERTIFICATE CONTAINER -->
<div class="certificate-container">

  <!-- HERO TOP BAND -->
  <div class="cert-hero-band">
    <div class="cert-logo">
      <div class="cert-logo-icon">🎓</div>
      <div class="cert-logo-text">
        <div class="cert-logo-title">EduManage</div>
        <div class="cert-logo-sub">Course Management System</div>
      </div>
    </div>
    
    <div class="cert-badge">
      <div class="cert-badge-icon">🏆</div>
      <div class="cert-badge-text">Certified</div>
    </div>
  </div>

  <!-- MAIN CERT BODY -->
  <div class="cert-body">
    <div class="intro-text">Certificate of Completion</div>
    <div class="presentation">This is to proudly certify that</div>
    
    <div class="student-name"><?= htmlspecialchars($student['name']) ?></div>
    
    <div class="completion">has successfully completed the course</div>
    
    <div class="course-title"><?= htmlspecialchars($course['title']) ?></div>
    
    <div class="course-meta">
      Academic Year: <?= htmlspecialchars($course['academic_year']) ?> · <?= htmlspecialchars($course['category'] ?? 'General') ?>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-box">
        <div class="stat-value"><?= $lessonCount ?></div>
        <div class="stat-label">Lessons <br>Completed</div>
      </div>
      <div class="stat-box">
        <div class="stat-value"><?= $durationLabel ?></div>
        <div class="stat-label">Total <br>Duration</div>
      </div>
      <div class="stat-box">
        <div class="stat-value">100%</div>
        <div class="stat-label">Passing <br>Score</div>
      </div>
      <div class="stat-box">
        <div class="stat-value"><?= date('M Y', strtotime($completedOn ?: 'now')) ?></div>
        <div class="stat-label">Issue <br>Month</div>
      </div>
    </div>

    <!-- Signatures & Seal -->
    <div class="signatures-area">
      <div class="sig-block">
        <div class="sig-img"><?= htmlspecialchars(explode(' ', $course['instructor_name'] ?? 'Instructor')[0]) ?></div>
        <div class="sig-line"></div>
        <div class="sig-name"><?= htmlspecialchars($course['instructor_name'] ?? 'EduManage') ?></div>
        <div class="sig-role">Course Instructor</div>
      </div>

      <div class="modern-seal">
        <div class="modern-seal-check">✓</div>
        <div class="modern-seal-text">VERIFIED</div>
      </div>

      <div class="sig-block">
        <div class="sig-img">Director</div>
        <div class="sig-line"></div>
        <div class="sig-name">EduManage Admin</div>
        <div class="sig-role">Platform Director</div>
      </div>
    </div>

  </div> <!-- /cert-body -->

  <!-- FOOTER STRIP -->
  <div class="cert-footer-strip">
    <div class="strip-id">Credential ID: <span><?= $certId ?></span></div>
    <div class="strip-date">Issued: <span><?= $completionDate ?></span></div>
  </div>

</div>


<script>
// Confetti Animation (Blue & Purple brand colors)
function launchConfetti() {
  const container = document.getElementById('confettiContainer');
  const colors = ['#1e3b8a', '#3b82f6', '#60a5fa', '#7c3aed', '#a78bfa', '#cbd5e1']; 
  for (let i = 0; i < 90; i++) {
    const el = document.createElement('div');
    el.className = 'cp';
    el.style.left         = Math.random() * 100 + 'vw';
    
    // Vary shapes
    if (Math.random() > 0.5) {
      el.style.width = (Math.random() * 8 + 4) + 'px';
      el.style.height = el.style.width;
      el.style.borderRadius = '50%';
    } else {
      el.style.width = (Math.random() * 6 + 4) + 'px';
      el.style.height = (Math.random() * 12 + 8) + 'px';
    }

    el.style.background = colors[Math.floor(Math.random() * colors.length)];
    el.style.animationName = 'fall';
    el.style.animationTimingFunction = 'linear';
    el.style.animationFillMode = 'forwards';
    el.style.animationDuration  = (Math.random() * 2.5 + 1.5) + 's';
    el.style.animationDelay     = (Math.random() * 1.5) + 's';
    
    container.appendChild(el);
    setTimeout(() => el.remove(), 5000);
  }
}
window.addEventListener('load', () => { launchConfetti(); });

// Share Feature
function shareCert() {
  const text = `🎓 I just earned my completion certificate for "${<?= json_encode($course['title']) ?>}"!\nVerify my credential ID: <?= $certId ?>`;
  if (navigator.share) {
    navigator.share({ title: 'My Official Certificate', text: text, url: window.location.href });
  } else {
    navigator.clipboard.writeText(text + '\n' + window.location.href)
      .then(() => { alert('Certificate link copied to clipboard!'); });
  }
}
</script>

</body>
</html>
