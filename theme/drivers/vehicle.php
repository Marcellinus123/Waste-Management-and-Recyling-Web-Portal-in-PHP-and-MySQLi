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

// Check if usertype is not 'waste_driver'
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'waste_driver') {
    header('Location: userlogin');
    exit();
}

// Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    header('Location: userlogin');
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$driverVehicle = null;
$hasVehicle = false;

// Check if driver already has a vehicle assigned
try {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE driver_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $driverVehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($driverVehicle) {
        $hasVehicle = true;
    }
} catch (Exception $e) {
    $toastMessage = "Error checking vehicle assignment: " . $e->getMessage();
    $toastType = "error";
}

// Handle new vehicle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_vehicle'])) {
    $vehicleNumber = $_POST['vehicle_number'] ?? '';
    $vehicleName = $_POST['vehicle_name'] ?? '';
    $vehicleType = $_POST['vehicle_type'] ?? '';
    $weight = $_POST['weight'] ?? '';
    
    try {
        // Validate inputs
        if (empty($vehicleNumber) || empty($vehicleName) || empty($vehicleType) || empty($weight)) {
            throw new Exception('All fields are required');
        }
        
        // Check if vehicle number already exists
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_number = ?");
        $stmt->execute([$vehicleNumber]);
        $existingVehicle = $stmt->fetch();
        
        if ($existingVehicle) {
            throw new Exception('Vehicle number already exists');
        }
        
        // Insert new vehicle
        $stmt = $pdo->prepare("INSERT INTO vehicles 
                            (vehicle_number, driver_id, vehicle_name, vehicle_type, weight, vehicle_status) 
                            VALUES (?, ?, ?, ?, ?, 'Available')");
        $stmt->execute([
            $vehicleNumber,
            $_SESSION['user_id'],
            $vehicleName,
            $vehicleType,
            $weight
        ]);
        
        $toastMessage = "Vehicle registered successfully!";
        $toastType = "success";
        
        // Set session variables for the toast message
        $_SESSION['toastMessage'] = $toastMessage;
        $_SESSION['toastType'] = $toastType;
        
        // Refresh to show the new vehicle
        header("Location: vehicle");
        exit();
        
    } catch (Exception $e) {
        $toastMessage = "Error registering vehicle: " . $e->getMessage();
        $toastType = "error";
        
        // Set session variables for the toast message
        $_SESSION['toastMessage'] = $toastMessage;
        $_SESSION['toastType'] = $toastType;
    }
}

// Check for toast messages in session
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
          <h1><i class="bi bi-car-front-fill"></i> My Vehicle</h1>
          <p><?= $hasVehicle ? 'View your assigned vehicle' : 'Register your vehicle' ?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item"><a href="#">My Vehicle</a></li>
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
            <?php if ($hasVehicle): ?>
              <!-- Show vehicle details (read-only) -->
              <h3 class="tile-title">Vehicle Details</h3>
              <div class="tile-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Vehicle Number</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($driverVehicle['vehicle_number']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Vehicle Name</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($driverVehicle['vehicle_name']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Vehicle Type</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($driverVehicle['vehicle_type']) ?>" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Weight Capacity</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($driverVehicle['weight']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($driverVehicle['vehicle_status']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Registration Date</label>
                      <input type="text" class="form-control" value="<?= date('M j, Y', strtotime($driverVehicle['date_created'])) ?>" readonly>
                    </div>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <!-- Show vehicle registration form -->
              <h3 class="tile-title">Register Your Vehicle</h3>
              <div class="tile-body">
                <form method="post" class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" required>
                    <small class="text-muted">e.g. GA-1234567</small>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Vehicle Name</label>
                    <input type="text" name="vehicle_name" class="form-control" required>
                    <small class="text-muted">e.g. Toyota Hilux</small>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Vehicle Type</label>
                    <select name="vehicle_type" class="form-control" required>
                      <option value="">Select Type</option>
                      <option value="Small Waste Truck">Small Waste Truck</option>
                      <option value="Standard Waste Truck">Standard Waste Truck</option>
                      <option value="Organic Waste Truck">Organic Waste Truck</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label">Weight Capacity</label>
                    <input type="text" name="weight" class="form-control" required>
                    <small class="text-muted">e.g. 500kg, 2000kg</small>
                  </div>
                  <div class="mb-3 col-md-12 align-self-end">
                    <button class="btn btn-primary" name="register_vehicle" type="submit">
                      <i class="bi bi-car-front me-2"></i>Register Vehicle
                    </button>
                  </div>
                </form>
              </div>
            <?php endif; ?>
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
        
        // Show toast if there's a message from PHP
        <?php if (!empty($toastMessage)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showToast("<?= addslashes($toastMessage) ?>", "<?= $toastType ?>");
            });
        <?php endif; ?>
    </script>
</body>
</html>