<?php  
include("sidebar.php"); 
include("db.php"); // use db.php since that's what you used earlier

// Fetch active term (name + year + id)
$activeTermQuery = mysqli_query($conn, "SELECT id, term_name, year FROM terms WHERE status = 'active' LIMIT 1");
$activeTermRow = mysqli_fetch_assoc($activeTermQuery);

$activeTermId   = $activeTermRow['id'] ?? null;
$activeTermName = $activeTermRow ? $activeTermRow['term_name']." ".$activeTermRow['year'] : "No Active Term";

// Total Students
$studentQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_students FROM students");
$studentRow = mysqli_fetch_assoc($studentQuery);
$totalStudents = $studentRow['total_students'] ?? 0;

// Fees Collected & Balances for active term (JOIN with terms)
$totalIncome = 0;
$totalBalance = 0;

if ($activeTermId) {
    $financeQuery = mysqli_query($conn, "
        SELECT 
            SUM(p.amount_paid) AS total_income, 
            SUM(p.balance) AS total_balance
        FROM payments p
        INNER JOIN terms t ON p.term_id = t.id
        WHERE t.id = '$activeTermId'
    ");
    $financeRow = mysqli_fetch_assoc($financeQuery);
    $totalIncome = $financeRow['total_income'] ?? 0;
    $totalBalance = $financeRow['total_balance'] ?? 0;
}
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
            margin-left: 260px; /* adjust if sidebar width is different */
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
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
            color: #16a085;
            font-weight: bold;
        }
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
        <h2>Dashboard - Active Term: <?php echo $activeTermName; ?></h2>

        <div class="cards">
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $totalStudents; ?></p>
            </div>
            <div class="card">
                <h3>Total Income</h3>
                <p>Ksh <?php echo number_format($totalIncome); ?></p>
            </div>
            <div class="card">
                <h3>Total Balance</h3>
                <p>Ksh <?php echo number_format($totalBalance); ?></p>
            </div>
        </div>

        <div class="chart-container">
            <h3>Income vs Balance</h3>
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Income', 'Balance'],
                datasets: [{
                    label: 'Ksh',
                    data: [<?php echo $totalIncome; ?>, <?php echo $totalBalance; ?>],
                    backgroundColor: ['#27ae60', '#e74c3c']
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
