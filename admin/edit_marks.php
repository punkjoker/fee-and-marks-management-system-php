<?php
session_start();
require 'db.php'; // your DB connection

// Get student details
$admission_no = $_GET['admission_no'] ?? null;
$exam_id = $_GET['exam_id'] ?? null;
$term_id = $_GET['term_id'] ?? null;

if (!$admission_no || !$exam_id || !$term_id) {
    die("Missing required parameters.");
}

// Fetch student info
$student_res = $conn->query("SELECT fullname, class FROM students WHERE admission_no='$admission_no'");
$student = $student_res ? $student_res->fetch_assoc() : null;

// Fetch exam info
$exam_res = $conn->query("SELECT exam_name FROM exam_sessions WHERE id='$exam_id'");
$exam = $exam_res ? $exam_res->fetch_assoc() : null;

// Fetch term info
$term_res = $conn->query("SELECT term_name FROM terms WHERE id='$term_id'");
$term = $term_res ? $term_res->fetch_assoc() : null;

// Fetch existing marks if any
$marks_res = $conn->query("SELECT * FROM marks 
    WHERE admission_no='$admission_no' AND exam_id='$exam_id' AND term_id='$term_id'");
$marks = $marks_res ? $marks_res->fetch_assoc() : [];

if (!$student || !$exam || !$term) {
    die("Invalid student, exam, or term.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_all'])) {
    $subjects = ['number_work','language','environment','psychomotor','religious','literacy'];
    
    // Check if a marks row exists, if not, insert
    $check_marks = $conn->query("SELECT * FROM marks WHERE admission_no='$admission_no' AND exam_id='$exam_id' AND term_id='$term_id'");
    if ($check_marks->num_rows == 0) {
        $conn->query("INSERT INTO marks (admission_no, exam_id, term_id) VALUES ('$admission_no', '$exam_id', '$term_id')");
    }

    foreach ($subjects as $subject) {
        $mark = $_POST[$subject] ?? '';
        $conn->query("UPDATE marks SET $subject='$mark' 
                      WHERE admission_no='$admission_no' 
                      AND exam_id='$exam_id' 
                      AND term_id='$term_id'");
    }
    echo "<p style='color:green;'>Marks updated successfully!</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Marks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f2ff;
            margin-left: 220px; /* space for sidebar */
            padding: 20px;
        }
        .content {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 0 10px rgba(128,0,128,0.2);
        }
        h2 {
            color: purple;
            text-align: center;
        }
        p {
            color: #333;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid purple;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: purple;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f4f2ff;
        }
        input[type=text] {
            padding: 6px;
            width: 80%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .edit-btn {
            background-color: orange;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: purple;
        }
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>

<div class="content">
    <h2>Enter Marks for <?php echo htmlspecialchars($student['fullname']); ?></h2>
    <p>Class: <?php echo htmlspecialchars($student['class']); ?></p>
    <p>Exam: <?php echo htmlspecialchars($exam['exam_name']); ?> | Term: <?php echo htmlspecialchars($term['term_name']); ?></p>

    <form method="post">
        <table>
            <tr>
                <th>Subject</th>
                <th>Marks (e.g., 8/25)</th>
                <th>Action</th>
            </tr>
            <?php
            $subjects_list = [
                'number_work'=>'Number Work',
                'language'=>'Language',
                'environment'=>'Environment',
                'psychomotor'=>'Psychomotor',
                'religious'=>'Religious',
                'literacy'=>'Literacy'
            ];
            foreach ($subjects_list as $col => $label) {
                $value = $marks[$col] ?? '';
                echo "<tr>
                        <td>$label</td>
                        <td><input type='text' name='$col' value='".htmlspecialchars($value)."' placeholder='e.g. 8/25'></td>
                        <td><button type='submit' name='save_$col' class='edit-btn'>Edit</button></td>
                      </tr>";
            }
            ?>
        </table>
        <br>
    <button type="submit" name="save_all" class="edit-btn">Save All Marks</button>
    </form>
</div>

</body>
</html>
