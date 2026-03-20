<?php
$pageTitle = 'Help Center';
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

<!-- Main Content -->
<main class="flex-1 w-full max-w-[1280px] mx-auto px-6 lg:px-20 py-16">
  
  <div class="text-center mb-16">
    <h1 class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-4">Help Center</h1>
    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Find answers to common questions and discover how to get the most out of EduManage.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- FAQ Section -->
    <div class="lg:col-span-2">
      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
        <h3 class="text-2xl font-bold mb-6 text-slate-900 dark:text-white">Frequently Asked Questions</h3>
        <div class="divide-y divide-slate-200 dark:divide-slate-800">
          <details class="group py-4" open>
            <summary class="flex items-center justify-between cursor-pointer font-semibold text-slate-800 dark:text-slate-200 list-none">
              <span>How do I reset my account password?</span>
              <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <p class="mt-3 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                You can reset your password by clicking on the "Forgot Password" link on the login page. An email with instructions will be sent to your registered address within minutes.
            </p>
          </details>
          <details class="group py-4">
            <summary class="flex items-center justify-between cursor-pointer font-semibold text-slate-800 dark:text-slate-200 list-none">
              <span>Where can I find my enrolled courses?</span>
              <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <p class="mt-3 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                Once logged in as a student, navigate to your Student Dashboard. You will see a dedicated section for "In Progress Courses" containing all the courses you are currently enrolled in.
            </p>
          </details>
          <details class="group py-4">
            <summary class="flex items-center justify-between cursor-pointer font-semibold text-slate-800 dark:text-slate-200 list-none">
              <span>How do I switch between dark mode and light mode?</span>
              <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <p class="mt-3 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                There is a sun/moon icon toggle located in the main navigation bar on public pages, and at the bottom of the sidebar within the user portals. Your preference is automatically saved.
            </p>
          </details>
          <details class="group py-4">
            <summary class="flex items-center justify-between cursor-pointer font-semibold text-slate-800 dark:text-slate-200 list-none">
              <span>How can I contact my instructor?</span>
              <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <p class="mt-3 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                Instructors can be reached through the dedicated course announcements and discussion forums. Alternatively, you can use the messaging feature within the specific course module view.
            </p>
          </details>
        </div>
      </div>
    </div>
    
    <!-- Sidebar Support Cards -->
    <div class="space-y-6">
      <!-- Documentation Card -->
      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg w-fit mb-4">
          <span class="material-symbols-outlined text-3xl">menu_book</span>
        </div>
        <h4 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Documentation</h4>
        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">Explore our comprehensive guides and tutorials for students and instructors.</p>
        <a class="flex items-center gap-2 text-primary font-bold group" href="#">
            Browse Knowledge Base
            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
      </div>

      <!-- Need More Help Card -->
      <div class="bg-primary/5 dark:bg-primary/10 rounded-xl p-6 border border-primary/10">
        <h5 class="font-bold text-primary dark:text-blue-400 text-lg">Still need help?</h5>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 mb-4">If you couldn't find the answer to your question in our FAQ, please don't hesitate to reach out to our dedicated support team directly.</p>
        <a href="/CMS/contact.php" class="inline-flex w-full items-center justify-center bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-md">
           Contact Us
        </a>
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
