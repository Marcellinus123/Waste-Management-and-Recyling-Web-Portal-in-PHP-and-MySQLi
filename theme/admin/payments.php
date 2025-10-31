<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once('db.php');
require '../libs/Spreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;

// Check admin authentication
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'admin_user') {
    header("Location: userlogin");
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$payments = [];
$filterDateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filterDateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$filterService = isset($_GET['service']) ? $_GET['service'] : 'all';

// Handle export requests
if (isset($_GET['export'])) {
    try {
        // Get filtered payments data
        $payments = getFilteredBookings($pdo, $filterDateFrom, $filterDateTo, $filterStatus, $filterService);
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Waste Wizard Admin")
            ->setTitle("Payments Report")
            ->setDescription("Payment records from Waste Wizard System");
        
        // Add headers
        $sheet->setCellValue('A1', 'Booking Code')
              ->setCellValue('B1', 'Customer')
              ->setCellValue('C1', 'Service Type')
              ->setCellValue('D1', 'Amount (GHS)')
              ->setCellValue('E1', 'Payment Status')
              ->setCellValue('F1', 'Collection Date')
              ->setCellValue('G1', 'Payment Date');
        
        // Add data
        $row = 2;
        foreach ($payments as $payment) {
            $sheet->setCellValue('A' . $row, $payment['booking_code'])
                  ->setCellValue('B' . $row, $payment['customer_name'])
                  ->setCellValue('C' . $row, ucfirst($payment['service_type']))
                  ->setCellValue('D' . $row, $payment['amount'])
                  ->setCellValue('E' . $row, ucfirst($payment['status']))
                  ->setCellValue('F' . $row, date('M j, Y', strtotime($payment['collection_date'])))
                  ->setCellValue('G' . $row, date('M j, Y', strtotime($payment['created_at'])));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Style the header row
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        
        // Export based on requested format
        $format = $_GET['export'];
        $filename = 'payments_export_' . date('Ymd_His');
        
        if ($format === 'excel') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
            
        } elseif ($format === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            header('Cache-Control: max-age=0');
            
            $writer = new Dompdf($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        
    } catch (Exception $e) {
        $toastMessage = "Export failed: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get filtered payments for display
try {
    $payments = getFilteredBookings($pdo, $filterDateFrom, $filterDateTo, $filterStatus, $filterService);
} catch (Exception $e) {
    $toastMessage = "Error loading payments: " . $e->getMessage();
    $toastType = "error";
}

/**
 * Helper function to get filtered bookings (payments)
 */
function getFilteredBookings($pdo, $dateFrom, $dateTo, $status, $service) {
    $query = "SELECT 
                b.booking_code,
                CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                b.service_type,
                b.amount,
                b.status,
                b.collection_date,
                b.created_at,
                b.id as booking_id
              FROM bookings b
              JOIN users u ON b.user_id = u.user_id
              WHERE b.amount > 0"; // Only bookings with payments
    
    $params = [];
    
    // Apply date filters
    if (!empty($dateFrom)) {
        $query .= " AND DATE(b.created_at) >= ?";
        $params[] = $dateFrom;
    }
    if (!empty($dateTo)) {
        $query .= " AND DATE(b.created_at) <= ?";
        $params[] = $dateTo;
    }
    
    // Apply status filter
    if ($status !== 'all') {
        $query .= " AND b.status = ?";
        $params[] = $status;
    }
    
    // Apply service type filter
    if ($service !== 'all') {
        $query .= " AND b.service_type = ?";
        $params[] = $service;
    }
    
    $query .= " ORDER BY b.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("head.php")?>
<body class="app sidebar-mini">
    <!-- Navbar-->
    <?php include("header.php")?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include("aside.php")?>
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="bi bi-credit-card-fill"></i> Payment Records</h1>
                <p>View and export all completed bookings (payments)</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Payments</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
        <?php if ($toastMessage): ?>
        <div class="alert alert-<?php echo $toastType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo $toastMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Filter Payments</h3>
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>">Excel</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tile-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($filterDateFrom); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($filterDateTo); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                    <option value="completed" <?php echo $filterStatus === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="approved" <?php echo $filterStatus === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="not_approved" <?php echo $filterStatus === 'not_approved' ? 'selected' : ''; ?>>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="service" class="form-label">Service</label>
                                <select class="form-select" id="service" name="service">
                                    <option value="all" <?php echo $filterService === 'all' ? 'selected' : ''; ?>>All Services</option>
                                    <option value="general" <?php echo $filterService === 'general' ? 'selected' : ''; ?>>General Waste</option>
                                    <option value="recycling" <?php echo $filterService === 'recycling' ? 'selected' : ''; ?>>Recycling</option>
                                    <option value="waste_collection" <?php echo $filterService === 'waste_collection' ? 'selected' : ''; ?>>Waste Collection</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel-fill"></i> Apply Filters
                                </button>
                                <a href="payments" class="btn btn-secondary ms-2">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Payment Records (Completed Bookings)</h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="paymentsTable">
                            <thead>
                                <tr>
                                    <th>Booking Code</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount (GHS)</th>
                                    <th>Status</th>
                                    <th>Collection Date</th>
                                    <th>Payment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['booking_code']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($payment['service_type'])); ?></td>
                                    <td class="text-end"><?php echo number_format($payment['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($payment['status']) {
                                                case 'completed': echo 'success'; break;
                                                case 'approved': echo 'primary'; break;
                                                case 'not_approved': echo 'warning'; break;
                                                case 'rejected': echo 'danger'; break;
                                                case 'cancelled': echo 'secondary'; break;
                                                default: echo 'info';
                                            }
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $payment['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($payment['collection_date'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($payment['created_at'])); ?></td>
                                    <td>
                                        <a href="ubookings?id=<?php echo htmlspecialchars($payment['booking_id']); ?>" class="btn btn-sm btn-primary" title="View Booking">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No payment records found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <!-- Page specific javascripts-->
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            $('#paymentsTable').DataTable({
                responsive: true,
                dom: '<"top"lf>rt<"bottom"ip>',
                order: [[6, 'desc']], 
                columnDefs: [
                    { orderable: false, targets: [7] }, 
                    { type: 'date', targets: [5, 6] } 
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search payments...",
                    lengthMenu: "Show _MENU_ payments per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ payments",
                    infoEmpty: "No payments available",
                    infoFiltered: "(filtered from _MAX_ total payments)"
                }
            });
        });
        
        function generateReceipt(bookingId) {

            //window.open('generate_receipt.php?booking_id=' + bookingId, '_blank');
        }
    </script>
</body>
</html>