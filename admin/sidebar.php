<!-- Sidebar --> 
<aside class="sidebar">
  <h4>📊 Matendeni ECD</h4>
  <a href="dashboard.php">Dashboard</a>
  
  <!-- Students Section -->
  <a href="#">👩‍🎓 Manage Students</a>
  <a href="add_student.php" style="margin-left:15px;">➕ Add Student</a>
  <a href="student_list.php" style="margin-left:15px;">📋 Student List</a>
  
  <!-- Fees Section -->
  <a href="#">💰 Fees Management</a>
  <a href="add_fees.php" style="margin-left:15px;">➕ Add Fees</a>
  <a href="fee_history.php" style="margin-left:15px;">📜 Fee History</a>
  
  <!-- Marks Section -->
  <a href="#">📝 Marks Management</a>
  <a href="add_marks.php" style="margin-left:15px;">➕ Add Marks</a>
  <a href="marks_history.php" style="margin-left:15px;">📜 Marks History</a>
  
  <!-- Completed Students Section -->
  <a href="#">🎓 Completed Students</a>
  <a href="completed_list.php" style="margin-left:15px;">📋 Completed List</a>
 
  <!-- Expenses Section -->
<a href="#">💸 Expenses</a>
<a href="add_expense.php" style="margin-left:15px;">➕ Add Expense</a>
<a href="expense_list.php" style="margin-left:15px;">📋 Expense List</a>

  <!-- Other Features -->
  <a href="logout.php" class="logout">🚪 Logout</a>
</aside>

<style>
/* Sidebar styling */
/* Sidebar container */
.sidebar {
  position: fixed;        /* Keep it fixed */
  top: 0;
  left: 0;
  height: 100vh;          /* Full viewport height */
  width: 250px;           /* Adjust to your layout */
  background: #4B0082;    /* Example background */
  color: #fff;
  overflow-y: auto;       /* Enable vertical scrolling */
  overflow-x: hidden;     /* Prevent horizontal scroll */
}

/* Ensure content beside sidebar does not overlap */
.content {
  margin-left: 250px;     /* Same width as sidebar */
  padding: 20px;
}

.sidebar h4 {
  color: #FFA500; /* Orange accent */
  margin-bottom: 25px;
}

.sidebar a {
  display: block;
  color: white;
  padding: 12px;
  margin: 6px 0;
  text-decoration: none;
  border-radius: 6px;
  transition: background 0.3s ease;
}

.sidebar a:hover {
  background-color: #6A0DAD; /* Lighter purple */
}

.sidebar a.logout {
  margin-top: 20px;
  background: #FFA500; /* Orange */
  color: #4B0082;
  font-weight: bold;
}
.sidebar a.logout:hover {
  background: #FF8C00; /* Darker orange */
  color: white;
}

/* Push page content to the right */
.content {
  margin-left: 250px;
  padding: 20px;
}
</style>
