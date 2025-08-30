<?php   
include("sidebar.php"); 
include("db.php");

// Fetch active term for completed students
$termQuery = mysqli_query($conn, "SELECT id, term_name FROM terms WHERE status='active' LIMIT 1");
$termRow = mysqli_fetch_assoc($termQuery);
$selectedTermId = $termRow['id'] ?? 0;

// Fetch completed students with their names
$query = mysqli_query($conn, "
    SELECT cs.id, cs.admission_no, cs.completion_year, s.fullname 
    FROM completed_students cs
    JOIN students s ON cs.admission_no = s.admission_no
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Completed Students</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f2ff;
        margin: 0;
        padding: 0;
    }
    .content {
        margin-left: 260px; /* space for sidebar */
        padding: 20px;
        text-align: center;
    }
    h2 {
        color: purple;
        margin-bottom: 20px;
    }
    table {
        margin: 0 auto;
        border-collapse: collapse;
        width: 90%;
        background: white;
        box-shadow: 0 0 12px rgba(0,0,0,0.1);
        border-radius: 10px;
        overflow: hidden;
    }
    th {
        background: purple;
        color: white;
        padding: 12px;
        text-transform: uppercase;
    }
    td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        color: #333;
    }
    tr:nth-child(even) {
        background: #f9f5ff;
    }
    tr:hover {
        background: #ffe5b4; /* light orange */
    }
    .highlight {
        color: orange;
        font-weight: bold;
    }
    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        color: white;
        font-size: 14px;
        margin: 2px;
        display: inline-block;
    }
    .btn-payment {
        background: #27ae60; /* green */
    }
    .btn-marks {
        background: #e67e22; /* orange */
    }
    .btn:hover {
        opacity: 0.85;
    }
</style>
</head>
<body>

<div class="content">
    <h2>🎓 Completed Students List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Admission No</th>
            <th>Student Name</th>
            <th class="highlight">Completion Year</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['admission_no']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td class="highlight"><?php echo $row['completion_year']; ?></td>
                <td>
                    <a href="view_payment_history.php?admission_no=<?php echo urlencode($row['admission_no']); ?>&term_id=<?php echo $selectedTermId; ?>" 
                       class="btn btn-payment">💰 Payment History</a>

                    <a href="view_marks_history.php?admission_no=<?php echo urlencode($row['admission_no']); ?>&exam_id=1&term_id=<?php echo $selectedTermId; ?>" 
                       class="btn btn-marks">📝 Marks</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
