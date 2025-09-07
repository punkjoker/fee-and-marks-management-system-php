<?php
session_start();
include 'db.php';

// check admin login
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$success = $error = "";

// Fetch available terms for dropdown
$termsResult = $conn->query("SELECT id, term_name, year FROM terms ORDER BY id DESC");

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $amount      = mysqli_real_escape_string($conn, $_POST['amount']);
    $person      = mysqli_real_escape_string($conn, $_POST['person']);
    $cheque      = mysqli_real_escape_string($conn, $_POST['cheque']);
    $term_id     = mysqli_real_escape_string($conn, $_POST['term_id']);

    if($description && $amount && $person && $term_id){
        $insert = $conn->query("INSERT INTO expenses (term_id, description, amount, person_responsible, cheque_number) 
                                VALUES ('$term_id', '$description', '$amount', '$person', '$cheque')");
        if($insert){
            $success = "Expense added successfully!";
        } else {
            $error = "Database error: ".$conn->error;
        }
    } else {
        $error = "Please fill all required fields.";
    }
}
?>

<?php include 'sidebar.php'; ?>
<div class="content">
    <h2>Add Expense</h2>

    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="" class="expense-form">
        <label>Description:</label><br>
        <input type="text" name="description" required><br><br>

        <label>Amount:</label><br>
        <input type="number" name="amount" step="0.01" required><br><br>

        <label>Person Responsible:</label><br>
        <input type="text" name="person" required><br><br>

        <label>Cheque Number (Optional):</label><br>
        <input type="text" name="cheque"><br><br>

        <label>Term:</label><br>
        <select name="term_id" required>
            <option value="">-- Select Term --</option>
            <?php while($term = $termsResult->fetch_assoc()) { ?>
                <option value="<?php echo $term['id']; ?>">
                    <?php echo $term['term_name']." ".$term['year']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <button type="submit">Add Expense</button>
    </form>
</div>

<style>
.content {
    text-align: center;
    color: #fff;
    padding: 40px 20px;
}

h2 {
    color: #FFA500;
    margin-bottom: 25px;
}

/* Success and error messages */
.success {
    color: #00FF00;
    margin-bottom: 15px;
}
.error {
    color: #FF6347;
    margin-bottom: 15px;
}

/* Expense form styling */
.expense-form {
    background: #4B0082;
    display: inline-block;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.3);
}

.expense-form input,
.expense-form select {
    width: 300px;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
    margin-bottom: 15px;
    font-size: 16px;
}

.expense-form input:focus,
.expense-form select:focus {
    border-color: #FFA500;
    box-shadow: 0 0 5px #FFA500;
}

.expense-form button {
    background: #FFA500;
    color: #4B0082;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.expense-form button:hover {
    background: #FF8C00;
    color: #fff;
}
</style>
