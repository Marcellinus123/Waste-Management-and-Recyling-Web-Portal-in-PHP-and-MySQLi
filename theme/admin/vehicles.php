<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once('db.php');

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
$vehicles = [];
$editMode = false;
$currentVehicle = null;

// Form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Add/Edit Vehicle
        if (isset($_POST['save_vehicle'])) {
            $vehicleNumber = trim($_POST['vehicle_number']);
            $driverId = trim($_POST['driver_id']);
            $vehicleName = trim($_POST['vehicle_name']);
            $vehicleType = $_POST['vehicle_type'];
            $weight = trim($_POST['weight']);
            $vehicleStatus = $_POST['vehicle_status'];
            $vehicleId = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : null;

            // Validate inputs
            if (empty($vehicleNumber) || empty($driverId) || empty($vehicleName) || empty($weight)) {
                throw new Exception("All fields are required");
            }

            if ($editMode && $vehicleId) {
                // Update existing vehicle
                $stmt = $pdo->prepare("UPDATE vehicles SET 
                    vehicle_number = ?, 
                    driver_id = ?, 
                    vehicle_name = ?, 
                    vehicle_type = ?, 
                    weight = ?, 
                    vehicle_status = ? 
                    WHERE id = ?");
                $stmt->execute([
                    $vehicleNumber, 
                    $driverId, 
                    $vehicleName, 
                    $vehicleType, 
                    $weight, 
                    $vehicleStatus, 
                    $vehicleId
                ]);
                
                $toastMessage = "Vehicle updated successfully";
                $toastType = "success";
            } else {
                // Add new vehicle
                $stmt = $pdo->prepare("INSERT INTO vehicles (
                    vehicle_number, 
                    driver_id, 
                    vehicle_name, 
                    vehicle_type, 
                    weight, 
                    vehicle_status
                ) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $vehicleNumber, 
                    $driverId, 
                    $vehicleName, 
                    $vehicleType, 
                    $weight, 
                    $vehicleStatus
                ]);
                
                $toastMessage = "Vehicle added successfully";
                $toastType = "success";
            }
        }
        
        // Delete Vehicle
        if (isset($_POST['delete_vehicle'])) {
            $vehicleId = $_POST['vehicle_id'];
            
            $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
            $stmt->execute([$vehicleId]);
            
            $toastMessage = "Vehicle deleted successfully";
            $toastType = "success";
        }
        
        // Edit Vehicle - Load data
        if (isset($_POST['edit_vehicle'])) {
            $vehicleId = $_POST['vehicle_id'];
            
            $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
            $stmt->execute([$vehicleId]);
            $currentVehicle = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($currentVehicle) {
                $editMode = true;
            }
        }
        
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get all vehicles
try {
    $stmt = $pdo->prepare("SELECT v.*, u.first_name, u.last_name 
                          FROM vehicles v
                          LEFT JOIN users u ON v.driver_id = u.user_id
                          ORDER BY v.vehicle_status, v.vehicle_name");
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get drivers for dropdown
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name FROM users WHERE usertype = 'waste_driver'");
    $stmt->execute();
    $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $toastMessage = "Error loading vehicles: " . $e->getMessage();
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
                <h1><i class="bi bi-truck"></i> Manage Vehicles</h1>
                <p>Add, edit, and manage waste collection vehicles</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Vehicles</a></li>
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
                        <h3 class="title"><?php echo $editMode ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h3>
                        <?php if ($editMode): ?>
                        <a href="vehicles" class="btn btn-danger">Cancel</a>
                        <?php endif; ?>
                    </div>
                    <div class="tile-body">
                        <form method="POST">
                            <input type="hidden" name="vehicle_id" value="<?php echo $editMode ? $currentVehicle['id'] : ''; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vehicle_number">Vehicle Number</label>
                                        <input class="form-control" id="vehicle_number" name="vehicle_number" 
                                            type="text" placeholder="Enter vehicle number" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentVehicle['vehicle_number']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="driver_id">Driver</label>
                                        <select class="form-control" id="driver_id" name="driver_id" required>
                                            <option value="">Select Driver</option>
                                            <?php foreach ($drivers as $driver): ?>
                                            <option value="<?php echo $driver['user_id']; ?>"
                                                <?php echo ($editMode && $currentVehicle['driver_id'] == $driver['user_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vehicle_name">Vehicle Name/Model</label>
                                        <input class="form-control" id="vehicle_name" name="vehicle_name" 
                                            type="text" placeholder="Enter vehicle name/model" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentVehicle['vehicle_name']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vehicle_type">Vehicle Type</label>
                                        <select class="form-control" id="vehicle_type" name="vehicle_type" required>
                                            <option value="Small Waste Truck" <?php echo ($editMode && $currentVehicle['vehicle_type'] == 'Small Waste Truck') ? 'selected' : ''; ?>>Small Waste Truck</option>
                                            <option value="Standard Waste Truck" <?php echo ($editMode && $currentVehicle['vehicle_type'] == 'Standard Waste Truck') ? 'selected' : ''; ?>>Standard Waste Truck</option>
                                            <option value="Organic Waste Truck" <?php echo ($editMode && $currentVehicle['vehicle_type'] == 'Organic Waste Truck') ? 'selected' : ''; ?>>Organic Waste Truck</option>
                                            <option value="Other" <?php echo ($editMode && $currentVehicle['vehicle_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="weight">Capacity (kg)</label>
                                        <input class="form-control" id="weight" name="weight" 
                                            type="text" placeholder="Enter vehicle capacity in kg" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentVehicle['weight']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vehicle_status">Status</label>
                                        <select class="form-control" id="vehicle_status" name="vehicle_status" required>
                                            <option value="Available" <?php echo ($editMode && $currentVehicle['vehicle_status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                            <option value="Not Available" <?php echo ($editMode && $currentVehicle['vehicle_status'] == 'Not Available') ? 'selected' : ''; ?>>Not Available</option>
                                            <option value="Banned" <?php echo ($editMode && $currentVehicle['vehicle_status'] == 'Banned') ? 'selected' : ''; ?>>Banned</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tile-footer">
                                <button class="btn btn-primary" type="submit" name="save_vehicle">
                                    <i class="bi bi-save-fill"></i> <?php echo $editMode ? 'Update' : 'Save'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Vehicle List</h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="vehiclesTable">
                            <thead>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <th>Driver</th>
                                    <th>Name/Model</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehicles as $vehicle): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vehicle['vehicle_number']); ?></td>
                                    <td>
                                        <?php 
                                        if ($vehicle['first_name']) {
                                            echo htmlspecialchars($vehicle['first_name'] . ' ' . $vehicle['last_name']);
                                        } else {
                                            echo 'Not assigned';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($vehicle['vehicle_name']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['vehicle_type']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['weight']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($vehicle['vehicle_status']) {
                                                case 'Available': echo 'success'; break;
                                                case 'Not Available': echo 'warning'; break;
                                                case 'Banned': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?>">
                                            <?php echo htmlspecialchars($vehicle['vehicle_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                            <button type="submit" name="edit_vehicle" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-fill"></i> Edit
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                            <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                            <button type="submit" name="delete_vehicle" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($vehicles)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No vehicles found</td>
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
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            // Focus on first input when in edit mode
            <?php if ($editMode): ?>
            $('#vehicle_number').focus();
            <?php endif; ?>
        });
    </script>
</body>
</html>