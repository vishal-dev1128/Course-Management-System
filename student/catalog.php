<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

$db        = getDB();
$pageTitle = 'Course Catalog';
$studentId = (int)$_SESSION['user_id'];
$flash     = getFlash();

// Filters
$yearFilter     = sanitize($_GET['year'] ?? '');
$categoryFilter = sanitize($_GET['category'] ?? '');
$search         = sanitize($_GET['search'] ?? '');

// Get enrolled course IDs for this student
$enrolledStmt = $db->prepare('SELECT course_id FROM enrollments WHERE student_id=?');
$enrolledStmt->execute([$studentId]);
$enrolledIds  = array_column($enrolledStmt->fetchAll(), 'course_id');

// Build filter
$conditions = ["c.status='active'"];
$params = [];
if ($yearFilter)     { $conditions[] = 'c.academic_year=:year'; $params[':year']=$yearFilter; }
if ($categoryFilter) { $conditions[] = 'c.category=:cat'; $params[':cat']=$categoryFilter; }
if ($search)         { $conditions[] = '(c.title LIKE :s OR c.description LIKE :s2)'; $params[':s']="%$search%"; $params[':s2']="%$search%"; }
$where = $conditions ? 'WHERE '.implode(' AND ',$conditions) : '';

$stmt = $db->prepare("SELECT c.*, u.name AS instructor_name FROM courses c LEFT JOIN users u ON c.instructor_id=u.id $where ORDER BY c.id DESC");
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Distinct categories for filter
$categories = $db->query("SELECT DISTINCT category FROM courses WHERE category IS NOT NULL AND status='active' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$categoryIcons = [
    'Programming'     => 'terminal',
    'Web Development' => 'html',
    'Data Science'    => 'monitoring',
    'Design'          => 'palette',
    'default'         => 'menu_book',
];

$years = ['First Year','Second Year','Third Year','Fourth Year'];

require_once '../includes/student_sidebar.php';
?>

<!-- Header -->
<header class="flex items-center justify-between whitespace-nowrap border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-6 py-4 sticky top-0 z-10">
  <div class="flex items-center gap-8">
    <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Course Catalog</h1>
  </div>
  <div class="flex flex-1 justify-end gap-4 max-w-sm ml-8">
    <form method="GET" class="flex w-full flex-1 items-stretch rounded-lg h-10">
      <div class="text-slate-400 flex bg-slate-100 dark:bg-slate-800 items-center justify-center pl-4 rounded-l-lg">
        <span class="material-symbols-outlined text-lg">search</span>
      </div>
      <input name="search" value="<?= htmlspecialchars($search) ?>" class="form-input flex w-full min-w-0 flex-1 border-none bg-slate-100 dark:bg-slate-800 focus:ring-2 focus:ring-primary rounded-r-lg text-sm placeholder:text-slate-500" placeholder="Search courses..." type="text"/>
    </form>
  </div>
</header>

