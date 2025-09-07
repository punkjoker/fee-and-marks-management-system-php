<?php 
session_start();
include 'db.php';

// check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// get all terms for filter dropdown
$termsRes = $conn->query("SELECT id, term_name, year FROM terms ORDER BY year DESC, id DESC");
$terms = [];
while ($row = $termsRes->fetch_assoc()) {
    $terms[] = $row;
}

// selected term
$selectedTerm = isset($_GET['term_id']) ? intval($_GET['term_id']) : 0;
if ($selectedTerm === 0 && count($terms) > 0) {
    $selectedTerm = $terms[0]['id']; // default to latest
}

// fetch term display
$termInfoRes = $conn->query("SELECT term_name, year FROM terms WHERE id = $selectedTerm LIMIT 1");
$termInfo = $termInfoRes->fetch_assoc();
$termDisplay = $termInfo ? $termInfo['term_name'] . " - " . $termInfo['year'] : "No Active Term";

// search by name
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

// fetch student list with latest payment for the selected term
$query = "
    SELECT 
        s.admission_no, 
        s.fullname, 
        s.class,
        COALESCE(lp.amount_paid, 0) AS total_paid,
        (sf.fee_amount - COALESCE(lp.amount_paid, 0) + sf.carried_forward) AS balance
    FROM students s
    LEFT JOIN student_fees sf 
        ON s.admission_no = sf.admission_no AND sf.term_id = $selectedTerm
    LEFT JOIN (
        SELECT p1.admission_no, p1.amount_paid
        FROM payments p1
        INNER JOIN (
            SELECT admission_no, MAX(payment_date) AS latest_date
            FROM payments
            WHERE term_id = $selectedTerm
            GROUP BY admission_no
        ) p2 
        ON p1.admission_no = p2.admission_no AND p1.payment_date = p2.latest_date
        WHERE p1.term_id = $selectedTerm
    ) lp
    ON s.admission_no = lp.admission_no
    WHERE s.status='active' AND s.fullname LIKE '%$search%'
    ORDER BY s.fullname ASC
";

$studentsRes = $conn->query($query); // <-- use $studentsRes
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Fees - Student Management</title>
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
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .top-bar a {
            background-color: orange;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }
        .top-bar a:hover {
            background-color: darkorange;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-box input {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .search-box button {
            padding: 6px 12px;
            background: purple;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .search-box button:hover {
            background: darkviolet;
        }
        h2 {
            color: purple;
            margin-bottom: 10px;
        }
        .term-display {
            font-weight: bold;
            margin-bottom: 20px;
            color: darkorange;
        }
        table {
            margin: 0 auto;
            width: 80%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background: purple;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .btn-edit {
            padding: 6px 12px;
            background: orange;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-edit:hover {
            background: darkorange;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> 

    <div class="content">
        <div class="top-bar">
            <div class="search-box">
                <form method="GET" action="">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search student by name...">
                    <button type="submit">🔍 Search</button>
                </form>
            </div>
            <a href="start_new_term.php">+ Start New Term</a>
        </div>

        <h2>Student Fees Management</h2>
        <div class="term-display">Current Term: <?php echo $termDisplay; ?></div>

        <table>
            <tr>
                <th>Admission No</th>
                <th>Full Name</th>
                <th>Class</th>
                <th>Total Paid</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($studentsRes)) { ?>

            <tr>
                <td><?php echo htmlspecialchars($row['admission_no']); ?></td>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['class']); ?></td>
                <td><?php echo number_format($row['total_paid']); ?></td>
                <td><?php echo number_format($row['balance']); ?></td>
                <td>
                    <a class="btn-edit" href="edit_payment.php?admission_no=<?php echo urlencode($row['admission_no']); ?>">Edit</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
