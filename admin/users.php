<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$pageTitle = 'Manage Users';

$perPage = 50;
$page    = max(1,(int)($_GET['page'] ?? 1));
$offset  = ($page-1)*$perPage;
$search  = sanitize($_GET['search'] ?? '');
$roleFilter = sanitize($_GET['role'] ?? '');

// Build WHERE
$conditions = [];
$params = [];
if ($search) { $conditions[] = '(name LIKE :s OR email LIKE :s2)'; $params[':s']="%$search%"; $params[':s2']="%$search%"; }
if ($roleFilter) { $conditions[] = 'role = :role'; $params[':role']=$roleFilter; }
$where = $conditions ? 'WHERE '.implode(' AND ',$conditions) : '';

$total = $db->prepare("SELECT COUNT(*) FROM users $where");
$total->execute($params);
$total = $total->fetchColumn();
$totalPages = (int)ceil($total/$perPage);

$stmt = $db->prepare("SELECT * FROM users $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
foreach($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':limit',$perPage,PDO::PARAM_INT);
$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Summary stats
$totalUsers      = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeInstructors = $db->query("SELECT COUNT(*) FROM users WHERE role='instructor' AND status='active'")->fetchColumn();
$pendingStudents = $db->query("SELECT COUNT(*) FROM users WHERE role='student' AND status='inactive'")->fetchColumn();

$flash = getFlash();

$roleBadge = [
    'admin'      => 'bg-red-100 text-red-700',
    'instructor' => 'bg-primary/10 text-primary',
    'student'    => 'bg-slate-100 text-slate-500',
];

require_once '../includes/admin_sidebar.php';
?>

<style> html { scroll-behavior: smooth; } </style>
<div class="p-8">
  <?php if($flash): ?>
  <div class="mb-4 px-4 py-3 rounded-lg flex items-center gap-2 text-sm <?= $flash['type']==='success'?'bg-green-50 border border-green-200 text-green-700':'bg-red-50 border border-red-200 text-red-700' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success'?'check_circle':'error' ?></span>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 -mx-8 -mt-8 px-8 py-6 flex flex-wrap justify-between items-center gap-4 mb-8">
    <div>
      <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Manage Users</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">View and update system user permissions</p>
    </div>
    <div class="flex items-center gap-3">
      <button onclick="openImportModal()" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20 px-4 py-2.5 rounded-lg flex items-center gap-2 text-sm font-bold transition-all border border-indigo-100 dark:border-indigo-500/20">
        <span class="material-symbols-outlined text-[20px]">upload_file</span> Import CSV
      </button>
      <button onclick="openAddModal()" class="bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg flex items-center gap-2 text-sm font-bold transition-all shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-[20px]">add</span> Add New User
      </button>
    </div>
  </div>

  <!-- Stats Row — Compact Premium Design -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">group</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Users</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalUsers) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(16,185,129,0.08),0 8px 24px rgba(16,185,129,0.07)">
      <div class="size-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-emerald-600 text-[18px]">person_check</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Active Inst.</p>
        <p class="text-xl font-black text-emerald-600 leading-tight"><?= $activeInstructors ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(245,158,11,0.08),0 8px 24px rgba(245,158,11,0.07)">
      <div class="size-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-amber-600 text-[18px]">pending</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Pending Std.</p>
        <p class="text-xl font-black text-amber-600 leading-tight"><?= $pendingStudents ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-indigo-500 text-[18px]">verified_user</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">System Admins</p>
        <p class="text-xl font-black text-primary leading-tight">2</p>
      </div>
    </div>
  </div>

  <!-- Table Card -->
  <div id="usersTable" class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden" style="box-shadow:0 2px 8px rgba(30,59,138,0.07),0 8px 24px rgba(30,59,138,0.06)">
    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/30">
      <form method="GET" class="flex flex-wrap gap-2 items-center">
        <div class="relative w-64">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
          <input name="search" value="<?= htmlspecialchars($search) ?>" class="block w-full pl-9 pr-3 py-1.5 bg-white dark:bg-slate-800 rounded-lg text-xs border-none focus:ring-2 focus:ring-primary/20 transition-all font-medium" placeholder="Search name or email..." type="text"/>
        </div>
        <select name="role" onchange="this.form.submit()" class="px-2 py-1.5 bg-white dark:bg-slate-800 rounded-lg text-xs border-none focus:ring-2 focus:ring-primary/20 outline-none font-bold text-slate-500">
          <option value="">All Roles</option>
          <option value="admin" <?= $roleFilter==='admin'?'selected':'' ?>>Admin</option>
          <option value="instructor" <?= $roleFilter==='instructor'?'selected':'' ?>>Instructor</option>
          <option value="student" <?= $roleFilter==='student'?'selected':'' ?>>Student</option>
        </select>
        <button type="submit" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-primary hover:text-white rounded-lg text-xs font-black transition-all">FILTER</button>
        <?php if($search||$roleFilter): ?><a href="/CMS/admin/users.php" class="px-2 py-1.5 text-[10px] font-black text-slate-400 hover:text-primary uppercase">Clear</a><?php endif; ?>
      </form>
      <!-- Bulk Actions Container -->
      <div id="bulkActions" class="hidden flex items-center gap-3">
        <span class="text-sm font-semibold text-slate-600 dark:text-slate-300"><span id="selectedCount">0</span> selected</span>
        <button onclick="confirmBulkDelete()" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-500/20 flex items-center gap-1.5 flex-shrink-0">
          <span class="material-symbols-outlined text-[16px]">delete</span> Delete Selected
        </button>
      </div>
    </div>
      <table class="w-full text-left">
        <thead class="bg-primary/[0.03] dark:bg-slate-800/50">
          <tr>
            <th class="px-6 py-4 w-12 text-center">
              <input type="checkbox" id="selectAll" class="rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary w-4 h-4 cursor-pointer align-middle">
            </th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-primary/5 dark:divide-slate-800">
          <?php if (empty($users)): ?>
          <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">No users found.</td></tr>
          <?php else: ?>
          <?php foreach ($users as $u):
            $initials = strtoupper(implode('',array_map(fn($w)=>$w[0],explode(' ',$u['name']))));
            $badgeCls = $roleBadge[$u['role']] ?? 'bg-slate-100 text-slate-500';
          ?>
          <tr id="user-<?= $u['id'] ?>" class="hover:bg-primary/[0.01] transition-colors">
            <td class="px-6 py-4 text-center">
              <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
              <input type="checkbox" value="<?= $u['id'] ?>" class="user-checkbox rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary w-4 h-4 cursor-pointer align-middle">
              <?php else: ?>
              <span class="inline-block w-4 h-4" title="Cannot select yourself"></span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                  <?= htmlspecialchars(substr($initials,0,2)) ?>
                </div>
                <span class="text-sm font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($u['name']) ?></span>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($u['email']) ?></td>
            <td class="px-6 py-4">
              <span class="px-2.5 py-1 text-[11px] font-bold rounded-full <?= $badgeCls ?> uppercase"><?= htmlspecialchars($u['role']) ?></span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <div class="size-2 rounded-full <?= $u['status']==='active'?'bg-emerald-500':'bg-slate-300' ?>"></div>
                <span class="text-xs font-medium text-slate-700 dark:text-slate-300"><?= ucfirst($u['status']) ?></span>
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2">
                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($u)) ?>)" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-lg transition-all">
                  <span class="material-symbols-outlined text-[18px]">edit</span>
                </button>
                <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                <button onclick="confirmDelete(<?= $u['id'] ?>,'<?= addslashes($u['name']) ?>')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
    <?php if($totalPages>1): ?>
    <div class="px-6 py-4 flex items-center justify-between border-t border-primary/5 dark:border-slate-800">
      <p class="text-xs text-slate-500">Showing <b><?= $offset+1 ?></b> to <b><?= min($offset+$perPage,$total) ?></b> of <b><?= $total ?></b> users</p>
      <div class="flex items-center gap-1">
        <?php if($page>1): ?><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>" class="size-8 flex items-center justify-center rounded-lg border border-primary/10 text-slate-400 hover:text-primary"><span class="material-symbols-outlined text-[18px]">chevron_left</span></a><?php endif; ?>
        <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>" class="size-8 flex items-center justify-center rounded-lg text-xs font-bold <?= $i===$page?'bg-primary text-white':'hover:bg-primary/5 text-slate-600' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if($page<$totalPages): ?><a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>" class="size-8 flex items-center justify-center rounded-lg border border-primary/10 text-slate-400 hover:text-primary"><span class="material-symbols-outlined text-[18px]">chevron_right</span></a><?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>



