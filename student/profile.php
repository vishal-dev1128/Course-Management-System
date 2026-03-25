<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('student');

$pageTitle = 'My Profile';
$flash = getFlash();
?>
<?php require_once '../includes/student_sidebar.php'; ?>

<!-- Header -->
<header class="sticky top-0 z-10 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between">
  <div>
    <h2 class="text-xl font-bold text-slate-900 dark:text-white">My Profile</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400">Manage your account settings</p>
  </div>
  <div class="flex items-center gap-4">
    <div class="flex items-center gap-3 pl-6 border-l border-slate-200 dark:border-slate-800">
      <div class="text-right hidden sm:block">
        <p class="text-sm font-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
        <p class="text-xs text-slate-500">Student</p>
      </div>
      <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-sm">
        <?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?>
      </div>
    </div>
  </div>
</header>

<div class="p-8 max-w-2xl mx-auto space-y-6">

  <!-- Flash message -->
  <div id="flash-container">
    <?php if ($flash): ?>
    <div class="px-4 py-3 rounded-xl flex items-center gap-3 text-sm border
      <?= $flash['type'] === 'success'
          ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700 dark:text-emerald-300'
          : 'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-700 dark:text-red-300' ?>">
      <span class="material-symbols-outlined text-lg">
        <?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?>
      </span>
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Profile Info Card -->
  <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-100 dark:border-slate-800"
       style="box-shadow:0 4px 24px -6px rgba(30,59,138,0.08)">
    <div class="flex items-center gap-5 mb-6">
      <div class="size-16 rounded-2xl bg-gradient-to-br from-primary/30 to-blue-500/30 flex items-center justify-center text-primary font-black text-2xl border border-primary/20">
        <?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?>
      </div>
      <div>
        <h3 class="font-bold text-xl text-slate-900 dark:text-white"><?= htmlspecialchars($_SESSION['user_name']) ?></h3>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
        <span class="inline-block mt-1 text-[10px] font-black text-primary bg-primary/10 px-2.5 py-0.5 rounded-full uppercase tracking-widest">Student</span>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
      <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Name</p>
        <p class="text-sm font-semibold mt-0.5 text-slate-700 dark:text-slate-200"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
      </div>
      <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</p>
        <p class="text-sm font-semibold mt-0.5 text-slate-700 dark:text-slate-200 truncate"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
      </div>
    </div>
  </div>

  <!-- Change Password Card -->
  <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-100 dark:border-slate-800"
       style="box-shadow:0 4px 24px -6px rgba(30,59,138,0.08)">
    <div class="flex items-center gap-3 mb-6">
      <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">
        <span class="material-symbols-outlined text-primary text-[20px]">lock_reset</span>
      </div>
      <div>
        <h3 class="font-bold text-slate-900 dark:text-white">Change Password</h3>
        <p class="text-xs text-slate-500">Keep your account secure with a strong password</p>
      </div>
    </div>

    <!-- Alert box (shown via JS) -->
    <div id="pw-alert" class="hidden mb-4 px-4 py-3 rounded-xl flex items-center gap-3 text-sm border"></div>

    <form id="change-password-form" class="space-y-4" novalidate>
      <!-- Current Password -->
      <div class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Current Password</label>
        <div class="relative">
          <input id="current_password" name="current_password" type="password" required
                 placeholder="Enter current password"
                 class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-xl px-4 py-3 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-900 dark:text-white placeholder-slate-400">
          <button type="button" onclick="togglePw('current_password', this)"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">visibility</span>
          </button>
        </div>
      </div>

      <!-- New Password -->
      <div class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">New Password</label>
        <div class="relative">
          <input id="new_password" name="new_password" type="password" required
                 placeholder="Min. 8 characters"
                 class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-xl px-4 py-3 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-900 dark:text-white placeholder-slate-400"
                 oninput="checkStrength(this.value)">
          <button type="button" onclick="togglePw('new_password', this)"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">visibility</span>
          </button>
        </div>
        <!-- Strength Meter -->
        <div class="flex gap-1 mt-1.5">
          <div id="s1" class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors"></div>
          <div id="s2" class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors"></div>
          <div id="s3" class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors"></div>
          <div id="s4" class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors"></div>
        </div>
        <p id="strength-label" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"></p>
      </div>

      <!-- Confirm Password -->
      <div class="space-y-1.5">
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Confirm New Password</label>
        <div class="relative">
          <input id="confirm_password" name="confirm_password" type="password" required
                 placeholder="Repeat new password"
                 class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-xl px-4 py-3 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-slate-900 dark:text-white placeholder-slate-400">
          <button type="button" onclick="togglePw('confirm_password', this)"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">visibility</span>
          </button>
        </div>
      </div>

      <!-- Requirements -->
      <ul class="space-y-1 text-xs text-slate-400">
        <li class="flex items-center gap-2" id="req-len">
          <span class="material-symbols-outlined text-[14px]">circle</span> At least 8 characters
        </li>
        <li class="flex items-center gap-2" id="req-matches">
          <span class="material-symbols-outlined text-[14px]">circle</span> Passwords match
        </li>
      </ul>

      <button type="submit" id="submit-btn"
              class="w-full py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/20 mt-2">
        <span class="material-symbols-outlined text-[18px]">lock</span>
        Update Password
      </button>
    </form>
  </div>
