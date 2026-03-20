<?php
// Shared admin sidebar + page wrapper start
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'Admin' ?> - EduManage</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
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
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="flex min-h-screen">
<!-- Sidebar -->
<aside class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sticky top-0 h-screen shrink-0 z-30">
  <a href="/CMS/admin/dashboard.php" class="p-6 flex items-center hover:opacity-80 transition-opacity cursor-pointer">
    <img src="/CMS/assets/images/logo.png" alt="Logo" class="h-14 w-auto object-contain">
  </a>
  <nav class="flex-1 px-4 py-4 space-y-1">
    <?php
    $navItems = [
      ['href'=>'/CMS/admin/dashboard.php',   'icon'=>'dashboard',          'label'=>'Dashboard'],
      ['href'=>'/CMS/admin/courses.php',      'icon'=>'book_2',             'label'=>'Manage Courses'],
      ['href'=>'/CMS/admin/users.php',        'icon'=>'group',              'label'=>'Manage Users'],
      ['href'=>'/CMS/admin/enrollments.php',  'icon'=>'person_add',         'label'=>'Enrollments'],
    ];
    foreach ($navItems as $item):
      $isActive = (basename($item['href']) === $currentPage);
      $cls = $isActive
        ? 'flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary font-medium'
        : 'flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors';
    ?>
    <a class="<?= $cls ?>" href="<?= $item['href'] ?>">
      <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
      <span><?= $item['label'] ?></span>
    </a>
    <?php endforeach; ?>
    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Settings</div>
    <?php
    $settingsItems = [
      ['href'=>'/CMS/admin/system_logs.php','icon'=>'analytics','label'=>'System Logs'],
    ];
    foreach ($settingsItems as $item):
    ?>
    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" href="<?= $item['href'] ?>">
      <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
      <span><?= $item['label'] ?></span>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-4 border-t border-slate-200 dark:border-slate-800">
    <div class="flex items-center gap-3 p-2">
      <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm">
        <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 2)) ?>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></p>
        <p class="text-xs text-slate-500 truncate">Super Admin</p>
      </div>
      <button onclick="toggleTheme()" title="Toggle Dark Mode" class="flex items-center justify-center mr-2">
        <span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-primary transition-colors">light_mode</span>
      </button>
      <a href="/CMS/auth/logout.php" title="Logout" class="flex items-center justify-center">
        <span class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-red-500 transition-colors">logout</span>
      </a>
    </div>
  </div>
</aside>
<!-- Main Content -->
<main class="flex-1 overflow-y-auto">
