<?php
include("db.php");

if(isset($_POST['add'])){
    $fullname = $_POST['fullname'];
    $admission = $_POST['admission'];
    $class = $_POST['class'];
    $gender = $_POST['gender'];
    $parent = $_POST['parent'];
    $contact = $_POST['contact'];

    // Insert new student
    $query = "INSERT INTO students (fullname, admission_no, class, gender, parent_name, parent_contact) 
              VALUES ('$fullname', '$admission', '$class', '$gender', '$parent', '$contact')";
    if (mysqli_query($conn, $query)) {

        // ✅ Step 1: Get the active term
        $term_q = mysqli_query($conn, "SELECT id FROM terms WHERE status='active' LIMIT 1");
        if (mysqli_num_rows($term_q) > 0) {
            $term = mysqli_fetch_assoc($term_q);
            $term_id = $term['id'];

            // ✅ Step 2: Assign default fees for this student in the active term
            $default_fee = 3500; // adjust if different per class
            $insert_fee = "INSERT INTO student_fees (admission_no, term_id, fee_amount, carried_forward) 
                           VALUES ('$admission', '$term_id', '$default_fee', 0)";
            mysqli_query($conn, $insert_fee);
        }

        echo "<script>alert('Student Added Successfully! Fees assigned for active term.'); window.location='add_student.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch students for table
$students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student - Matendeni ECD</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:#f5f5fa;
            display: flex;
        }
        .content {
            margin-left: 250px; /* Sidebar width */
            padding: 20px;
            width: calc(100% - 250px);
        }
        .form-container {
            background:white;
            padding:25px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-grid input, .form-grid select {
            width: 100%;
            padding:12px;
            border-radius:8px;
            border:1px solid #ccc;
            font-size:14px;
        }
        button {
            background:#4B0082; /* Purple */
            color:white;
            padding:12px 20px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-size:15px;
            margin-top: 15px;
        }
        button:hover {
            background:#FFA500; /* Orange */
        }
        h2 {
            color:#4B0082;
            margin-bottom: 20px;
            text-align: center;
        }
        /* Loader */
        .loader {
            display: none;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #4B0082;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            animation: spin 1s linear infinite;
            margin: 15px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
        /* Student List Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table th {
            background: #4B0082;
            color: white;
        }
        table tr:hover {
            background: #f9f2ff;
        }
    </style>
    <script>
        function showLoader() {
            document.getElementById("loader").style.display = "block";
        }
    </script>
</head>
<body>
    <?php include("sidebar.php"); ?>

    <div class="content">
        <div class="form-container">
            <h2>➕ Add Student</h2>
            <form method="POST" onsubmit="showLoader()">
                <div class="form-grid">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <input type="text" name="admission" placeholder="Admission Number" required>
                    <select name="class" required>
                        <option value="">Select Class</option>
                        <option value="PP1">PP1</option>
                        <option value="PP2">PP2</option>
                        <option value="PP3">PP3</option>
                    </select>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                    <input type="text" name="parent" placeholder="Parent/Guardian Name" required>
                    <input type="text" name="contact" placeholder="Parent Contact" required>
                </div>
                <button type="submit" name="add">Add Student</button>
                <div id="loader" class="loader"></div>
            </form>
        </div>

        <h2>📋 Student List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Admission No</th>
                <th>Class</th>
                <th>Gender</th>
                <th>Parent</th>
                <th>Contact</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($students)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['admission_no']; ?></td>
                <td><?php echo $row['class']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo $row['parent_name']; ?></td>
                <td><?php echo $row['parent_contact']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