<main class="flex-1 px-8 py-8 w-full">
  <?php if($flash): ?>
  <div class="mb-4 px-4 py-3 rounded-lg flex items-center gap-2 text-sm <?= $flash['type']==='success'?'bg-green-50 border border-green-200 text-green-700':'bg-red-50 border border-red-200 text-red-700' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <!-- Intro -->
  <div class="mb-8">
    <h2 class="text-slate-900 dark:text-white text-4xl font-black leading-tight tracking-tight mb-2">Explore Our Course Catalog</h2>
    <p class="text-slate-600 dark:text-slate-400 text-lg max-w-2xl">Discover industry-leading courses designed to accelerate your academic and professional journey.</p>
  </div>

  <!-- Filters -->
  <div class="flex flex-col gap-4 mb-10">
    <form method="GET" class="flex flex-wrap gap-4">
      <?php if($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>

      <div class="flex flex-wrap gap-2 pr-6 border-r border-slate-200 dark:border-slate-800">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider w-full mb-1">Academic Year</span>
        <a href="?<?= http_build_query(array_merge($_GET,['year'=>''])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 shadow-sm text-sm font-medium transition-all <?= !$yearFilter ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>">All Years</a>
        <?php foreach ($years as $y): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['year'=>$y])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium transition-all <?= $yearFilter===$y ? 'bg-primary text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>"><?= $y ?></a>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($categories)): ?>
      <div class="flex flex-wrap gap-2">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider w-full mb-1">Category</span>
        <a href="?<?= http_build_query(array_merge($_GET,['category'=>''])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium transition-all <?= !$categoryFilter ? 'bg-primary text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['category'=>$cat])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium transition-all <?= $categoryFilter===$cat ? 'bg-primary text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>"><?= htmlspecialchars($cat) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </form>
  </div>

  <!-- Course Grid -->
  <?php if (empty($courses)): ?>
  <div class="text-center py-16 text-slate-400">
    <span class="material-symbols-outlined text-5xl mb-3 block">search_off</span>
    <p class="text-lg font-medium">No courses found.</p>
    <a href="/CMS/student/catalog.php" class="text-primary font-semibold hover:underline text-sm mt-1 inline-block">Clear filters</a>
  </div>
  <?php else: ?>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($courses as $c):
      $icon       = $categoryIcons[$c['category'] ?? ''] ?? $categoryIcons['default'];
      $isEnrolled = in_array($c['id'], $enrolledIds);
      $yearColors = ['First Year'=>'bg-blue-100 text-blue-700','Second Year'=>'bg-emerald-100 text-emerald-700','Third Year'=>'bg-purple-100 text-purple-700','Fourth Year'=>'bg-orange-100 text-orange-700'];
      $yearCls    = $yearColors[$c['academic_year']] ?? 'bg-slate-100 text-slate-600';
    ?>
    <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden flex flex-col transition-all group hover:scale-[1.02]" style="box-shadow:0 4px 20px -5px rgba(30,59,138,0.05), 0 20px 40px -15px rgba(30,59,138,0.08)">
      <div class="h-44 w-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center relative border-b border-slate-50 dark:border-slate-800 overflow-hidden">
        <?php if (!empty($c['image'])): ?>
        <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        <?php else: ?>
        <span class="material-symbols-outlined text-6xl text-primary opacity-20 group-hover:scale-110 transition-transform"><?= $icon ?></span>
        <?php endif; ?>
        <div class="absolute top-3 right-3 bg-white/95 dark:bg-slate-900/95 px-1.5 py-0.5 rounded text-[9px] font-black text-primary uppercase shadow-sm border border-slate-100 dark:border-slate-800 tracking-widest"><?= htmlspecialchars($c['academic_year']) ?></div>
        <?php if ($isEnrolled): ?>
        <div class="absolute top-3 left-3 bg-emerald-500 text-white px-2 py-0.5 rounded text-[9px] font-black flex items-center gap-1 shadow-sm tracking-widest uppercase"><span class="material-symbols-outlined text-[12px]">check</span>Enrolled</div>
        <?php endif; ?>
      </div>
      <div class="p-5 flex flex-col flex-1">
        <div class="flex items-center gap-2 mb-1.5">
          <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.1em]"><?= htmlspecialchars($c['category'] ?? 'General') ?></span>
        </div>
        <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1.5 tracking-tight leading-snug group-hover:text-primary transition-colors"><?= htmlspecialchars($c['title']) ?></h3>
        <p class="text-slate-500 text-[11px] mb-4 flex-1 line-clamp-2 leading-relaxed"><?= htmlspecialchars($c['description'] ?? 'Unlock your potential with this comprehensive course.') ?></p>
        
        <div class="flex items-center gap-2.5 mb-4 border-t border-slate-50 dark:border-slate-800 pt-3.5">
          <div class="size-7 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-[10px]">
            <?= strtoupper(substr($c['instructor_name']??'A',0,1)) ?>
          </div>
          <div class="min-w-0">
            <p class="font-bold text-slate-800 dark:text-slate-200 text-[10px] truncate"><?= htmlspecialchars($c['instructor_name'] ?? 'EduStream Expert') ?></p>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Certified Lead</p>
          </div>
        </div>

        <?php if ($isEnrolled): ?>
        <a href="/CMS/student/dashboard.php" class="w-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 py-2.5 rounded-lg font-black text-[10px] text-center uppercase tracking-widest hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-all">
          ✓ In Learning Dashboard
        </a>
        <?php else: ?>
        <form method="POST" action="/CMS/api/enroll.php">
          <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
          <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
            Enroll Now +
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- Empty Placeholder -->
    <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-800 p-8 text-center bg-slate-50 dark:bg-slate-900/50">
      <span class="material-symbols-outlined text-4xl text-slate-300 mb-4">add_circle</span>
      <h3 class="text-slate-900 dark:text-white font-semibold mb-1">More courses coming soon</h3>
      <p class="text-slate-500 text-sm">We are constantly adding new content to help you learn.</p>
    </div>
  </div>
  <?php endif; ?>
</main>

</main>
</div>
</body>
</html>
