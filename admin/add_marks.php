<?php
session_start();
include 'sidebar.php';
include 'db.php'; // database connection
// Get current active term
$term_res = mysqli_query($conn, "SELECT id, term_name FROM terms WHERE status='active' LIMIT 1");
if ($term_res && mysqli_num_rows($term_res) > 0) {
    $term_row = mysqli_fetch_assoc($term_res);
    $current_term_id = $term_row['id'];
    $_SESSION['current_term_id'] = $current_term_id;
} else {
    die("No active term found. Please set an active term first.");
}

// Handle exam session from POST or session
if (isset($_POST['set_exam_session']) && !empty($_POST['exam_session'])) {
    $current_exam_session = $_POST['exam_session'];
    $_SESSION['current_exam_session'] = $current_exam_session;

    // Insert exam session into DB if not exists
    $check_exam = mysqli_query($conn, "SELECT id FROM exam_sessions WHERE exam_name='$current_exam_session' AND term_id=$current_term_id");
    if (mysqli_num_rows($check_exam) > 0) {
        $exam_row = mysqli_fetch_assoc($check_exam);
        $_SESSION['current_exam_id'] = $exam_row['id'];
    } else {
        mysqli_query($conn, "INSERT INTO exam_sessions (exam_name, term_id) VALUES ('$current_exam_session', $current_term_id)");
        $_SESSION['current_exam_id'] = mysqli_insert_id($conn);
    }
} else {
    // Load from session if set
    $current_exam_session = $_SESSION['current_exam_session'] ?? '';
    $_SESSION['current_exam_id'] = $_SESSION['current_exam_id'] ?? null;
}

// Define real subjects
$subjects = [
    'number_work' => 'Number Work',
    'language' => 'Language',
    'environment' => 'Environment',
    'psychomotor' => 'Psychomotor',
    'religious' => 'Religious',
    'literacy' => 'Literacy'
];

// Fetch students
$query = "SELECT * FROM students ORDER BY fullname ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Marks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin-left: 220px; /* space for sidebar */
            padding: 20px;
        }
        .container {
            text-align: center;
        }
        h2 {
            color: purple;
        }
        .btn {
            background: purple;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: orange;
        }
        .search-filter {
            margin: 15px 0;
        }
        .search-filter input, .search-filter select {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 95%;
            margin: 15px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid purple;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: purple;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .edit-btn {
            background: orange;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
        }
        .edit-btn:hover {
            background: purple;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Marks Management</h2>

    <form method="post">
    <label><b>Select Exam Session:</b></label>
    <select name="exam_session" required>
        <option value="">-- Select Exam --</option>
        <option value="Opener Exam" <?php if($current_exam_session=='Opener Exam') echo 'selected'; ?>>Opener Exam</option>
        <option value="Midterm Exam" <?php if($current_exam_session=='Midterm Exam') echo 'selected'; ?>>Midterm Exam</option>
        <option value="Endterm Exam" <?php if($current_exam_session=='Endterm Exam') echo 'selected'; ?>>Endterm Exam</option>
    </select>
    <button type="submit" name="set_exam_session" class="btn">Start Marks Entry</button>
</form>

    <?php if ($current_exam_session): ?>
        <p style="color:orange;"><b>Current Session:</b> <?php echo $current_exam_session; ?></p>
    <?php endif; ?>

    <div class="search-filter">
        <input type="text" placeholder="Search by student name...">
        <select>
            <option value="">-- Filter by Term --</option>
            <option value="Term 1">Term 1</option>
            <option value="Term 2">Term 2</option>
            <option value="Term 3">Term 3</option>
        </select>
        <button class="btn">Apply</button>
    </div>

    <table>
        <tr>
            <th>Student Name</th>
            <th>Class</th>
            <th>Number Work</th>
            <th>Language</th>
            <th>Environment</th>
            <th>Psychomotor</th>
            <th>Religious</th>
            <th>Literacy</th>
            <th>Edit</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <<tr>
    <td><?php echo $row['fullname']; ?></td>
    <td><?php echo $row['class']; ?></td>
    <?php
        $marks_res = mysqli_query($conn, "SELECT * FROM marks WHERE admission_no='{$row['admission_no']}' AND exam_id='{$_SESSION['current_exam_id']}' AND term_id='{$_SESSION['current_term_id']}'");
        $marks = mysqli_fetch_assoc($marks_res);
        foreach(array_keys($subjects) as $sub) {
            echo "<td>" . ($marks[$sub] ?? '-') . "</td>";
        }
    ?>
    <td><a href="edit_marks.php?
    admission_no=<?php echo $row['admission_no']; ?>
    &exam_id=<?php echo $_SESSION['current_exam_id']; ?>
    &term_id=<?php echo $_SESSION['current_term_id']; ?>">
    <button class="btn-edit">✏️ Edit</button>
</a>

        </td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
