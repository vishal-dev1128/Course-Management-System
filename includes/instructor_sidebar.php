<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'Instructor' ?> - EduManage</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
  tailwind.config = {
    darkMode:"class",
    theme:{extend:{
      colors:{"primary":"#1e3b8a","background-light":"#f6f6f8","background-dark":"#121620"},
      fontFamily:{"display":["Inter"]},
      borderRadius:{"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},
    }}
  }
</script>
<style>
  body{font-family:'Inter',sans-serif;}
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  .active-nav{background-color:rgba(30,59,138,0.1);color:#1e3b8a;border-right:3px solid #1e3b8a;}
  
  /* Prevent flash of unstyled content for dark mode */
  .dark body { background-color: #121620; color: #f1f5f9; }

  /* Hide all scrollbars globally */
  *::-webkit-scrollbar { display: none; }
  * { scrollbar-width: none; -ms-overflow-style: none; }
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
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="flex min-h-screen">
<!-- Sidebar -->
<!-- Custom Modern Sidebar -->
<aside class="w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col sticky top-0 h-screen shrink-0 z-40 transition-all shadow-xl shadow-slate-200/50 dark:shadow-none">
  <!-- Brand Header -->
  <div class="p-8 pb-6 flex items-center justify-between">
    <a href="/CMS/instructor/dashboard.php" class="flex items-center gap-3 group transition-transform active:scale-95">
      <div class="size-10 rounded-xl bg-gradient-to-tr from-primary to-blue-600 flex items-center justify-center shadow-lg shadow-primary/30 group-hover:rotate-12 transition-transform">
        <span class="material-symbols-outlined text-white text-2xl">history_edu</span>
      </div>
      <div class="flex flex-col">
        <span class="text-xl font-black tracking-tight text-slate-800 dark:text-white leading-none">EduManage</span>
        <span class="text-[10px] font-bold text-primary uppercase tracking-widest mt-1">Instructor Portal</span>
      </div>
    </a>
  </div>

  <!-- Navigation Scroll Area -->
  <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-2 custom-scrollbar">
    <div class="px-4 mb-3">
      <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Academic Hub</p>
    </div>

    <?php
    $navItems = [
      ['href'=>'/CMS/instructor/dashboard.php', 'icon'=>'grid_view',     'label'=>'Overview'],
      ['href'=>'/CMS/instructor/courses.php',   'icon'=>'auto_stories',  'label'=>'My Courses'],
      ['href'=>'/CMS/instructor/students.php',  'icon'=>'group',         'label'=>'Student Rosters'],
    ];

    foreach ($navItems as $item):
      $isActive = (basename($item['href']) === basename($_SERVER['PHP_SELF']));
    ?>
    <a href="<?= $item['href'] ?>" 
       class="group flex items-center gap-4 px-4 py-3 rounded-2xl transition-all duration-300 <?= $isActive 
         ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' 
         : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary' ?>">
      <div class="size-8 rounded-lg <?= $isActive ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-800 group-hover:bg-primary/10 group-hover:text-primary' ?> flex items-center justify-center transition-colors">
        <span class="material-symbols-outlined text-[20px]"><?= $item['icon'] ?></span>
      </div>
      <span class="text-sm tracking-tight"><?= $item['label'] ?></span>
      <?php if ($isActive): ?>
        <div class="ml-auto size-1.5 rounded-full bg-white animate-pulse"></div>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- User Profile & Theme -->
  <div class="p-4 mx-4 mb-6 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
    <div class="flex items-center gap-3">
      <div class="size-10 rounded-xl bg-gradient-to-br from-primary/20 to-blue-500/20 flex items-center justify-center text-primary font-black text-xs border border-primary/20">
        <?= strtoupper(substr($_SESSION['user_name'] ?? 'I', 0, 2)) ?>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-slate-800 dark:text-white truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Instructor') ?></p>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Faculty Member</p>
      </div>
    </div>
    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-700/50">
      <button onclick="toggleTheme()" class="flex-1 flex items-center justify-center gap-2 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-primary transition-colors text-slate-500 dark:text-slate-400 hover:text-primary">
        <span class="material-symbols-outlined text-[18px]">dark_mode</span>
        <span class="text-[10px] font-bold uppercase tracking-wider">Theme</span>
      </button>
      <a href="/CMS/auth/logout.php" class="size-9 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-500 hover:text-white transition-all">
        <span class="material-symbols-outlined text-[18px]">logout</span>
      </a>
    </div>
  </div>
</aside>
<!-- Main -->
<main class="flex-1 overflow-y-auto">
