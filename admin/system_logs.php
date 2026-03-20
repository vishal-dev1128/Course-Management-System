<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$pageTitle = 'System Logs';

// Fetch combined activity
$query = "
    (SELECT 'user' as type, name as title, 'New user registered' as detail, created_at as date FROM users)
    UNION
    (SELECT 'course' as type, title as title, 'New course created' as detail, created_at as date FROM courses)
    UNION
    (SELECT 'enrollment' as type, (SELECT name FROM users WHERE id=student_id) as title, 
            CONCAT('Enrolled in: ', (SELECT title FROM courses WHERE id=course_id)) as detail, 
            enrolled_at as date FROM enrollments)
    ORDER BY date DESC
    LIMIT 50
";

$logs = $db->query($query)->fetchAll();

require_once '../includes/admin_sidebar.php';
?>

<div class="p-8">
  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">System Logs</h2>
    <p class="text-slate-500 dark:text-slate-400">Monitoring platform activity and history.</p>
  </div>

  <!-- Timeline Card -->
  <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-sm" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
    <div class="p-5 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between bg-slate-50/30 dark:bg-slate-800/20">
      <h3 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight">Activity Timeline</h3>
      <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Showing Last 50 Events</span>
    </div>

    <div class="p-6">
      <?php if (empty($logs)): ?>
      <div class="text-center py-12">
        <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">history</span>
        <p class="text-slate-400 text-sm italic">No system activity recorded yet.</p>
      </div>
      <?php else: ?>
      <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-100 dark:before:via-slate-800 before:to-transparent">
        <?php foreach ($logs as $log): 
          $icon = 'info';
          $color = 'text-blue-500 bg-blue-50';
          if ($log['type'] === 'user') { $icon = 'person_add'; $color = 'text-emerald-500 bg-emerald-50'; }
          if ($log['type'] === 'course') { $icon = 'library_add'; $color = 'text-purple-500 bg-purple-50'; }
          if ($log['type'] === 'enrollment') { $icon = 'how_to_reg'; $color = 'text-amber-500 bg-amber-50'; }
        ?>
        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
          <!-- Icon -->
          <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white dark:border-slate-900 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 <?= $color ?> z-10">
            <span class="material-symbols-outlined text-lg"><?= $icon ?></span>
          </div>
          <!-- Content -->
          <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded border border-slate-50 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between space-x-2 mb-1">
              <div class="font-black text-xs text-slate-900 dark:text-white truncate uppercase tracking-widest"><?= htmlspecialchars($log['title']) ?></div>
              <time class="font-medium text-[10px] text-slate-400 whitespace-nowrap"><?= date('M d, H:i', strtotime($log['date'])) ?></time>
            </div>
            <div class="text-[11px] text-slate-500"><?= htmlspecialchars($log['detail']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
