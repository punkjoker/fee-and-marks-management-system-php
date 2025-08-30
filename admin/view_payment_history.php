<?php
session_start();
include 'db.php';

// check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// get student admission_no and term_id
if (!isset($_GET['admission_no']) || !isset($_GET['term_id'])) {
    header("Location: fee_history.php");
    exit();
}

$admission_no = mysqli_real_escape_string($conn, $_GET['admission_no']);
$term_id = intval($_GET['term_id']);

// fetch student info
$studentRes = $conn->query("SELECT fullname, class FROM students WHERE admission_no='$admission_no'");
$student = $studentRes->fetch_assoc();

// fetch term info
$termRes = $conn->query("SELECT term_name, year FROM terms WHERE id=$term_id");
$term = $termRes->fetch_assoc();

// fetch payments with term column
$paymentsRes = $conn->query("
    SELECT id, amount_paid, payment_date, receipt_no, term
    FROM payments
    WHERE admission_no='$admission_no' AND term_id=$term_id
    ORDER BY payment_date ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment History - <?php echo htmlspecialchars($student['fullname']); ?></title>
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
        h2, h3 {
            color: purple;
            margin-bottom: 10px;
        }
        .card {
            background: #fff;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        th {
            background: purple;
            color: white;
        }
        .btn {
            display: inline-block;
            margin: 15px 5px 0;
            padding: 10px 16px;
            background: orange;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover {
            background: darkorange;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .btn { display: none; }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="card print-area">
            <h2>Matendeni ECD Management</h2>
            <h3>Fee Payment History Receipt</h3>
            <p><strong>Student:</strong> <?php echo htmlspecialchars($student['fullname']); ?> (<?php echo htmlspecialchars($student['class']); ?>)</p>
            <p><strong>Admission No:</strong> <?php echo htmlspecialchars($admission_no); ?></p>
            <p><strong>Term:</strong> <?php echo htmlspecialchars($term['term_name'] . " - " . $term['year']); ?></p>

            <table>
                <tr>
                    <th>Receipt No</th>
                    <th>Term</th>
                    <th>Amount Paid</th>
                    <th>Date</th>
                </tr>
                <?php if ($paymentsRes->num_rows > 0): ?>
                    <?php while($p = $paymentsRes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['receipt_no']); ?></td>
                            <td><?php echo htmlspecialchars($p['term']); ?></td>
                            <td><?php echo number_format($p['amount_paid'], 2); ?></td>
                            <td><?php echo date("d M Y", strtotime($p['payment_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No payments found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
<form action="save_receipt.php" method="POST" id="saveForm">
            <input type="hidden" name="admission_no" value="<?php echo $admission_no; ?>">
            <input type="hidden" name="term_id" value="<?php echo $term_id; ?>">
            <!-- this will hold the receipt HTML -->
            <textarea name="receipt_html" id="receipt_html" style="display:none;"></textarea>
            <button type="button" class="btn" onclick="saveReceipt()">💾 Save</button>
        </form>

        <a href="#" class="btn" onclick="window.print()">🖨️ Print</a>
        
        <a href="fee_history.php?term_id=<?php echo $term_id; ?>" class="btn">Back</a>
    </div>
    <script>
function saveReceipt() {
    // Get the inner HTML of the receipt section
    const receiptContent = document.querySelector('.print-area').innerHTML;
    document.getElementById('receipt_html').value = receiptContent;
    document.getElementById('saveForm').submit();
}
</script>

</body>
</html>
