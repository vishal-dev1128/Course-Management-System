<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

$db        = getDB();
$pageTitle = 'Learning';
$studentId = (int)$_SESSION['user_id'];

$courseId = (int)($_GET['course_id'] ?? 0);
if (!$courseId) {
    header('Location: /CMS/student/my_courses.php');
    exit;
}

// Fetch course
$course = $db->prepare(
    'SELECT c.*, u.name AS instructor_name
     FROM courses c
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE c.id = ?'
);
$course->execute([$courseId]);
$course = $course->fetch();

if (!$course) {
    setFlash('error', 'Course not found.');
    header('Location: /CMS/student/my_courses.php');
    exit;
}

// Verify enrollment
$enrollment = $db->prepare('SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?');
$enrollment->execute([$studentId, $courseId]);
$enrollment = $enrollment->fetch();

if (!$enrollment) {
    setFlash('error', 'You are not enrolled in this course.');
    header('Location: /CMS/student/catalog.php');
    exit;
}

// Fetch all lessons
$lessonsStmt = $db->prepare(
    'SELECT * FROM lessons WHERE course_id = ? AND status = "active" ORDER BY order_num ASC'
);
$lessonsStmt->execute([$courseId]);
$lessons = $lessonsStmt->fetchAll();

// Completed lessons
$completedStmt = $db->prepare('SELECT lesson_id FROM lesson_progress WHERE student_id = ? AND completed = 1');
$completedStmt->execute([$studentId]);
$completedLessons = array_column($completedStmt->fetchAll(), 'lesson_id');

$totalLessons   = count($lessons);
$completedCount = count(array_intersect($completedLessons, array_column($lessons, 'id')));
$overallProgress = $totalLessons > 0 ? (int)(($completedCount / $totalLessons) * 100) : 0;
$isCourseComplete = ($totalLessons > 0 && $completedCount === $totalLessons);

// Handle POST: mark lesson complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'complete_lesson') {
        $lessonId = (int)($_POST['lesson_id'] ?? 0);
        $check = $db->prepare('SELECT id FROM lessons WHERE id = ? AND course_id = ?');
        $check->execute([$lessonId, $courseId]);
        if ($check->fetch()) {
            $existing = $db->prepare('SELECT id FROM lesson_progress WHERE student_id = ? AND lesson_id = ?');
            $existing->execute([$studentId, $lessonId]);
            if ($existing->fetch()) {
                $upd = $db->prepare('UPDATE lesson_progress SET completed = 1, completed_at = NOW() WHERE student_id = ? AND lesson_id = ?');
                $upd->execute([$studentId, $lessonId]);
            } else {
                $ins = $db->prepare('INSERT INTO lesson_progress (student_id, lesson_id, completed, completed_at) VALUES (?, ?, 1, NOW())');
                $ins->execute([$studentId, $lessonId]);
            }
            // Recalculate progress
            $newCompleted = $db->prepare('SELECT COUNT(*) as cnt FROM lesson_progress lp JOIN lessons l ON lp.lesson_id=l.id WHERE lp.student_id=? AND l.course_id=? AND lp.completed=1');
            $newCompleted->execute([$studentId, $courseId]);
            $newCount = (int)$newCompleted->fetchColumn();
            $newProgress = $totalLessons > 0 ? (int)(($newCount / $totalLessons) * 100) : 0;
            $updProg = $db->prepare('UPDATE enrollments SET progress = ? WHERE student_id = ? AND course_id = ?');
            $updProg->execute([$newProgress, $studentId, $courseId]);
        }
        $nextLesson = (int)($_POST['next_lesson_id'] ?? 0);
        $redirect = "/CMS/student/learning.php?course_id=$courseId";
        if ($nextLesson) $redirect .= "&lesson_id=$nextLesson";
        header("Location: $redirect");
        exit;
    }
}

// Active lesson (from URL or default first)
$activeLessonId = (int)($_GET['lesson_id'] ?? ($lessons[0]['id'] ?? 0));
$activeLesson   = null;
$activeLessonIndex = 0;
foreach ($lessons as $idx => $l) {
    if ($l['id'] == $activeLessonId) {
        $activeLesson      = $l;
        $activeLessonIndex = $idx;
        break;
    }
}
if (!$activeLesson && !empty($lessons)) {
    $activeLesson      = $lessons[0];
    $activeLessonId    = $lessons[0]['id'];
    $activeLessonIndex = 0;
}

