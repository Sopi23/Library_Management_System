<?php
include 'config.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1; 
$start = ($page - 1) * $limit;
$countSql = "SELECT COUNT(*) AS total FROM entries 
             WHERE name LIKE '%$search%' 
             OR email LIKE '%$search%'";
$countResult = $conn->query($countSql);
$total = 0;
$pages = 1;

if ($countResult && $countResult->num_rows > 0) {
    $total = $countResult->fetch_assoc()['total'];
    $pages = ($total > 0) ? ceil($total / $limit) : 1;
}
$sql = "SELECT * FROM entries 
        WHERE name LIKE '%$search%' 
        OR email LIKE '%$search%' 
        ORDER BY id DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Entries - Library Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
    body {
      background-color: #F6E1D3;
    }
    h2 {
      color: #2c3e50; 
    }
  </style>
</head>
<body>
<div class="container mt-5">

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
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result && $result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['name']}</td>
                      <td>{$row['email']}</td>
                      <td>{$row['phone']}</td>
                      <td>{$row['created_at']}</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='5' class='text-center'>No entries found</td></tr>";
      }
      ?>
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

</div>
</body>
</html>
