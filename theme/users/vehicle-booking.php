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
$availableVehicles = [];
$serviceCosts = [];
$weightOptions = [
    '50kg' => '50kg',
    '100kg' => '100kg',
    '150kg' => '150kg',
    '200kg' => '200kg',
    '250kg' => '250kg',
    '300kg' => '300kg',
    '350kg' => '350kg',
    '400kg' => '400kg',
    '450kg' => '450kg',
    '500kg' => '500kg'
];

// Get available vehicles
try {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_status = 'Available'");
    $stmt->execute();
    $availableVehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading vehicles: " . $e->getMessage();
    $toastType = "error";
}

// Get service costs
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE status = 'Active'");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($services as $service) {
        $serviceCosts[$service['service_name']] = $service['cost'];
    }
} catch (Exception $e) {
    $toastMessage = "Error loading services: " . $e->getMessage();
    $toastType = "error";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $serviceType = $_POST['service_type'] ?? '';
    $vehicleType = $_POST['vehicle_type'] ?? '';
    $collectionDate = $_POST['collection_date'] ?? '';
    $timeSlot = $_POST['time_slot'] ?? '';
    $estimatedWeight = $_POST['estimated_weight'] ?? '';
    $location = $_POST['location'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    try {
        // Validate inputs
        if (empty($serviceType) || empty($vehicleType) || empty($collectionDate) || 
            empty($timeSlot) || empty($estimatedWeight) || empty($location)) {
            throw new Exception('All fields are required');
        }
        
        // Check if vehicle is still available
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_number = ? AND vehicle_status = 'Available'");
        $stmt->execute([$vehicleType]);
        $vehicle = $stmt->fetch();
        
        if (!$vehicle) {
            throw new Exception('Selected vehicle is no longer available. Please select another vehicle.');
        }
        
        // Calculate cost
        $weight = (int)str_replace('kg', '', $estimatedWeight);
        $cost = 0;
        
        if ($serviceType === 'general') {
            $cost = $weight * $serviceCosts['general'];
        } elseif ($serviceType === 'recycling') {
            $cost = $weight * $serviceCosts['recycling'];
        } elseif ($serviceType === 'waste_collection') {
            $cost = $weight * $serviceCosts['waste_collection'];
        }
        
        // Generate booking code
        $bookingCode = 'BK-' . strtoupper(uniqid());
        
        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO bookings 
                            (booking_code, user_id, service_type, vehicle_type, collection_date, 
                            time_slot, estimated_weight, location, notes, amount, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'not_approved')");
        $stmt->execute([
            $bookingCode,
            $_SESSION['user_id'],
            $serviceType,
            $vehicleType,
            $collectionDate,
            $timeSlot,
            $estimatedWeight,
            $location,
            $notes,
            $cost
        ]);
        
        $bookingId = $pdo->lastInsertId();
        
        // Update vehicle status to "Not Available"
        $stmt = $pdo->prepare("UPDATE vehicles SET vehicle_status = 'Not Available' WHERE vehicle_number = ?");
        $stmt->execute([$vehicleType]);
        
        $toastMessage = "Your booking has been submitted successfully! Booking Code: $bookingCode";
        $toastType = "success";
        
        // Generate PDF
        if ($bookingId) {
            generateBookingPDF($bookingId, $bookingCode, $serviceType, $vehicleType, $collectionDate, 
                              $timeSlot, $estimatedWeight, $location, $notes, $cost);
        }
        
    } catch (Exception $e) {
        $toastMessage = "Error submitting booking: " . $e->getMessage();
        $toastType = "error";
    }
}

// Function to generate PDF
function generateBookingPDF($bookingId, $bookingCode, $serviceType, $vehicleType, $collectionDate, 
                          $timeSlot, $estimatedWeight, $location, $notes, $cost) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Waste Wizard');
    $pdf->SetTitle('Booking Confirmation - ' . $bookingCode);
    $pdf->SetSubject('Booking Confirmation');
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, 'Booking Confirmation', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Set font for content
    $pdf->SetFont('helvetica', '', 12);
    
    // Booking details
    $html = '
    <h3>Booking Details</h3>
    <table border="0" cellpadding="5">
        <tr>
            <td width="40%"><strong>Booking Code:</strong></td>
            <td width="60%">' . $bookingCode . '</td>
        </tr>
        <tr>
            <td><strong>Service Type:</strong></td>
            <td>' . ucfirst($serviceType) . '</td>
        </tr>
        <tr>
            <td><strong>Vehicle Type:</strong></td>
            <td>' . $vehicleType . '</td>
        </tr>
        <tr>
            <td><strong>Collection Date:</strong></td>
            <td>' . $collectionDate . '</td>
        </tr>
        <tr>
            <td><strong>Time Slot:</strong></td>
            <td>' . ucfirst($timeSlot) . '</td>
        </tr>
        <tr>
            <td><strong>Estimated Weight:</strong></td>
            <td>' . $estimatedWeight . '</td>
        </tr>
        <tr>
            <td><strong>Location:</strong></td>
            <td>' . $location . '</td>
        </tr>
        <tr>
            <td><strong>Total Cost:</strong></td>
            <td>GHS ' . number_format($cost, 2) . '</td>
        </tr>
        <tr>
            <td><strong>Special Instructions:</strong></td>
            <td>' . $notes . '</td>
        </tr>
    </table>
    <p><em>Thank you for using Waste Wizard services. Your booking is pending approval.</em></p>
    ';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Save PDF file
    $pdfFilePath = 'bookings/' . $bookingCode . '.pdf';
    $pdf->Output(__DIR__ . '/' . $pdfFilePath, 'F');
}

