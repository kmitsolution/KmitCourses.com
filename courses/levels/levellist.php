<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['is_admin'])) {
  header('Location: ../users/login.php');
  exit;
}
require_once __DIR__ . '/../../config.php';
$pdo = getPDO();
$message = '';
// Handle add/update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $level_id = intval($_POST['level_id'] ?? 0);
  $level_name = trim($_POST['level_name'] ?? '');
  try {
    if ($action === 'add' && $level_name) {
      $stmt = $pdo->prepare('CALL add_level(?)');
      $stmt->execute([$level_name]);
      $stmt->closeCursor();
      $message = 'Level added.';
    } elseif ($action === 'update' && $level_id && $level_name) {
      $stmt = $pdo->prepare('CALL update_level(?, ?)');
      $stmt->execute([$level_id, $level_name]);
      $stmt->closeCursor();
      $message = 'Level updated.';
    } elseif ($action === 'delete' && $level_id) {
      $stmt = $pdo->prepare('CALL delete_level(?)');
      $stmt->execute([$level_id]);
      $stmt->closeCursor();
      $message = 'Level deleted.';
    }
  } catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
  }
}
// Fetch categories
$categories = [];
$allCategories = [];
try {
  $catStmt = $pdo->query('CALL list_levels()');
  $allCategories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
  $catStmt->closeCursor();
} catch (Exception $e) {
  $allCategories = [];
}

