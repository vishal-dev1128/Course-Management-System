<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

$db        = getDB();
$pageTitle = 'My Courses';
$studentId = (int)$_SESSION['user_id'];

$enrolled = $db->prepare(
    'SELECT c.*, e.enrolled_at, e.progress, u.name AS instructor_name
     FROM enrollments e
     JOIN courses c ON e.course_id = c.id
     LEFT JOIN users u ON c.instructor_id = u.id
     WHERE e.student_id = ?
     ORDER BY e.enrolled_at DESC'
);
$enrolled->execute([$studentId]);
$enrolled = $enrolled->fetchAll();

// Get lesson counts per course
$lessonCounts = [];
$completedCounts = [];
if (!empty($enrolled)) {
    $courseIds = array_column($enrolled, 'id');
    $inClause  = implode(',', array_fill(0, count($courseIds), '?'));

    $lcStmt = $db->prepare("SELECT course_id, COUNT(*) as total FROM lessons WHERE course_id IN ($inClause) AND status='active' GROUP BY course_id");
    $lcStmt->execute($courseIds);
    foreach ($lcStmt->fetchAll() as $row) {
        $lessonCounts[$row['course_id']] = (int)$row['total'];
    }

    $ccStmt = $db->prepare("SELECT l.course_id, COUNT(lp.id) as done FROM lesson_progress lp JOIN lessons l ON lp.lesson_id=l.id WHERE lp.student_id=? AND lp.completed=1 AND l.course_id IN ($inClause) GROUP BY l.course_id");
    $ccStmt->execute(array_merge([$studentId], $courseIds));
    foreach ($ccStmt->fetchAll() as $row) {
        $completedCounts[$row['course_id']] = (int)$row['done'];
    }
}

// Find last lesson accessed per course for "Continue" link
$lastLesson = [];
foreach ($enrolled as $c) {
    $lStmt = $db->prepare("SELECT lp.lesson_id FROM lesson_progress lp JOIN lessons l ON lp.lesson_id=l.id WHERE lp.student_id=? AND l.course_id=? ORDER BY lp.completed_at DESC LIMIT 1");
    $lStmt->execute([$studentId, $c['id']]);
    $ll = $lStmt->fetchColumn();
    if ($ll) {
        // Find next uncompleted lesson
        $nextStmt = $db->prepare("SELECT id FROM lessons WHERE course_id=? AND status='active' AND id NOT IN (SELECT lesson_id FROM lesson_progress WHERE student_id=? AND completed=1) ORDER BY order_num ASC LIMIT 1");
        $nextStmt->execute([$c['id'], $studentId]);
        $nextId = $nextStmt->fetchColumn();
        $lastLesson[$c['id']] = $nextId ?: $ll;
    }
}

$categoryIcons = [
    'Programming'     => 'terminal',
    'Web Development' => 'html',
    'Data Science'    => 'monitoring',
    'Design'          => 'palette',
    'default'         => 'menu_book',
];

require_once '../includes/student_sidebar.php';
?>

