<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

$db        = getDB();
$pageTitle = 'My Dashboard';
$studentId = (int)$_SESSION['user_id'];

$flash = getFlash();

// Enrolled courses
$enrolled = $db->prepare(
    'SELECT c.*, e.enrolled_at, e.progress
     FROM enrollments e
     JOIN courses c ON e.course_id = c.id
     WHERE e.student_id = ?
     ORDER BY e.enrolled_at DESC'
);
$enrolled->execute([$studentId]);
$enrolled = $enrolled->fetchAll();

// Not-enrolled courses (recommended)
$enrolledIds = array_column($enrolled,'id');
if (!empty($enrolledIds)) {
    $placeholders = implode(',', array_fill(0, count($enrolledIds), '?'));
    $recommended  = $db->prepare("SELECT c.*, u.name AS instructor_name FROM courses c LEFT JOIN users u ON c.instructor_id=u.id WHERE c.status='active' AND c.id NOT IN ($placeholders) ORDER BY c.id DESC LIMIT 4");
    $recommended->execute($enrolledIds);
} else {
    $recommended = $db->prepare("SELECT c.*, u.name AS instructor_name FROM courses c LEFT JOIN users u ON c.instructor_id=u.id WHERE c.status='active' ORDER BY c.id DESC LIMIT 4");
    $recommended->execute();
}
$recommended = $recommended->fetchAll();

// Recently added courses
$recentCourses = $db->query("SELECT c.*, u.name AS instructor_name FROM courses c LEFT JOIN users u ON c.instructor_id=u.id WHERE c.status='active' ORDER BY c.created_at DESC LIMIT 5")->fetchAll();



// Icon per category
$categoryIcons = [
    'Programming'     => 'terminal',
    'Web Development' => 'html',
    'Data Science'    => 'monitoring',
    'Design'          => 'palette',
    'default'         => 'menu_book',
];

require_once '../includes/student_sidebar.php';
?>

<!-- Header -->
<header class="sticky top-0 z-10 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between">
  <div class="relative w-80">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
    <input class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/20" placeholder="Search for courses..." type="text" onkeyup="filterCourses(this.value)"/>
  </div>
  <div class="flex items-center gap-6">
    <div class="flex items-center gap-3 pl-6 border-l border-slate-200 dark:border-slate-800">
      <div class="text-right hidden sm:block">
        <p class="text-sm font-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
        <p class="text-xs text-slate-500">Student</p>
      </div>
      <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-sm">
        <?= strtoupper(substr($_SESSION['user_name'],0,2)) ?>
      </div>
    </div>
  </div>
</header>

