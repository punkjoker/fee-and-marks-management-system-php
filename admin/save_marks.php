<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Dompdf autoload

use Dompdf\Dompdf;

// Get parameters
$admission_no = $_GET['admission_no'] ?? null;
$term_id = $_GET['term_id'] ?? null;

if (!$admission_no || !$term_id) {
    die("Missing student or term information.");
}

// Fetch student info
$student_res = mysqli_query($conn, "SELECT fullname, class FROM students WHERE admission_no='$admission_no'");
$student = mysqli_fetch_assoc($student_res);

// Fetch term info
$term_res = mysqli_query($conn, "SELECT term_name FROM terms WHERE id='$term_id'");
$term_name = mysqli_fetch_assoc($term_res)['term_name'];

// Exam types and subjects
$exam_types = ['Opener Exam','Midterm Exam','Endterm Exam'];
$subjects = [
    'number_work'=>'Number Work', 
    'language'=>'Language', 
    'environment'=>'Environment', 
    'psychomotor'=>'Psychomotor', 
    'religious'=>'Religious', 
    'literacy'=>'Literacy'
];

// Function to get marks
function get_marks($conn, $admission_no, $exam_name, $term_id){
    $res = mysqli_query($conn, "SELECT * FROM exam_sessions WHERE exam_name='$exam_name' AND term_id='$term_id'");
    if(mysqli_num_rows($res)==0) return null;
    $exam = mysqli_fetch_assoc($res);
    $exam_id = $exam['id'];

    $marks_res = mysqli_query($conn, "SELECT * FROM marks WHERE admission_no='$admission_no' AND exam_id='$exam_id' AND term_id='$term_id'");
    if(mysqli_num_rows($marks_res)==0) return null;
    return mysqli_fetch_assoc($marks_res);
}

// Build HTML for PDF
$html = '
<h1 style="text-align:center;color:purple;">MATENDENI ECD</h1>
<p style="text-align:center;color:orange;font-weight:bold;">P.O BOX 205 EMBU</p>
<h2 style="text-align:center;color:purple;">Marks Report</h2>
<p style="text-align:center;color:orange;font-weight:bold;">Student: '.$student['fullname'].' | Class: '.$student['class'].' | Term: '.$term_name.'</p>
<table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse;">
<tr style="background-color:purple;color:white;">
    <th>Subject</th>';

foreach($exam_types as $exam){
    $html .= '<th>'.$exam.'</th>';
}
$html .= '</tr>';

foreach($subjects as $key=>$label){
    $html .= '<tr>';
    $html .= '<td>'.$label.'</td>';
    foreach($exam_types as $exam){
        $marks = get_marks($conn, $admission_no, $exam, $term_id);
        $html .= '<td>'.($marks[$key] ?? '-').'</td>';
    }
    $html .= '</tr>';
}

$html .= '</table>';

// Add key/legend below table
$html .= '
<h3 style="color:purple; margin-top:20px;">Key / Rating:</h3>
<table border="1" cellpadding="6" cellspacing="0" width="50%" style="border-collapse:collapse; margin-top:5px;">
<tr style="background-color:purple;color:white;">
    <th>Rating</th>
    <th>Meaning</th>
</tr>
<tr><td>5</td><td>Exceeding Expectations</td></tr>
<tr><td>4</td><td>Very Good</td></tr>
<tr><td>3</td><td>Good</td></tr>
<tr><td>2</td><td>Fair</td></tr>
<tr><td>1</td><td>Needs Improvement</td></tr>
</table>';

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF to browser for download
$filename = $student['fullname'].'_Marks_'.$term_name.'.pdf';
$dompdf->stream($filename, array("Attachment" => true));