</div>

</main>
</div>

<script>
function togglePw(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector('.material-symbols-outlined');
  if (input.type === 'password') {
    input.type = 'text';
    icon.textContent = 'visibility_off';
  } else {
    input.type = 'password';
    icon.textContent = 'visibility';
  }
}

function checkStrength(val) {
  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const bars = ['s1','s2','s3','s4'];
  const colors = ['bg-red-400','bg-amber-400','bg-yellow-400','bg-emerald-500'];
  const labels = ['','Weak','Fair','Good','Strong'];

  bars.forEach((id, i) => {
    const el = document.getElementById(id);
    el.className = 'h-1 flex-1 rounded-full transition-colors ' +
      (i < score ? colors[score - 1] : 'bg-slate-200 dark:bg-slate-700');
  });

  document.getElementById('strength-label').textContent = val.length ? labels[score] : '';

  // Update requirements
  updateReq('req-len', val.length >= 8);
  updateMatch();
}

function updateReq(id, ok) {
  const el = document.getElementById(id);
  const icon = el.querySelector('.material-symbols-outlined');
  icon.textContent = ok ? 'check_circle' : 'circle';
  el.className = 'flex items-center gap-2 text-xs ' + (ok ? 'text-emerald-500' : 'text-slate-400');
}

function updateMatch() {
  const np = document.getElementById('new_password').value;
  const cp = document.getElementById('confirm_password').value;
  updateReq('req-matches', np && cp && np === cp);
}

document.getElementById('confirm_password').addEventListener('input', updateMatch);

document.getElementById('change-password-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('submit-btn');
  const currentPw = document.getElementById('current_password').value;
  const newPw = document.getElementById('new_password').value;
  const confirmPw = document.getElementById('confirm_password').value;

  if (newPw.length < 8) { showAlert('error', 'Password must be at least 8 characters.'); return; }
  if (newPw !== confirmPw) { showAlert('error', 'Passwords do not match.'); return; }

  btn.disabled = true;
  btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">autorenew</span> Updating...';

  const formData = new FormData();
  formData.append('current_password', currentPw);
  formData.append('new_password', newPw);
  formData.append('confirm_password', confirmPw);

  try {
    const res = await fetch('/CMS/api/update_password.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      showAlert('success', data.message);
      this.reset();
      // Reset strength meter
      ['s1','s2','s3','s4'].forEach(id => {
        document.getElementById(id).className = 'h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors';
      });
      document.getElementById('strength-label').textContent = '';
      updateReq('req-len', false);
      updateReq('req-matches', false);
    } else {
      showAlert('error', data.message);
    }
  } catch {
    showAlert('error', 'Something went wrong. Please try again.');
  }

  btn.disabled = false;
  btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">lock</span> Update Password';
});

function showAlert(type, msg) {
  const el = document.getElementById('pw-alert');
  const isSuccess = type === 'success';
  el.className = 'mb-4 px-4 py-3 rounded-xl flex items-center gap-3 text-sm border ' +
    (isSuccess
      ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-700 dark:text-emerald-300'
      : 'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-700 dark:text-red-300');
  el.innerHTML = `<span class="material-symbols-outlined text-lg">${isSuccess ? 'check_circle' : 'error'}</span>${msg}`;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  setTimeout(() => { el.className += ' hidden'; }, 5000);
}
</script>
</body>
</html>
