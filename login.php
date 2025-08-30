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
<html>
<head>
  <title>Admin Login | Matendeni ECD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card p-4 shadow-lg">
          <h3 class="text-center mb-3">Admin Login</h3>
          <?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
          <form method="POST">
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
            <div class="text-center mt-3">
              <a href="signup.php">Don't have an account? Signup</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
