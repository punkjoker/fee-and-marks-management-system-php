<?php 
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/vendor/autoload.php'; // Dompdf autoload
use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admission_no'])) {
    require 'db.php'; // include DB connection
    $admission_no = $_POST['admission_no'];

    // Fetch student info
    $student_res = mysqli_query($conn, "SELECT fullname, class FROM students WHERE admission_no='$admission_no'");
    $student = mysqli_fetch_assoc($student_res);

    // Fetch latest balance from payments table
    $balance_res = mysqli_query($conn, "SELECT balance AS total_due FROM payments WHERE admission_no='$admission_no' ORDER BY payment_date DESC LIMIT 1");
    $balance = mysqli_fetch_assoc($balance_res);
    $total_due = $balance['total_due'] ?? 0;

    // Fetch payment history (join with terms for year & term name)
    $payments_res = mysqli_query($conn, "
        SELECT p.receipt_no, t.term_name, t.year, p.amount_paid, p.payment_date 
        FROM payments p 
        JOIN terms t ON p.term_id = t.id 
        WHERE p.admission_no='$admission_no'
        ORDER BY p.payment_date ASC
    ");

    // Build receipt HTML with styling
    $receiptHTML = '
    <div style="font-family: Arial, sans-serif; max-width:700px; margin:auto; padding:20px; border:2px solid purple; border-radius:12px; background:#fff;">
        <!-- Header with logo left and school info right -->
        <div style="width:100%; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <img src="file://'.__DIR__.'/logo.png" style="height:80px;>
            </div>
            <div style="text-align:right;">
                <h1 style="color:purple; margin:0;">MATENDENI ECD</h1>
                <p style="color:orange; font-weight:bold; margin:0;">P.O BOX 205 EMBU</p>
                <p style="color:purple; font-weight:bold; margin:0;">ACC NO: 01129407576700</p>
            </div>
        </div>

        <h2 style="text-align:center; color:purple; margin-top:20px;">Payment Receipt</h2>

        <!-- Student info -->
        <table style="width:100%; margin-top:20px; border-collapse: collapse;">
            <tr>
                <td style="padding:8px; border:1px solid purple; background:purple; color:white;"><strong>Student Name:</strong></td>
                <td style="padding:8px; border:1px solid purple;">'.$student['fullname'].'</td>
            </tr>
            <tr>
                <td style="padding:8px; border:1px solid purple; background:purple; color:white;"><strong>Class:</strong></td>
                <td style="padding:8px; border:1px solid purple;">'.$student['class'].'</td>
            </tr>
        </table>

        <!-- Total due -->
        <h3 style="margin-top:30px; color:purple;">Total Fees Due: Ksh '.number_format($total_due,2).'</h3>

        <!-- Payment history -->
        <h3 style="margin-top:20px; color:purple;">Payment History</h3>
        <table style="width:100%; border-collapse: collapse;">
            <tr style="background-color:purple; color:white;">
                <th style="padding:8px; border:1px solid purple;">Receipt No</th>
                <th style="padding:8px; border:1px solid purple;">Term</th>
                <th style="padding:8px; border:1px solid purple;">Year</th>
                <th style="padding:8px; border:1px solid purple;">Amount Paid (Ksh)</th>
                <th style="padding:8px; border:1px solid purple;">Date Paid</th>
            </tr>';

    while($payment = mysqli_fetch_assoc($payments_res)){
        $receiptHTML .= '<tr>
            <td style="padding:6px; border:1px solid purple;">'.$payment['receipt_no'].'</td>
            <td style="padding:6px; border:1px solid purple;">'.$payment['term_name'].'</td>
            <td style="padding:6px; border:1px solid purple;">'.$payment['year'].'</td>
            <td style="padding:6px; border:1px solid purple;">'.number_format($payment['amount_paid'],2).'</td>
            <td style="padding:6px; border:1px solid purple;">'.$payment['payment_date'].'</td>
        </tr>';
    }

    $receiptHTML .= '</table>

        <p style="margin-top:20px; color:purple; font-weight:bold;">Date: '.date("d-m-Y").'</p>
        <p style="margin-top:30px; color:orange; font-weight:bold; text-align:center;">Thank you for your payment!</p>
    </div>';

    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($receiptHTML);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = "Receipt_".$admission_no."_".date("Ymd_His").".pdf";
    $dompdf->stream($filename, ["Attachment" => true]);
    exit();

} else {
    echo "Invalid request.";
}
