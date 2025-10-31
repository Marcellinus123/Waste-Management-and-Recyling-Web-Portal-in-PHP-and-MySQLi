<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once('db.php');
include("../libs/tcpdf-main/tcpdf.php");

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin'])) {
    header("Location: userlogin");
    exit();
}

if (!isset($_SESSION['usertype'])) {
    header('Location: userlogin');
    exit();
}

if ($_SESSION['usertype'] !== 'admin_user') {
    header('Location: userlogin');
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$bookings = [];
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$filterService = isset($_GET['service']) ? $_GET['service'] : 'all';
$filterDateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filterDateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Handle booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Approve Booking
        if (isset($_POST['approve_booking'])) {
            $bookingId = $_POST['booking_id'];
            
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
            $stmt->execute([$bookingId]);
            
            $toastMessage = "Booking approved successfully";
            $toastType = "success";
        }
        
        // Reject Booking
        if (isset($_POST['reject_booking'])) {
            $bookingId = $_POST['booking_id'];
            
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$bookingId]);
            
            $toastMessage = "Booking rejected successfully";
            $toastType = "success";
        }
        
        // Mark as Completed (only if both signatures exist)
        if (isset($_POST['complete_booking'])) {
            $bookingId = $_POST['booking_id'];
            
            // Check if both signatures exist
            $stmt = $pdo->prepare("SELECT * FROM booking_agreements WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            $agreement = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($agreement && $agreement['waste_user_sign'] && $agreement['waste_driver_sign']) {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
                $stmt->execute([$bookingId]);
                
                $toastMessage = "Booking marked as completed";
                $toastType = "success";
            } else {
                throw new Exception("Cannot complete booking - both user and driver signatures are required");
            }
        }
        
        // Generate PDF
        if (isset($_POST['generate_pdf'])) {
            $bookingId = $_POST['booking_id'];
            
            // Get booking details
            $stmt = $pdo->prepare("SELECT b.*, u.first_name, u.last_name, u.email, u.phone 
                                 FROM bookings b
                                 JOIN users u ON b.user_id = u.user_id
                                 WHERE b.id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($booking) {
                // Create new PDF document
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                
                // Set document information
                $pdf->SetCreator('Waste Wizard');
                $pdf->SetAuthor('Waste Wizard');
                $pdf->SetTitle('Booking #' . $booking['booking_code']);
                $pdf->SetSubject('Booking Details');
                
                // Add a page
                $pdf->AddPage();
                
                // Set font
                $pdf->SetFont('helvetica', 'B', 16);
                $pdf->Cell(0, 10, 'Booking Details', 0, 1, 'C');
                $pdf->SetFont('helvetica', '', 12);
                $pdf->Ln(10);
                
                // Booking information
                $html = '
                <table border="1" cellpadding="5">
                    <tr>
                        <th width="30%">Booking Code</th>
                        <td width="70%">' . $booking['booking_code'] . '</td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td>' . $booking['first_name'] . ' ' . $booking['last_name'] . '</td>
                    </tr>
                    <tr>
                        <th>Customer Email</th>
                        <td>' . $booking['email'] . '</td>
                    </tr>
                    <tr>
                        <th>Customer Phone</th>
                        <td>' . $booking['phone'] . '</td>
                    </tr>
                    <tr>
                        <th>Service Type</th>
                        <td>' . ucfirst($booking['service_type']) . '</td>
                    </tr>
                    <tr>
                        <th>Collection Date</th>
                        <td>' . date('F j, Y', strtotime($booking['collection_date'])) . '</td>
                    </tr>
                    <tr>
                        <th>Time Slot</th>
                        <td>' . ucfirst($booking['time_slot']) . '</td>
                    </tr>
                    <tr>
                        <th>Estimated Weight</th>
                        <td>' . $booking['estimated_weight'] . '</td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td>' . $booking['location'] . '</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>GHS ' . number_format($booking['amount'], 2) . '</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>' . ucfirst(str_replace('_', ' ', $booking['status'])) . '</td>
                    </tr>
                </table>';
                
                $pdf->writeHTML($html, true, false, true, false, '');
                
                // Check if agreement exists and add to PDF
                $stmt = $pdo->prepare("SELECT * FROM booking_agreements WHERE booking_id = ?");
                $stmt->execute([$bookingId]);
                $agreement = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($agreement) {
                    $pdf->Ln(10);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->Cell(0, 10, 'Booking Agreement', 0, 1);
                    $pdf->SetFont('helvetica', '', 12);
                    
                    $agreementHtml = '
                    <table border="1" cellpadding="5">
                        <tr>
                            <th width="30%">User Signed</th>
                            <td width="70%">' . ($agreement['waste_user_sign'] ? 'Yes' : 'No') . '</td>
                        </tr>
                        <tr>
                            <th>Driver Signed</th>
                            <td>' . ($agreement['waste_driver_sign'] ? 'Yes' : 'No') . '</td>
                        </tr>
                        <tr>
                            <th>User Signed Date</th>
                            <td>' . ($agreement['date_user_sign'] ? date('F j, Y H:i', strtotime($agreement['date_user_sign'])) : 'Not signed') . '</td>
                        </tr>
                        <tr>
                            <th>Driver Signed Date</th>
                            <td>' . ($agreement['date_driver_sign'] ? date('F j, Y H:i', strtotime($agreement['date_driver_sign'])) : 'Not signed') . '</td>
                        </tr>
                    </table>';
                    
                    $pdf->writeHTML($agreementHtml, true, false, true, false, '');
                }
                
                // Output PDF as download
                $pdf->Output('booking_' . $booking['booking_code'] . '.pdf', 'D');
                exit();
            }
        }
        
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get all bookings with filters
try {
    $query = "SELECT b.*, u.first_name, u.last_name, u.email, u.phone, 
                     a.waste_user_sign, a.waste_driver_sign
              FROM bookings b
              JOIN users u ON b.user_id = u.user_id
              LEFT JOIN booking_agreements a ON b.id = a.booking_id
              WHERE 1=1";
    
    $params = [];
    
    // Apply status filter
    if ($filterStatus !== 'all') {
        $query .= " AND b.status = ?";
        $params[] = $filterStatus;
    }
    
    // Apply service type filter
    if ($filterService !== 'all') {
        $query .= " AND b.service_type = ?";
        $params[] = $filterService;
    }
    
    // Apply date range filter
    if ($filterDateFrom) {
        $query .= " AND b.collection_date >= ?";
        $params[] = $filterDateFrom;
    }
    
    if ($filterDateTo) {
        $query .= " AND b.collection_date <= ?";
        $params[] = $filterDateTo;
    }
    
    $query .= " ORDER BY b.collection_date DESC, b.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $toastMessage = "Error loading bookings: " . $e->getMessage();
    $toastType = "error";
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
                <h1><i class="bi bi-calendar-check"></i> Manage Bookings</h1>
                <p>View, approve, and manage waste collection bookings</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Bookings</a></li>
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
                        <h3 class="title">Filter Bookings</h3>
                    </div>
                    <div class="tile-body">
                        <form method="GET" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                        <option value="not_approved" <?php echo $filterStatus === 'not_approved' ? 'selected' : ''; ?>>Not Approved</option>
                                        <option value="approved" <?php echo $filterStatus === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="completed" <?php echo $filterStatus === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="rejected" <?php echo $filterStatus === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        <option value="cancelled" <?php echo $filterStatus === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="service">Service Type</label>
                                    <select class="form-control" id="service" name="service">
                                        <option value="all" <?php echo $filterService === 'all' ? 'selected' : ''; ?>>All Services</option>
                                        <option value="general" <?php echo $filterService === 'general' ? 'selected' : ''; ?>>General Waste</option>
                                        <option value="recycling" <?php echo $filterService === 'recycling' ? 'selected' : ''; ?>>Recycling</option>
                                        <option value="waste_collection" <?php echo $filterService === 'waste_collection' ? 'selected' : ''; ?>>Waste Collection</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from">From Date</label>
                                    <input class="form-control" type="date" id="date_from" name="date_from" 
                                        value="<?php echo htmlspecialchars($filterDateFrom); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to">To Date</label>
                                    <input class="form-control" type="date" id="date_to" name="date_to" 
                                        value="<?php echo htmlspecialchars($filterDateTo); ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="bookings" class="btn btn-secondary">Reset Filters</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Booking List</h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th>Booking Code</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Collection Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Agreement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['booking_code']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?><br>
                                        <small><?php echo htmlspecialchars($booking['phone']); ?></small>
                                    </td>
                                    <td><?php echo ucfirst(htmlspecialchars($booking['service_type'])); ?></td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($booking['collection_date'])); ?><br>
                                        <small><?php echo ucfirst(htmlspecialchars($booking['time_slot'])); ?></small>
                                    </td>
                                    <td>GHS <?php echo number_format($booking['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($booking['status']) {
                                                case 'completed': echo 'success'; break;
                                                case 'approved': echo 'primary'; break;
                                                case 'not_approved': echo 'warning'; break;
                                                case 'rejected': echo 'danger'; break;
                                                case 'cancelled': echo 'secondary'; break;
                                                default: echo 'info';
                                            }
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($booking['waste_user_sign'] && $booking['waste_driver_sign']): ?>
                                        <span class="badge bg-success">Both Signed</span>
                                        <?php elseif ($booking['waste_user_sign'] || $booking['waste_driver_sign']): ?>
                                        <span class="badge bg-warning">Partially Signed</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Not Signed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                
                                                <?php if ($booking['status'] === 'not_approved'): ?>
                                                <button type="submit" name="approve_booking" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                                <button type="submit" name="reject_booking" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                                <?php elseif ($booking['status'] === 'approved' && $booking['waste_user_sign'] && $booking['waste_driver_sign']): ?>
                                                <button type="submit" name="complete_booking" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-check-all"></i> Complete
                                                </button>
                                                <?php endif; ?>
                                                
                                                <button type="submit" name="generate_pdf" class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No bookings found</td>
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
            
            $('#date_from, #date_to').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        });
    </script>
</body>
</html>