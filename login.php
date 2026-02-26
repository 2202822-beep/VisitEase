<?php
session_start();
include 'db.php'; // Importante ito para maka-connect sa DB

// Kung naka-login na, derecho sa admin page
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ── LOGIN LOGIC ──
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $login_id = trim($_POST['login_id']); // Pwedeng username o email
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $login_id, $login_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                
                header("Location: admin.php");
                exit();
            } else {
                $error = "Invalid Password!";
            }
        } else {
            $error = "User or email not found!";
        }
    }
    
    // ── REGISTER LOGIC ──
    elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
        $new_username = trim($_POST['reg_username']);
        $new_email = trim($_POST['reg_email']);
        $new_password = $_POST['reg_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
        } else {
            // I-check kung ginagamit na ang email o username
            $check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
            $check_stmt->bind_param("ss", $new_username, $new_email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Username or Email already exists!";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $insert_stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $new_username, $new_email, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $success = "Account successfully created! You can now sign in.";
                } else {
                    $error = "Error creating account. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase | Account Access</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #f7f2ea;
            --dark-brown: #1e1a14;
            --gold: #b8842a;
            --terracotta: #c4704a;
            --muted-brown: #7c5c2e;
            
            --font-display: 'Cormorant Garamond', serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(30, 26, 20, 0.85), rgba(30, 26, 20, 0.85)), 
                        url('https://images.unsplash.com/photo-1541123356219-284ebe98ae3b?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            overflow: hidden;
            margin: 0;
        }

        /* PINALAKI YUNG MAX-WIDTH DITO */
        .auth-wrapper { width: 100%; max-width: 550px; padding: 20px; }
        
        /* DINAGDAGAN ANG PADDING PARA BALANSE SA LAKI */
        .auth-card {
            background-color: var(--bg-cream);
            border: 1px solid var(--gold);
            padding: 50px; 
            border-radius: 3px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            transition: all 0.4s ease;
        }
        
        .auth-card::before {
            content: ''; position: absolute; top: 6px; left: 6px; right: 6px; bottom: 6px;
            border: 1px solid rgba(184, 132, 42, 0.3); pointer-events: none;
        }
        
        .brand-title { font-family: var(--font-display); font-size: 2.5rem; color: var(--dark-brown); font-weight: 700; text-align: center; margin-bottom: 5px; }
        .auth-subtitle { text-align: center; color: var(--muted-brown); font-size: 1rem; margin-bottom: 30px; }
        
        .form-label { font-weight: 600; font-size: 0.85rem; color: var(--dark-brown); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        
        /* PINALAKI ANG INPUT FIELDS KONTING-KONTI */
        .form-control { background-color: rgba(255,255,255,0.6); border: 1px solid rgba(124, 92, 46, 0.3); border-radius: 3px; padding: 12px 15px; color: var(--dark-brown); font-size: 1rem; }
        .form-control:focus { background-color: #fff; box-shadow: 0 0 0 0.2rem rgba(184, 132, 42, 0.15); border-color: var(--gold); }
        
        .pass-toggle { cursor: pointer; color: var(--muted-brown); transition: color 0.3s; }
        .pass-toggle:hover { color: var(--gold); }
        
        .btn-custom { border-radius: 3px; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.9rem; font-weight: 600; padding: 14px 28px; width: 100%; transition: all 0.3s ease; }
        .btn-gold { background-color: var(--gold); color: #fff; border: 1px solid var(--gold); }
        .btn-gold:hover { background-color: transparent; color: var(--gold); }
        
        .text-accent { color: var(--gold); text-decoration: none; font-weight: 600; }
        .text-accent:hover { color: var(--dark-brown); text-decoration: underline; }
        
        #registerFormContainer { display: none; }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            
            <h3 class="brand-title" id="formTitle">Welcome Back</h3>
            <p class="auth-subtitle" id="formSubtitle">Sign in to your VisitEase account</p>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger text-center p-2 small border-0 rounded-0" style="background-color: rgba(196, 112, 74, 0.1); color: var(--terracotta);">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($success)): ?>
                <div class="alert alert-success text-center p-2 small border-0 rounded-0" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-circle me-1"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div id="loginFormContainer">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="mb-4 position-relative z-2">
                        <label class="form-label">Username or Email</label>
                        <input type="text" name="login_id" class="form-control" placeholder="Enter your username or email" required autofocus>
                    </div>

                    <div class="mb-4 position-relative z-2">
                        <div class="d-flex justify-content-between">
                            <label class="form-label">Password</label>
                            <a href="#" class="text-accent" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Forgot Password?</a>
                        </div>
                        <div class="position-relative">
                            <input type="password" id="loginPassword" name="password" class="form-control pe-5" placeholder="Enter your password" required>
                            <i class="fas fa-eye pass-toggle position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePass('loginPassword', this)"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom btn-gold mt-3 position-relative z-2">Sign In</button>

                    <div class="text-center mt-4 position-relative z-2" style="font-size: 0.9rem; color: var(--muted-brown);">
                        Don't have an account? <a href="javascript:void(0);" onclick="showRegister()" class="text-accent">Create Account</a>
                    </div>
                </form>
            </div>

            <div id="registerFormContainer">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="register">

                    <div class="mb-3 position-relative z-2">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="reg_email" class="form-control" placeholder="Enter your email" required>
                    </div>

                    <div class="mb-3 position-relative z-2">
                        <label class="form-label">Username</label>
                        <input type="text" name="reg_username" class="form-control" placeholder="Choose a username" required>
                    </div>

                    <div class="mb-3 position-relative z-2">
                        <label class="form-label">Create Password</label>
                        <div class="position-relative">
                            <input type="password" id="regPassword" name="reg_password" class="form-control pe-5" placeholder="Create a password" required>
                            <i class="fas fa-eye pass-toggle position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePass('regPassword', this)"></i>
                        </div>
                    </div>

                    <div class="mb-4 position-relative z-2">
                        <label class="form-label">Re-enter Password</label>
                        <div class="position-relative">
                            <input type="password" id="regConfirmPassword" name="confirm_password" class="form-control pe-5" placeholder="Confirm your password" required>
                            <i class="fas fa-eye pass-toggle position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePass('regConfirmPassword', this)"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom btn-gold mt-2 position-relative z-2">Sign Up</button>

                    <div class="text-center mt-4 position-relative z-2" style="font-size: 0.9rem; color: var(--muted-brown);">
                        Already have an account? <a href="javascript:void(0);" onclick="showLogin()" class="text-accent">Sign In</a>
                    </div>
                </form>
            </div>

        </div>
        
        <div class="text-center mt-4 position-relative z-2">
            <p class="text-white-50 small" style="letter-spacing: 1px; font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> VisitEase Museum</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
        function showRegister() {
            document.getElementById('loginFormContainer').style.display = 'none';
            document.getElementById('registerFormContainer').style.display = 'block';
            document.getElementById('formTitle').innerText = 'Create Account';
            document.getElementById('formSubtitle').innerText = 'Join the VisitEase community';
        }
        function showLogin() {
            document.getElementById('registerFormContainer').style.display = 'none';
            document.getElementById('loginFormContainer').style.display = 'block';
            document.getElementById('formTitle').innerText = 'Welcome Back';
            document.getElementById('formSubtitle').innerText = 'Sign in to your VisitEase account';
        }
    </script>
</body>
</html>
