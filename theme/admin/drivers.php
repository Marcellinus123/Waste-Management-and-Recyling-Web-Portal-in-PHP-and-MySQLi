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
$drivers = [];
$editMode = false;
$currentDriver = null;

// Form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Add/Edit Driver
        if (isset($_POST['save_driver'])) {
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            $city = trim($_POST['city']);
            $region = $_POST['region'];
            $accountStatus = $_POST['account_status'];
            $driverId = isset($_POST['driver_id']) ? $_POST['driver_id'] : null;

            // Validate inputs
            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
                throw new Exception("All required fields must be filled");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            if ($editMode && $driverId) {
                // Update existing driver
                $stmt = $pdo->prepare("UPDATE users SET 
                    first_name = ?, 
                    last_name = ?, 
                    email = ?, 
                    phone = ?, 
                    address = ?, 
                    city = ?, 
                    region = ?,
                    account_status = ?
                    WHERE user_id = ? AND usertype = 'waste_driver'");
                $stmt->execute([
                    $firstName, $lastName, $email, $phone, 
                    $address, $city, $region, $accountStatus,
                    $driverId
                ]);
                
                $toastMessage = "Driver updated successfully";
                $toastType = "success";
            } else {
                // Add new driver - generate random password
                $tempPassword = bin2hex(random_bytes(4));
                $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (
                    user_id, first_name, last_name, email, username, 
                    password, usertype, phone, address, city, 
                    region, account_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'waste_driver', ?, ?, ?, ?, 'active', NOW())");
                
                $userId = 'driver_' . uniqid();
                $username = strtolower($firstName[0] . $lastName) . rand(100, 999);
                
                $stmt->execute([
                    $userId, $firstName, $lastName, $email, $username,
                    $hashedPassword, $phone, $address, $city,
                    $region, $accountStatus
                ]);
                
                // In a real app, you would send the temp password to the driver's email
                $toastMessage = "Driver added successfully. Temporary password: $tempPassword";
                $toastType = "success";
            }
        }
        
        // Delete Driver
        if (isset($_POST['delete_driver'])) {
            $driverId = $_POST['driver_id'];
            
            // Check if driver has any assigned vehicles
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE driver_id = ?");
            $stmt->execute([$driverId]);
            $vehicleCount = $stmt->fetchColumn();
            
            if ($vehicleCount > 0) {
                throw new Exception("Cannot delete driver with assigned vehicles");
            }
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ? AND usertype = 'waste_driver'");
            $stmt->execute([$driverId]);
            
            $toastMessage = "Driver deleted successfully";
            $toastType = "success";
        }
        
        // Reset Driver Password
        if (isset($_POST['reset_password'])) {
            $driverId = $_POST['driver_id'];
            $tempPassword = bin2hex(random_bytes(4));
            $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ? AND usertype = 'waste_driver'");
            $stmt->execute([$hashedPassword, $driverId]);
            
            // In a real app, you would send the new password to the driver's email
            $toastMessage = "Password reset successfully. New temporary password: $tempPassword";
            $toastType = "success";
        }
        
        // Edit Driver - Load data
        if (isset($_POST['edit_driver'])) {
            $driverId = $_POST['driver_id'];
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND usertype = 'waste_driver'");
            $stmt->execute([$driverId]);
            $currentDriver = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($currentDriver) {
                $editMode = true;
            }
        }
        
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get all drivers
try {
    $stmt = $pdo->prepare("SELECT u.*, 
                          (SELECT COUNT(*) FROM vehicles WHERE driver_id = u.user_id) as vehicle_count
                          FROM users u
                          WHERE u.usertype = 'waste_driver'
                          ORDER BY u.account_status, u.first_name, u.last_name");
    $stmt->execute();
    $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $toastMessage = "Error loading drivers: " . $e->getMessage();
    $toastType = "error";
}

// Ghana regions for dropdown
$ghanaRegions = [
    'Ashanti', 'Brong-Ahafo', 'Central', 'Eastern', 'Greater Accra', 
    'Northern', 'Upper East', 'Upper West', 'Volta', 'Western',
    'Ahafo', 'Bono East', 'North East', 'Oti', 'Savannah', 'Western North'
];
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
                <h1><i class="bi bi-people-fill"></i> Manage Drivers</h1>
                <p>Add, edit, and manage waste collection drivers</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Drivers</a></li>
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
                        <h3 class="title"><?php echo $editMode ? 'Edit Driver' : 'Add New Driver'; ?></h3>
                        <?php if ($editMode): ?>
                        <a href="drivers" class="btn btn-danger">Cancel</a>
                        <?php endif; ?>
                    </div>
                    <div class="tile-body">
                        <form method="POST">
                            <input type="hidden" name="driver_id" value="<?php echo $editMode ? $currentDriver['user_id'] : ''; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name *</label>
                                        <input class="form-control" id="first_name" name="first_name" 
                                            type="text" placeholder="Enter first name" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentDriver['first_name']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name *</label>
                                        <input class="form-control" id="last_name" name="last_name" 
                                            type="text" placeholder="Enter last name" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentDriver['last_name']) : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input class="form-control" id="email" name="email" 
                                            type="email" placeholder="Enter email" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentDriver['email']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone *</label>
                                        <input class="form-control" id="phone" name="phone" 
                                            type="tel" placeholder="Enter phone number" required
                                            value="<?php echo $editMode ? htmlspecialchars($currentDriver['phone']) : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input class="form-control" id="address" name="address" 
                                            type="text" placeholder="Enter address"
                                            value="<?php echo $editMode ? htmlspecialchars($currentDriver['address']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input class="form-control" id="city" name="city" 
                                            type="text" placeholder="Enter city"
                                            value="<?php echo $editMode ? htmlspecialchars($currentDriver['city']) : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="region">Region</label>
                                        <select class="form-control" id="region" name="region">
                                            <?php foreach ($ghanaRegions as $region): ?>
                                            <option value="<?php echo htmlspecialchars($region); ?>"
                                                <?php echo ($editMode && $currentDriver['region'] === $region) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($region); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="account_status">Account Status</label>
                                        <select class="form-control" id="account_status" name="account_status">
                                            <option value="active" <?php echo ($editMode && $currentDriver['account_status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="suspended" <?php echo ($editMode && $currentDriver['account_status'] === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                            <option value="banned" <?php echo ($editMode && $currentDriver['account_status'] === 'banned') ? 'selected' : ''; ?>>Banned</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tile-footer">
                                <button class="btn btn-primary" type="submit" name="save_driver">
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
                    <h3 class="tile-title">Driver List</h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="driversTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th>Vehicles</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($drivers as $driver): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($driver['first_name']) . ' ' . htmlspecialchars($driver['last_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($driver['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($driver['phone']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($driver['city']); ?>, <?php echo htmlspecialchars($driver['region']); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo (int)$driver['vehicle_count']; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            switch($driver['account_status']) {
                                                case 'active': echo 'success'; break;
                                                case 'suspended': echo 'warning'; break;
                                                case 'banned': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?>">
                                            <?php echo ucfirst($driver['account_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="driver_id" value="<?php echo $driver['user_id']; ?>">
                                                <button type="submit" name="edit_driver" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil-fill"></i> Edit
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to reset this driver\'s password?');">
                                                <input type="hidden" name="driver_id" value="<?php echo $driver['user_id']; ?>">
                                                <button type="submit" name="reset_password" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-key-fill"></i> Reset PW
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                                <input type="hidden" name="driver_id" value="<?php echo $driver['user_id']; ?>">
                                                <button type="submit" name="delete_driver" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash-fill"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($drivers)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No drivers found</td>
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
            
            // Initialize DataTable for drivers table
            $('#driversTable').DataTable({
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [5] } // Disable sorting on actions column
                ]
            });
        });
    </script>
</body>
</html>