<style>
.progress-bar-track { height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.dark .progress-bar-track { background: #1e293b; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #1e3b8a, #3b82f6); border-radius: 99px; transition: width .5s ease; }
.progress-bar-fill.complete { background: linear-gradient(90deg, #10b981, #059669); }
.course-card { transition: transform .2s, box-shadow .2s; }
.course-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px -8px rgba(30,59,138,0.15); }
</style>

<!-- Header -->
<header class="sticky top-0 z-10 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between">
  <div>
    <h1 class="text-xl font-black text-slate-900 dark:text-white">My Courses</h1>
    <p class="text-xs text-slate-500 mt-0.5"><?= count($enrolled) ?> enrolled · Your learning hub</p>
  </div>
  <a href="/CMS/student/catalog.php" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 transition-all shadow-md hover:shadow-primary/30">
    <span class="material-symbols-outlined text-lg">explore</span> Browse More Courses
  </a>
</header>

<div class="p-8">
  <?php if (empty($enrolled)): ?>
  <!-- Empty State -->
  <div class="text-center py-24">
    <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
      <span class="material-symbols-outlined text-5xl text-primary">auto_stories</span>
    </div>
    <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-3">No Courses Yet</h2>
    <p class="text-slate-500 mb-8 max-w-sm mx-auto">You haven't enrolled in any courses. Start your learning journey today!</p>
    <a href="/CMS/student/catalog.php" class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white font-black rounded-xl hover:bg-primary/90 shadow-lg hover:shadow-primary/30 transition-all">
      <span class="material-symbols-outlined">rocket_launch</span> Browse Courses
    </a>
  </div>

  <?php else: ?>
  <!-- Stats Bar -->
  <?php
    $totalDone = count(array_filter($enrolled, fn($c) => (int)($c['progress'] ?? 0) >= 100));
    $avgProgress = count($enrolled) > 0 ? (int)(array_sum(array_column($enrolled, 'progress')) / count($enrolled)) : 0;
  ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3 shadow-sm">
      <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center"><span class="material-symbols-outlined text-blue-500">menu_book</span></div>
      <div><p class="text-xs text-slate-400 font-semibold uppercase">Enrolled</p><p class="text-xl font-black text-slate-900 dark:text-white"><?= count($enrolled) ?></p></div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3 shadow-sm">
      <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center"><span class="material-symbols-outlined text-emerald-500">emoji_events</span></div>
      <div><p class="text-xs text-slate-400 font-semibold uppercase">Completed</p><p class="text-xl font-black text-slate-900 dark:text-white"><?= $totalDone ?></p></div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3 shadow-sm">
      <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center"><span class="material-symbols-outlined text-amber-500">trending_up</span></div>
      <div><p class="text-xs text-slate-400 font-semibold uppercase">Avg Progress</p><p class="text-xl font-black text-slate-900 dark:text-white"><?= $avgProgress ?>%</p></div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3 shadow-sm">
      <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center"><span class="material-symbols-outlined text-purple-500">local_fire_department</span></div>
      <div><p class="text-xs text-slate-400 font-semibold uppercase">In Progress</p><p class="text-xl font-black text-slate-900 dark:text-white"><?= count($enrolled) - $totalDone ?></p></div>
    </div>
  </div>

  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-black text-slate-900 dark:text-white">All My Courses</h2>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php foreach ($enrolled as $c):
      $progress      = (int)($c['progress'] ?? 0);
      $icon          = $categoryIcons[$c['category'] ?? ''] ?? $categoryIcons['default'];
      $isComplete    = $progress >= 100;
      $totalL        = $lessonCounts[$c['id']] ?? 0;
      $doneL         = $completedCounts[$c['id']] ?? 0;
      $continueId    = $lastLesson[$c['id']] ?? '';
      $learnUrl      = '/CMS/student/learning.php?course_id=' . $c['id'] . ($continueId ? '&lesson_id=' . $continueId : '');
      $ctaLabel      = $doneL === 0 ? 'Start Learning' : ($isComplete ? 'Review Course' : 'Continue Learning');
      $ctaIcon       = $doneL === 0 ? 'play_arrow' : ($isComplete ? 'replay' : 'arrow_forward');
      $yearColors    = ['First Year'=>'bg-blue-100 text-blue-700','Second Year'=>'bg-emerald-100 text-emerald-700','Third Year'=>'bg-purple-100 text-purple-700','Fourth Year'=>'bg-orange-100 text-orange-700'];
      $yearCls       = $yearColors[$c['academic_year']] ?? 'bg-slate-100 text-slate-600';
    ?>
    <div class="course-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
      <!-- Thumbnail -->
      <div class="h-40 bg-primary/10 flex items-center justify-center relative overflow-hidden group cursor-pointer" onclick="window.location='<?= $learnUrl ?>'">
        <?php if (!empty($c['image'])): ?>
        <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <?php else: ?>
        <span class="material-symbols-outlined text-7xl text-primary/20 group-hover:scale-110 transition-transform"><?= $icon ?></span>
        <?php endif; ?>
        <div class="absolute top-3 right-3">
          <span class="px-2 py-0.5 text-xs font-black rounded-full <?= $yearCls ?>"><?= htmlspecialchars($c['academic_year']) ?></span>
        </div>
        <?php if ($isComplete): ?>
        <div class="absolute top-3 left-3 bg-emerald-500 text-white px-2 py-0.5 rounded-full text-xs font-black flex items-center gap-1">
          <span class="material-symbols-outlined text-xs">emoji_events</span> Completed!
        </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">
          <span class="text-white text-sm font-black flex items-center gap-1">
            <span class="material-symbols-outlined">play_circle</span> <?= $ctaLabel ?>
          </span>
        </div>
      </div>

      <!-- Info -->
      <div class="p-5 flex flex-col flex-1">
        <div class="flex items-start justify-between gap-2 mb-2">
          <h3 class="font-bold text-slate-900 dark:text-slate-100 leading-snug"><?= htmlspecialchars($c['title']) ?></h3>
          <?php if ($totalL > 0): ?>
          <span class="text-[10px] font-black text-slate-400 shrink-0 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full"><?= $doneL ?>/<?= $totalL ?> lessons</span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mb-1"><?= htmlspecialchars($c['instructor_name'] ?? 'EduManage') ?></p>
        <p class="text-[10px] text-slate-400 mb-4">Enrolled <?= date('M d, Y', strtotime($c['enrolled_at'])) ?></p>

        <!-- Progress -->
        <div class="space-y-1.5 mb-5">
          <div class="flex justify-between text-xs font-semibold">
            <span class="text-slate-500">Completion</span>
            <span class="<?= $isComplete ? 'text-emerald-500' : 'text-primary' ?> font-black"><?= $progress ?>%</span>
          </div>
          <div class="progress-bar-track">
            <div class="progress-bar-fill <?= $isComplete ? 'complete' : '' ?>" style="width:<?= $progress ?>%"></div>
          </div>
          <?php if ($totalL > 0): ?>
          <p class="text-[10px] text-slate-400"><?= $doneL ?> of <?= $totalL ?> lessons completed</p>
          <?php endif; ?>
        </div>

        <!-- CTA Button -->
        <a href="<?= $learnUrl ?>"
           class="mt-auto w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-black transition-all <?= $isComplete ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-500 hover:text-white border border-emerald-200' : 'bg-primary text-white hover:bg-primary/90 shadow-md hover:shadow-primary/25' ?>">
          <span class="material-symbols-outlined text-sm"><?= $ctaIcon ?></span>
          <?= $ctaLabel ?>
          <?php if (!$isComplete && $totalL > 0): ?>
          <span class="ml-auto text-[10px] opacity-60 font-bold">⌨ ← →</span>
          <?php endif; ?>
        </a>
        <?php if ($isComplete): ?>
        <a href="/CMS/student/certificate.php?course_id=<?= $c['id'] ?>"
           class="mt-2 w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-black bg-amber-400 text-amber-900 hover:bg-amber-300 transition-all shadow-md">
          🏆 View Certificate
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

</main>
</div>
</body>
</html>
