<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$pageTitle = 'Dashboard';

// Stats
$totalUsers       = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalCourses     = $db->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$totalEnrollments = $db->query('SELECT COUNT(*) FROM enrollments')->fetchColumn();
$totalStudents    = $db->query('SELECT COUNT(*) FROM users WHERE role="student"')->fetchColumn();

// Recent enrollments
$recentEnrollments = $db->query(
    'SELECT e.enrolled_at, u.name AS student_name, c.title AS course_title
     FROM enrollments e
     JOIN users u ON e.student_id = u.id
     JOIN courses c ON e.course_id = c.id
     ORDER BY e.enrolled_at DESC
     LIMIT 5'
)->fetchAll();

// Enrollment trend (last 7 days)
$trend = $db->query(
    'SELECT DATE(enrolled_at) as day, COUNT(*) as cnt
     FROM enrollments
     WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     GROUP BY DATE(enrolled_at)
     ORDER BY day ASC'
)->fetchAll();

// Recent activity items
$recentActivity = [];
$actStmt = $db->query(
    'SELECT "enrollment" as type, u.name, c.title, e.enrolled_at as created_at
     FROM enrollments e JOIN users u ON e.student_id=u.id JOIN courses c ON e.course_id=c.id
     ORDER BY e.enrolled_at DESC LIMIT 5'
);
$recentActivity = $actStmt->fetchAll();

require_once '../includes/admin_sidebar.php';
?>

<div class="p-8">
  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard Overview</h2>
      <p class="text-slate-500 dark:text-slate-400">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>. Here's what's happening today.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="/CMS/admin/courses.php?action=add" class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition-all shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-[20px]">add_circle</span>
        <span class="text-sm font-bold">Add New Course</span>
      </a>
    </div>
  </div>

  <!-- Stats Grid — compact, shadow-only cards -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Total Users -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">group</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Users</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalUsers) ?></p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full shrink-0">All</span>
    </div>

    <!-- Total Courses -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(99,102,241,0.08),0 8px 24px rgba(99,102,241,0.07)">
      <div class="size-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-indigo-500 text-[18px]">menu_book</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Courses</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalCourses) ?></p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full shrink-0">Active</span>
    </div>

    <!-- Total Enrollments -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(245,158,11,0.08),0 8px 24px rgba(245,158,11,0.07)">
      <div class="size-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-amber-500 text-[18px]">how_to_reg</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Enrollments</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalEnrollments) ?></p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full shrink-0">Total</span>
    </div>

    <!-- Total Students -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(244,63,94,0.08),0 8px 24px rgba(244,63,94,0.07)">
      <div class="size-9 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-rose-500 text-[18px]">school</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Students</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalStudents) ?></p>
      </div>
      <span class="ml-auto text-[10px] font-bold text-rose-600 bg-rose-50 dark:bg-rose-900/20 px-2 py-0.5 rounded-full shrink-0">Active</span>
    </div>

  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Enrollment Chart -->
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl p-5" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h4 class="text-base font-bold text-slate-900 dark:text-white">Enrollment Trends</h4>
          <p class="text-xs text-slate-500">Last 7 days student enrollment performance</p>
        </div>
      </div>
      <?php
      $days = [];
      for ($i = 6; $i >= 0; $i--) {
          $day = date('Y-m-d', strtotime("-$i days"));
          $days[$day] = 0;
      }
      foreach ($trend as $t) {
          if (isset($days[$t['day']])) $days[$t['day']] = $t['cnt'];
      }
      $maxVal = max(array_values($days) ?: [1]);
      ?>
      <div class="relative h-48 mt-2">
        <!-- Background Grid -->
        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none px-1">
          <div class="border-t border-slate-100 dark:border-slate-800 w-full h-0"></div>
          <div class="border-t border-slate-100 dark:border-slate-800 w-full h-0"></div>
          <div class="border-t border-slate-100 dark:border-slate-800 w-full h-0"></div>
        </div>
        
        <!-- Bars Container -->
        <div class="absolute inset-0 flex items-end gap-3 px-1 pb-1">
          <?php foreach ($days as $day => $cnt):
            $pct = $maxVal > 0 ? round(($cnt / $maxVal) * 85) : 0;
          ?>
          <div class="flex-1 flex flex-col items-center justify-end h-full">
            <!-- Label above bar -->
            <span class="text-[10px] font-bold text-slate-400 mb-1"><?= $cnt ?></span>
            <!-- Bar -->
            <div class="w-full bg-primary rounded-t-lg transition-all hover:bg-primary/80 relative cursor-default" style="height:<?= max($pct, 4) ?>%; min-height: 4px; box-shadow: 0 4px 12px rgba(30,59,138,0.15)">
            </div>
            <!-- Day Label -->
            <span class="text-[10px] text-slate-400 font-bold mt-3"><?= date('D', strtotime($day)) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
      <h4 class="text-base font-bold text-slate-900 dark:text-white mb-5">System Activity</h4>
      <div class="space-y-4">
        <?php if (empty($recentActivity)): ?>
        <p class="text-sm text-slate-400">No recent activity.</p>
        <?php else: ?>
        <?php foreach ($recentActivity as $act): ?>
        <div class="flex gap-3 items-start">
          <div class="size-8 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-[16px]">person_add</span>
          </div>
          <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate"><?= htmlspecialchars($act['name']) ?> enrolled</p>
            <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($act['title']) ?></p>
            <p class="text-[10px] text-slate-400 mt-0.5 uppercase font-bold tracking-tight"><?= date('d M Y', strtotime($act['created_at'])) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Recent Enrollments Table -->
  <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
      <h4 class="text-base font-bold text-slate-900 dark:text-white">Recent Enrollments</h4>
      <a href="/CMS/admin/enrollments.php" class="text-primary text-xs font-semibold hover:underline">View All →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-slate-50 dark:bg-slate-800/50">
          <tr>
            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Student</th>
            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Course</th>
            <th class="px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php if (empty($recentEnrollments)): ?>
          <tr><td colspan="3" class="px-5 py-6 text-center text-sm text-slate-400">No enrollments yet.</td></tr>
          <?php else: ?>
          <?php foreach ($recentEnrollments as $e):
            $initials = implode('', array_map(fn($w)=>strtoupper($w[0]), explode(' ', $e['student_name'])));
            $colors = ['bg-indigo-100 text-indigo-700','bg-pink-100 text-pink-700','bg-amber-100 text-amber-700','bg-blue-100 text-blue-700'];
            $color = $colors[crc32($e['student_name']) % count($colors)];
          ?>
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
            <td class="px-5 py-3">
              <div class="flex items-center gap-2.5">
                <div class="size-7 rounded-full <?= $color ?> flex items-center justify-center text-[10px] font-bold uppercase"><?= htmlspecialchars(substr($initials,0,2)) ?></div>
                <span class="text-sm font-medium"><?= htmlspecialchars($e['student_name']) ?></span>
              </div>
            </td>
            <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300"><?= htmlspecialchars($e['course_title']) ?></td>
            <td class="px-5 py-3 text-xs text-slate-400"><?= date('M d, Y', strtotime($e['enrolled_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
