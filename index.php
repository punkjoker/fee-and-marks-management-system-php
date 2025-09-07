<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Matendeni ECD Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
      color: #333;
    }
    nav {
      background: #4B0082; /* deep purple */
    }
    .hero {
      height: 80vh;
      background: linear-gradient(rgba(75,0,130,0.65), rgba(37,117,252,0.65)),
                  url('assets/images/school-bg.jpg') center/cover no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }
    .hero p {
      font-size: 1.3rem;
      margin-top: 15px;
    }
    .login-section {
      background: #fff;
      padding: 50px 0;
    }
    .login-card {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0px 6px 20px rgba(0,0,0,0.15);
    }
    .btn-login {
      background-color: #FF7F50; /* orange */
      border: none;
      transition: 0.3s;
    }
    .btn-login:hover {
      background-color: #e6643f;
    }
    footer {
      text-align: center;
      padding: 15px;
      background: #4B0082;
      color: #fff;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">🏫 Matendeni ECD Management System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1>Welcome to Matendeni ECD Management System</h1>
      <p>Simple • Modern • Efficient</p>
    </div>
  </section>

  
  <!-- About Section -->
  <section id="about" class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 text-center">
        <h2 class="mb-4 text-purple">About This System</h2>
        <p class="lead">
          This platform helps manage students, fees, and marks for Play Group, PP1, and PP2 classes. 
          Administrators can track payments, balances, generate receipts, and manage student promotion.
        </p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?>Matendeni ECD Management System.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
