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

// Check account status
if (isset($_SESSION['account_status']) && $_SESSION['account_status'] !== 'active') {
    header("Location: userlogin?error=account_inactive");
    exit();
}

// Initialize toast variables
$toastMessage = '';
$toastType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add/Update Service
    if (isset($_POST['save_service'])) {
        try {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $service_name = filter_input(INPUT_POST, 'service_name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $cost = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            // Validate required fields
            if (empty($service_name) || empty($cost)) {
                throw new Exception("Service name and cost are required");
            }
            
            if ($cost <= 0) {
                throw new Exception("Cost must be greater than 0");
            }
            
            if ($id > 0) {
                // Update existing service
                $stmt = $pdo->prepare("UPDATE services SET 
                                    service_name = ?, 
                                    description = ?, 
                                    cost = ?,
                                    status = ?,
                                    updated_at = NOW()
                                    WHERE id = ?");
                $stmt->execute([$service_name, $description, $cost, $status, $id]);
                
                $toastMessage = "Service updated successfully!";
            } else {
                // Insert new service
                $stmt = $pdo->prepare("INSERT INTO services 
                                    (service_name, description, cost, status, created_at, updated_at) 
                                    VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([$service_name, $description, $cost, $status]);
                
                $toastMessage = "Service added successfully!";
            }
            
            $toastType = "success";
        } catch (Exception $e) {
            $toastMessage = "Error saving service: " . $e->getMessage();
            $toastType = "error";
        }
    }
    
    // Delete Service
    if (isset($_POST['delete_service'])) {
        try {
            $id = intval($_POST['id']);
            
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            
            $toastMessage = "Service deleted successfully!";
            $toastType = "success";
        } catch (Exception $e) {
            $toastMessage = "Error deleting service: " . $e->getMessage();
            $toastType = "error";
        }
    }
    
    // Redirect to prevent form resubmission
    if (!empty($toastMessage)) {
        $_SESSION['toastMessage'] = $toastMessage;
        $_SESSION['toastType'] = $toastType;
        header("Location: services-page");
        exit();
    }
}

// Check for toast message in session
if (isset($_SESSION['toastMessage'])) {
    $toastMessage = $_SESSION['toastMessage'];
    $toastType = $_SESSION['toastType'];
    unset($_SESSION['toastMessage']);
    unset($_SESSION['toastType']);
}

// Fetch all services
$services = [];
try {
    $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error fetching services: " . $e->getMessage();
    $toastType = "error";
}

// Fetch a single service for editing
$edit_service = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $edit_service = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $toastMessage = "Error fetching service for editing: " . $e->getMessage();
        $toastType = "error";
    }
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
                <h1><i class="bi bi-truck"></i> Manage Services</h1>
                <p>Create and manage waste collection services</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Services</li>
                <li class="breadcrumb-item"><a href="#">Manage Services</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
        <?php if (!empty($toastMessage)): ?>
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1001">
                <div id="liveToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-<?= $toastType ?> text-white">
                        <strong class="me-auto">Notification</strong>
                        <small>Just now</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <?= htmlspecialchars($toastMessage) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <div class="row">
                            <!-- Add/Edit Form -->
                            <div class="col-lg-5">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5><i class="bi bi-pencil-square"></i> <?= $edit_service ? 'Edit' : 'Add'; ?> Service</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?= $edit_service ? $edit_service['id'] : ''; ?>">
                                            
                                            <div class="mb-3">
                                                <label for="service_name" class="form-label">Service Name</label>
                                                <select class="form-select" id="service_name" name="service_name" required>
                                                    <option value="general" <?= ($edit_service && $edit_service['service_name'] === 'general') ? 'selected' : '' ?>>General Waste</option>
                                                    <option value="recycling" <?= ($edit_service && $edit_service['service_name'] === 'recycling') ? 'selected' : '' ?>>Recycling</option>
                                                    <option value="waste_collection" <?= ($edit_service && $edit_service['service_name'] === 'waste_collection') ? 'selected' : '' ?>>Waste Collection</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"><?= 
                                                    $edit_service ? htmlspecialchars($edit_service['description'] ?? '') : ''; ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="cost" class="form-label">Cost (GHC per kg)</label>
                                                <input type="number" class="form-control" id="cost" name="cost" 
                                                       value="<?= $edit_service ? $edit_service['cost'] : ''; ?>" min="1" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="Active" <?= ($edit_service && $edit_service['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                                                    <option value="Inactive" <?= ($edit_service && $edit_service['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" name="save_service" class="btn btn-primary">
                                                <i class="bi bi-save"></i> <?= $edit_service ? 'Update' : 'Save'; ?> Service
                                            </button>
                                            
                                            <?php if ($edit_service): ?>
                                                <a href="services-page" class="btn btn-secondary">Cancel</a>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Services List -->
                            <div class="col-lg-7">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5><i class="bi bi-list-ul"></i> Services List</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($services)): ?>
                                            <p class="text-muted">No services found. Add your first service using the form.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Service Name</th>
                                                            <th>Description</th>
                                                            <th>Cost (GHC/kg)</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($services as $service): ?>
                                                            <tr>
                                                                <td>
                                                                    <?= htmlspecialchars(ucfirst($service['service_name'])) ?>
                                                                </td>
                                                                <td>
                                                                    <?= !empty($service['description']) ? htmlspecialchars($service['description']) : '<span class="text-muted">No description</span>' ?>
                                                                </td>
                                                                <td>
                                                                    <?= number_format($service['cost'], 2) ?>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?= $service['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                                                        <?= htmlspecialchars($service['status']) ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="services-page?edit=<?= $service['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                                                        <button type="submit" name="delete_service" class="btn btn-sm btn-outline-danger" 
                                                                                onclick="return confirm('Are you sure you want to delete this service?')">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
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
        // Auto-hide toast after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.toast').toast('hide');
            }, 5000);
        });
    </script>
</body>
</html>