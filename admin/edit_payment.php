<?php
session_start();
include 'db.php';

// check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// get admission number from URL
if (!isset($_GET['admission_no'])) {
    header("Location: add_fees.php");
    exit();
}
$admission_no = mysqli_real_escape_string($conn, $_GET['admission_no']);

// get current active term
$currentTerm = $conn->query("SELECT id, term_name, year FROM terms WHERE status='active' ORDER BY id DESC LIMIT 1");
$termInfo = $currentTerm->fetch_assoc();
$term_id = $termInfo ? $termInfo['id'] : 0;
$termDisplay = $termInfo ? $termInfo['term_name'] . " - " . $termInfo['year'] : "No Active Term";

// fetch student info + payments
$query = "
    SELECT s.fullname, s.class,
           IFNULL(SUM(p.amount_paid),0) AS total_paid,
           (sf.fee_amount - IFNULL(SUM(p.amount_paid),0) + sf.carried_forward) AS balance
    FROM students s
    LEFT JOIN student_fees sf ON s.admission_no = sf.admission_no AND sf.term_id = $term_id
    LEFT JOIN payments p ON s.admission_no = p.admission_no AND p.term_id = $term_id
    WHERE s.admission_no = '$admission_no'
    GROUP BY s.fullname, s.class, sf.fee_amount, sf.carried_forward
";
$res = $conn->query($query);
$student = $res->fetch_assoc();

// handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $receipt_no = mysqli_real_escape_string($conn, $_POST['receipt_no']);
    $newPaid = $student['total_paid'] + $amount;
    $newBalance = $student['balance'] - $amount;

    // insert payment record
    $stmt = $conn->prepare("
        INSERT INTO payments (admission_no, term_id, amount_paid, balance, receipt_no, term, payment_date) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("sidsss", $admission_no, $term_id, $amount, $newBalance, $receipt_no, $termDisplay);
    $stmt->execute();
    $stmt->close();

    // ✅ update student_fees table balance
    $update = $conn->prepare("
        UPDATE student_fees 
        SET balance = ? 
        WHERE admission_no = ? AND term_id = ?
    ");
    $update->bind_param("dsi", $newBalance, $admission_no, $term_id);
    $update->execute();
    $update->close();

    header("Location: add_fees.php?updated=1");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Fee - Student Management</title>
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
        .card {
            background: #fff;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin: 10px 0 6px;
            text-align: left;
            font-weight: bold;
            color: purple;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .readonly-box {
            background: #f9f9f9;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 15px;
            padding: 10px 20px;
            background: orange;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: darkorange;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h2>Edit Student Fee</h2>
        <div class="card">
            <p><strong>Student:</strong> <?php echo htmlspecialchars($student['fullname']); ?> (<?php echo htmlspecialchars($student['class']); ?>)</p>
            <p><strong>Term:</strong> <?php echo $termDisplay; ?></p>
            <p><strong>Total Paid:</strong> <?php echo number_format($student['total_paid']); ?></p>
            <p><strong>Balance:</strong> <?php echo number_format($student['balance']); ?></p>

            <form method="POST">
                <label>Enter Payment Amount</label>
                <input type="number" name="amount" step="0.01" min="0" required oninput="updateBalance(this.value)">

                <label>Remaining Balance</label>
                <input type="text" id="remaining" class="readonly-box" readonly value="<?php echo number_format($student['balance']); ?>">

                <label>Receipt No</label>
                <input type="text" name="receipt_no" placeholder="e.g., 1003" required>

                <button type="submit">Submit Payment</button>
            </form>
        </div>
    </div>

    <script>
        function updateBalance(val) {
            let balance = <?php echo $student['balance']; ?>;
            let newBal = balance - parseFloat(val || 0);
            document.getElementById("remaining").value = newBal.toFixed(2);
        }
    </script>
</body>
</html>
