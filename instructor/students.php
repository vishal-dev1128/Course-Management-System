<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('instructor');

$db      = getDB();
$pageTitle = 'My Students';
$instrId = (int)$_SESSION['user_id'];
$selectedCourse = (int)($_GET['course_id'] ?? 0);

// Get instructor's courses for filter dropdown
$myCourses = $db->prepare('SELECT id, title FROM courses WHERE instructor_id=? ORDER BY title');
$myCourses->execute([$instrId]);
$myCourses = $myCourses->fetchAll();

$search = sanitize($_GET['search'] ?? '');

// Build query
$conditions = ['c.instructor_id = :iid'];
$params = [':iid' => $instrId];
if ($selectedCourse) { $conditions[] = 'e.course_id = :cid'; $params[':cid'] = $selectedCourse; }
if ($search)         { $conditions[] = '(u.name LIKE :s OR u.email LIKE :s2)'; $params[':s'] = "%$search%"; $params[':s2'] = "%$search%"; }
$where = 'WHERE '.implode(' AND ',$conditions);

$students = $db->prepare("SELECT DISTINCT u.id, u.name, u.email, u.status, c.title AS course_title, c.id AS course_id, e.enrolled_at, e.progress
    FROM enrollments e
    JOIN users u ON e.student_id=u.id
    JOIN courses c ON e.course_id=c.id
    $where
    ORDER BY e.enrolled_at DESC");
$students->execute($params);
$students = $students->fetchAll();

require_once '../includes/instructor_sidebar.php';
?>

<!-- Header -->
<header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
  <div>
    <h2 class="text-2xl font-bold">Students</h2>
    <p class="text-slate-500 text-sm">Students enrolled in your courses</p>
  </div>
</header>

<div class="p-8">
  <!-- Filters Table Card -->
  <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
    <div class="p-4 border-b border-slate-50 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/30">
      <h3 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight ml-1">Learning Community</h3>
      <form method="GET" class="flex gap-2 flex-wrap">
        <div class="relative w-56">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
          <input name="search" value="<?= htmlspecialchars($search) ?>" class="block w-full pl-9 pr-3 py-1.5 bg-white dark:bg-slate-800 rounded-lg text-xs border-none focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Search students..." type="text"/>
        </div>
        <select name="course_id" onchange="this.form.submit()" class="px-2 py-1.5 bg-white dark:bg-slate-800 rounded-lg text-xs border-none focus:ring-2 focus:ring-primary/20 outline-none font-bold text-slate-500">
          <option value="">All Assigned Courses</option>
          <?php foreach($myCourses as $mc): ?>
          <option value="<?= $mc['id'] ?>" <?= $selectedCourse===$mc['id']?'selected':'' ?>><?= htmlspecialchars($mc['title']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-primary hover:text-white rounded-lg text-xs font-black transition-all">FILTER</button>
        <?php if($search||$selectedCourse): ?><a href="/CMS/instructor/students.php" class="px-2 py-1.5 text-[10px] font-black text-slate-400 hover:text-primary uppercase flex items-center">Clear</a><?php endif; ?>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-[10px] font-black uppercase tracking-widest">
          <tr>
            <th class="px-5 py-3.5">Student Name</th>
            <th class="px-5 py-3.5">Course Focus</th>
            <th class="px-5 py-3.5">Status</th>
            <th class="px-5 py-3.5">Progress</th>
            <th class="px-5 py-3.5 text-right">Enrolled</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php if (empty($students)): ?>
          <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">No students found.</td></tr>
          <?php else: ?>
          <?php foreach ($students as $s):
            $initials = strtoupper(implode('',array_map(fn($w)=>$w[0],explode(' ',$s['name']))));
          ?>
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-xs text-primary"><?= htmlspecialchars(substr($initials,0,2)) ?></div>
                <div>
                  <p class="text-sm font-semibold"><?= htmlspecialchars($s['name']) ?></p>
                  <p class="text-xs text-slate-400"><?= htmlspecialchars($s['email']) ?></p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($s['course_title']) ?></td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $s['status']==='active'?'bg-green-100 text-green-800':'bg-orange-100 text-orange-800' ?>"><?= ucfirst($s['status']) ?></span>
            </td>
            <td class="px-6 py-4">
               <div class="flex items-center gap-2">
                 <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full w-16 overflow-hidden">
                   <div class="bg-primary h-full rounded-full" style="width:<?= (int)$s['progress'] ?>%"></div>
                 </div>
                 <span class="text-[10px] font-bold text-slate-500"><?= (int)$s['progress'] ?>%</span>
               </div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 text-right"><?= date('M d, Y', strtotime($s['enrolled_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="px-5 py-3 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-50 dark:border-slate-800">
      <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Showing <?= count($students) ?> Active Community Member<?= count($students)!==1?'s':'' ?></span>
    </div>
  </div>
</div>

</main>
</div>
</body>
</html>
