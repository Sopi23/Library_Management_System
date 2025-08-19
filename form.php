<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

     if (empty($name) || empty($email) || empty($phone)) {
        die("⚠️ All fields are required!");
        }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format!");
    }
    if (!is_numeric($phone) || strlen($phone) != 10) {
        die("📞 Phone must be 10 digits!");
    }
     $sql_check = "SELECT * FROM entries WHERE email='$email'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        die("⚠️ Email already exists!");
    }

    $sql = "INSERT INTO entries (name, email, phone) VALUES ('$name','$email','$phone')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>✅ Entry added successfully!</p>";
    } else {
        echo "❌ Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
