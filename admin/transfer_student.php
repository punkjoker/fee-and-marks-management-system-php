<?php
include("db.php");

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);

    // Fetch student details
    $student = $conn->query("SELECT * FROM students WHERE id=$student_id")->fetch_assoc();

    if ($student) {
        $admission_no = $student['admission_no'];
        $completion_year = date("Y");

        // Insert into completed_students
        $conn->query("INSERT INTO completed_students (admission_no, completion_year) 
                      VALUES ('$admission_no', '$completion_year')");

        // Update student status to deactivated
        $conn->query("UPDATE students SET status='deactivated' WHERE id=$student_id");
    }

    header("Location: student_list.php");
    exit();
}
?>
