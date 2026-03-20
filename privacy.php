<?php
$pageTitle = 'Privacy Policy';
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
<main class="flex-1 w-full max-w-[800px] mx-auto px-6 lg:px-20 py-16">
  
  <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 lg:p-12 prose dark:prose-invert max-w-none">
    <h1 class="text-3xl lg:text-4xl font-black text-slate-900 dark:text-white mb-2">Privacy Policy</h1>
    <p class="text-sm text-slate-500 mb-8">Last Updated: October 2023</p>

    <div class="space-y-6 text-slate-700 dark:text-slate-300">
        <p>
            At EduManage, we take your privacy extremely seriously. This Privacy Policy details how we collect, use, and protect your personal information when you use our Course Management System designed for students, instructors, and administrators.
        </p>

        <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-8 mb-4">1. Information We Collect</h3>
        <p>
            We collect information you provide directly to us when you create an account, enroll in courses, communicate with instructors, or otherwise interact with the platform. This includes:
        </p>
        <ul class="list-disc pl-6 space-y-2 mt-2">
            <li><strong>Account Data:</strong> Name, email address, and encrypted passwords.</li>
            <li><strong>Educational Data:</strong> Course enrollments, grades, learning progress, and completion certificates.</li>
            <li><strong>Communication Data:</strong> Messages sent over internal forums and contact support inquiries.</li>
        </ul>

        <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-8 mb-4">2. How We Use Your Information</h3>
        <p>
            The collected data is exclusively used to provide and enhance your educational experience. Specifically, we use it to:
        </p>
        <ul class="list-disc pl-6 space-y-2 mt-2">
            <li>Provide access to purchased and free course materials.</li>
            <li>Track and record academic progression directly to your transcript.</li>
            <li>Communicate critical system updates and responses to support inquiries.</li>
            <li>Ensure the safety and security of our educational platform against unauthorized access.</li>
        </ul>

        <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-8 mb-4">3. Data Security</h3>
        <p>
            We employ industry-standard encryption practices to secure your data both in transit and at rest. User passwords are automatically hashed, meaning even our database administrators cannot reverse-engineer your chosen credentials.
        </p>

        <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-8 mb-4">4. Your Rights</h3>
        <p>
            As a user, you have the absolute right to view the personal data we hold about you and request complete deletion of your account footprint from our active systems. To invoke these rights, please contact us via the <a href="/CMS/contact.php" class="text-primary hover:underline">Contact Us</a> page.
        </p>
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
