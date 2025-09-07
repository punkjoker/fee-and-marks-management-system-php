<?php
// edit_student.php
include 'sidebar.php';
include("db.php");

// Get student ID from URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    $query = "SELECT * FROM students WHERE id = '$student_id'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);
}

// Handle update
if (isset($_POST['update'])) {
    $fullname   = $_POST['fullname'];
    $admission  = $_POST['admission'];
    $class      = $_POST['class'];
    $gender     = $_POST['gender'];
    $parent     = $_POST['parent'];
    $contact    = $_POST['contact'];

    $update = "UPDATE students 
               SET fullname='$fullname', admission_no='$admission', class='$class', gender='$gender', 
                   parent_name='$parent', parent_contact='$contact' 
               WHERE id='$student_id'";
    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Student updated successfully'); window.location='student_list.php';</script>";
    } else {
        echo "<script>alert('Error updating student');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      background-color: #f9f7ff;
    }
    .content {
      margin-left: 220px; /* Sidebar width */
      width: calc(100% - 220px);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh; /* full height for vertical centering */
      padding: 20px;
      box-sizing: border-box;
    }
    .form-container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      width: 100%;
      max-width: 600px;
    }
    h2 {
      color: purple;
      margin-bottom: 20px;
      text-align: center;
    }
    label {
      display: block;
      margin-top: 12px;
      font-weight: bold;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 8px;
      border: 1px solid #ddd;
      font-size: 14px;
    }
    button {
      margin-top: 20px;
      background: purple;
      color: white;
      padding: 12px 18px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      font-size: 15px;
      transition: 0.3s;
      width: 100%;
    }
    button:hover {
      background: orange;
    }
  </style>
</head>
<body>
  <div class="content">
    <div class="form-container">
      <h2>✏️ Edit Student</h2>
      <form method="POST">
        <label>Full Name</label>
        <input type="text" name="fullname" value="<?php echo $student['fullname']; ?>" required>

        <label>Admission Number</label>
        <input type="text" name="admission" value="<?php echo $student['admission_no']; ?>" required>

        <label>Class</label>
        <select name="class" required>
          <option value="Play Group" <?php if($student['class']=="Play Group") echo "selected"; ?>>Play Group</option>
          <option value="PP1" <?php if($student['class']=="PP1") echo "selected"; ?>>PP1</option>
          <option value="PP2" <?php if($student['class']=="PP2") echo "selected"; ?>>PP2</option>
        </select>

        <label>Gender</label>
        <select name="gender" required>
          <option value="Male" <?php if($student['gender']=="Male") echo "selected"; ?>>Male</option>
          <option value="Female" <?php if($student['gender']=="Female") echo "selected"; ?>>Female</option>
        </select>

        <label>Parent/Guardian Name</label>
        <input type="text" name="parent" value="<?php echo $student['parent_name']; ?>" required>

        <label>Parent Contact</label>
        <input type="text" name="contact" value="<?php echo $student['parent_contact']; ?>" required>

        <button type="submit" name="update">Update Student</button>
      </form>
    </div>
  </div>
</body>
</html>
