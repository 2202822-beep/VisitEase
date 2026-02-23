<?php
session_start();
include 'db.php'; // Importante ito para maka-connect sa DB

// Kung naka-login na, derecho sa admin page
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 1. Hanapin ang username sa database
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // 2. I-verify ang password (dahil naka-encrypt ito sa DB)
        if (password_verify($password, $row['password'])) {
            // Password Correct!
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            
            header("Location: admin.php");
            exit();
        } else {
            // Password Wrong
            $error = "Invalid Password!";
        }
    } else {
        // Username not found
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | VisitEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Raleway', sans-serif; }
        .login-card { width: 100%; max-width: 400px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px; border-radius: 10px; background: white; }
        .btn-login { background: #c5a059; color: white; width: 100%; padding: 10px; font-weight: bold; border: none; }
        .btn-login:hover { background: #b08d4b; }
        .brand-title { text-align: center; font-weight: 800; letter-spacing: 2px; margin-bottom: 30px; color: #2c3e50; }
    </style>
</head>
<body>

    <div class="login-card">
        <h3 class="brand-title">VISITEASE ADMIN</h3>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center p-2 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login">LOGIN</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="text-muted small text-decoration-none">Back to Website</a>
        </div>
    </div>

</body>
</html>