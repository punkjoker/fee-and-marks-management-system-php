<?php
session_start();
include 'sidebar.php';
include 'db.php'; // Database connection

// Handle filters
$year = $_POST['year'] ?? date('Y');
$term_id = $_POST['term_id'] ?? '';
$exam_id = $_POST['exam_id'] ?? '';
$class_filter = $_POST['class'] ?? '';
$search = $_POST['search'] ?? '';

// Fetch terms, exams, and classes for filter dropdowns
$terms_res = mysqli_query($conn, "SELECT * FROM terms ORDER BY id ASC");
$exams_res = mysqli_query($conn, "SELECT * FROM exam_sessions ORDER BY id ASC");
$classes_res = mysqli_query($conn, "SELECT DISTINCT class FROM students ORDER BY class ASC");

// Build query
// Build query
$query = "SELECT s.*, s.class AS student_class, m.* 
          FROM students s 
          LEFT JOIN marks m ON s.admission_no = m.admission_no";

// Always filter active students
$conditions = ["s.status='active'"];

// Add dynamic filters
if($term_id)    $conditions[] = "(m.term_id='$term_id' OR m.term_id IS NULL)";
if($exam_id)    $conditions[] = "(m.exam_id='$exam_id' OR m.exam_id IS NULL)";
if($class_filter) $conditions[] = "s.class='$class_filter'";
if($search) $conditions[] = "s.fullname LIKE '%$search%'";

// Combine conditions
if(!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY s.fullname ASC";

$result = mysqli_query($conn, $query);


// Subjects
$subjects = ['number_work'=>'Number Work', 'language'=>'Language', 'environment'=>'Environment', 'psychomotor'=>'Psychomotor', 'religious'=>'Religious', 'literacy'=>'Literacy'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Marks History</title>
<style>
    body { font-family: Arial, sans-serif; background-color:#f4f2ff; margin-left:220px; padding:20px; }
    .container { max-width:1000px; margin:auto; background:white; padding:20px; border-radius:12px; box-shadow:0 0 10px rgba(128,0,128,0.2); }
    h2 { color:purple; text-align:center; }
    form { text-align:center; margin-bottom:20px; }
    input[type=text], select { padding:6px; margin:0 5px; border-radius:5px; border:1px solid #ccc; }
    .btn { background:purple; color:white; padding:6px 12px; border:none; border-radius:5px; cursor:pointer; margin-left:5px; }
    .btn:hover { background:orange; }
    table { width:100%; border-collapse: collapse; margin-top:15px; }
    th, td { border:1px solid purple; padding:8px; text-align:center; }
    th { background-color:purple; color:white; }
    tr:nth-child(even) { background-color:#f9f9f9; }
    .view-btn { background:orange; color:white; padding:5px 10px; border:none; border-radius:4px; cursor:pointer; }
    .view-btn:hover { background:purple; }
</style>
</head>
<body>
<div class="container">
<h2>View Marks History</h2>

<form method="post">
    <input type="text" name="search" placeholder="Search by student name" value="<?php echo htmlspecialchars($search); ?>">
    <select name="year">
        <option value="<?php echo date('Y'); ?>" <?php if($year==date('Y')) echo 'selected'; ?>><?php echo date('Y'); ?></option>
        <option value="<?php echo date('Y')-1; ?>" <?php if($year==date('Y')-1) echo 'selected'; ?>><?php echo date('Y')-1; ?></option>
        <option value="<?php echo date('Y')-2; ?>" <?php if($year==date('Y')-2) echo 'selected'; ?>><?php echo date('Y')-2; ?></option>
    </select>
    <select name="term_id">
        <option value="">-- Select Term --</option>
        <?php while($term = mysqli_fetch_assoc($terms_res)): ?>
            <option value="<?php echo $term['id']; ?>" <?php if($term_id==$term['id']) echo 'selected'; ?>><?php echo $term['term_name']; ?></option>
        <?php endwhile; ?>
    </select>
    <select name="exam_id">
        <option value="">-- Select Exam --</option>
        <?php while($exam = mysqli_fetch_assoc($exams_res)): ?>
            <option value="<?php echo $exam['id']; ?>" <?php if($exam_id==$exam['id']) echo 'selected'; ?>><?php echo $exam['exam_name']; ?></option>
        <?php endwhile; ?>
    </select>
    <?php
$classes = [];
while($cls = mysqli_fetch_assoc($classes_res)){
    $classes[] = $cls['class'];
}
?>

    <select name="class">
    <option value="">-- Select Class --</option>
    <?php foreach($classes as $cls_name): ?>
        <option value="<?php echo $cls_name; ?>" <?php if($class_filter==$cls_name) echo 'selected'; ?>>
            <?php echo $cls_name; ?>
        </option>
    <?php endforeach; ?>
</select>


    <button type="submit" class="btn">Filter</button>
</form>

<!-- Marks Table -->
<table>
<tr>
    <th>Student Name</th>
    <th>Class</th>
    <?php foreach($subjects as $sub_name): ?>
        <th><?php echo $sub_name; ?></th>
    <?php endforeach; ?>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo $row['fullname']; ?></td>
    <td><?php echo $row['student_class']; ?></td>
    <?php foreach(array_keys($subjects) as $sub): ?>
        <td><?php echo $row[$sub] ?? '-'; ?></td>
    <?php endforeach; ?>
    <td>
        <a href="view_marks_history.php?admission_no=<?php echo $row['admission_no']; ?>&exam_id=<?php echo $row['exam_id'] ?? ''; ?>&term_id=<?php echo $row['term_id'] ?? ''; ?>">
            <button class="view-btn">View marks History</button>
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<!-- Save Button -->
<form method="post" action="save_class_marks.php">
    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
    <input type="hidden" name="term_id" value="<?php echo htmlspecialchars($term_id); ?>">
    <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($exam_id); ?>">
    <input type="hidden" name="class" value="<?php echo htmlspecialchars($class_filter); ?>">
    <button type="submit" class="btn" style="margin-top:15px;">Save Marks as PDF</button>
</form>

</div>
</body>
</html>
