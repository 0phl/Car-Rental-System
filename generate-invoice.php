<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
require_once 'config/database.php';
require_once('tcpdf/tcpdf.php');

if (!isset($_GET['ref'])) {
    die('Reference number is required');
}

$ref = $_GET['ref'];

// Get booking details
$stmt = $pdo->prepare("SELECT b.*, c.brand, c.model, c.image, c.cost_per_day 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    WHERE b.reference_no = ?");
$stmt->execute([$ref]);
$booking = $stmt->fetch();

if (!$booking) {
    die('Booking not found');
}

// Calculate rental duration and total cost
$pickup = new DateTime($booking['pickup_datetime']);
$return = new DateTime($booking['return_datetime']);
$duration = $pickup->diff($return)->days;
$total_cost = $duration * $booking['cost_per_day'];

// Create new PDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// Set document information
$pdf->SetCreator('The DGMT');
$pdf->SetAuthor('The DGMT');
$pdf->SetTitle('Invoice - ' . $ref);

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);

// Add a page
$pdf->AddPage();


// Set content
$html = <<<EOD
<style>
    h1 { color: #2563eb; font-size: 24pt; }
    .company-info { color: #666666; font-size: 10pt; line-height: 1.5; }
    .invoice-info { background-color: #f8fafc; padding: 10px; margin: 20px 0; border-radius: 5px; }
    .invoice-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    .invoice-table th { background-color: #2563eb; color: white; padding: 10px; }
    .invoice-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
    .total-row td { border-top: 2px solid #2563eb; font-weight: bold; }
    .notes { font-size: 9pt; color: #666666; margin-top: 30px; }
</style>

<table width="100%">
    <tr>
        <td width="50%">
            <h1>DGMT Car Rental</h1>
            <div class="company-info">
                Niog Panapaan 2<br>
                Bacoor City, Cavite 4102<br>
                Phone: (0917) 810-1111<br>
                Email: dgmt@carrental.com
            </div>
        </td>
        <td width="50%" align="right">
            <h2>INVOICE</h2>
            <div class="invoice-info">
                Reference No: {$booking['reference_no']}<br>
                Date: {$pickup->format('M d, Y')}<br>
                Status: {$booking['status']}
            </div>
        </td>
    </tr>
</table>

<div style="height: 20px;"></div>

<table width="100%">
    <tr>
        <td width="50%">
            <strong>BILLED TO</strong><br>
            {$booking['client_name']}<br>
            {$booking['email']}<br>
            {$booking['phone']}
        </td>
        <td width="50%">
            <strong>PICKUP / RETURN LOCATION</strong><br>
            {$booking['location']}
        </td>
    </tr>
</table>

<table class="invoice-table">
    <thead>
        <tr>
            <th width="35%">Car Rented</th>
            <th width="25%">Duration</th>
            <th width="20%">Rate</th>
            <th width="20%">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="35%">{$booking['brand']} {$booking['model']}</td>
            <td width="25%">{$duration} days</td>
            <td width="20%">PHP {$booking['cost_per_day']} / day</td>
            <td width="20%">PHP {$total_cost}</td>
        </tr>
        <tr class="total-row">
            <td colspan="3" align="right"><strong>Total</strong></td>
            <td width="20%"><strong>PHP {$total_cost}</strong></td>
        </tr>
    </tbody>
</table>

<div class="notes">
    <strong>Rental Period:</strong><br>
    Pickup: {$pickup->format('M d, Y h:i A')}<br>
    Return: {$return->format('M d, Y h:i A')}<br><br>
    
    <strong>Terms & Conditions:</strong><br>
    1. Payment is due upon vehicle pickup<br>
    2. Security deposit required<br>
    3. Valid driver's license required<br>
    4. Full insurance coverage included
</div>
EOD;

// Print content
$pdf->writeHTML($html, true, false, true, false, '');

// Before output
ob_end_clean(); 
// Output PDF
$pdf->Output('invoice-' . $ref . '.pdf', 'I');
exit;