<?php
include 'config.php';

$error = "";
$success = "";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $sql = "SELECT * FROM entries WHERE id=$id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        die("Record not found.");
    }
    
    $row = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $name  = trim($_POST['name']); 
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    if (empty($name) || empty($email) || empty($phone)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be exactly 10 digits!";
    } else {
     
        $stmt = $conn->prepare("UPDATE entries SET name=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
        
        if ($stmt->execute()) {
            header("Location: view.php");
            exit();
        } else {
            $error = "Error updating record: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Record</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Update Record</h2>

    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" class="form-control" required pattern="[0-9]{10}" title="Enter exactly 10 digits">
        </div>
        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="view.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("name").addEventListener("input", function() {
let name = this.value;
if(name.length < 3){
this.style.borderColor = "red";
} else {
this.style.borderColor = "green";
}
});
</script>
</body>
</html>