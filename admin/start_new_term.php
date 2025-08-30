<?php
session_start();
include 'db.php';

// check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $term_name  = $_POST['term_name'];
    $year       = $_POST['year'];
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];

    // 1. Complete any currently active term
    $conn->query("UPDATE terms SET status='completed' WHERE status='active'");

    // 2. Insert new term
    $stmt = $conn->prepare("INSERT INTO terms (term_name, year, start_date, end_date, status) 
                            VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("siss", $term_name, $year, $start_date, $end_date);
    $stmt->execute();
    $new_term_id = $stmt->insert_id;
    $stmt->close();

    // 3. Carry forward balances & setup new student_fees
    $students = $conn->query("SELECT * FROM students");

    while ($row = $students->fetch_assoc()) {
        $admission_no = $row['admission_no'];
        $class = $row['class'];

        // Get last term’s balance
        $last_balance = 0;
        $last_term = $conn->query("
            SELECT balance, carried_forward 
            FROM student_fees 
            WHERE admission_no='$admission_no' 
            ORDER BY id DESC LIMIT 1
        ");
        if ($last_term->num_rows > 0) {
            $p = $last_term->fetch_assoc();
            $last_balance = $p['balance'];
        }

        // Default new term fee
        $term_fee = 3500;

        // If student completed PP3 in Term 3 → move to completed_students
        if ($class === 'PP3' && $term_name === 'Term 3') {
            $conn->query("INSERT INTO completed_students (admission_no, completion_year)
                          VALUES ('$admission_no', '$year')");
            continue; // skip fees record
        }

        // New balance includes carried forward
        $new_balance = $term_fee + $last_balance;

        // Insert into student_fees
        $conn->query("INSERT INTO student_fees (admission_no, term_id, fee_amount, balance, carried_forward) 
                      VALUES ('$admission_no', '$new_term_id', '$term_fee', '$new_balance', '$last_balance')");
    }

    $success = "✅ New term started successfully and balances carried forward!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start New Term</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: purple;
            margin-bottom: 20px;
        }
        form {
            background: #fff;
            width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: purple;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        button {
            background: orange;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: darkorange;
        }
        .success {
            color: green;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h2>Start New Term</h2>
        <?php if (!empty($success)) { echo "<p class='success'>$success</p>"; } ?>

        <form method="POST" action="">
            <label>Term Name</label>
            <input type="text" name="term_name" required placeholder="e.g., Term 1">

            <label>Year</label>
            <input type="number" name="year" required value="<?php echo date('Y'); ?>">

            <label>Start Date</label>
            <input type="date" name="start_date" required>

            <label>End Date</label>
            <input type="date" name="end_date" required>

            <button type="submit">Start Term</button>
        </form>
    </div>

</body>
</html>
