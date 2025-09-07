<?php   
include("sidebar.php"); 
include("db.php"); 

// Selected filters
$selectedTermId = $_GET['term_id'] ?? null;
$selectedYear   = $_GET['year'] ?? null;

// Fetch active term
$activeTermQuery = mysqli_query($conn, "SELECT id, term_name, year FROM terms WHERE status = 'active' LIMIT 1");
$activeTermRow   = mysqli_fetch_assoc($activeTermQuery);
$activeTermId    = $activeTermRow['id'] ?? null;
$activeYear      = $activeTermRow['year'] ?? null;

// Default fallback
if (!$selectedTermId && !$selectedYear) {
    $selectedTermId = $activeTermId;
    $selectedYear   = $activeYear;
}

// Fetch selected term details
$termQuery   = mysqli_query($conn, "SELECT id, term_name, year FROM terms WHERE id = '$selectedTermId' LIMIT 1");
$termRow     = mysqli_fetch_assoc($termQuery);
$selectedTermName = $termRow ? $termRow['term_name']." ".$termRow['year'] : "All Terms in ".$selectedYear;

// Fetch all terms for filter dropdown
$termsResult = mysqli_query($conn, "SELECT id, term_name, year FROM terms ORDER BY id DESC");

// Fetch distinct years for filter dropdown
$yearsResult = mysqli_query($conn, "SELECT DISTINCT year FROM terms ORDER BY year DESC");

// Total Students (all-time)
$studentQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM students");
$studentRow   = mysqli_fetch_assoc($studentQuery);
$totalStudents = $studentRow['total_students'] ?? 0;

// Fees Collected & Balances
$totalIncome  = 0;
$totalBalance = 0;

if ($selectedTermId) {
    $financeQuery = mysqli_query($conn, "
        SELECT 
            SUM(amount_paid) AS total_income, 
            SUM(balance) AS total_balance
        FROM payments
        WHERE term_id = '$selectedTermId'
    ");
} elseif ($selectedYear) {
    $financeQuery = mysqli_query($conn, "
        SELECT 
            SUM(p.amount_paid) AS total_income, 
            SUM(p.balance) AS total_balance
        FROM payments p
        INNER JOIN terms t ON p.term_id = t.id
        WHERE t.year = '$selectedYear'
    ");
} else {
    $financeQuery = mysqli_query($conn, "
        SELECT 
            SUM(amount_paid) AS total_income, 
            SUM(balance) AS total_balance
        FROM payments
        WHERE term_id = '$activeTermId'
    ");
}

$financeRow   = mysqli_fetch_assoc($financeQuery);
$totalIncome  = $financeRow['total_income'] ?? 0;
$totalBalance = $financeRow['total_balance'] ?? 0;

// Total Expenses (from your expenses table)
$totalExpenses = 0;

if ($selectedYear) {
    $expenseQuery = mysqli_query($conn, "
        SELECT SUM(amount) AS total_expenses 
        FROM expenses
        WHERE YEAR(expense_date) = '$selectedYear'
    ");
} else {
    $expenseQuery = mysqli_query($conn, "
        SELECT SUM(amount) AS total_expenses 
        FROM expenses
        WHERE YEAR(expense_date) = '$activeYear'
    ");
}

$expenseRow = mysqli_fetch_assoc($expenseQuery);
$totalExpenses = $expenseRow['total_expenses'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        .dashboard {
            margin-left: 260px;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .filter-box {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filter-box select, 
        .filter-box button {
            padding: 8px 12px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .filter-box button {
            background: purple;
            color: white;
            cursor: pointer;
        }
        .filter-box button:hover {
            background: #6A0DAD;
        }
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            flex: 1;
            min-width: 200px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin: 10px 0;
            font-size: 22px;
            color: #2c3e50;
        }
        .card p {
            font-size: 18px;
            font-weight: bold;
        }
        .card.income p { color: #27ae60; }
        .card.balance p { color: #e74c3c; }
        .card.expense p { color: #8e44ad; }
        .chart-container {
            margin-top: 40px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Dashboard - Report for: <?php echo $selectedTermName; ?></h2>

        <!-- Filter Section -->
        <div class="filter-box">
            <form method="GET" action="dashboard.php">
                <label for="term_id">Select Term: </label>
                <select name="term_id" id="term_id">
                    <option value="">-- All Terms --</option>
                    <?php while ($row = mysqli_fetch_assoc($termsResult)) { ?>
                        <option value="<?php echo $row['id']; ?>" 
                            <?php if ($row['id'] == $selectedTermId) echo 'selected'; ?>>
                            <?php echo $row['term_name']." ".$row['year']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="year">Select Year: </label>
                <select name="year" id="year">
                    <option value="">-- All Years --</option>
                    <?php while ($yr = mysqli_fetch_assoc($yearsResult)) { ?>
                        <option value="<?php echo $yr['year']; ?>" 
                            <?php if ($yr['year'] == $selectedYear) echo 'selected'; ?>>
                            <?php echo $yr['year']; ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit">Filter Report</button>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $totalStudents; ?></p>
            </div>
            <div class="card income">
                <h3>Total Income</h3>
                <p>Ksh <?php echo number_format($totalIncome); ?></p>
            </div>
            <div class="card balance">
                <h3>Total Balance</h3>
                <p>Ksh <?php echo number_format($totalBalance); ?></p>
            </div>
            <div class="card expense">
                <h3>Total Expenses</h3>
                <p>Ksh <?php echo number_format($totalExpenses); ?></p>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-container">
            <h3>Income vs Balance vs Expenses</h3>
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Income', 'Balance', 'Expenses'],
                datasets: [{
                    label: 'Ksh',
                    data: [<?php echo $totalIncome; ?>, <?php echo $totalBalance; ?>, <?php echo $totalExpenses; ?>],
                    backgroundColor: ['#27ae60', '#e74c3c', '#8e44ad']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
