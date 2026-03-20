<?php
require_once '../config/db.php';
require_once '../config/session.php';
requireRole('admin');

$db = getDB();
$pageTitle = 'Manage Courses';

// Pagination
$perPage = 50;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$search  = sanitize($_GET['search'] ?? '');

// Count
$whereClause = $search ? 'WHERE c.title LIKE :s OR u.name LIKE :s2' : '';
$countSql    = "SELECT COUNT(*) FROM courses c LEFT JOIN users u ON c.instructor_id=u.id $whereClause";
$countStmt   = $db->prepare($countSql);
if ($search) { $countStmt->execute([':s'=>"%$search%",':s2'=>"%$search%"]); }
else         { $countStmt->execute(); }
$total     = $countStmt->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// Fetch courses
$sql  = "SELECT c.*, u.name AS instructor_name
         FROM courses c
         LEFT JOIN users u ON c.instructor_id = u.id
         $whereClause
         ORDER BY c.id DESC
         LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
if ($search) { $stmt->bindValue(':s', "%$search%"); $stmt->bindValue(':s2', "%$search%"); }
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll();

// Instructors for dropdown
$instructors = $db->query("SELECT id, name FROM users WHERE role='instructor' AND status='active' ORDER BY name ASC")->fetchAll();

// Stats
$totalActive = $db->query("SELECT COUNT(*) FROM courses WHERE status='active'")->fetchColumn();
$totalDraft  = $db->query("SELECT COUNT(*) FROM courses WHERE status='draft'")->fetchColumn();
$totalAll    = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalEnrolled = $db->query("SELECT COUNT(DISTINCT student_id) FROM enrollments")->fetchColumn();

$flash = getFlash();

// Academic year label colors
$yearColors = [
    'First Year'  => 'bg-blue-100 text-blue-700',
    'Second Year' => 'bg-emerald-100 text-emerald-700',
    'Third Year'  => 'bg-purple-100 text-purple-700',
    'Fourth Year' => 'bg-orange-100 text-orange-700',
];

require_once '../includes/admin_sidebar.php';
?>