// Search/filter like userlist.php
$searchValue = isset($_POST['search_value']) ? trim($_POST['search_value']) : '';
$searchType = isset($_POST['search_type']) ? $_POST['search_type'] : '';
if ($searchType === 'Level' && $searchValue !== '') {
  $searchValueLower = mb_strtolower($searchValue);
  $categories = array_filter($allCategories, function($cat) use ($searchValueLower) {
    return mb_strpos(mb_strtolower($cat['level_name']), $searchValueLower) !== false;
  });
  $categories = array_values($categories); // reindex
} else {
  $categories = $allCategories;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Level List</title>
  <link rel="stylesheet" href="../../assets/style.css">
  <style>
    .Level-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
    .Level-table th, .Level-table td { border: 1px solid #e0e0e0; padding: 10px 8px; text-align: left; color: #fff; }
    .Level-table th { background: #f3f4f6; color: #081326; }
    .action-icon {
        transition: background 0.2s, color 0.2s;
        border-radius: 6px;
        padding: 4px 7px;
        display: inline-block;
        cursor: pointer;
    }
    .action-icon:hover {
        background: #e6f2ff;
        color: #0078d4 !important;
        text-decoration: none;
        cursor: pointer;
    }
    .delete-icon:hover {
        background: #ffeaea;
        color: #c00 !important;
    }
    .update-icon:hover {
        background: #e6f2ff;
        color: #0078d4 !important;
    }
    .form-section { background:#f8fafc; border-radius:12px; padding:24px; margin-bottom:32px; box-shadow:0 2px 12px #00214711; }
    .form-section h2 { margin-top:0; }
    .form-group { margin-bottom:16px; }
    .form-group label { font-weight:600; display:block; margin-bottom:6px; }
    .form-group input { width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; font-size:1rem; }
    .form-actions { display:flex; gap:12px; }
    .btn { padding:8px 14px; border-radius:7px; border:none; font-weight:600; cursor:pointer; }
    .btn.primary { background:#0078d4; color:#fff; }
    .btn.danger { background:#d32f2f; color:#fff; }
  </style>
</head>
<body>
<div class="container">
  <div style="margin-bottom:8px;">
    <h2 style="color:white;margin:0 0 18px 0;white-space:nowrap;font-size:1.5em;text-align:center;width:100%;">Level List</h2>
    <?php if ($message) echo '<div style="color:#0078d4;font-weight:600;margin-bottom:12px;">'.htmlspecialchars($message).'</div>'; ?>
    <form method="post" class="search-bar" id="LevelSearchForm" style="margin:0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:18px;">
      <label for="search_type" style="font-size:0.96em;">Search by:</label>
      <select name="search_type" id="search_type" required style="font-size:0.96em;min-width:80px;">
        <option value="Level" selected>Level</option>
      </select>
      <input type="text" name="search_value" id="search_value" value="<?= isset($_POST['search_value']) ? htmlspecialchars($_POST['search_value']) : '' ?>" required placeholder="Enter value..." style="font-size:0.96em;max-width:120px;">
      <button type="submit" style="font-size:0.96em;padding:5px 10px;cursor:pointer;display:none;">Search</button>
      <a href="#" id="showLevelForm" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#28a745;color:#fff;border:none;font-weight:600;font-size:0.96em;text-decoration:none;cursor:pointer;display:inline-block;">Add Level</a>
      <button type="button" onclick="window.location.reload();" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#0078d4;color:#fff;border:none;font-weight:600;font-size:0.96em;cursor:pointer;">Refresh</button>
    </form>
    <form method="post" class="form-section" id="LevelForm" style="max-width:400px;margin:auto;display:none;color:#081326;">
      <h3 style="color:#081326;">Add / Update Level</h3>
      <div class="form-group">
        <label for="level_name" style="color:#081326;">Level Name:</label>
        <input type="text" name="level_name" id="level_name" required style="color:#081326;">
        <input type="hidden" name="level_id" id="level_id">
      </div>
      <div class="form-actions" style="justify-content: flex-end;">
        <button type="submit" name="action" value="add" class="btn primary" id="addBtn">Add</button>
        <button type="submit" name="action" value="update" class="btn primary" id="updateBtn" style="display:none;background:#ffa500;color:#222;">Update</button>
      </div>
    </form>
    <table class="Level-table" style="table-layout:fixed;width:100%;margin-top:32px;">
      <thead>
        <tr>
          <th style="width:220px;">Name</th>
          <th style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td><?php echo htmlspecialchars($cat['level_name']); ?></td>
            <td style="width:120px;display:flex;gap:8px;align-items:center;">
              <a href="#" class="action-icon delete-icon" style="border:none;text-decoration:none;" onclick="if(confirm('Delete this Level?')) { document.getElementById('level_id').value=<?php echo $cat['level_id']; ?>; document.getElementById('level_name').value='<?php echo htmlspecialchars(addslashes($cat['level_name'])); ?>'; var form = document.getElementById('LevelForm'); form.style.display = 'block'; document.getElementById('addBtn').style.display = 'none'; document.getElementById('updateBtn').style.display = 'none'; var deleteBtn = document.createElement('button'); deleteBtn.type = 'submit'; deleteBtn.name = 'action'; deleteBtn.value = 'delete'; deleteBtn.style.display = 'none'; form.appendChild(deleteBtn); deleteBtn.click(); form.removeChild(deleteBtn); } return false;" title="Delete"><span>🗑️</span></a>
              <a href="#" class="action-icon update-icon" style="border:none;text-decoration:none;" onclick="fillForm(<?php echo $cat['level_id']; ?>, '<?php echo htmlspecialchars(addslashes($cat['level_name'])); ?>', true); return false;" title="Update"><span>✏️</span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
document.getElementById('showLevelForm').onclick = function(e) {
  e.preventDefault();
  var form = document.getElementById('LevelForm');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
  document.getElementById('addBtn').style.display = 'inline-block';
  document.getElementById('updateBtn').style.display = 'none';
  resetForm();
};
function fillForm(id, name, isUpdate) {
  document.getElementById('level_id').value = id;
  document.getElementById('level_name').value = name;
  var form = document.getElementById('LevelForm');
  form.style.display = 'block';
  if (isUpdate) {
    document.getElementById('addBtn').style.display = 'none';
    document.getElementById('updateBtn').style.display = 'inline-block';
  } else {
    document.getElementById('addBtn').style.display = 'inline-block';
    document.getElementById('updateBtn').style.display = 'none';
  }
}
function resetForm() {
  document.getElementById('level_id').value = '';
  document.getElementById('level_name').value = '';
}
// Instant search/filter like userlist.php
document.getElementById('search_value').addEventListener('input', function() {
  var filter = this.value.toLowerCase();
  var rows = document.querySelectorAll('.Level-table tbody tr');
  rows.forEach(function(row) {
    var nameCell = row.querySelector('td');
    if (nameCell) {
      var name = nameCell.textContent.toLowerCase();
      if (name.indexOf(filter) !== -1) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    }
  });
});
</script>
</body>
</html>
