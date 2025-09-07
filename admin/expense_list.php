<?php
session_start();
include 'db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

// Fetch expenses with their term name
$result = $conn->query("
    SELECT e.*, t.term_name, t.year 
    FROM expenses e
    LEFT JOIN terms t ON e.term_id = t.id
    ORDER BY e.expense_date DESC
");
?>

<?php include 'sidebar.php'; ?>
<div class="content">
    <h2>Expense List</h2>
    <table class="expense-table">
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Person Responsible</th>
            <th>Cheque Number</th>
            <th>Term</th>
            <th>Date</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo number_format($row['amount'],2); ?></td>
            <td><?php echo htmlspecialchars($row['person_responsible']); ?></td>
            <td><?php echo htmlspecialchars($row['cheque_number']); ?></td>
            <td>
                <?php 
                    echo $row['term_name'] 
                        ? htmlspecialchars($row['term_name']." ".$row['year']) 
                        : "N/A"; 
                ?>
            </td>
            <td><?php echo $row['expense_date']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<style>
.content h2 {
    color: #FFA500;
    margin-bottom: 20px;
}

/* Table Styling */
.expense-table {
    width: 90%;
    margin: 0 auto;
    border-collapse: collapse;
    background: #4B0082;
    color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.3);
}

.expense-table th, .expense-table td {
    padding: 12px 15px;
    text-align: center;
}

.expense-table th {
    background: #6A0DAD;
}

.expense-table tr:nth-child(even) {
    background: #5A0B9A;
}

.expense-table tr:hover {
    background: #FFA500;
    color: #4B0082;
}
</style>
