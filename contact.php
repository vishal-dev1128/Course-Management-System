<?php
$pageTitle = 'Contact Us';
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
    <h1 class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-4">How can we help?</h1>
    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">We're here to help and answer any questions you might have. We look forward to hearing from you.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Contact Form Section -->
    <div class="lg:col-span-2 space-y-8">
      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
        <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">mail</span> Send us a message
        </h3>
        <form action="#" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
              <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Full Name</label>
              <input class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary px-4 py-3" placeholder="John Doe" type="text" required/>
            </div>
            <div class="flex flex-col gap-2">
              <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email Address</label>
              <input class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary px-4 py-3" placeholder="john@university.edu" type="email" required/>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
              <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Category</label>
              <select class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary px-4 py-3">
                <option>Technical Issue</option>
                <option>Billing &amp; Payments</option>
                <option>Course Inquiry</option>
                <option>Account Settings</option>
                <option>Other</option>
              </select>
            </div>
            <div class="flex flex-col gap-2">
              <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Subject</label>
              <input class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary px-4 py-3" placeholder="Summary of your issue" type="text" required/>
            </div>
          </div>
          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Message</label>
            <textarea class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary px-4 py-3 resize-none" placeholder="Please describe your issue in detail..." rows="5" required></textarea>
          </div>
          <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 px-6 rounded-lg transition-all shadow-md flex items-center justify-center gap-2" type="button" onclick="alert('Message sent successfully! Our team will get back to you shortly.');">
            <span class="material-symbols-outlined">send</span> Submit Request
          </button>
        </form>
      </div>
    </div>
    
    <!-- Sidebar Contact Cards -->
    <div class="space-y-6">
      <!-- Live Chat Card -->
      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-start justify-between mb-4">
          <div class="p-3 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg">
            <span class="material-symbols-outlined text-3xl">chat</span>
          </div>
          <div class="flex items-center gap-1.5 px-2.5 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Online
          </div>
        </div>
        <h4 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Live Chat</h4>
        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">Chat with our support agents for immediate assistance with technical issues.</p>
        <button class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all font-bold py-3 rounded-lg" onclick="alert('Live chat is currently connecting...');">
          Start Chatting
        </button>
      </div>
      
      <!-- Email Support Card -->
      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-primary dark:text-blue-400 rounded-lg w-fit mb-4">
          <span class="material-symbols-outlined text-3xl">alternate_email</span>
        </div>
        <h4 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Email Support</h4>
        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">Prefer email? Send us your queries and we'll respond within 24 business hours.</p>
        <a class="text-primary font-bold hover:underline" href="mailto:support@edumanage.com">support@edumanage.com</a>
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
