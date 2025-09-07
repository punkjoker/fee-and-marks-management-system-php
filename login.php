<?php
session_start();
require 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login | Matendeni ECD</title>
<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url('images/login.jpg') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-container {
        background: rgba(75, 0, 130, 0.95); /* Semi-transparent purple */
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        width: 350px;
        text-align: center;
        color: #fff;
    }
    .login-container h3 {
        margin-bottom: 25px;
        color: #FFA500; /* Orange heading */
        font-size: 28px;
    }
    .login-container input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: none;
        outline: none;
        font-size: 16px;
    }
    .login-container input[type="email"],
    .login-container input[type="password"] {
        background: #fff;
        color: #4B0082;
    }
    .login-container button {
        width: 100%;
        padding: 12px;
        background: #FFA500;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        color: #4B0082;
        cursor: pointer;
        transition: 0.3s;
    }
    .login-container button:hover {
        background: #ff8c00;
        color: #fff;
    }
    .login-container .alert {
        background: #ff6347;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: bold;
    }
    .login-container a {
        color: #FFA500;
        text-decoration: none;
    }
    .login-container a:hover {
        text-decoration: underline;
    }
    .back-home {
        display: inline-block;
        margin-top: 20px;
        padding: 8px 15px;
        background: #fff;
        color: #4B0082;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }
    .back-home:hover {
        background: #FFA500;
        color: #fff;
    }
</style>
</head>
<body>

<div class="login-container">
    <h3>Admin Login</h3>
    <?php if($message) echo "<div class='alert'>$message</div>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <div style="margin-top: 15px;">
        <a href="signup.php">Don't have an account? Signup</a>
    </div>
    <a href="index.php" class="back-home">Back to Home</a>
</div>

</body>
</html>
