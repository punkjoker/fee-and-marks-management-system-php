<?php
session_start();
include 'db.php';
require 'vendor/autoload.php'; 

use Dompdf\Dompdf;

// Get filter values
$year = $_POST['year'] ?? date('Y');
$term_id = $_POST['term_id'] ?? '';
$exam_id = $_POST['exam_id'] ?? '';
$class_filter = $_POST['class'] ?? '';

// Subjects
$subjects = ['number_work'=>'Number Work', 'language'=>'Language', 'environment'=>'Environment', 'psychomotor'=>'Psychomotor', 'religious'=>'Religious', 'literacy'=>'Literacy'];

// Fetch term and exam names
$term_name = '';
if($term_id){
    $res = mysqli_query($conn, "SELECT term_name FROM terms WHERE id='$term_id'");
    $term_row = mysqli_fetch_assoc($res);
    $term_name = $term_row['term_name'] ?? '';
}

$exam_name = '';
if($exam_id){
    $res = mysqli_query($conn, "SELECT exam_name FROM exam_sessions WHERE id='$exam_id'");
    $exam_row = mysqli_fetch_assoc($res);
    $exam_name = $exam_row['exam_name'] ?? '';
}

// Fetch students and marks
$query = "SELECT s.fullname, s.class AS student_class, m.* 
          FROM students s 
          LEFT JOIN marks m 
            ON s.admission_no = m.admission_no
            AND ".($term_id ? "m.term_id='$term_id'" : "1")."
            AND ".($exam_id ? "m.exam_id='$exam_id'" : "1")."
          WHERE ".($class_filter ? "s.class='$class_filter'" : "1")."
          ORDER BY s.fullname ASC";

$result = mysqli_query($conn, $query);

// Start HTML content
$html = '
<html>
<head>
<style>
body { font-family: Arial, sans-serif; font-size:12px; }
h2, h3 { text-align:center; margin:0; }
table { width:100%; border-collapse: collapse; margin-top:10px; }
th, td { border:1px solid #000; padding:5px; text-align:center; }
th { background-color:#6a0dad; color:white; }
</style>
</head>
<body>
<h2>SCHOOL NAME HERE</h2>
<h3>Class: '.($class_filter ?: 'All Classes').'</h3>
<h3>Exam: '.($exam_name ?: 'All Exams').' | Term: '.($term_name ?: 'All Terms').' | Year: '.$year.'</h3>
<table>
<tr>
    <th>Student Name</th>
    <th>Class</th>';

foreach($subjects as $sub_name){
    $html .= '<th>'.$sub_name.'</th>';
}

$html .= '</tr>';

// Table Data
while($row = mysqli_fetch_assoc($result)){
    $html .= '<tr>';
    $html .= '<td>'.$row['fullname'].'</td>';
    $html .= '<td>'.$row['student_class'].'</td>';
    foreach(array_keys($subjects) as $sub){
        $html .= '<td>'.($row[$sub] ?? '-').'</td>';
    }
    $html .= '</tr>';
}

$html .= '</table>
</body>
</html>';

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output PDF to browser
$dompdf->stream("Class_Marks_".$class_filter."_".$exam_name.".pdf", ["Attachment" => false]);
exit;
?>
