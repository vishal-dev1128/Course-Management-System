<?php
require_once 'config/db.php';
require_once 'config/session.php';

$db = getDB();
$pageTitle = 'Courses';

// Filters
$yearFilter     = sanitize($_GET['year'] ?? '');
$categoryFilter = sanitize($_GET['category'] ?? '');
$search         = sanitize($_GET['search'] ?? '');

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?> - EduManage</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {"primary":"#1e3b8a","background-light":"#f6f6f8"},
        fontFamily: {"display":["Inter","sans-serif"]},
        borderRadius: {"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},
      }
    }
  }
</script>
<style>
  body { font-family:'Inter',sans-serif; }
  .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
  
  /* Prevent flash of unstyled content for dark mode */
  .dark body { background-color: #121620; color: #f1f5f9; }
</style>
<script>
  if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
  function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
      document.documentElement.classList.remove('dark');
      localStorage.theme = 'light';
    } else {
      document.documentElement.classList.add('dark');
      localStorage.theme = 'dark';
    }
  }
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">

<!-- Navigation -->
<header class="flex items-center justify-between whitespace-nowrap border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-6 lg:px-20 py-4 sticky top-0 z-50 shadow-sm shrink-0">
  <a href="/CMS/index.php" class="flex items-center hover:opacity-80 transition-opacity cursor-pointer">
    <img src="/CMS/assets/images/logo.png" alt="EduStream Logo" class="h-14 w-auto object-contain">
  </a>
  <div class="flex flex-1 justify-end gap-8 items-center">
    <nav class="hidden md:flex items-center gap-8">
      <a class="text-slate-700 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="/CMS/index.php">Home</a>
      <a class="text-primary text-sm font-bold transition-colors" href="/CMS/courses.php">Courses</a>
      <a class="text-slate-700 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="/CMS/about.php">About</a>
      <a class="text-slate-700 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="/CMS/auth/login.php">Login</a>
    </nav>
    <div class="flex items-center gap-4">
      <button onclick="toggleTheme()" title="Toggle Dark Mode" class="flex items-center justify-center">
        <span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-primary transition-colors">light_mode</span>
      </button>
      <a href="/CMS/auth/login.php?tab=register" class="flex min-w-[110px] cursor-pointer items-center justify-center rounded-lg h-10 px-5 bg-primary text-white text-sm font-bold transition-all hover:bg-primary/90 shadow-md hover:shadow-lg hover:shadow-primary/30">
        Get Started
      </a>
    </div>
  </div>
</header>

