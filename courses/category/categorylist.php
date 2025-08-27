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
  $categoryid = intval($_POST['categoryid'] ?? 0);
  $categoryname = trim($_POST['categoryname'] ?? '');
  try {
    if ($action === 'add' && $categoryname) {
      $stmt = $pdo->prepare('CALL AddCategory(?)');
      $stmt->execute([$categoryname]);
      $stmt->closeCursor();
      $message = 'Category added.';
    } elseif ($action === 'update' && $categoryid && $categoryname) {
      $stmt = $pdo->prepare('CALL UpdateCategory(?, ?)');
      $stmt->execute([$categoryid, $categoryname]);
      $stmt->closeCursor();
      $message = 'Category updated.';
    } elseif ($action === 'delete' && $categoryid) {
      $stmt = $pdo->prepare('CALL DeleteCategory(?)');
      $stmt->execute([$categoryid]);
      $stmt->closeCursor();
      $message = 'Category deleted.';
    }
  } catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
  }
}
// Fetch categories
$categories = [];
$allCategories = [];
try {
  $catStmt = $pdo->query('CALL ListAllCategories()');
  $allCategories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
  $catStmt->closeCursor();
} catch (Exception $e) {
  $allCategories = [];
}

// Search/filter like userlist.php
$searchValue = isset($_POST['search_value']) ? trim($_POST['search_value']) : '';
$searchType = isset($_POST['search_type']) ? $_POST['search_type'] : '';
if ($searchType === 'category' && $searchValue !== '') {
  $searchValueLower = mb_strtolower($searchValue);
  $categories = array_filter($allCategories, function($cat) use ($searchValueLower) {
    return mb_strpos(mb_strtolower($cat['categoryname']), $searchValueLower) !== false;
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
  <title>Category List</title>
  <link rel="stylesheet" href="../../assets/style.css">
  <style>
    .category-table { width: 100%; border-collapse: collapse; margin-top: 24px; }
    .category-table th, .category-table td { border: 1px solid #e0e0e0; padding: 10px 8px; text-align: left; color: #fff; }
    .category-table th { background: #f3f4f6; color: #081326; }
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
    <h2 style="color:white;margin:0 0 18px 0;white-space:nowrap;font-size:1.5em;text-align:center;width:100%;">Category List</h2>
    <?php if ($message) echo '<div style="color:#0078d4;font-weight:600;margin-bottom:12px;">'.htmlspecialchars($message).'</div>'; ?>
    <form method="post" class="search-bar" id="categorySearchForm" style="margin:0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:18px;">
      <label for="search_type" style="font-size:0.96em;">Search by:</label>
      <select name="search_type" id="search_type" required style="font-size:0.96em;min-width:80px;">
        <option value="category" selected>Category</option>
      </select>
      <input type="text" name="search_value" id="search_value" value="<?= isset($_POST['search_value']) ? htmlspecialchars($_POST['search_value']) : '' ?>" required placeholder="Enter value..." style="font-size:0.96em;max-width:120px;">
      <button type="submit" style="font-size:0.96em;padding:5px 10px;cursor:pointer;display:none;">Search</button>
      <a href="#" id="showCategoryForm" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#28a745;color:#fff;border:none;font-weight:600;font-size:0.96em;text-decoration:none;cursor:pointer;display:inline-block;">Add Category</a>
      <button type="button" onclick="window.location.reload();" style="margin-left:5px;padding:5px 10px;border-radius:6px;background:#0078d4;color:#fff;border:none;font-weight:600;font-size:0.96em;cursor:pointer;">Refresh</button>
    </form>
    <form method="post" class="form-section" id="categoryForm" style="max-width:400px;margin:auto;display:none;color:#081326;">
      <h3 style="color:#081326;">Add / Update Category</h3>
      <div class="form-group">
        <label for="categoryname" style="color:#081326;">Category Name:</label>
        <input type="text" name="categoryname" id="categoryname" required style="color:#081326;">
        <input type="hidden" name="categoryid" id="categoryid">
      </div>
      <div class="form-actions" style="justify-content: flex-end;">
        <button type="submit" name="action" value="add" class="btn primary" id="addBtn">Add</button>
        <button type="submit" name="action" value="update" class="btn primary" id="updateBtn" style="display:none;background:#ffa500;color:#222;">Update</button>
      </div>
    </form>
    <table class="category-table" style="table-layout:fixed;width:100%;margin-top:32px;">
      <thead>
        <tr>
          <th style="width:220px;">Name</th>
          <th style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td><?php echo htmlspecialchars($cat['categoryname']); ?></td>
            <td style="width:120px;display:flex;gap:8px;align-items:center;">
              <a href="#" class="action-icon delete-icon" style="border:none;text-decoration:none;" onclick="if(confirm('Delete this category?')) { document.getElementById('categoryid').value=<?php echo $cat['categoryid']; ?>; document.getElementById('categoryname').value='<?php echo htmlspecialchars(addslashes($cat['categoryname'])); ?>'; var form = document.getElementById('categoryForm'); form.style.display = 'block'; document.getElementById('addBtn').style.display = 'none'; document.getElementById('updateBtn').style.display = 'none'; var deleteBtn = document.createElement('button'); deleteBtn.type = 'submit'; deleteBtn.name = 'action'; deleteBtn.value = 'delete'; deleteBtn.style.display = 'none'; form.appendChild(deleteBtn); deleteBtn.click(); form.removeChild(deleteBtn); } return false;" title="Delete"><span>🗑️</span></a>
              <a href="#" class="action-icon update-icon" style="border:none;text-decoration:none;" onclick="fillForm(<?php echo $cat['categoryid']; ?>, '<?php echo htmlspecialchars(addslashes($cat['categoryname'])); ?>', true); return false;" title="Update"><span>✏️</span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
document.getElementById('showCategoryForm').onclick = function(e) {
  e.preventDefault();
  var form = document.getElementById('categoryForm');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
  document.getElementById('addBtn').style.display = 'inline-block';
  document.getElementById('updateBtn').style.display = 'none';
  resetForm();
};
function fillForm(id, name, isUpdate) {
  document.getElementById('categoryid').value = id;
  document.getElementById('categoryname').value = name;
  var form = document.getElementById('categoryForm');
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
  document.getElementById('categoryid').value = '';
  document.getElementById('categoryname').value = '';
}
// Instant search/filter like userlist.php
document.getElementById('search_value').addEventListener('input', function() {
  var filter = this.value.toLowerCase();
  var rows = document.querySelectorAll('.category-table tbody tr');
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
