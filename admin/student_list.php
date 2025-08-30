<?php
include("db.php");

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$classFilter = isset($_GET['class']) ? $_GET['class'] : '';

$query = "SELECT * FROM students WHERE 1";

if (!empty($search)) {
    $query .= " AND fullname LIKE '%$search%'";
}
if (!empty($classFilter)) {
    $query .= " AND class='$classFilter'";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student List - Matendeni ECD</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:#f9f9fc;
            display: flex;
        }
        .content {
            margin-left: 250px; /* Sidebar width */
            padding: 20px;
            width: calc(100% - 250px);
        }
        h2 {
            text-align:center;
            color:#4B0082; /* Purple */
            margin-bottom: 20px;
        }
        .filters {
            text-align: center;
            margin-bottom: 20px;
        }
        .filters input, .filters select, .filters button {
            padding: 10px;
            margin: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .filters button {
            background:#4B0082; 
            color:white;
            border:none;
            cursor:pointer;
        }
        .filters button:hover {
            background:#FFA500; /* Orange */
        }
        table {
            width:90%;
            margin:0 auto;
            border-collapse:collapse;
            background:white;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding:12px;
            text-align:center;
            border-bottom:1px solid #eee;
        }
        th {
            background:#4B0082;
            color:white;
        }
        tr:nth-child(even){
            background:#f9f2ff;
        }
        .btn-edit {
            padding: 6px 12px;
            background: #FFA500;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-edit:hover {
            background: #e69500;
        }
    </style>
</head>
<body>
    <?php include("sidebar.php"); ?>

    <div class="content">
        <h2>📋 Student List</h2>

        <div class="filters">
            <form method="GET">
                <input type="text" name="search" placeholder="Search by name" value="<?php echo $search; ?>">
                <select name="class">
                    <option value="">Filter by Class</option>
                    <option value="PP1" <?php if($classFilter=="PP1") echo "selected"; ?>>PP1</option>
                    <option value="PP2" <?php if($classFilter=="PP2") echo "selected"; ?>>PP2</option>
                    <option value="PP3" <?php if($classFilter=="PP3") echo "selected"; ?>>PP3</option>
                </select>
                <button type="submit">🔍 Search</button>
            </form>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Admission No</th>
                <th>Class</th>
                <th>Gender</th>
                <th>Parent Name</th>
                <th>Contact</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['admission_no']; ?></td>
                <td><?php echo $row['class']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo $row['parent_name']; ?></td>
                <td><?php echo $row['parent_contact']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td><a href="edit_student.php?id=<?php echo $row['id']; ?>"><button class="btn-edit">✏️ Edit</button></a></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