// Get user's bookings
$userBookings = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <h1><i class="bi bi-car-front-fill"></i> Vehicle Booking</h1>
          <p>Schedule waste collection services</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item"><a href="#">Dashboard / Vehicle Booking</a></li>
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
        <div class="clearix"></div>
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">Book a Vehicle</h3>
            <div class="tile-body">
              <form method="post" class="row">
                <div class="mb-3 col-md-3">
                  <label class="form-label">Service Type</label>
                  <select id="service-type" name="service_type" class="form-control" required>
                    <option value="">Select service</option>
                    <option value="general" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'general' ? 'selected' : '' ?>>General Waste Collection</option>
                    <option value="recycling" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'recycling' ? 'selected' : '' ?>>Recycling Pickup</option>
                    <option value="waste_collection" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'waste_collection' ? 'selected' : '' ?>>Waste Collection</option>
                  </select>
                </div>
                
                <div class="mb-3 col-md-3">
                  <label class="form-label">Vehicle Type</label>
                  <select id="vehicle-type" name="vehicle_type" class="form-control" required>
                    <option value="">Select Vehicle</option>
                    <?php foreach ($availableVehicles as $vehicle): ?>
                      <option value="<?= htmlspecialchars($vehicle['vehicle_number']) ?>" 
                              data-weight="<?= htmlspecialchars($vehicle['weight']) ?>"
                              <?= isset($_POST['vehicle_type']) && $_POST['vehicle_type'] === $vehicle['vehicle_number'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vehicle['vehicle_name']) ?> - <?= htmlspecialchars($vehicle['weight']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="mb-3 col-md-3">
                  <label class="form-label">Collection Date</label>
                  <input class="form-control" type="date" name="collection_date" 
                         min="<?= date('Y-m-d') ?>" 
                         value="<?= isset($_POST['collection_date']) ? htmlspecialchars($_POST['collection_date']) : '' ?>" required>
                </div>
                
                <div class="mb-3 col-md-3">
                  <label class="form-label">Time Slot</label>
                  <select id="time-slot" name="time_slot" class="form-control" required>
                    <option value="">Select Time</option>
                    <option value="morning" <?= isset($_POST['time_slot']) && $_POST['time_slot'] === 'morning' ? 'selected' : '' ?>>Morning (8am-12pm)</option>
                    <option value="afternoon" <?= isset($_POST['time_slot']) && $_POST['time_slot'] === 'afternoon' ? 'selected' : '' ?>>Afternoon (12pm-4pm)</option>
                    <option value="evening" <?= isset($_POST['time_slot']) && $_POST['time_slot'] === 'evening' ? 'selected' : '' ?>>Evening (4pm-8pm)</option>
                  </select>
                </div>
                
                <div class="mb-3 col-md-4">
                  <label class="form-label">Estimated Weight</label>
                  <select id="estimated-weight" name="estimated_weight" class="form-control" required>
                    <option value="">Select Weight</option>
                    <?php foreach ($weightOptions as $value => $label): ?>
                      <option value="<?= htmlspecialchars($value) ?>" 
                              <?= isset($_POST['estimated_weight']) && $_POST['estimated_weight'] === $value ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="mb-3 col-md-4">
                  <label class="form-label">Cost</label>
                  <input type="text" id="cost-display" class="form-control" readonly>
                  <input type="hidden" id="actual-cost" name="cost">
                </div>
                
                <div class="mb-3 col-md-4">
                  <label class="form-label">Location</label>
                  <input class="form-control" type="text" name="location" 
                         value="<?= isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '' ?>" required>
                </div>
                
                <div class="mb-3">
                  <label class="form-label">Special Instructions</label>
                  <textarea class="form-control" name="notes"><?= isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '' ?></textarea>
                </div>
                
                <div class="mb-3 col-md-4 align-self-end">
                  <button class="btn btn-primary" name="book" type="submit">
                    <i class="bi bi-cash me-2"></i>Book Now
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Previous Bookings Section -->
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">Your Previous Bookings</h3>
            <div class="tile-body">
              <?php if (empty($userBookings)): ?>
                <div class="alert alert-info">You haven't made any bookings yet.</div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                      <tr>
                        <th>Booking Code</th>
                        <th>Service Type</th>
                        <th>Vehicle</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Weight</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($userBookings as $booking): ?>
                        <tr>
                          <td><?= htmlspecialchars($booking['booking_code']) ?></td>
                          <td><?= ucfirst(htmlspecialchars($booking['service_type'])) ?></td>
                          <td><?= htmlspecialchars($booking['vehicle_type']) ?></td>
                          <td><?= date('M j, Y', strtotime($booking['collection_date'])) ?></td>
                          <td><?= ucfirst(htmlspecialchars($booking['time_slot'])) ?></td>
                          <td><?= htmlspecialchars($booking['estimated_weight']) ?></td>
                          <td>GHS <?= number_format($booking['amount'], 2) ?></td>
                          <td>
                            <?php 
                              $statusClass = '';
                              switch($booking['status']) {
                                case 'approved': $statusClass = 'success'; break;
                                case 'completed': $statusClass = 'primary'; break;
                                case 'rejected': $statusClass = 'danger'; break;
                                case 'cancelled': $statusClass = 'warning'; break;
                                default: $statusClass = 'secondary';
                              }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>">
                              <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                            </span>
                          </td>
                          <td>
                            <?php if (file_exists('bookings/' . $booking['booking_code'] . '.pdf')): ?>
                              <a href="vehicle-booking/<?= htmlspecialchars($booking['booking_code']) ?>.pdf" 
                                 class="btn btn-sm btn-info" download>
                                <i class="bi bi-download"></i> Download
                              </a>
                            <?php endif; ?>
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
            delay: 5000 // 5 seconds
        });

        // Function to show toast with message and type
        function showToast(message, type = 'info') {
            const toastHeader = toastEl.querySelector('.toast-header');
            const toastBody = toastEl.querySelector('.toast-body');
            
            // Set message
            toastBody.innerText = message;
            
            // Set color based on type
            switch(type) {
                case 'success':
                    toastHeader.className = 'toast-header bg-success text-white';
                    break;
                case 'error':
                    toastHeader.className = 'toast-header bg-danger text-white';
                    break;
                case 'warning':
                    toastHeader.className = 'toast-header bg-warning text-dark';
                    break;
                default:
                    toastHeader.className = 'toast-header bg-primary text-white';
            }
            
            toast.show();
        }

        // Calculate cost when service type or weight changes
        function calculateCost() {
            const serviceType = document.getElementById('service-type').value;
            const estimatedWeight = document.getElementById('estimated-weight').value;
            const costDisplay = document.getElementById('cost-display');
            const actualCost = document.getElementById('actual-cost');
            
            if (serviceType && estimatedWeight) {
                const weight = parseInt(estimatedWeight.replace('kg', ''));
                let costPerKg = 0;
                
                // Get cost per kg based on service type
                switch(serviceType) {
                    case 'general':
                        costPerKg = 3800; // GHS 38.00 per kg
                        break;
                    case 'recycling':
                        costPerKg = 150; // GHS 1.50 per kg
                        break;
                    case 'waste_collection':
                        costPerKg = 200; // GHS 2.00 per kg
                        break;
                }
                
                const totalCost = weight * costPerKg;
                costDisplay.value = 'GHS ' + totalCost.toLocaleString('en-US', {minimumFractionDigits: 2});
                actualCost.value = totalCost;
            } else {
                costDisplay.value = '';
                actualCost.value = '';
            }
        }
        
        // Add event listeners
        document.getElementById('service-type').addEventListener('change', calculateCost);
        document.getElementById('estimated-weight').addEventListener('change', calculateCost);
        
        // Show toast if there's a message from PHP
        <?php if (!empty($toastMessage)): ?>
            window.addEventListener('DOMContentLoaded', () => {
                showToast("<?= addslashes($toastMessage) ?>", "<?= $toastType ?>");
            });
        <?php endif; ?>
    </script>
    
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
</body>
</html>