<div class="p-8 space-y-8">
  <?php if($flash): ?>
  <div class="px-4 py-3 rounded-lg flex items-center gap-2 text-sm <?= $flash['type']==='success'?'bg-green-50 border border-green-200 text-green-700':'bg-red-50 border border-red-200 text-red-700' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <!-- Student Stats Row -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-2">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">menu_book</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Enrolled</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= count($enrolled) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(16,185,129,0.08),0 8px 24px rgba(16,185,129,0.07)">
      <div class="size-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-green-500 text-[18px]">bolt</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">In Progress</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= count($enrolled) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(245,158,11,0.08),0 8px 24px rgba(245,158,11,0.07)">
      <div class="size-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-amber-500 text-[18px]">trending_up</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Avg. Grade</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight">A+</p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(99,102,241,0.08),0 8px 24px rgba(99,102,241,0.07)">
      <div class="size-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-indigo-500 text-[18px]">military_tech</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">EP Points</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight">1,250</p>
      </div>
    </div>
  </div>

  <!-- My Enrolled Courses -->
  <div>
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Active Learning</h2>
    <p class="text-[11px] text-slate-500 mt-1 uppercase font-bold tracking-widest">
      <?php if(count($enrolled)===0): ?>No active courses found. <a href="/CMS/student/catalog.php" class="text-primary hover:underline">BROWSE CATALOG</a>
      <?php else: ?>YOU HAVE <?= count($enrolled) ?> COURSE<?= count($enrolled)!==1?'S':'' ?> IN PROGRESS<?php endif; ?>
    </p>
  </div>

  <?php if(!empty($enrolled)): ?>
  <!-- Course Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5" id="enrolledGrid">
    <?php foreach ($enrolled as $c):
      $progress    = (int)($c['progress'] ?? 0);
      $icon        = $categoryIcons[$c['category'] ?? ''] ?? $categoryIcons['default'];
      $iconColors  = ['terminal'=>'bg-blue-50 text-blue-500','html'=>'bg-orange-50 text-orange-500','monitoring'=>'bg-emerald-50 text-emerald-500','palette'=>'bg-pink-50 text-pink-500','menu_book'=>'bg-primary/10 text-primary'];
      $iconCls     = $iconColors[$icon] ?? $iconColors['menu_book'];
    ?>
    <a href="/CMS/student/learning.php?course_id=<?= $c['id'] ?>" class="block bg-white dark:bg-slate-900 rounded-xl p-3 flex flex-col transition-all group hover:scale-[1.02] course-card cursor-pointer" data-title="<?= strtolower(htmlspecialchars($c['title'])) ?>" style="box-shadow:0 4px 20px -5px rgba(30,59,138,0.05), 0 20px 40px -15px rgba(30,59,138,0.08)">
      <div class="aspect-video rounded-lg mb-3 overflow-hidden relative <?= $iconCls ?> flex items-center justify-center">
        <?php if (!empty($c['image'])): ?>
        <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover">
        <?php else: ?>
        <span class="material-symbols-outlined text-4xl opacity-30"><?= $icon ?></span>
        <?php endif; ?>
        <div class="absolute top-2 left-2 bg-white/90 dark:bg-slate-800/90 px-1.5 py-0.5 rounded text-[8px] font-black text-primary uppercase shadow-sm"><?= htmlspecialchars($c['academic_year']) ?></div>
        <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl text-white">play_circle</span>
        </div>
      </div>
      <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 truncate px-1"><?= htmlspecialchars($c['title']) ?></h3>
      <p class="text-[10px] text-slate-400 mb-3 px-1">Started <?= date('M d, Y', strtotime($c['enrolled_at'])) ?></p>
      <div class="space-y-1.5 px-1 pb-1">
        <div class="flex justify-between text-[10px] font-black uppercase tracking-tighter">
          <span class="text-slate-400">Completion</span>
          <span class="text-primary"><?= $progress ?>%</span>
        </div>
        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
          <div class="bg-primary h-full rounded-full transition-all" style="width:<?= $progress ?>%"></div>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Recommended -->
  <?php if (!empty($recommended)): ?>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recommended for You</h2>
      <a class="text-[11px] font-bold text-primary hover:underline uppercase tracking-tight" href="/CMS/student/catalog.php">All courses →</a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <?php foreach ($recommended as $r):
        $icon    = $categoryIcons[$r['category'] ?? ''] ?? $categoryIcons['default'];
      ?>
      <div class="flex gap-4 bg-white dark:bg-slate-900 p-3 rounded-xl items-center transition-all hover:translate-x-1" style="box-shadow:0 4px 15px -5px rgba(0,0,0,0.05)">
        <div class="size-16 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
          <?php if (!empty($r['image'])): ?>
          <img src="/CMS/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="w-full h-full object-cover">
          <?php else: ?>
          <span class="material-symbols-outlined text-2xl text-primary/30"><?= $icon ?></span>
          <?php endif; ?>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-0.5">
            <span class="text-[8px] font-black bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded uppercase"><?= htmlspecialchars($r['academic_year']) ?></span>
            <span class="text-[10px] text-slate-400 font-medium truncate"><?= htmlspecialchars($r['instructor_name']??'—') ?></span>
          </div>
          <h4 class="font-bold text-sm text-slate-900 dark:text-slate-100 truncate"><?= htmlspecialchars($r['title']) ?></h4>
          <form method="POST" action="/CMS/api/enroll.php" class="mt-1">
            <input type="hidden" name="course_id" value="<?= $r['id'] ?>">
            <button type="submit" class="text-[11px] font-black text-primary hover:underline uppercase tracking-tight">Quick Enroll +</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Recently Added -->
  <?php if (!empty($recentCourses)): ?>
  <div class="space-y-4">
    <h2 class="text-lg font-bold text-slate-900 dark:text-white">New Arrivals</h2>
    <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
      <?php foreach ($recentCourses as $r):
        $icon = $categoryIcons[$r['category']??''] ?? $categoryIcons['default'];
      ?>
      <div class="min-w-[180px] bg-white dark:bg-slate-900 p-3 rounded-xl flex-shrink-0" style="box-shadow:0 2px 10px -3px rgba(0,0,0,0.04)">
        <div class="w-full h-24 rounded-lg mb-3 bg-slate-50 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
          <?php if (!empty($r['image'])): ?>
          <img src="/CMS/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="w-full h-full object-cover">
          <?php else: ?>
          <span class="material-symbols-outlined text-3xl text-primary/20"><?= $icon ?></span>
          <?php endif; ?>
        </div>
        <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($r['title']) ?></h5>
        <p class="text-[10px] text-slate-400 mt-1"><?= htmlspecialchars($r['instructor_name'] ?? 'EduManage') ?></p>
        <form method="POST" action="/CMS/api/enroll.php" class="mt-2">
          <input type="hidden" name="course_id" value="<?= $r['id'] ?>">
          <?php if(in_array($r['id'], $enrolledIds ?? [])): ?>
          <span class="text-[10px] text-emerald-600 font-black uppercase tracking-widest">✓ Enrolled</span>
          <?php else: ?>
          <button type="submit" class="text-[10px] text-primary font-black hover:underline uppercase tracking-widest">Enroll</button>
          <?php endif; ?>
        </form>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function filterCourses(query){
  const cards = document.querySelectorAll('.course-card');
  const q = query.toLowerCase();
  cards.forEach(card => {
    const title = card.dataset.title || '';
    card.style.display = title.includes(q) ? '' : 'none';
  });
}
</script>

</main>
</div>
</body>
</html>
