<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('instructor');

$db = getDB();
$pageTitle = 'Instructor Dashboard';
$instrId   = (int)$_SESSION['user_id'];

// Stats
$totalStudents = $db->prepare('SELECT COUNT(DISTINCT e.student_id) FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE c.instructor_id=?');
$totalStudents->execute([$instrId]);
$totalStudents = $totalStudents->fetchColumn();

$totalCourses = $db->prepare('SELECT COUNT(*) FROM courses WHERE instructor_id=?');
$totalCourses->execute([$instrId]);
$totalCourses = $totalCourses->fetchColumn();

// Assigned courses
$courses = $db->prepare('SELECT c.*, (SELECT COUNT(*) FROM enrollments e WHERE e.course_id=c.id) AS student_count FROM courses c WHERE c.instructor_id=? ORDER BY c.id DESC');
$courses->execute([$instrId]);
$courses = $courses->fetchAll();

// Student list (first course of this instructor)
$students = [];
$firstCourse = null;
if (!empty($courses)) {
    $firstCourse = $courses[0];
    $st = $db->prepare('SELECT u.name, u.email, e.enrolled_at FROM enrollments e JOIN users u ON e.student_id=u.id WHERE e.course_id=? ORDER BY e.enrolled_at DESC LIMIT 10');
    $st->execute([$firstCourse['id']]);
    $students = $st->fetchAll();
}

require_once '../includes/instructor_sidebar.php';
?>

<!-- Header -->
<header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
  <div>
    <h2 class="text-2xl font-bold">Instructor Portal</h2>
    <p class="text-slate-500 text-sm">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>. Here's what's happening today.</p>
  </div>
  <div class="flex items-center gap-4">
    <a href="/CMS/instructor/courses.php" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-semibold transition-all">
      <span class="material-symbols-outlined text-sm">book</span> My Courses
    </a>
  </div>
</header>

<div class="p-8">
  <!-- Stats Row — Compact Premium Design -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- Total Students -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">groups</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Students</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalStudents) ?></p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full shrink-0">Total</span>
    </div>

    <!-- Active Courses -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(139,92,246,0.08),0 8px 24px rgba(139,92,246,0.07)">
      <div class="size-9 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-purple-500 text-[18px]">menu_book</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Active Courses</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalCourses) ?></p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-purple-600 bg-purple-50 dark:bg-purple-900/20 px-2 py-0.5 rounded-full shrink-0">Active</span>
    </div>

    <!-- Avg Completion -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(16,185,129,0.08),0 8px 24px rgba(16,185,129,0.07)">
      <div class="size-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-green-500 text-[18px]">task_alt</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Avg. Completion</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight">84.2%</p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full shrink-0">Avg</span>
    </div>

    <!-- Instructor Rating -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(245,158,11,0.08),0 8px 24px rgba(245,158,11,0.07)">
      <div class="size-9 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-orange-500 text-[18px]">star</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Inst. Rating</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight">4.9/5</p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-orange-600 bg-orange-50 dark:bg-orange-900/20 px-2 py-0.5 rounded-full shrink-0">Top 1%</span>
    </div>
  </div>

  <!-- Assigned Courses -->
  <div class="mb-8">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Assigned Courses</h3>
      <a class="text-primary text-xs font-bold hover:underline" href="/CMS/instructor/courses.php">View All Courses →</a>
    </div>
    <?php if (empty($courses)): ?>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-10 text-center text-slate-400" style="box-shadow:0 10px 30px -12px rgba(0,0,0,0.05)">
      <span class="material-symbols-outlined text-4xl mb-2 block">menu_book</span>
      <p class="text-sm">No courses assigned yet.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <?php foreach (array_slice($courses,0,4) as $c):
        $yearColors = ['First Year'=>'bg-blue-50 text-blue-700','Second Year'=>'bg-emerald-50 text-emerald-700','Third Year'=>'bg-purple-50 text-purple-700','Fourth Year'=>'bg-orange-50 text-orange-700'];
        $yearCls = $yearColors[$c['academic_year']] ?? 'bg-slate-50 text-slate-600';
      ?>
      <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden flex transition-all group hover:scale-[1.01]" style="box-shadow:0 4px 20px -5px rgba(30,59,138,0.05), 0 20px 40px -15px rgba(30,59,138,0.08)">
        <div class="w-28 flex items-center justify-center bg-slate-50 dark:bg-slate-800/50 flex-shrink-0 border-r border-slate-100 dark:border-slate-800 overflow-hidden relative">
          <?php if (!empty($c['image'])): ?>
          <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover">
          <?php else: ?>
          <span class="material-symbols-outlined text-4xl text-primary/30">menu_book</span>
          <?php endif; ?>
        </div>
        <div class="p-4 flex flex-col justify-between flex-1 min-w-0">
          <div class="min-w-0">
            <div class="flex justify-between items-start mb-1.5">
              <span class="text-[9px] font-black uppercase tracking-widest <?= $yearCls ?> px-2 py-0.5 rounded-full"><?= htmlspecialchars($c['academic_year']) ?></span>
              <span class="text-[10px] font-bold text-slate-400"><?= $c['status'] === 'active' ? 'Active' : 'Draft' ?></span>
            </div>
            <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate mb-1"><?= htmlspecialchars($c['title']) ?></h4>
            <p class="text-slate-500 text-[11px] leading-relaxed line-clamp-2"><?= htmlspecialchars($c['description'] ?? 'No description.') ?></p>
          </div>
          <div class="mt-3 flex items-center justify-between border-t border-slate-50 dark:border-slate-800 pt-2.5">
            <div class="flex items-center gap-1.5 text-slate-500">
              <span class="material-symbols-outlined text-[16px]">group</span>
              <span class="text-[11px] font-bold"><?= $c['student_count'] ?> Students</span>
            </div>
            <a href="/CMS/instructor/students.php?course_id=<?= $c['id'] ?>" class="text-[11px] text-primary font-black hover:underline tracking-tight uppercase">View Students →</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Student List Table -->
  <?php if ($firstCourse): ?>
  <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Recent Students</h3>
        <p class="text-[11px] text-slate-500 mt-0.5">Focus: <span class="font-bold text-primary"><?= htmlspecialchars($firstCourse['title']) ?></span></p>
      </div>
      <a href="/CMS/instructor/students.php" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-800 text-slate-600 rounded-lg text-[11px] font-bold hover:bg-primary hover:text-white transition-all">
        <span class="material-symbols-outlined text-[16px]">open_in_full</span> VIEW ALL
      </a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-[10px] font-bold uppercase tracking-widest">
          <tr>
            <th class="px-5 py-3">Student Name</th>
            <th class="px-5 py-3 text-center">Contact</th>
            <th class="px-5 py-3 text-right">Enrolled Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php if (empty($students)): ?>
          <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400">No students enrolled yet.</td></tr>
          <?php else: ?>
          <?php foreach ($students as $s):
            $initials = strtoupper(implode('',array_map(fn($w)=>$w[0],explode(' ',$s['name']))));
          ?>
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
            <td class="px-5 py-3">
              <div class="flex items-center gap-2.5">
                <div class="size-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-[10px]"><?= htmlspecialchars(substr($initials,0,2)) ?></div>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100"><?= htmlspecialchars($s['name']) ?></p>
              </div>
            </td>
            <td class="px-5 py-3 text-center">
               <span class="text-[11px] font-medium text-slate-400"><?= htmlspecialchars($s['email']) ?></span>
            </td>
            <td class="px-5 py-3 text-right text-[11px] font-bold text-slate-500"><?= date('M d, Y', strtotime($s['enrolled_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

</main>
</div>
</body>
</html>