$prevLesson = $activeLessonIndex > 0 ? $lessons[$activeLessonIndex - 1] : null;
$nextLesson = $activeLessonIndex < count($lessons) - 1 ? $lessons[$activeLessonIndex + 1] : null;

$categoryIcons = [
    'Programming'     => 'terminal',
    'Web Development' => 'html',
    'Data Science'    => 'monitoring',
    'Design'          => 'palette',
    'default'         => 'menu_book',
];
$icon = $categoryIcons[$course['category'] ?? ''] ?? $categoryIcons['default'];

require_once '../includes/student_sidebar.php';
?>

<style>
/* ===== PREMIUM LEARNING PAGE STYLES ===== */
.learn-layout { display: flex; height: calc(100vh - 0px); flex-direction: column; }
.learn-header  { flex-shrink: 0; }
.learn-body    { display: flex; flex: 1; overflow: hidden; }
.lesson-sidebar { width: 300px; flex-shrink: 0; overflow-y: auto; background: #fff; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; }
.dark .lesson-sidebar { background: #0f172a; border-color: #1e293b; }
.lesson-main { flex: 1; overflow-y: auto; background: #f8fafc; }
.dark .lesson-main { background: #0a0f1e; }

/* Lesson list items */
.lesson-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; cursor: pointer; border-radius: 10px; margin: 2px 8px; transition: all .18s; border: 1.5px solid transparent; }
.lesson-item:hover { background: #f1f5f9; }
.dark .lesson-item:hover { background: #1e293b; }
.lesson-item.active { background: #eff6ff; border-color: #bfdbfe; }
.dark .lesson-item.active { background: #1e3054; border-color: #1e3b8a; }
.lesson-item.completed .lesson-num { background: #d1fae5; color: #059669; }
.lesson-num { width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 900; flex-shrink: 0; color: #64748b; }
.dark .lesson-num { background: #1e293b; color: #94a3b8; }

/* Tabs */
.learn-tabs { display: flex; border-bottom: 2px solid #e2e8f0; background: #fff; gap: 0; }
.dark .learn-tabs { background: #0f172a; border-color: #1e293b; }
.learn-tab { padding: 14px 24px; font-size: 13px; font-weight: 700; cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all .2s; color: #64748b; display: flex; align-items: center; gap: 6px; letter-spacing: .02em; }
.learn-tab:hover { color: #1e3b8a; background: #f8fafc; }
.dark .learn-tab:hover { background: #1e293b; color: #60a5fa; }
.learn-tab.active { color: #1e3b8a; border-bottom-color: #1e3b8a; background: transparent; }
.dark .learn-tab.active { color: #60a5fa; border-bottom-color: #60a5fa; }
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* Content styling */
.lesson-content-area { padding: 32px 40px; max-width: 860px; }
.lesson-content-area h2 { font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 12px; margin-top: 28px; }
.lesson-content-area h3 { font-size: 1.1rem; font-weight: 700; color: #1e3b8a; margin-bottom: 10px; margin-top: 22px; }
.dark .lesson-content-area h2 { color: #f1f5f9; }
.dark .lesson-content-area h3 { color: #60a5fa; }
.lesson-content-area p { color: #475569; line-height: 1.75; margin-bottom: 14px; }
.dark .lesson-content-area p { color: #94a3b8; }
.lesson-content-area pre { background: #0f172a; color: #e2e8f0; border-radius: 10px; padding: 20px; font-family: 'Fira Code', 'Courier New', monospace; font-size: 13px; overflow-x: auto; margin: 16px 0; line-height: 1.7; border: 1px solid #1e293b; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
.lesson-content-area ul, .lesson-content-area ol { padding-left: 24px; color: #475569; margin-bottom: 14px; }
.dark .lesson-content-area ul, .dark .lesson-content-area ol { color: #94a3b8; }
.lesson-content-area li { margin-bottom: 6px; line-height: 1.6; }
.lesson-content-area strong { color: #0f172a; font-weight: 700; }
.dark .lesson-content-area strong { color: #e2e8f0; }

/* Resource section (extracted from lesson content) */
.lesson-resources { background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #bfdbfe; border-radius: 14px; padding: 20px 24px; margin-top: 28px; }
.dark .lesson-resources { background: linear-gradient(135deg, #1e3054 0%, #0f2e2e 100%); border-color: #1e3b8a; }
.lesson-resources h3 { color: #1e3b8a; font-size: 1rem; font-weight: 800; margin-top: 0; margin-bottom: 14px; }
.dark .lesson-resources h3 { color: #60a5fa; }
.lesson-resources ul { padding-left: 0; list-style: none; margin: 0; }
.lesson-resources li { margin-bottom: 10px; }
.lesson-resources a { display: inline-flex; align-items: center; gap: 6px; color: #1e3b8a; font-size: 13.5px; font-weight: 600; text-decoration: none; padding: 6px 12px; background: #fff; border: 1px solid #dbeafe; border-radius: 8px; transition: all .2s; }
.lesson-resources a::before { content: "🔗"; font-size: 13px; }
.lesson-resources a:hover { background: #1e3b8a; color: #fff; border-color: #1e3b8a; transform: translateX(2px); }
.dark .lesson-resources a { background: #1e293b; color: #60a5fa; border-color: #1e3b8a; }
.dark .lesson-resources a:hover { background: #1e3b8a; color: #fff; }

/* Progress bar */
.progress-bar-track { height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.dark .progress-bar-track { background: #1e293b; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #1e3b8a, #3b82f6); border-radius: 99px; transition: width .4s ease; }

/* Video embed */
.video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 14px; background: #000; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }

/* Certificate banner */
.cert-banner { background: linear-gradient(135deg, #1e3b8a 0%, #0ea5e9 50%, #7c3aed 100%); border-radius: 18px; padding: 36px; text-align: center; color: #fff; position: relative; overflow: hidden; }
.cert-banner::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
.confetti-particle { position: absolute; width: 8px; height: 8px; border-radius: 2px; animation: confetti-fall 3s ease-in infinite; }
@keyframes confetti-fall { 0% { transform: translateY(-20px) rotate(0deg); opacity: 1; } 100% { transform: translateY(300px) rotate(720deg); opacity: 0; } }

/* Scrollbar */
.lesson-sidebar::-webkit-scrollbar { width: 4px; }
.lesson-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.dark .lesson-sidebar::-webkit-scrollbar-thumb { background: #334155; }

/* Bottom nav */
.lesson-bottom-nav { display: flex; align-items: center; justify-content: space-between; padding: 16px 40px; background: #fff; border-top: 1px solid #e2e8f0; flex-shrink: 0; }
.dark .lesson-bottom-nav { background: #0f172a; border-color: #1e293b; }
.nav-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; transition: all .2s; cursor: pointer; border: none; }
.nav-btn-prev { background: #f1f5f9; color: #475569; }
.nav-btn-prev:hover { background: #e2e8f0; }
.dark .nav-btn-prev { background: #1e293b; color: #94a3b8; }
.dark .nav-btn-prev:hover { background: #334155; }
.nav-btn-next { background: #1e3b8a; color: #fff; box-shadow: 0 4px 14px rgba(30,59,138,0.35); }
.nav-btn-next:hover { background: #1a327a; transform: translateX(2px); }
.nav-btn-complete { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 4px 14px rgba(16,185,129,0.35); }
.nav-btn-complete:hover { filter: brightness(1.05); }
.mark-done-badge { display: inline-flex; align-items: center; gap: 6px; background: #d1fae5; color: #065f46; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; }
</style>

<?php
// Build lesson list for JS
$lessonIds = array_column($lessons, 'id');
?>

<!-- ===== LEARNING LAYOUT ===== -->
<div class="learn-layout">

  <!-- TOP HEADER -->
  <header class="learn-header bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-3 flex items-center justify-between gap-4 z-10">
    <div class="flex items-center gap-4 min-w-0">
      <a href="/CMS/student/my_courses.php" class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-primary transition-colors shrink-0">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        <span class="hidden sm:inline">My Courses</span>
      </a>
      <div class="h-5 w-px bg-slate-200 dark:bg-slate-700 shrink-0"></div>
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-primary text-lg"><?= $icon ?></span>
        </div>
        <div class="min-w-0">
          <h1 class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate"><?= htmlspecialchars($course['title']) ?></h1>
          <p class="text-xs text-slate-400"><?= htmlspecialchars($course['instructor_name'] ?? 'EduManage') ?></p>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-4 shrink-0">
      <div class="hidden sm:flex items-center gap-3">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Progress</span>
        <div class="w-36">
          <div class="progress-bar-track">
            <div class="progress-bar-fill" style="width:<?= $overallProgress ?>%"></div>
          </div>
        </div>
        <span class="text-sm font-black text-primary"><?= $overallProgress ?>%</span>
      </div>
      <div class="text-xs text-slate-400 font-semibold hidden md:block"><?= $completedCount ?>/<?= $totalLessons ?> done</div>
    </div>
  </header>

  <!-- BODY: SIDEBAR + MAIN -->
  <div class="learn-body">

    <!-- LESSON SIDEBAR -->
    <aside class="lesson-sidebar">
      <div class="p-4 border-b border-slate-200 dark:border-slate-800 sticky top-0 bg-white dark:bg-slate-900 z-10">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest">Course Content</h2>
          <span class="text-xs font-bold text-primary"><?= $completedCount ?>/<?= $totalLessons ?></span>
        </div>
        <div class="progress-bar-track">
          <div class="progress-bar-fill" style="width:<?= $overallProgress ?>%"></div>
        </div>
      </div>

      <div class="py-3">
        <?php foreach ($lessons as $idx => $lesson):
          $isDone      = in_array($lesson['id'], $completedLessons);
          $isActive    = ($lesson['id'] == $activeLessonId);
          $typeIcon    = $lesson['content_type'] === 'video' ? 'play_circle' : ($lesson['content_type'] === 'quiz' ? 'quiz' : 'article');
          $typeColor   = $lesson['content_type'] === 'video' ? 'text-blue-500' : ($lesson['content_type'] === 'quiz' ? 'text-amber-500' : 'text-slate-400');
        ?>
        <a href="/CMS/student/learning.php?course_id=<?= $courseId ?>&lesson_id=<?= $lesson['id'] ?>"
           class="lesson-item <?= $isActive ? 'active' : '' ?> <?= $isDone ? 'completed' : '' ?>">
          <div class="lesson-num">
            <?php if ($isDone): ?>
              <span class="material-symbols-outlined text-sm text-emerald-600">check</span>
            <?php else: ?>
              <?= $idx + 1 ?>
            <?php endif; ?>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug truncate"><?= htmlspecialchars($lesson['title']) ?></p>
            <div class="flex items-center gap-2 mt-1">
              <span class="material-symbols-outlined text-xs <?= $typeColor ?>"><?= $typeIcon ?></span>
              <span class="text-[10px] text-slate-400 font-semibold"><?= $lesson['duration_minutes'] ?? 0 ?> min</span>
              <?php if (!empty($lesson['video_url'])): ?>
              <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 rounded font-bold">VIDEO</span>
              <?php endif; ?>
            </div>
          </div>
          <?php if ($isDone): ?>
          <span class="material-symbols-outlined text-emerald-500 text-sm flex-shrink-0">check_circle</span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="lesson-main flex flex-col">

      <?php if (!$activeLesson): ?>
      <div class="flex-1 flex items-center justify-center">
        <div class="text-center py-20">
          <span class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-700 block mb-4">menu_book</span>
          <h2 class="text-2xl font-bold mb-2">No Lessons Available</h2>
          <p class="text-slate-500">Lessons for this course will be added soon.</p>
          <a href="/CMS/student/my_courses.php" class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined">arrow_back</span> Back to My Courses
          </a>
        </div>
      </div>
      <?php else: ?>

      <!-- TABS -->
      <div class="learn-tabs px-4">
        <button class="learn-tab active" id="tab-lesson" onclick="switchTab('lesson')">
          <span class="material-symbols-outlined text-sm">article</span> Lesson
        </button>
        <?php if (!empty($activeLesson['video_url'])): ?>
        <button class="learn-tab" id="tab-video" onclick="switchTab('video')">
          <span class="material-symbols-outlined text-sm">play_circle</span> Video
        </button>
        <?php endif; ?>
        <button class="learn-tab" id="tab-resources" onclick="switchTab('resources')">
          <span class="material-symbols-outlined text-sm">link</span> Resources
        </button>
      </div>

      <!-- FLEX MAIN BODY -->
      <div class="flex-1 overflow-y-auto" id="tab-content-wrapper">

        <!-- TAB: LESSON -->
        <div id="panel-lesson" class="tab-panel active">
          <div class="lesson-content-area">
            <!-- Lesson Header -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black text-primary uppercase tracking-widest bg-primary/10 px-3 py-1 rounded-full">
                  Lesson <?= $activeLessonIndex + 1 ?> of <?= $totalLessons ?>
                </span>
                <?php if (in_array($activeLessonId, $completedLessons)): ?>
                <span class="flex items-center gap-1.5 text-xs font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1.5 rounded-full">
                  <span class="material-symbols-outlined text-sm">check_circle</span> Completed
                </span>
                <?php endif; ?>
              </div>
              <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 leading-tight"><?= htmlspecialchars($activeLesson['title']) ?></h2>
              <?php if (!empty($activeLesson['description'])): ?>
              <p class="text-slate-500 mt-2 text-sm"><?= htmlspecialchars($activeLesson['description']) ?></p>
              <?php endif; ?>
              <div class="flex items-center gap-4 mt-3 text-xs text-slate-400 font-semibold">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span><?= $activeLesson['duration_minutes'] ?? 0 ?> min</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">category</span><?= ucfirst($activeLesson['content_type']) ?></span>
              </div>
            </div>
            <hr class="border-slate-200 dark:border-slate-800 mb-6">
            <!-- Lesson Body -->
            <div class="lesson-body-content">
              <?= $activeLesson['content'] ?>
            </div>
          </div>
        </div>

        <!-- TAB: VIDEO -->
        <?php if (!empty($activeLesson['video_url'])): ?>
        <div id="panel-video" class="tab-panel">
          <div class="lesson-content-area">
            <h2 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-2">🎬 <?= htmlspecialchars($activeLesson['title']) ?></h2>
            <p class="text-sm text-slate-500 mb-6">Watch the video lecture for this lesson and follow along.</p>
            <div class="video-container mb-8">
              <iframe
                src="<?= htmlspecialchars($activeLesson['video_url']) ?>?rel=0&modestbranding=1"
                title="<?= htmlspecialchars($activeLesson['title']) ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
              </iframe>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 text-sm text-blue-700 dark:text-blue-300 flex items-start gap-3">
              <span class="material-symbols-outlined text-blue-500">info</span>
              <div>
                <strong>Study Tip:</strong> Pause the video frequently to take notes. Apply what you learn in the lesson tab with the code examples.
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- TAB: RESOURCES -->
        <div id="panel-resources" class="tab-panel">
          <div class="lesson-content-area">
            <h2 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-2">🔗 Learning Resources</h2>
            <p class="text-sm text-slate-500 mb-6">Curated external resources to deepen your understanding of this lesson.</p>

            <?php
            // Extract resource block from lesson content
            $resourcesHtml = '';
            if (!empty($activeLesson['content'])) {
                preg_match('/<div class="lesson-resources">(.*?)<\/div>/si', $activeLesson['content'], $matches);
                if (!empty($matches[0])) {
                    $resourcesHtml = $matches[0];
                }
            }
            ?>

            <?php if (!empty($resourcesHtml)): ?>
            <div class="grid gap-4">
              <?= str_replace(
                '<div class="lesson-resources">',
                '<div class="lesson-resources" style="margin-top:0">',
                $resourcesHtml
              ) ?>
            </div>
            <?php else: ?>
            <div class="text-center py-16 text-slate-400">
              <span class="material-symbols-outlined text-5xl block mb-3">link_off</span>
              <p class="font-semibold">No specific resources for this lesson.</p>
              <p class="text-sm mt-2">Check the lesson content for embedded links.</p>
            </div>
            <?php endif; ?>

            <!-- General resources by category -->
            <div class="mt-8 bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
              <h3 class="text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-4">🌐 General Learning Platforms</h3>
              <div class="grid grid-cols-2 gap-3 text-sm">
                <?php
                $platforms = [
                  ['name'=>'freeCodeCamp','url'=>'https://www.freecodecamp.org/','icon'=>'💻'],
                  ['name'=>'Khan Academy','url'=>'https://www.khanacademy.org/computing','icon'=>'🎓'],
                  ['name'=>'MIT OpenCourseWare','url'=>'https://ocw.mit.edu/','icon'=>'🏛️'],
                  ['name'=>'Coursera (Audit Free)','url'=>'https://www.coursera.org/','icon'=>'📚'],
                  ['name'=>'edX Free Courses','url'=>'https://www.edx.org/','icon'=>'🎯'],
                  ['name'=>'W3Schools','url'=>'https://www.w3schools.com/','icon'=>'🌐'],
                  ['name'=>'GeeksForGeeks','url'=>'https://www.geeksforgeeks.org/','icon'=>'⚙️'],
                  ['name'=>'The Odin Project','url'=>'https://www.theodinproject.com/','icon'=>'🔱'],
                ];
                foreach ($platforms as $p): ?>
                <a href="<?= $p['url'] ?>" target="_blank" rel="noopener"
                   class="flex items-center gap-2 px-3 py-2.5 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-primary hover:text-primary transition-all font-semibold text-slate-600 dark:text-slate-300">
                  <span><?= $p['icon'] ?></span>
                  <span class="text-xs"><?= $p['name'] ?></span>
                  <span class="material-symbols-outlined ml-auto text-sm opacity-40">open_in_new</span>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /tab-content-wrapper -->

      <!-- BOTTOM NAVIGATION -->
      <div class="lesson-bottom-nav">
        <div>
          <?php if ($prevLesson): ?>
          <a href="/CMS/student/learning.php?course_id=<?= $courseId ?>&lesson_id=<?= $prevLesson['id'] ?>" class="nav-btn nav-btn-prev">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Previous</span>
          </a>
          <?php else: ?>
          <div></div>
          <?php endif; ?>
        </div>

        <div class="flex items-center gap-3">
          <?php if (!in_array($activeLessonId, $completedLessons)): ?>
          <form method="POST" id="completeForm">
            <input type="hidden" name="action" value="complete_lesson">
            <input type="hidden" name="lesson_id" value="<?= $activeLessonId ?>">
            <input type="hidden" name="next_lesson_id" value="<?= $nextLesson ? $nextLesson['id'] : '' ?>">
            <button type="submit" class="nav-btn nav-btn-complete">
              <span class="material-symbols-outlined text-sm">check_circle</span>
              Mark as Complete
            </button>
          </form>
          <?php else: ?>
          <div class="mark-done-badge">
            <span class="material-symbols-outlined text-sm">verified</span>
            Completed!
          </div>
          <?php endif; ?>
        </div>

        <div>
          <?php if ($nextLesson): ?>
          <a href="/CMS/student/learning.php?course_id=<?= $courseId ?>&lesson_id=<?= $nextLesson['id'] ?>" class="nav-btn nav-btn-next">
            <span>Next Lesson</span>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
          <?php elseif ($isCourseComplete): ?>
          <a href="/CMS/student/my_courses.php" class="nav-btn" style="background: linear-gradient(135deg,#7c3aed,#1e3b8a); color:#fff;">
            <span class="material-symbols-outlined text-sm">school</span>
            My Courses
          </a>
          <?php else: ?>
          <div></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- CERTIFICATE BANNER (when course complete) -->
      <?php if ($isCourseComplete): ?>
      <div class="px-10 pb-8">
        <div class="cert-banner" id="certBanner">
          <!-- confetti particles -->
          <?php for ($c=0; $c<12; $c++):
            $colors = ['#fbbf24','#60a5fa','#34d399','#f472b6','#a78bfa','#fb923c'];
            $color  = $colors[$c % count($colors)];
            $left   = rand(5,95);
            $delay  = ($c * 0.25);
            $size   = rand(6,12);
          ?>
          <div class="confetti-particle" style="left:<?=$left?>%; background:<?=$color?>; width:<?=$size?>px; height:<?=$size?>px; animation-delay:<?=$delay?>s;"></div>
          <?php endfor; ?>
          <div class="relative z-10">
            <div class="text-5xl mb-4">🎓</div>
            <h2 class="text-2xl font-black mb-2">Congratulations! You've Completed This Course!</h2>
            <p class="text-blue-100 mb-6 text-sm max-w-lg mx-auto">You've successfully completed all <?= $totalLessons ?> lessons in <strong><?= htmlspecialchars($course['title']) ?></strong>. This is a fantastic achievement!</p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
              <a href="/CMS/student/certificate.php?course_id=<?= $courseId ?>" class="inline-flex items-center gap-2 bg-amber-400 text-amber-900 font-black px-8 py-3.5 rounded-xl hover:bg-amber-300 transition-all shadow-xl text-base">
                🏆 Get Your Certificate
              </a>
              <a href="/CMS/student/catalog.php" class="inline-flex items-center gap-2 bg-white text-primary font-black px-6 py-3 rounded-xl hover:bg-blue-50 transition-all shadow-lg">
                <span class="material-symbols-outlined">explore</span>
                Explore More Courses
              </a>
              <a href="/CMS/student/my_courses.php" class="inline-flex items-center gap-2 bg-white/20 border border-white/40 text-white font-bold px-6 py-3 rounded-xl hover:bg-white/30 transition-all">
                <span class="material-symbols-outlined">library_books</span>
                My Courses
              </a>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php endif; // end if $activeLesson ?>
    </div>
  </div>
</div>

<script>
// ===== TAB SWITCHING =====
function switchTab(tab) {
  document.querySelectorAll('.learn-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  const btn = document.getElementById('tab-' + tab);
  const panel = document.getElementById('panel-' + tab);
  if (btn) btn.classList.add('active');
  if (panel) panel.classList.add('active');
  // scroll main back to top
  const wrapper = document.getElementById('tab-content-wrapper');
  if (wrapper) wrapper.scrollTop = 0;
}

// ===== KEYBOARD NAV =====
document.addEventListener('keydown', function(e) {
  if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
  <?php if ($prevLesson): ?>
  if (e.code === 'ArrowLeft') {
    window.location.href = '/CMS/student/learning.php?course_id=<?= $courseId ?>&lesson_id=<?= $prevLesson['id'] ?>';
  }
  <?php endif; ?>
  <?php if ($nextLesson): ?>
  if (e.code === 'ArrowRight') {
    window.location.href = '/CMS/student/learning.php?course_id=<?= $courseId ?>&lesson_id=<?= $nextLesson['id'] ?>';
  }
  <?php endif; ?>
  // M = mark complete
  if (e.code === 'KeyM') {
    const form = document.getElementById('completeForm');
    if (form) form.submit();
  }
});

// ===== CONFETTI on course complete =====
<?php if ($isCourseComplete): ?>
document.addEventListener('DOMContentLoaded', function() {
  const banner = document.getElementById('certBanner');
  if (banner) {
    banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});
<?php endif; ?>

// ===== KEYBOARD HINT TOOLTIP =====
document.addEventListener('DOMContentLoaded', function() {
  const hint = document.createElement('div');
  hint.innerHTML = '⌨️ <strong>Tip:</strong> Use ← → arrow keys to navigate lessons. Press <kbd>M</kbd> to mark complete.';
  hint.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#0f172a;color:#94a3b8;font-size:12px;padding:8px 16px;border-radius:8px;z-index:999;opacity:0;transition:opacity .4s;white-space:nowrap;pointer-events:none;';
  document.body.appendChild(hint);
  setTimeout(() => { hint.style.opacity = '1'; }, 1000);
  setTimeout(() => { hint.style.opacity = '0'; }, 4000);
  setTimeout(() => { hint.remove(); }, 4500);
});
</script>

</main>
</div>
</body>
</html>