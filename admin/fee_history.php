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

// search by name
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

// fetch student list with fees
$query = "
    SELECT s.admission_no, s.fullname, s.class,
           IFNULL(SUM(p.amount_paid),0) AS total_paid,
           (sf.fee_amount - IFNULL(SUM(p.amount_paid),0) + sf.carried_forward) AS balance
    FROM students s
    LEFT JOIN student_fees sf ON s.admission_no = sf.admission_no AND sf.term_id = $selectedTerm
    LEFT JOIN payments p ON s.admission_no = p.admission_no AND p.term_id = $selectedTerm
    WHERE s.fullname LIKE '%$search%'
    GROUP BY s.admission_no, s.fullname, s.class, sf.fee_amount, sf.carried_forward
    ORDER BY s.fullname ASC
";
$studentsRes = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee History - Student Management</title>
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
        .filter-bar {
            margin-bottom: 20px;
        }
        select, input[type="text"] {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-right: 10px;
        }
        button, .btn {
            padding: 6px 12px;
            background: orange;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        button:hover, .btn:hover {
            background: darkorange;
        }
        table {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        th {
            background: purple;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h2>Fee History</h2>

        <form method="GET" class="filter-bar">
            <select name="term_id" onchange="this.form.submit()">
                <?php foreach ($terms as $t): ?>
                    <option value="<?php echo $t['id']; ?>" <?php if ($t['id']==$selectedTerm) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($t['term_name']." - ".$t['year']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Filter</button>
        </form>

        <table>
            <tr>
                <th>Admission No</th>
                <th>Name</th>
                <th>Class</th>
                <th>Total Paid</th>
                <th>Balance</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $studentsRes->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['admission_no']); ?></td>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['class']); ?></td>
                <td><?php echo number_format($row['total_paid']); ?></td>
                <td><?php echo number_format($row['balance']); ?></td>
                <td>
                    <a href="view_payment_history.php?admission_no=<?php echo $row['admission_no']; ?>&term_id=<?php echo $selectedTerm; ?>" class="btn">View History</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
