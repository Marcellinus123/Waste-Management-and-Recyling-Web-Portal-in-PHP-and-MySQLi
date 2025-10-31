<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once('db.php');
include("../libs/tcpdf-main/tcpdf.php");

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: userlogin");
    exit();
}

// Check if usertype is not 'waste_user'
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'waste_user') {
    header('Location: userlogin');
    exit();
}

// Check if user_id is set and not null
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: userlogin');
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$userPayments = [];

// Get user's payments from bookings table
try {
    $stmt = $pdo->prepare("SELECT 
                            booking_code, 
                            service_type, 
                            amount, 
                            status, 
                            created_at,
                            collection_date
                          FROM bookings 
                          WHERE user_id = ? 
                          ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $userPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading payments: " . $e->getMessage();
    $toastType = "error";
}

// Handle PDF generation for payment receipt
if (isset($_GET['download_receipt']) && !empty($_GET['booking_code'])) {
    $bookingCode = $_GET['booking_code'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_code = ? AND user_id = ?");
        $stmt->execute([$bookingCode, $_SESSION['user_id']]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($payment) {
            generatePaymentReceiptPDF($payment);
        } else {
            throw new Exception('Payment record not found');
        }
    } catch (Exception $e) {
        $toastMessage = "Error generating receipt: " . $e->getMessage();
        $toastType = "error";
    }
}

// Function to generate payment receipt PDF
function generatePaymentReceiptPDF($payment) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Waste Wizard');
    $pdf->SetTitle('Payment Receipt - ' . $payment['booking_code']);
    $pdf->SetSubject('Payment Receipt');
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Set font for content
    $pdf->SetFont('helvetica', '', 12);
    
    // Payment details
    $html = '
    <h3>Payment Details</h3>
    <table border="0" cellpadding="5">
        <tr>
            <td width="40%"><strong>Booking Code:</strong></td>
            <td width="60%">' . $payment['booking_code'] . '</td>
        </tr>
        <tr>
            <td><strong>Service Type:</strong></td>
            <td>' . ucfirst($payment['service_type']) . '</td>
        </tr>
        <tr>
            <td><strong>Payment Date:</strong></td>
            <td>' . date('M j, Y H:i', strtotime($payment['created_at'])) . '</td>
        </tr>
        <tr>
            <td><strong>Collection Date:</strong></td>
            <td>' . date('M j, Y', strtotime($payment['collection_date'])) . '</td>
        </tr>
        <tr>
            <td><strong>Amount Paid:</strong></td>
            <td>GHS ' . number_format($payment['amount'], 2) . '</td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>' . ucfirst(str_replace('_', ' ', $payment['status'])) . '</td>
        </tr>
    </table>
    <p><em>Thank you for using Waste Wizard services.</em></p>
    ';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Output PDF directly to browser
    $pdf->Output('payment_receipt_' . $payment['booking_code'] . '.pdf', 'D');
    exit();
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
          <h1><i class="bi bi-credit-card"></i> Payment History</h1>
          <p>View your payment transactions</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item"><a href="#">Payments</a></li>
        </ul>
      </div>
      
      <!-- Toast Container -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1001">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="toast-header bg-primary text-white">
            <strong class="me-auto">Notification</strong>
            <small>Just now</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            <!-- Message will be inserted here by JavaScript -->
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">Your Payment History</h3>
            <div class="tile-body">
              <?php if (empty($userPayments)): ?>
                <div class="alert alert-info">You haven't made any payments yet.</div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                      <tr>
                        <th>Booking Code</th>
                        <th>Service Type</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($userPayments as $payment): ?>
                        <tr>
                          <td><?= htmlspecialchars($payment['booking_code']) ?></td>
                          <td><?= ucfirst(htmlspecialchars($payment['service_type'])) ?></td>
                          <td>GHS <?= number_format($payment['amount'], 2) ?></td>
                          <td><?= date('M j, Y H:i', strtotime($payment['created_at'])) ?></td>
                          <td>
                            <?php 
                              $statusClass = '';
                              switch($payment['status']) {
                                case 'approved': $statusClass = 'success'; break;
                                case 'completed': $statusClass = 'primary'; break;
                                case 'rejected': $statusClass = 'danger'; break;
                                case 'cancelled': $statusClass = 'warning'; break;
                                default: $statusClass = 'secondary';
                              }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>">
                              <?= ucfirst(str_replace('_', ' ', $payment['status'])) ?>
                            </span>
                          </td>
                          <td>
                            <a href="payments.php?download_receipt=1&booking_code=<?= $payment['booking_code'] ?>" 
                               class="btn btn-sm btn-info">
                              <i class="bi bi-download"></i> Receipt
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // Initialize Toast
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000
        });

        // Show toast if there's a message from PHP
        <?php if (!empty($toastMessage)): ?>
            window.addEventListener('DOMContentLoaded', () => {
                const toastBody = toastEl.querySelector('.toast-body');
                toastBody.innerText = "<?= addslashes($toastMessage) ?>";
                
                const toastHeader = toastEl.querySelector('.toast-header');
                toastHeader.className = 'toast-header bg-<?= $toastType === "error" ? "danger" : "success" ?> text-white';
                
                toast.show();
            });
        <?php endif; ?>
    </script>
</body>
</html>