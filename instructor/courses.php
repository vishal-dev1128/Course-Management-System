<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('instructor');

$db      = getDB();
$pageTitle = 'My Courses';
$instrId = (int)$_SESSION['user_id'];

$flash = getFlash();

$courses = $db->prepare('SELECT c.*, (SELECT COUNT(*) FROM enrollments e WHERE e.course_id=c.id) AS student_count FROM courses c WHERE c.instructor_id=? ORDER BY c.id DESC');
$courses->execute([$instrId]);
$courses = $courses->fetchAll();

// Handle description update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_desc'])) {
    $courseId = (int)$_POST['course_id'];
    $desc = sanitize($_POST['description']);
    $stmt = $db->prepare('UPDATE courses SET description=? WHERE id=? AND instructor_id=?');
    $stmt->execute([$desc, $courseId, $instrId]);
    setFlash('success','Course description updated!');
    header('Location: /CMS/instructor/courses.php');
    exit;
}

require_once '../includes/instructor_sidebar.php';
?>

<!-- Header -->
<header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 py-4 sticky top-0 z-10">
  <h2 class="text-2xl font-bold">My Courses</h2>
  <p class="text-slate-500 text-sm">Manage your assigned courses</p>
</header>

<div class="p-8">
  <?php if($flash): ?>
  <div class="mb-4 px-4 py-3 rounded-lg flex items-center gap-2 text-sm <?= $flash['type']==='success'?'bg-green-50 border border-green-200 text-green-700':'bg-red-50 border border-red-200 text-red-700' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <?php if (empty($courses)): ?>
  <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-16 text-center">
    <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">menu_book</span>
    <h3 class="font-bold text-slate-700 mb-1">No courses assigned yet</h3>
    <p class="text-slate-400 text-sm">Contact your administrator to get courses assigned.</p>
  </div>
  <?php else: ?>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <?php foreach ($courses as $c):
      $yearColors = ['First Year'=>'bg-blue-50 text-blue-700','Second Year'=>'bg-emerald-50 text-emerald-700','Third Year'=>'bg-purple-50 text-purple-700','Fourth Year'=>'bg-orange-50 text-orange-700'];
      $yearCls = $yearColors[$c['academic_year']] ?? 'bg-slate-50 text-slate-600';
    ?>
    <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm transition-all hover:scale-[1.01]" style="box-shadow:0 4px 20px -5px rgba(30,59,138,0.05), 0 20px 40px -15px rgba(30,59,138,0.08)">
      <div class="flex items-center gap-4 p-4 border-b border-slate-50 dark:border-slate-800">
        <div class="size-11 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden relative border border-primary/10">
          <?php if (!empty($c['image'])): ?>
          <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover">
          <?php else: ?>
          <span class="material-symbols-outlined text-primary text-xl">menu_book</span>
          <?php endif; ?>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-0.5">
            <span class="text-[9px] font-black uppercase tracking-widest <?= $yearCls ?> px-2 py-0.5 rounded-full"><?= htmlspecialchars($c['academic_year']) ?></span>
            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full <?= $c['status']==='active'?'bg-green-50 text-green-600':'bg-slate-100 text-slate-400' ?>"><?= $c['status'] ?></span>
          </div>
          <h4 class="font-extrabold text-sm text-slate-900 dark:text-white truncate"><?= htmlspecialchars($c['title']) ?></h4>
          <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter"><?= $c['student_count'] ?> Students Enrolled</p>
        </div>
      </div>
      <!-- Edit description form -->
      <form method="POST" class="p-4 bg-white/50 dark:bg-slate-900/50">
        <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5 ml-1">Learning Description</label>
        <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-xs resize-none mb-3"><?= htmlspecialchars($c['description'] ?? '') ?></textarea>
        <div class="flex items-center justify-between gap-3">
          <a href="/CMS/instructor/students.php?course_id=<?= $c['id'] ?>" class="flex items-center gap-1.5 text-[11px] text-primary font-black hover:underline uppercase tracking-tight">
            <span class="material-symbols-outlined text-[16px]">group</span> View Students
          </a>
          <button type="submit" name="update_desc" class="flex items-center gap-1.5 px-4 py-1.5 bg-primary text-white rounded-lg text-[11px] font-black hover:bg-primary/90 transition-all shadow-md shadow-primary/20 uppercase tracking-tight">
            <span class="material-symbols-outlined text-[16px]">save</span> Save
          </button>
        </div>
      </form>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

</main>
</div>
</body>
</html>
