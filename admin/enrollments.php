<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$pageTitle = 'Enrollments';

$perPage = 50;
$page    = max(1,(int)($_GET['page'] ?? 1));
$offset  = ($page-1)*$perPage;
$search  = sanitize($_GET['search'] ?? '');

$where = $search ? 'WHERE u.name LIKE :s OR c.title LIKE :s2' : '';
$params = $search ? [':s'=>"%$search%",':s2'=>"%$search%"] : [];

$total = $db->prepare("SELECT COUNT(*) FROM enrollments e JOIN users u ON e.student_id=u.id JOIN courses c ON e.course_id=c.id $where");
$total->execute($params);
$total = $total->fetchColumn();
$totalPages = (int)ceil($total/$perPage);

$stmt = $db->prepare("SELECT e.id, e.enrolled_at, u.name AS student_name, u.email AS student_email, c.title AS course_title, c.academic_year, c.category
    FROM enrollments e
    JOIN users u ON e.student_id=u.id
    JOIN courses c ON e.course_id=c.id
    $where
    ORDER BY e.enrolled_at DESC
    LIMIT :limit OFFSET :offset");
foreach($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':limit',$perPage,PDO::PARAM_INT);
$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
$stmt->execute();
$enrollments = $stmt->fetchAll();

$totalEnrollments = $db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$totalStudents    = $db->query("SELECT COUNT(DISTINCT student_id) FROM enrollments")->fetchColumn();
$totalCoursesWith = $db->query("SELECT COUNT(DISTINCT course_id) FROM enrollments")->fetchColumn();

require_once '../includes/admin_sidebar.php';
?>

<div class="p-8">
  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Enrollment List</h2>
    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">All student course enrollments across the platform.</p>
  </div>

  <!-- Stats Row — Compact Premium Design -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">how_to_reg</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Enrollments</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalEnrollments) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(16,185,129,0.08),0 8px 24px rgba(16,185,129,0.07)">
      <div class="size-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-emerald-600 text-[18px]">group</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Unique Std.</p>
        <p class="text-xl font-black text-emerald-600 leading-tight"><?= number_format($totalStudents) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(245,158,11,0.08),0 8px 24px rgba(245,158,11,0.07)">
      <div class="size-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-amber-600 text-[18px]">menu_book</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Active Crse.</p>
        <p class="text-xl font-black text-amber-600 leading-tight"><?= number_format($totalCoursesWith) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-indigo-500 text-[18px]">payments</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Revenue</p>
        <p class="text-xl font-black text-primary leading-tight">Free</p>
      </div>
    </div>
  </div>

  <!-- Search + Table -->
  <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-200 dark:border-slate-800">
      <form method="GET" class="flex gap-3">
        <div class="relative flex-1 max-w-md">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
          <input name="search" value="<?= htmlspecialchars($search) ?>" class="w-full pl-10 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary" placeholder="Search student or course..." type="text"/>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90">Search</button>
        <?php if($search): ?><a href="/CMS/admin/enrollments.php" class="px-3 py-2 text-sm text-slate-500 hover:text-primary self-center">Clear</a><?php endif; ?>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-slate-50 dark:bg-slate-800/50">
          <tr>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Student</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Course</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Year</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Enrolled Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php if (empty($enrollments)): ?>
          <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No enrollments found.</td></tr>
          <?php else: ?>
          <?php foreach ($enrollments as $i => $e):
            $initials = strtoupper(implode('', array_map(fn($w)=>$w[0], explode(' ', $e['student_name']))));
            $yearColors = ['First Year'=>'bg-blue-100 text-blue-700','Second Year'=>'bg-emerald-100 text-emerald-700','Third Year'=>'bg-purple-100 text-purple-700','Fourth Year'=>'bg-orange-100 text-orange-700'];
            $yearCls = $yearColors[$e['academic_year']] ?? 'bg-slate-100 text-slate-600';
          ?>
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
            <td class="px-6 py-4 text-sm text-slate-400"><?= $offset+$i+1 ?></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/15 flex items-center justify-center text-primary text-xs font-bold"><?= htmlspecialchars(substr($initials,0,2)) ?></div>
                <div>
                  <p class="text-sm font-semibold"><?= htmlspecialchars($e['student_name']) ?></p>
                  <p class="text-xs text-slate-400"><?= htmlspecialchars($e['student_email']) ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <p class="text-sm font-medium"><?= htmlspecialchars($e['course_title']) ?></p>
              <?php if($e['category']): ?><p class="text-xs text-slate-400"><?= htmlspecialchars($e['category']) ?></p><?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs font-bold rounded-full <?= $yearCls ?>"><?= htmlspecialchars($e['academic_year']) ?></span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-500"><?= date('M d, Y', strtotime($e['enrolled_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages>1): ?>
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
      <p class="text-sm text-slate-500">Showing <?= $offset+1 ?>–<?= min($offset+$perPage,$total) ?> of <?= $total ?> enrollments</p>
      <div class="flex items-center gap-2">
        <?php if($page>1): ?><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-white"><span class="material-symbols-outlined text-xl">chevron_left</span></a><?php endif; ?>
        <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="size-9 flex items-center justify-center rounded-lg text-sm font-bold <?= $i===$page?'bg-primary text-white':'border border-transparent hover:border-slate-200 text-slate-600' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if($page<$totalPages): ?><a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-white"><span class="material-symbols-outlined text-xl">chevron_right</span></a><?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
