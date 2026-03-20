<?php
$pageTitle = 'About Us';
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
      <a class="text-slate-700 dark:text-slate-300 text-sm font-medium hover:text-primary transition-colors" href="/CMS/courses.php">Courses</a>
      <a class="text-primary text-sm font-bold transition-colors" href="/CMS/about.php">About</a>
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

<!-- Main Content -->
<main class="flex-1">
  <div class="px-6 lg:px-20 py-16 lg:py-24 max-w-[1280px] mx-auto">
    
    <div class="text-center max-w-3xl mx-auto mb-16">
      <div class="inline-flex items-center gap-2 bg-primary/10 text-primary px-4 py-1.5 rounded-full text-sm font-semibold mb-6">
        <span class="material-symbols-outlined text-sm">auto_awesome</span> Our Story
      </div>
      <h1 class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-6 leading-tight">
        Empowering the Next Generation of <span class="text-primary">Learners</span>
      </h1>
      <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
        EduManage was founded with a simple mission: to make world-class education accessible, organized, and engaging for everyone. We build tools that take the friction out of learning so students and educators can focus on what matters most.
      </p>
    </div>

    <!-- Mission grid -->
    <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
      <div class="aspect-video bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden relative shadow-xl group">
         <img src="https://loremflickr.com/800/600/education" alt="Students learning together" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
         <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
         <div class="absolute bottom-6 left-6 text-white font-bold text-xl">Inspiring Minds</div>
      </div>
      <div class="flex flex-col gap-6">
        <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Built for the Modern Classroom</h2>
        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
          Whether you are an administrator managing hundreds of courses, an instructor charting the progress of your students, or a learner hungry for new skills, EduManage provides a unified platform that adapts to your unique workflow.
        </p>
        <ul class="space-y-4 mt-2">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary bg-primary/10 p-1 rounded">check</span>
            <span class="text-slate-700 dark:text-slate-300 font-medium pt-0.5">Streamlined Dashboard Experiences</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary bg-primary/10 p-1 rounded">check</span>
            <span class="text-slate-700 dark:text-slate-300 font-medium pt-0.5">Advanced Course Analytics</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary bg-primary/10 p-1 rounded">check</span>
            <span class="text-slate-700 dark:text-slate-300 font-medium pt-0.5">Secure, Role-Based Access</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Stats -->
    <div class="bg-primary rounded-2xl p-12 text-center text-white shadow-xl shadow-primary/20">
      <h2 class="text-2xl font-bold mb-10">Trusted by Educational Institutions Worldwide</h2>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
        <div>
          <div class="text-4xl font-black mb-2">500+</div>
          <div class="text-primary-100 text-sm font-medium uppercase tracking-wider">Active Students</div>
        </div>
        <div>
          <div class="text-4xl font-black mb-2">50+</div>
          <div class="text-primary-100 text-sm font-medium uppercase tracking-wider">Expert Courses</div>
        </div>
        <div>
          <div class="text-4xl font-black mb-2">15+</div>
          <div class="text-primary-100 text-sm font-medium uppercase tracking-wider">Instructors</div>
        </div>
        <div>
          <div class="text-4xl font-black mb-2">99%</div>
          <div class="text-primary-100 text-sm font-medium uppercase tracking-wider">Satisfaction</div>
        </div>
      </div>
    </div>

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
