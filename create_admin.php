<?php
include 'db.php';

// Palitan mo ito ng gusto mong username at password
$username = "admin"; 
$password = "admin123"; 

// Hashing the password (Security)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if username exists
$check = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<h1>Admin account already exists!</h1>";
} else {
    // Insert new admin
    $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo "<h1>Success! Admin Account Created.</h1>";
        echo "<p>Username: $username</p>";
        echo "<p>Password: $password</p>";
        echo "<br><a href='login.php'>Go to Login Page</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>