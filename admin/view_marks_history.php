<?php
session_start();
include 'sidebar.php';
include 'db.php'; // DB connection

// Filters
$admission_no = $_GET['admission_no'] ?? null;
$term_id = $_POST['term_id'] ?? $_GET['term_id'] ?? '';
$year = $_POST['year'] ?? date('Y');

if (!$admission_no || !$term_id) {
    die("Missing student or term information.");
}

// Fetch student info
$student_res = mysqli_query($conn, "SELECT fullname, class FROM students WHERE admission_no='$admission_no'");
$student = mysqli_fetch_assoc($student_res);

// Fetch term info
$term_res = mysqli_query($conn, "SELECT term_name FROM terms WHERE id='$term_id'");
$term_name = mysqli_fetch_assoc($term_res)['term_name'];

// Exam types
$exam_types = ['Opener Exam','Midterm Exam','Endterm Exam'];

// Subjects
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Marks History</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f2ff; margin-left:220px; padding:20px; }
.container { max-width:900px; margin:auto; background:white; padding:20px; border-radius:12px; box-shadow:0 0 10px rgba(128,0,128,0.2); }
h2 { color:purple; text-align:center; }
form { text-align:center; margin-bottom:15px; }
input[type=text], select { padding:6px; margin:0 5px; border-radius:5px; border:1px solid #ccc; }
.btn { background:purple; color:white; padding:6px 12px; border:none; border-radius:5px; cursor:pointer; margin-left:5px; }
.btn:hover { background:orange; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th, td { border:1px solid purple; padding:10px; text-align:center; }
th { background-color:purple; color:white; }
tr:nth-child(even){ background-color:#f9f9f9; }
.print-btn { background:orange; color:white; padding:6px 12px; border:none; border-radius:5px; cursor:pointer; margin-right:5px; }
.print-btn:hover { background:purple; }
.save-btn { background:green; color:white; padding:6px 12px; border:none; border-radius:5px; cursor:pointer; }
.save-btn:hover { background:purple; }
</style>
<script>
function printPage(){ window.print(); }
function savePDF(){
    alert("Saving as PDF will be implemented via browser's print-to-PDF option.");
}
</script>
</head>
<body>
<div class="container">
<h2>Marks History: <?php echo htmlspecialchars($student['fullname']); ?></h2>

<form method="post">
    <label>Year:</label>
    <select name="year" required>
        <option value="<?php echo date('Y'); ?>" <?php if($year==date('Y')) echo 'selected'; ?>><?php echo date('Y'); ?></option>
        <option value="<?php echo date('Y')-1; ?>" <?php if($year==date('Y')-1) echo 'selected'; ?>><?php echo date('Y')-1; ?></option>
        <option value="<?php echo date('Y')-2; ?>" <?php if($year==date('Y')-2) echo 'selected'; ?>><?php echo date('Y')-2; ?></option>
    </select>

    <label>Term:</label>
    <select name="term_id" required>
        <?php 
        $terms_res = mysqli_query($conn, "SELECT * FROM terms ORDER BY id ASC");
        while($term = mysqli_fetch_assoc($terms_res)):
        ?>
        <option value="<?php echo $term['id']; ?>" <?php if($term_id==$term['id']) echo 'selected'; ?>>
            <?php echo $term['term_name']; ?>
        </option>
        <?php endwhile; ?>
    </select>

    <button type="submit" class="btn">Filter</button>
</form>

<p style="text-align:center; color:orange; font-weight:bold;">
    Showing results for: <?php echo htmlspecialchars($term_name).' | '.$year; ?>
</p>

<div style="text-align:right; margin-bottom:10px;">
    <button class="print-btn" onclick="printPage()">Print</button>
<button class="save-btn" onclick="savePDF()">Save PDF</button>

<script>
function savePDF() {
    // Replace these with actual values from PHP
    var admission_no = "<?php echo $admission_no; ?>";
    var term_id = "<?php echo $term_id; ?>";

    // Open the save_marks.php in a new tab for download
    var url = 'save_marks.php?admission_no=' + admission_no + '&term_id=' + term_id;
    window.open(url, '_blank');
}
</script>

<style>
.save-btn {
    background: purple;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    float:right;
    margin-bottom: 10px;
}
.save-btn:hover {
    background: orange;
}
</style>

</div>

<table>
<tr>
    <th>Subject</th>
    <?php foreach($exam_types as $exam): ?>
        <th><?php echo $exam; ?></th>
    <?php endforeach; ?>
</tr>

<?php foreach($subjects as $sub_key=>$sub_label): ?>
<tr>
    <td><?php echo $sub_label; ?></td>
    <?php 
    foreach($exam_types as $exam_name):
        $marks = get_marks($conn, $admission_no, $exam_name, $term_id);
        echo "<td>".($marks[$sub_key] ?? '-')."</td>";
    endforeach;
    ?>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