<main class="flex-1 px-6 md:px-20 py-10 max-w-[1280px] mx-auto w-full">
  <!-- Hero/Intro -->
  <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
      <h1 class="text-slate-900 dark:text-white text-4xl font-black leading-tight tracking-tight mb-3">Explore Our Course Catalog</h1>
      <p class="text-slate-600 dark:text-slate-400 text-lg max-w-2xl">Discover industry-leading technical courses designed to accelerate your academic and professional journey.</p>
    </div>
    
    <form method="GET" class="w-full md:w-auto min-w-[300px]">
      <div class="flex items-stretch rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden">
        <div class="text-slate-400 flex items-center justify-center pl-4">
          <span class="material-symbols-outlined text-lg">search</span>
        </div>
        <input name="search" value="<?= htmlspecialchars($search) ?>" class="form-input flex w-full min-w-0 flex-1 border-none bg-transparent focus:ring-0 text-slate-900 dark:text-slate-100 placeholder:text-slate-500 text-sm font-normal py-2.5" placeholder="Search courses..."/>
      </div>
      <?php if($yearFilter): ?><input type="hidden" name="year" value="<?= htmlspecialchars($yearFilter) ?>"><?php endif; ?>
      <?php if($categoryFilter): ?><input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>"><?php endif; ?>
    </form>
  </div>

  <!-- Filters Section -->
  <div class="flex flex-col gap-6 mb-12">
    <div class="flex flex-col gap-4">
      <div class="flex items-center gap-2 text-slate-900 dark:text-white font-semibold">
        <span class="material-symbols-outlined text-lg">filter_list</span>
        <span>Refine Search</span>
      </div>
      
      <div class="flex flex-wrap gap-4">
        <div class="flex flex-wrap gap-2 pr-6 border-r border-slate-200 dark:border-slate-800">
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest w-full mb-1">Academic Year</span>
          <a href="?<?= http_build_query(array_merge($_GET,['year'=>''])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 shadow-sm text-sm font-medium transition-all <?= !$yearFilter ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>">All Years</a>
          <?php foreach ($years as $y): ?>
          <a href="?<?= http_build_query(array_merge($_GET,['year'=>$y])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium transition-all <?= $yearFilter===$y ? 'bg-primary text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>"><?= $y ?></a>
          <?php endforeach; ?>
        </div>

        <?php if (!empty($categories)): ?>
        <div class="flex flex-wrap gap-2">
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest w-full mb-1">Category</span>
          <a href="?<?= http_build_query(array_merge($_GET,['category'=>''])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium transition-all <?= !$categoryFilter ? 'bg-primary text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>">All</a>
          <?php foreach ($categories as $cat): ?>
          <a href="?<?= http_build_query(array_merge($_GET,['category'=>$cat])) ?>" class="flex h-10 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium transition-all <?= $categoryFilter===$cat ? 'bg-primary text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-primary text-slate-700 dark:text-slate-300' ?>"><?= htmlspecialchars($cat) ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Course Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (empty($courses)): ?>
      <div class="col-span-full py-20 text-center">
        <span class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-700 mb-4 block">search_off</span>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No courses found</h3>
        <p class="text-slate-500">Try adjusting your filters or search query.</p>
        <a href="courses.php" class="mt-4 inline-block text-primary font-semibold hover:underline">Clear all filters</a>
      </div>
    <?php else: ?>
      <?php foreach ($courses as $c): 
        $icon = $categoryIcons[$c['category'] ?? ''] ?? $categoryIcons['default'];
      ?>
      <div class="flex flex-col overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm hover:shadow-lg transition-all group">
        <div class="h-48 w-full bg-primary/10 flex items-center justify-center relative border-b border-primary/5 overflow-hidden">
          <?php if (!empty($c['image'])): ?>
          <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
          <?php else: ?>
          <span class="material-symbols-outlined text-6xl text-primary/40 group-hover:scale-110 transition-transform"><?= $icon ?></span>
          <?php endif; ?>
          <div class="absolute top-4 right-4 bg-white/95 dark:bg-slate-900/95 px-2 py-1 rounded text-[10px] font-black text-primary uppercase shadow-sm tracking-widest border border-primary/10"><?= htmlspecialchars($c['academic_year']) ?></div>
        </div>
        <div class="p-6 flex flex-col flex-1">
          <div class="flex items-center gap-2 mb-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?= htmlspecialchars($c['category'] ?? 'General') ?></span>
          </div>
          <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2 leading-tight group-hover:text-primary transition-colors"><?= htmlspecialchars($c['title']) ?></h3>
          <p class="text-slate-500 text-sm mb-6 flex-1 line-clamp-2 leading-relaxed"><?= htmlspecialchars($c['description'] ?? 'Unlock your potential with this comprehensive course.') ?></p>
          
          <div class="flex items-center gap-3 mb-6 pt-4 border-t border-slate-100 dark:border-slate-800">
            <div class="size-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-primary font-black text-[12px]">
               <?= strtoupper(substr($c['instructor_name']??'A',0,1)) ?>
            </div>
            <div class="text-sm min-w-0">
              <p class="font-bold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($c['instructor_name'] ?? 'EduManage Expert') ?></p>
              <p class="text-slate-500 text-[10px] uppercase font-bold tracking-tight">Instructor</p>
            </div>
          </div>
          
          <!-- Redirect to login for public viewing -->
          <a href="/CMS/auth/login.php?tab=login&redirect=enroll" class="w-full bg-slate-900 dark:bg-slate-800 text-white dark:text-slate-200 py-3 rounded-lg font-black text-[11px] uppercase tracking-widest hover:bg-primary transition-all shadow-md flex items-center justify-center gap-2">
            Enroll Now <span class="material-symbols-outlined text-sm">login</span>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

<!-- Footer -->
<footer class="bg-black border-t border-slate-900 px-6 lg:px-20 py-12 text-white shrink-0 mt-auto">
  <div class="max-w-[1280px] mx-auto grid md:grid-cols-4 gap-12">
    <div class="flex flex-col gap-4 col-span-1 md:col-span-2">
      <div class="flex items-center gap-3 text-white">
        <div class="size-7 bg-white rounded flex items-center justify-center text-black">
          <span class="material-symbols-outlined text-sm">school</span>
        </div>
        <h2 class="text-white text-lg font-bold">EduManage</h2>
      </div>
      <p class="text-slate-400 text-sm max-w-xs">The future of academic management. Empowering educators and students worldwide with cutting-edge technology.</p>
    </div>
    <div class="flex flex-col gap-4">
      <h4 class="text-white font-bold text-sm">Quick Links</h4>
      <nav class="flex flex-col gap-2">
        <a class="text-slate-400 text-sm hover:text-white transition-colors" href="/CMS/courses.php">Courses</a>
        <a class="text-slate-400 text-sm hover:text-white transition-colors" href="/CMS/auth/login.php">Login</a>
        <a class="text-slate-400 text-sm hover:text-white transition-colors" href="/CMS/auth/login.php?tab=register">Register</a>
      </nav>
    </div>
    <div class="flex flex-col gap-4">
      <h4 class="text-white font-bold text-sm">Support</h4>
      <nav class="flex flex-col gap-2">
        <a class="text-slate-400 text-sm hover:text-white transition-colors" href="/CMS/help_center.php">Help Center</a>
        <a class="text-slate-400 text-sm hover:text-white transition-colors" href="/CMS/privacy.php">Privacy Policy</a>
        <a class="text-slate-400 text-sm hover:text-white transition-colors" href="/CMS/contact.php">Contact Us</a>
      </nav>
    </div>
  </div>
  <div class="max-w-[1280px] mx-auto mt-10 pt-8 border-t border-slate-800 text-center">
    <p class="text-slate-500 text-xs">© <?= date('Y') ?> EduManage Course Management System. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