<!-- Add/Edit User Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
      <h3 id="userModalTitle" class="text-lg font-bold">Add New User</h3>
      <button onclick="closeUserModal()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>
    <form method="POST" action="/CMS/api/user_action.php">
      <input type="hidden" name="user_id" id="u_id" value="">
      <input type="hidden" name="page" value="<?= $page ?>">
      <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
      <input type="hidden" name="role_filter" value="<?= htmlspecialchars($roleFilter) ?>">
      <div class="p-6 space-y-4">
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-1">Full Name *</label>
          <input name="name" id="u_name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm"/>
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-1">Email Address *</label>
          <input name="email" id="u_email" type="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm"/>
        </div>
        <div id="passwordField">
          <label class="text-sm font-semibold text-slate-700 block mb-1">Password <span id="passwordHint" class="text-slate-400 font-normal text-xs">(leave blank to keep existing)</span></label>
          <input name="password" id="u_password" type="password" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm" placeholder="••••••••"/>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-slate-700 block mb-1">Role</label>
            <select name="role" id="u_role" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm">
              <option value="student">Student</option>
              <option value="instructor">Instructor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-semibold text-slate-700 block mb-1">Status</label>
            <select name="status" id="u_status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="p-6 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
        <button type="button" onclick="closeUserModal()" class="px-5 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50">Cancel</button>
        <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 shadow-sm">Save User</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center">
    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <span class="material-symbols-outlined text-red-500 text-3xl">person_remove</span>
    </div>
    <h3 class="text-lg font-bold mb-2" id="deleteModalTitle">Delete User?</h3>
    <p class="text-slate-500 text-sm mb-6" id="deleteMessage">This action cannot be undone.</p>
    <form method="POST" action="/CMS/api/user_action.php" id="deleteForm">
      <input type="hidden" name="delete_id" id="deleteId">
      <input type="hidden" name="bulk_delete_ids" id="bulkDeleteIds">
      <input type="hidden" name="page" value="<?= $page ?>">
      <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
      <input type="hidden" name="role_filter" value="<?= htmlspecialchars($roleFilter) ?>">
      <div class="flex justify-center gap-3">
        <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="px-5 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50">Cancel</button>
        <button type="submit" class="px-5 py-2.5 bg-red-500 text-white rounded-lg text-sm font-bold hover:bg-red-600">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- Import CSV Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
      <h3 class="text-lg font-bold">Import Users via CSV</h3>
      <button onclick="closeImportModal()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>
    <form method="POST" action="/CMS/api/import_users.php" enctype="multipart/form-data">
      <div class="p-6 space-y-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 p-4 rounded-lg text-xs leading-relaxed">
          <p class="font-bold mb-1">Expected CSV Format (with header row):</p>
          <code class="block bg-white dark:bg-slate-900 px-2 py-1 rounded border border-blue-100 dark:border-blue-800 mt-2">name, email, password, role, status</code>
          <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Roles: <span class="font-semibold">admin, instructor, student</span></li>
            <li>Status: <span class="font-semibold">active, inactive</span></li>
            <li>Existing emails will be skipped.</li>
          </ul>
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-2">Select CSV File *</label>
          <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all cursor-pointer border border-slate-200 dark:border-slate-700 rounded-lg"/>
        </div>
      </div>
      <div class="p-6 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
        <button type="button" onclick="closeImportModal()" class="px-5 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50">Cancel</button>
        <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 shadow-sm flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">upload</span> Upload & Import
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal(){
  document.getElementById('userModalTitle').textContent='Add New User';
  document.getElementById('u_id').value='';
  ['u_name','u_email','u_password'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('u_role').value='student';
  document.getElementById('u_status').value='active';
  document.getElementById('passwordHint').style.display='none';
  document.getElementById('userModal').classList.remove('hidden');
}
function openEditModal(u){
  document.getElementById('userModalTitle').textContent='Edit User';
  document.getElementById('u_id').value=u.id;
  document.getElementById('u_name').value=u.name;
  document.getElementById('u_email').value=u.email;
  document.getElementById('u_password').value='';
  document.getElementById('u_role').value=u.role;
  document.getElementById('u_status').value=u.status;
  document.getElementById('passwordHint').style.display='';
  document.getElementById('userModal').classList.remove('hidden');
}
function closeUserModal(){document.getElementById('userModal').classList.add('hidden');}
function openImportModal(){document.getElementById('importModal').classList.remove('hidden');}
function closeImportModal(){document.getElementById('importModal').classList.add('hidden');}
function confirmDelete(id,name){
  document.getElementById('deleteModalTitle').textContent='Delete User?';
  document.getElementById('deleteId').value=id;
  document.getElementById('bulkDeleteIds').value='';
  document.getElementById('deleteMessage').textContent='Delete user "'+name+'"? All their data will be removed.';
  document.getElementById('deleteModal').classList.remove('hidden');
}
function confirmBulkDelete(){
  const checked = document.querySelectorAll('.user-checkbox:checked');
  if (checked.length === 0) return;
  const ids = Array.from(checked).map(cb => cb.value).join(',');
  document.getElementById('deleteModalTitle').textContent='Delete Multiple Users?';
  document.getElementById('deleteId').value='';
  document.getElementById('bulkDeleteIds').value=ids;
  document.getElementById('deleteMessage').textContent='Delete ' + checked.length + ' selected users? All their data will be removed.';
  document.getElementById('deleteModal').classList.remove('hidden');
}

// Bulk Selection Logic
document.addEventListener('DOMContentLoaded', function() {
  const selectAll = document.getElementById('selectAll');
  const userCheckboxes = document.querySelectorAll('.user-checkbox');
  const bulkActions = document.getElementById('bulkActions');
  const selectedCount = document.getElementById('selectedCount');

  function updateBulkActions() {
    const checked = document.querySelectorAll('.user-checkbox:checked');
    if (checked.length > 0) {
      bulkActions.classList.remove('hidden');
      selectedCount.textContent = checked.length;
    } else {
      bulkActions.classList.add('hidden');
      selectAll.checked = false;
    }
    
    if (checked.length === userCheckboxes.length && userCheckboxes.length > 0) {
      selectAll.checked = true;
    } else {
      selectAll.checked = false;
    }
  }

  if (selectAll) {
    selectAll.addEventListener('change', function() {
      userCheckboxes.forEach(cb => {
        cb.checked = selectAll.checked;
      });
      updateBulkActions();
    });
  }

  userCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
  });
});

document.getElementById('userModal').addEventListener('click',function(e){if(e.target===this)closeUserModal();});
document.getElementById('importModal').addEventListener('click',function(e){if(e.target===this)closeImportModal();});
document.getElementById('deleteModal').addEventListener('click',function(e){if(e.target===this)this.classList.add('hidden');});
</script>

<?php require_once '../includes/admin_footer.php'; ?>
