<?php
require_once '../config/db.php';
require_once '../config/session.php';

if (isLoggedIn()) {
    redirectByRole($_SESSION['user_role']);
}

$error = '';
$activeTab = $_GET['tab'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND status = "active" LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_role']  = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                redirectByRole($user['role']);
            } else {
                $error = 'Invalid email or password.';
            }
        }
        $activeTab = 'login';

    } elseif ($action === 'register') {
        $name     = sanitize($_POST['name'] ?? '');
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered. Please login.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt   = $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)');
                $stmt->execute([$name, $email, $hashed, 'student']);

                $userStmt = $db->prepare('SELECT * FROM users WHERE email = ?');
                $userStmt->execute([$email]);
                $newUser = $userStmt->fetch();

                $_SESSION['user_id']    = $newUser['id'];
                $_SESSION['user_name']  = $newUser['name'];
                $_SESSION['user_role']  = $newUser['role'];
                $_SESSION['user_email'] = $newUser['email'];
                redirectByRole($newUser['role']);
            }
        }
        if ($error) $activeTab = 'register';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>EduManage | Welcome</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {"primary":"#1e3b8a","background-light":"#f6f6f8"},
        fontFamily: {"display":["Inter","sans-serif"]},
      }
    }
  }
</script>
<style>
  body { font-family:'Inter',sans-serif; overflow: hidden; }
  .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
  .form-container { display: none; opacity: 0; transition: opacity 0.3s ease; }
  .form-container.active { display: flex; opacity: 1; }

  .custom-scrollbar::-webkit-scrollbar { width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
</style>
</head>
<body class="bg-white font-display text-slate-900 h-screen w-screen overflow-hidden flex flex-col md:flex-row">

  <div class="hidden md:flex flex-col justify-center items-center w-1/2 h-full bg-[#111827] text-white p-12 relative z-10">
    <a href="/CMS/index.php" class="absolute top-8 left-8 hover:opacity-80 transition-opacity">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-white text-3xl">school</span>
        <span class="font-bold text-xl tracking-wide">EduManage</span>
      </div>
    </a>

    <div class="max-w-md text-center">
      <h1 class="text-5xl lg:text-6xl font-black mb-4 tracking-tight leading-tight">Course <br/> Management <br/> System</h1>
      <p class="text-slate-400 text-sm tracking-widest uppercase font-semibold mb-8">Empower Your Learning</p>
      <p class="text-slate-500 text-base leading-relaxed">
        Join our exclusive educational community. Experience learning redefined with comprehensive tools, expert instructors, and a seamless academic journey.
      </p>
    </div>
  </div>

  <div class="w-full md:w-1/2 h-full bg-white flex flex-col relative overflow-y-auto custom-scrollbar">

    <div class="md:hidden flex items-center justify-between p-6 border-b border-gray-100">
      <a href="/CMS/index.php" class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-2xl">school</span>
        <span class="font-bold text-lg text-slate-900">EduManage</span>
      </a>
    </div>

    <div class="absolute top-4 left-0 right-0 px-8 z-50 flex flex-col gap-2" id="alert-container">
      <?php if ($error): ?>
      <div class="alert-message bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded flex items-center gap-2 text-sm shadow-sm">
        <span class="material-symbols-outlined text-lg">error</span> <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>
      <?php if (isset($success) && $success): ?>
      <div class="alert-message bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded flex items-center gap-2 text-sm shadow-sm">
        <span class="material-symbols-outlined text-lg">check_circle</span> <?= htmlspecialchars($success) ?>
      </div>
      <?php endif; ?>
    </div>

    <script>
      setTimeout(function() {
        document.querySelectorAll('.alert-message').forEach(function(el) {
          el.style.transition = 'opacity 0.5s ease';
          el.style.opacity = '0';
          setTimeout(function() { el.remove(); }, 500);
        });
      }, 5000);
    </script>

    <div class="flex-1 flex flex-col items-center justify-center p-4 sm:p-8 lg:px-20 lg:py-2">
      <div class="w-full max-w-md">

        <div id="login-container" class="form-container flex-col <?= $activeTab === 'login' ? 'active' : '' ?>">
          <div class="mb-10 text-center sm:text-left">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Welcome Back</h2>
            <p class="text-slate-500 text-sm">Don't have an account? <button type="button" onclick="switchForm('register')" class="text-slate-900 font-bold underline hover:text-primary transition-colors">Create one</button></p>
          </div>

          <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="login">

            <div class="space-y-1">
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Email address</label>
              <input name="email" type="email" required class="w-full border-0 border-b-2 border-slate-200 px-0 py-3 text-slate-900 focus:ring-0 focus:border-primary transition-colors bg-transparent" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="space-y-1">
              <div class="flex justify-between items-center">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Password</label>
                <a href="#" class="text-xs font-medium text-slate-400 hover:text-primary transition-colors">Forgot?</a>
              </div>
              <input name="password" type="password" required class="w-full border-0 border-b-2 border-slate-200 px-0 py-3 text-slate-900 focus:ring-0 focus:border-primary transition-colors bg-transparent" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-black text-white font-bold rounded tracking-wide transition-all mt-8">
              SIGN IN
            </button>
          </form>

          <div class="mt-6 pt-4 border-t border-slate-100">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Demo Credentials</p>
            <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
              <div class="p-2 bg-slate-50 rounded border border-slate-100 hover:border-slate-300 transition-colors cursor-pointer" onclick="fillDemo('admin@cms.com', 'admin_pass_2026')">
                <span class="font-bold block text-slate-700">Admin</span>admin@cms.com
              </div>
              <div class="p-2 bg-slate-50 rounded border border-slate-100 hover:border-slate-300 transition-colors cursor-pointer" onclick="fillDemo('vikram@cms.com', 'instructor123')">
                <span class="font-bold block text-slate-700">Instructor 1</span>vikram@cms.com
              </div>
              <div class="p-2 bg-slate-50 rounded border border-slate-100 hover:border-slate-300 transition-colors cursor-pointer" onclick="fillDemo('michael@cms.com', 'instructor123')">
                <span class="font-bold block text-slate-700">Instructor 2</span>michael@cms.com
              </div>
              <div class="p-2 bg-slate-50 rounded border border-slate-100 hover:border-slate-300 transition-colors cursor-pointer" onclick="fillDemo('anita@cms.com', 'instructor123')">
                <span class="font-bold block text-slate-700">Instructor 3</span>anita@cms.com
              </div>
              <div class="p-2 bg-slate-50 rounded border border-slate-100 hover:border-slate-300 transition-colors cursor-pointer" onclick="fillDemo('alice@cms.com', 'student123')">
                <span class="font-bold block text-slate-700">Student</span>alice@cms.com
              </div>
            </div>
          </div>
        </div>

        <div id="register-container" class="form-container flex-col <?= $activeTab === 'register' ? 'active' : '' ?>">
          <div class="mb-6 text-center sm:text-left">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Create Account</h2>
            <p class="text-slate-500 text-sm">Already have an account? <button type="button" onclick="switchForm('login')" class="text-slate-900 font-bold underline hover:text-primary transition-colors">Sign in</button></p>
          </div>

          <form method="POST" class="space-y-5">
            <input type="hidden" name="action" value="register">

            <div class="space-y-1">
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Full Name</label>
              <input name="name" type="text" required class="w-full border-0 border-b-2 border-slate-200 px-0 py-3 text-slate-900 focus:ring-0 focus:border-primary transition-colors bg-transparent" placeholder="John Doe" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Email address</label>
              <input name="email" type="email" required class="w-full border-0 border-b-2 border-slate-200 px-0 py-3 text-slate-900 focus:ring-0 focus:border-primary transition-colors bg-transparent" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Password</label>
              <input name="password" type="password" required class="w-full border-0 border-b-2 border-slate-200 px-0 py-3 text-slate-900 focus:ring-0 focus:border-primary transition-colors bg-transparent" placeholder="••••••••">
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Confirm Password</label>
              <input name="confirm_password" type="password" required class="w-full border-0 border-b-2 border-slate-200 px-0 py-3 text-slate-900 focus:ring-0 focus:border-primary transition-colors bg-transparent" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded tracking-wide transition-all mt-6">
              CREATE ACCOUNT
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script>
    function switchForm(form) {
      document.getElementById('login-container').classList.remove('active');
      document.getElementById('register-container').classList.remove('active');

      if (form === 'login') {
        document.getElementById('login-container').classList.add('active');
      } else {
        document.getElementById('register-container').classList.add('active');
      }
    }

    function fillDemo(email, password) {
      document.querySelector('input[name="email"]').value = email;
      document.querySelector('input[name="password"]').value = password;
    }
  </script>
</body>
</html>