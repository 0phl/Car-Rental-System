<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
require_once '../config/database.php';
require_once('../tcpdf/tcpdf.php');

date_default_timezone_set('Asia/Manila');

// Get all bookings
$stmt = $pdo->prepare("SELECT b.*, c.brand, c.model, c.cost_per_day 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    ORDER BY b.created_at DESC");
$stmt->execute();
$bookings = $stmt->fetchAll();

// Create PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('The DGMT');
$pdf->SetAuthor('The DGMT');
$pdf->SetTitle('All Bookings Report');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Add font that supports the peso symbol
$pdf->SetFont('helvetica', '', 11);

// CSS styles
$html = '
<style>
    h1 { 
        color: #1a5f7a;
        font-size: 26pt;
        margin-bottom: 20px;
    }
    .header-info {
        color: #666;
        font-size: 11pt;
        margin-bottom: 30px;
        text-align: right;
    }
    .report-table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        font-size: 11pt;
    }
    .report-table th {
        background-color: #1a5f7a;
        color: white;
        font-weight: bold;
        padding: 12px 10px;
        border-bottom: 2px solid #0e4f68;
        font-size: 11pt;
    }
    .report-table th.text-left { text-align: left; }
    .report-table th.text-center { text-align: center; }
    .report-table th.text-right { text-align: right; }
    .report-table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .report-table tbody tr:first-child td {
        padding-top: 15px;
    }
    .report-table thead tr th {
        padding-bottom: 15px;
    }
    .report-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .status-pending { color: #f59e0b; }
    .status-confirmed { color: #3b82f6; }
    .status-completed { color: #10b981; }
    .status-cancelled { color: #ef4444; }
    .summary-box {
        margin-top: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        font-size: 11pt;
    }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
</style>';

// Add header with company info and date
$html .= '
<table width="100%" style="margin-bottom: 30px;">
    <tr>
        <td width="50%" style="padding-bottom: 25px;">
            <h1 style="margin-bottom: 10px;">DGMT Car Rental</h1>
            <div style="color: #666; font-size: 11pt; line-height: 1.5;">
                Niog Panapaan 2<br>
                Bacoor City, Cavite 4102<br>
                Phone: (0917) 810-1111<br>
                Email: dgmt@carrental.com<br>&nbsp;
            </div>
        </td>
        <td width="50%" align="right">
            <div class="header-info">
                <strong>Bookings Report</strong><br>
                Generated on: ' . date('F d, Y h:i A') . ' PHT<br>
                Total Bookings: ' . count($bookings) . '
            </div>
        </td>
    </tr>
</table>';

// Start table
$html .= '<table class="report-table" cellpadding="5">
    <thead>
        <tr style="background-color: #1a5f7a; color: white;">
            <th width="12%" style="text-align: left;">Reference No.</th>
            <th width="13%" style="text-align: left;">Client Name</th>
            <th width="12%" style="text-align: left;">Car</th>
            <th width="12%" style="text-align: left;">Pickup Date</th>
            <th width="15%" style="text-align: left;">Return Date</th>
            <th width="8%" style="text-align: center;">Duration</th>
            <th width="15%" style="text-align: right;">Total Cost</th>
            <th width="14%" style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>';

$total_revenue = 0;
$status_counts = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];

foreach($bookings as $booking) {
    $pickup = new DateTime($booking['pickup_datetime']);
    $return = new DateTime($booking['return_datetime']);
    $duration = $pickup->diff($return)->days;
    $total_cost = $duration * $booking['cost_per_day'];
    $total_revenue += $total_cost;
    $status_counts[$booking['status']]++;
    
    $html .= '<tr>
        <td style="text-align: left; white-space: nowrap;">'.$booking['reference_no'].'</td>
        <td style="text-align: left;">'.$booking['client_name'].'</td>
        <td style="text-align: left;">'.$booking['brand'].' '.$booking['model'].'</td>
        <td style="text-align: left;">'.$pickup->format('M d, Y').'</td>
        <td style="text-align: left;">'.$return->format('M d, Y').'</td>
        <td style="text-align: center;">'.$duration.' days</td>
        <td style="text-align: right;">PHP '.number_format($total_cost, 2).'</td>
        <td style="text-align: center;" class="status-'.$booking['status'].'">'.ucfirst($booking['status']).'</td>
    </tr>';
}

$html .= '</tbody></table>';

// Add summary section
$html .= '
<div class="summary-box">
    <h3 style="color: #1a5f7a; margin-bottom: 10px;">Booking Summary</h3>
    <table width="100%">
        <tr>
            <td width="50%">
                <strong>Total Revenue:</strong> PHP '.number_format($total_revenue, 2).'<br>
                <strong>Total Bookings:</strong> '.count($bookings).'
            </td>
            <td width="50%">
                <strong>Status Breakdown:</strong><br>
                Pending: '.$status_counts['pending'].'<br>
                Confirmed: '.$status_counts['confirmed'].'<br>
                Completed: '.$status_counts['completed'].'<br>
                Cancelled: '.$status_counts['cancelled'].'
            </td>
        </tr>
    </table>
</div>';

// Clean any output before sending PDF
ob_end_clean();

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('DGMT_Bookings_Report.pdf', 'I');
exit; 