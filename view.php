<?php
include 'config.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;
$countSql = "SELECT COUNT(*) AS total FROM entries 
             WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
$countResult = $conn->query($countSql);
$total = $countResult->fetch_assoc()['total'];
$pages = ceil($total / $limit);
$sql = "SELECT * FROM entries 
        WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'
        ORDER BY id DESC LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Entries</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    th { cursor: pointer; }
    tr:hover { background-color: #f2f2f2 !important; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Library System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="view.php">View Entries</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
        </ul>
      </div>
    </div>
  <h2 class="mb-4">All Entries</h2>
  <form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control"
           placeholder="Search by Name or Email"
           value="<?php echo htmlspecialchars($search); ?>">
  </form>

  <table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?=htmlspecialchars($row['created_at'])?></td>
            <td>
                <a href="update.php? id=<?=$row['id']?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this record?');">
                   Delete
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">No records found</td>
    </tr>
<?php endif; ?>
</tbody>
  </table>

  <nav>
    <ul class="pagination">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">Previous</a>
        </li>
      <?php endif; ?>

      <?php for($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?php if($i == $page) echo 'active'; ?>">
          <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $pages): ?>
        <li class="page-item">
          <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>

  <div class="container-fluid py-4">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-12">

        <h2 class="mb-4">Entries List</h2>
        <form method="get" class="mb-3 row g-2">
          <div class="col-md-6">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name, email, or phone">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
          </div>
        </form>
        <div class="table-responsive">
          <table id="entriesTable" class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th onclick="sortTable(1)">Name ⬍</th>
                <th onclick="sortTable(2)">Email ⬍</th>
                <th onclick="sortTable(3)">Phone ⬍</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['phone']) ?></td>
                  <td><?= $row['created_at'] ?></td>
                  <td>
                    <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">
                      Delete
                    </button>
                    <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to delete <b><?= htmlspecialchars($row['name']) ?></b>?
                          </div>
                          <div class="modal-footer">
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger">Yes, Delete</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          </div>
                        </div>
                      </div>
                    </div>

                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center">No records found</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
        <nav>
          <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
              <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <?php if ($page < $pages): ?>
              <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a></li>
            <?php endif; ?>
          </ul>
        </nav>

      </div>
    </div>
  </div>
  <script>
  function sortTable(n) {
    let table, rows, switching, i, x, y, shouldSwitch;
    table = document.getElementById("entriesTable");
    switching = true;
    while (switching) {
      switching = false;
      rows = table.rows;
      for (i = 1; i < (rows.length - 1); i++) {
        shouldSwitch = false;
        x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
      if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
      }
    }
  }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