<style> html { scroll-behavior: smooth; } </style>
<div class="p-8">
  <!-- Flash -->
  <?php if ($flash): ?>
  <div class="mb-4 px-4 py-3 rounded-lg flex items-center gap-2 text-sm <?= $flash['type']==='success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' ?>">
    <span class="material-symbols-outlined text-lg"><?= $flash['type']==='success' ? 'check_circle' : 'error' ?></span>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Courses Library</h2>
    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">View and manage all educational content available in the system.</p>
  </div>

  <!-- Stats row — Compact Premium Design -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-blue-500 text-[18px]">menu_book</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Courses</p>
        <p class="text-xl font-black text-slate-900 dark:text-white leading-tight"><?= number_format($totalAll) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(16,185,129,0.08),0 8px 24px rgba(16,185,129,0.07)">
      <div class="size-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-green-500 text-[18px]">check_circle</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Active</p>
        <p class="text-xl font-black text-green-600 leading-tight"><?= number_format($totalActive) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(100,116,139,0.08),0 8px 24px rgba(100,116,139,0.07)">
      <div class="size-9 rounded-lg bg-slate-50 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-slate-400 text-[18px]">edit_note</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Drafts</p>
        <p class="text-xl font-black text-slate-500 leading-tight"><?= number_format($totalDraft) ?></p>
      </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 flex items-center gap-3" style="box-shadow:0 2px 8px rgba(30,59,138,0.08),0 8px 24px rgba(30,59,138,0.07)">
      <div class="size-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-indigo-500 text-[18px]">school</span>
      </div>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider truncate">Total Students</p>
        <p class="text-xl font-black text-primary leading-tight"><?= number_format($totalEnrolled) ?></p>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div id="coursesTable" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
      <form method="GET" class="flex items-center gap-2">
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
          <input name="search" value="<?= htmlspecialchars($search) ?>" class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64" placeholder="Search courses or instructors..." type="text"/>
        </div>
        <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-sm font-medium">Search</button>
        <?php if($search): ?><a href="/CMS/admin/courses.php" class="px-3 py-2 text-sm text-slate-500 hover:text-primary">Clear</a><?php endif; ?>
      </form>
      <div class="flex items-center gap-3">
        <a href="/CMS/api/randomize_instructors.php" onclick="return confirm('This will randomly assign all courses to active instructors. Continue?')" class="bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all border border-amber-100 dark:border-amber-500/20 shadow-sm">
          <span class="material-symbols-outlined text-lg">shuffle</span> Randomize All
        </a>
        <button onclick="openImportModal()" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all border border-indigo-100 dark:border-indigo-500/20 shadow-sm">
          <span class="material-symbols-outlined text-lg">upload_file</span> Import CSV
        </button>
        <button onclick="openAddModal()" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-all shadow-sm">
          <span class="material-symbols-outlined text-lg">add</span> Add New Course
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Course Title</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Academic Year</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Instructor</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
          <?php if (empty($courses)): ?>
          <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">No courses found. <button onclick="openAddModal()" class="text-primary underline ml-1">Add your first course.</button></td></tr>
          <?php else: ?>
          <?php foreach ($courses as $c):
            $yearCls = $yearColors[$c['academic_year']] ?? 'bg-slate-100 text-slate-600';
          ?>
          <tr id="course-<?= $c['id'] ?>" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
            <td class="px-6 py-4 text-sm font-medium text-slate-500">#CS-<?= str_pad($c['id'],3,'0',STR_PAD_LEFT) ?></td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="size-10 rounded bg-primary/10 flex-shrink-0 flex items-center justify-center overflow-hidden">
                  <?php if (!empty($c['image'])): ?>
                  <img src="/CMS/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="w-full h-full object-cover">
                  <?php else: ?>
                  <span class="material-symbols-outlined text-primary">menu_book</span>
                  <?php endif; ?>
                </div>
                <div>
                  <span class="text-sm font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($c['title']) ?></span>
                  <?php if ($c['category']): ?>
                  <p class="text-xs text-slate-400"><?= htmlspecialchars($c['category']) ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2.5 py-1 text-xs font-medium <?= $yearCls ?> rounded-full"><?= htmlspecialchars($c['academic_year']) ?></span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
              <?php if ($c['instructor_id']): ?>
              <div class="flex items-center gap-2">
                <div class="size-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500">
                  <?= strtoupper(substr($c['instructor_name'], 0, 1)) ?>
                </div>
                <span><?= htmlspecialchars($c['instructor_name']) ?></span>
              </div>
              <?php else: ?>
              <span class="flex items-center gap-1.5 text-rose-500 font-bold bg-rose-50 dark:bg-rose-500/10 px-2 py-1 rounded w-fit">
                <span class="material-symbols-outlined text-[16px]">warning</span> Unassigned
              </span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <?php if ($c['status']==='active'): ?>
              <div class="flex items-center gap-1.5 text-green-600">
                <span class="size-2 bg-green-500 rounded-full"></span><span class="text-xs font-bold">Active</span>
              </div>
              <?php else: ?>
              <div class="flex items-center gap-1.5 text-slate-400">
                <span class="size-2 bg-slate-300 rounded-full"></span><span class="text-xs font-bold">Draft</span>
              </div>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2">
                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($c)) ?>)" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-slate-400 hover:text-primary transition-colors">
                  <span class="material-symbols-outlined text-lg">edit</span>
                </button>
                <button onclick="confirmDelete('/CMS/api/course_action.php',<?= $c['id'] ?>,'<?= addslashes($c['title']) ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-400 hover:text-red-500 transition-colors">
                  <span class="material-symbols-outlined text-lg">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
      <p class="text-sm text-slate-500">Showing <?= $offset+1 ?>–<?= min($offset+$perPage,$total) ?> of <?= $total ?> results</p>
      <div class="flex items-center gap-2">
        <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="size-9 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-white text-slate-500">
          <span class="material-symbols-outlined text-xl">chevron_left</span>
        </a>
        <?php endif; ?>
        <?php for ($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="size-9 flex items-center justify-center rounded-lg text-sm font-bold <?= $i===$page ? 'bg-primary text-white' : 'border border-transparent hover:border-slate-200 text-slate-600' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="size-9 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-white text-slate-500">
          <span class="material-symbols-outlined text-xl">chevron_right</span>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Add/Edit Modal -->
<div id="courseModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0">
      <h3 id="modalTitle" class="text-lg font-bold">Add New Course</h3>
      <button onclick="closeModal()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>
    <form method="POST" action="/CMS/api/course_action.php" enctype="multipart/form-data" class="flex flex-col min-h-0">
      <input type="hidden" name="course_id" id="course_id" value="">
      <input type="hidden" name="page" value="<?= $page ?>">
      <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
      <div class="p-6 space-y-4 overflow-y-auto flex-1">
        <div>
          <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Course Title *</label>
          <input name="title" id="m_title" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm"/>
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Course Image</label>
          <div class="flex items-start gap-4">
            <div class="relative group">
            <div id="imagePreviewContainer" class="hidden size-20 rounded-lg bg-slate-100 dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-600 overflow-hidden flex-shrink-0 flex items-center justify-center">
              <img id="imagePreview" src="" class="w-full h-full object-cover">
              <button type="button" onclick="removeImagePreview()" class="absolute -top-2 -right-2 size-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors z-10">
                <span class="material-symbols-outlined text-sm">close</span>
              </button>
            </div>
          </div>
            <div class="flex-1">
              <input type="file" name="image" id="m_image" accept="image/*" onchange="previewImage(this)" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-colors"/>
            </div>
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Description</label>
          <textarea name="description" id="m_desc" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm resize-none"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Category</label>
            <input name="category" id="m_category" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm" placeholder="e.g. Programming"/>
          </div>
          <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Academic Year</label>
            <select name="academic_year" id="m_year" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm">
              <option>First Year</option>
              <option>Second Year</option>
              <option>Third Year</option>
              <option>Fourth Year</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Instructor</label>
            <select name="instructor_id" id="m_instructor" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm">
              <option value="">— None —</option>
              <?php foreach($instructors as $i): ?>
              <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 block mb-1">Status</label>
            <select name="status" id="m_status" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-sm">
              <option value="active">Active</option>
              <option value="draft">Draft</option>
            </select>
          </div>
        </div>
      </div>
      <div class="p-6 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3 flex-shrink-0">
        <button type="button" onclick="closeModal()" class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Cancel</button>
        <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary/90 shadow-sm transition-all">Save Course</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center">
    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <span class="material-symbols-outlined text-red-500 text-3xl">delete_forever</span>
    </div>
    <h3 class="text-lg font-bold mb-2">Delete Course?</h3>
    <p class="text-slate-500 text-sm mb-6" id="deleteMessage">This action cannot be undone.</p>
    <form method="POST" id="deleteForm" action="/CMS/api/course_action.php">
      <input type="hidden" name="delete_id" id="deleteId">
      <input type="hidden" name="page" value="<?= $page ?>">
      <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
      <div class="flex justify-center gap-3">
        <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="px-5 py-2.5 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50">Cancel</button>
        <button type="submit" class="px-5 py-2.5 bg-red-500 text-white rounded-lg text-sm font-bold hover:bg-red-600 transition-colors">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- Import CSV Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
      <h3 class="text-lg font-bold">Import Courses via CSV</h3>
      <button onclick="closeImportModal()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"><span class="material-symbols-outlined">close</span></button>
    </div>
    <form method="POST" action="/CMS/api/import_courses.php" enctype="multipart/form-data">
      <div class="p-6 space-y-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 p-4 rounded-lg text-xs leading-relaxed">
          <p class="font-bold mb-1">Expected CSV Format (with header row):</p>
          <code class="block bg-white dark:bg-slate-900 px-2 py-1 rounded border border-blue-100 dark:border-blue-800 mt-2">title, description, category, academic_year, instructor_id, status</code>
          <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Academic Year: <span class="font-semibold">First Year, Second Year, Third Year, Fourth Year</span></li>
            <li>Status: <span class="font-semibold">active, draft</span></li>
            <li>Instructor ID should be a valid user ID (optional).</li>
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
function removeImagePreview() {
  const input = document.getElementById('m_image');
  const previewContainer = document.getElementById('imagePreviewContainer');
  const previewImage = document.getElementById('imagePreview');
  input.value = '';
  previewImage.src = '';
  previewContainer.classList.add('hidden');
}
function previewImage(input) {
  const previewContainer = document.getElementById('imagePreviewContainer');
  const previewImage = document.getElementById('imagePreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImage.src = e.target.result;
      previewContainer.classList.remove('hidden');
    }
    reader.readAsDataURL(input.files[0]);
  } else {
    removeImagePreview();
  }
}
function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Add New Course';
  document.getElementById('course_id').value = '';
  document.getElementById('m_title').value = '';
  document.getElementById('m_desc').value = '';
  document.getElementById('m_category').value = '';
  document.getElementById('m_year').value = 'First Year';
  document.getElementById('m_instructor').value = '';
  document.getElementById('m_status').value = 'active';
  document.getElementById('m_image').value = '';
  document.getElementById('imagePreviewContainer').classList.add('hidden');
  document.getElementById('imagePreview').src = '';
  document.getElementById('courseModal').classList.remove('hidden');
}
function openEditModal(course) {
  document.getElementById('modalTitle').textContent = 'Edit Course';
  document.getElementById('course_id').value = course.id;
  document.getElementById('m_title').value = course.title;
  document.getElementById('m_desc').value = course.description ?? '';
  document.getElementById('m_category').value = course.category ?? '';
  document.getElementById('m_year').value = course.academic_year;
  document.getElementById('m_instructor').value = course.instructor_id ?? '';
  document.getElementById('m_status').value = course.status;
  document.getElementById('m_image').value = '';
  if (course.image) {
    document.getElementById('imagePreview').src = '/CMS/' + course.image;
    document.getElementById('imagePreviewContainer').classList.remove('hidden');
  } else {
    document.getElementById('imagePreviewContainer').classList.add('hidden');
    document.getElementById('imagePreview').src = '';
  }
  document.getElementById('courseModal').classList.remove('hidden');
}
function closeModal() {
  document.getElementById('courseModal').classList.add('hidden');
}
function confirmDelete(action, id, name) {
  document.getElementById('deleteId').value = id;
  document.getElementById('deleteMessage').textContent = 'Delete "' + name + '"? This cannot be undone.';
  document.getElementById('deleteModal').classList.remove('hidden');
}
function openImportModal() {
  document.getElementById('importModal').classList.remove('hidden');
}
function closeImportModal() {
  document.getElementById('importModal').classList.add('hidden');
}
// Close modals on backdrop click
document.getElementById('courseModal').addEventListener('click', function(e) { if (e.target===this) closeModal(); });
document.getElementById('importModal').addEventListener('click', function(e) { if (e.target===this) closeImportModal(); });
document.getElementById('deleteModal').addEventListener('click', function(e) { if (e.target===this) document.getElementById('deleteModal').classList.add('hidden'); });
</script>

<?php require_once '../includes/admin_footer.php'; ?>
