<?php
session_start();
require_once('db.php');

// Check admin authentication
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'admin_user') {
    header("Location: userlogin");
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$bookings = [];
$selectedBooking = null;
$agreementStatus = null;
$canEndorse = false;

// Get bookings that have both signatures but aren't yet endorsed
try {
    $stmt = $pdo->prepare("SELECT b.* 
                          FROM bookings b
                          JOIN booking_agreements ba ON ba.booking_id = b.booking_code
                          WHERE ba.waste_user_sign IS NOT NULL 
                          AND ba.waste_driver_sign IS NOT NULL
                          AND ba.status = 'completed'
                          ORDER BY b.collection_date DESC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading bookings: " . $e->getMessage();
    $toastType = "danger";
}

// Handle viewing a specific booking
if (isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];
    
    try {
        // Get booking details
        $stmt = $pdo->prepare("SELECT b.* FROM bookings b
                              WHERE b.booking_code = ?");
        $stmt->execute([$bookingId]);
        $selectedBooking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($selectedBooking) {
            // Check agreement status
            $stmt = $pdo->prepare("SELECT * FROM booking_agreements
                                 WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            $agreement = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($agreement) {
                $agreementStatus = $agreement['status'];
                // Check if both parties have signed and it's ready for endorsement
                $canEndorse = ($agreement['waste_user_sign'] && $agreement['waste_driver_sign'] && $agreement['status'] === 'completed');
            }
        }
    } catch (Exception $e) {
        $toastMessage = "Error loading booking details: " . $e->getMessage();
        $toastType = "danger";
    }
}

// Handle agreement endorsement by admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['endorse_agreement'])) {
    $bookingId = $_POST['booking_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Verify the booking exists and has both signatures
        $stmt = $pdo->prepare("SELECT * FROM booking_agreements
                             WHERE booking_id = ? 
                             AND waste_user_sign IS NOT NULL
                             AND waste_driver_sign IS NOT NULL
                             AND status = 'completed'
                             FOR UPDATE");
        $stmt->execute([$bookingId]);
        $agreement = $stmt->fetch();
        
        if (!$agreement) {
            throw new Exception("Agreement not ready for endorsement - both parties must sign first");
        }
        
        // Update agreement status to endorsed
        $stmt = $pdo->prepare("UPDATE booking_agreements
                             SET status = 'endorsed'
                             WHERE booking_id = ?");
        $stmt->execute([$bookingId]);
        
        $pdo->commit();
        
        $_SESSION['toastMessage'] = "Agreement successfully endorsed!";
        $_SESSION['toastType'] = "success";
        
        // Redirect to prevent form resubmission
        header("Location: booking-agreement?booking_id=$bookingId");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['toastMessage'] = "Error: " . $e->getMessage();
        $_SESSION['toastType'] = "danger";
        header("Location: booking-agreement?booking_id=$bookingId");
        exit();
    }
}

// Check for session toast messages
if (isset($_SESSION['toastMessage'])) {
    $toastMessage = $_SESSION['toastMessage'];
    $toastType = $_SESSION['toastType'];
    unset($_SESSION['toastMessage']);
    unset($_SESSION['toastType']);
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
                <h1><i class="bi bi-file-earmark-check"></i> Booking Agreements</h1>
                <p>Review and endorse signed service agreements</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Bookings</a></li>
                <li class="breadcrumb-item"><a href="#">Agreements</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
        <?php if ($toastMessage): ?>
        <div class="alert alert-<?= $toastType ?> alert-dismissible fade show" role="alert">
            <?= $toastMessage ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            // Auto-dismiss alert after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        </script>
        <?php endif; ?>
        
        <div class="row">
            <!-- Bookings List -->
            <div class="col-md-4">
                <div class="tile">
                    <h3 class="tile-title">Ready for Endorsement</h3>
                    <div class="list-group">
                        <?php if (empty($bookings)): ?>
                            <div class="list-group-item">
                                <p class="mb-0 text-muted">No agreements ready for endorsement</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <a href="booking-agreement?booking_id=<?= htmlspecialchars($booking['booking_code']) ?>" 
                                   class="list-group-item list-group-item-action <?= $selectedBooking && $selectedBooking['booking_code'] === $booking['booking_code'] ? 'active' : '' ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">#<?= htmlspecialchars($booking['booking_code']) ?></h6>
                                        <small><?= date('M j, Y', strtotime($booking['collection_date'])) ?></small>
                                    </div>
                                    <p class="mb-1"><?= htmlspecialchars(ucfirst($booking['service_type'])) ?> - <?= htmlspecialchars($booking['vehicle_type']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($booking['location']) ?></small>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Booking Agreement Details -->
            <div class="col-md-8">
                <?php if ($selectedBooking): ?>
                    <div class="tile">
                        <div class="tile-title-w-btn">
                            <h3 class="title">Booking Agreement</h3>
                            <span class="badge bg-<?= $agreementStatus === 'endorsed' ? 'success' : 'warning' ?>">
                                <?= $agreementStatus === 'endorsed' ? 'Endorsed' : 'Pending Endorsement' ?>
                            </span>
                        </div>
                        
                        <div class="tile-body">
                            <div class="booking-details mb-4">
                                <h5>Service Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Booking Reference</th>
                                                <td>#<?= htmlspecialchars($selectedBooking['booking_code']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Service Type</th>
                                                <td><?= htmlspecialchars(ucfirst($selectedBooking['service_type'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Vehicle Type</th>
                                                <td><?= htmlspecialchars($selectedBooking['vehicle_type']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Collection Date</th>
                                                <td><?= date('F j, Y', strtotime($selectedBooking['collection_date'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Location</th>
                                                <td><?= htmlspecialchars($selectedBooking['location']) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="agreement-status">
                                <h5>Agreement Status</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Customer Signature</th>
                                                <td>
                                                    <?php if ($agreement && $agreement['waste_user_sign']): ?>
                                                        <span class="text-success">Signed on <?= date('M j, Y g:i a', strtotime($agreement['date_user_sign'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-danger">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Driver Signature</th>
                                                <td>
                                                    <?php if ($agreement && $agreement['waste_driver_sign']): ?>
                                                        <span class="text-success">Signed on <?= date('M j, Y g:i a', strtotime($agreement['date_driver_sign'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-danger">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Admin Endorsement</th>
                                                <td>
                                                    <?php if ($agreement && $agreement['status'] === 'endorsed'): ?>
                                                        <span class="text-success">Endorsed</span>
                                                    <?php else: ?>
                                                        <span class="text-warning">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <?php if ($canEndorse): ?>
                                <div class="endorsement-form mt-4">
                                    <form method="POST" id="endorsementForm">
                                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($selectedBooking['booking_code']) ?>">
                                        
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="card-title mb-0">Admin Endorsement</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>By endorsing this agreement, you confirm:</p>
                                                <ol>
                                                    <li>Both parties have properly signed the agreement</li>
                                                    <li>The service was completed as described</li>
                                                    <li>All terms and conditions were met</li>
                                                    <li>The agreement is ready for final processing</li>
                                                </ol>
                                                
                                                <div class="d-grid gap-2">
                                                    <button type="submit" name="endorse_agreement" class="btn btn-primary">
                                                        <i class="bi bi-check-circle-fill"></i> Endorse Agreement
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php elseif ($agreementStatus === 'endorsed'): ?>
                                <div class="alert alert-success mt-4">
                                    <i class="bi bi-check-circle-fill"></i> 
                                    This agreement was endorsed on 
                                    <?php 
                                        $stmt = $pdo->prepare("SELECT updated_at FROM booking_agreements WHERE booking_id = ?");
                                        $stmt->execute([$selectedBooking['booking_code']]);
                                        $endorseDate = $stmt->fetchColumn();
                                        echo date('F j, Y \a\t g:i a', strtotime($endorseDate));
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mt-4">
                                    <i class="bi bi-info-circle-fill"></i> 
                                    This agreement is waiting for both parties to sign before it can be endorsed.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="tile">
                        <div class="tile-body">
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-text fs-1 text-muted"></i>
                                <h4 class="mt-3">Select an agreement</h4>
                                <p class="text-muted">Choose an agreement from the list to review and endorse</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <style>
        .booking-details table th,
        .agreement-status table th {
            width: 30%;
        }
        .endorsement-form .card {
            border-width: 2px;
        }
    </style>
</body>
</html>