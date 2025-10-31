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
$pricingPlans = [];
$currentPlan = null;
$featuresList = [];

// Get all pricing plans
try {
    $stmt = $pdo->query("SELECT * FROM service_pricing ORDER BY created_at DESC");
    $pricingPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading pricing plans: " . $e->getMessage();
    $toastType = "danger";
}

// Get features list for form
$featuresList = [
    'Waste Tracking',
    'Collection Scheduling',
    'Recycling Rewards',
    'Facility Locator',
    'Advanced Analytics',
    'Waste Audit Tools',
    'Multi-Location Management',
    'API Integration',
    'Priority Support',
    'Custom Reporting'
];

// Handle viewing/editing a specific plan
if (isset($_GET['plan_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM service_pricing WHERE service_id = ?");
        $stmt->execute([$_GET['plan_id']]);
        $currentPlan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($currentPlan) {
            // Decode JSON features
            $currentPlan['features'] = json_decode($currentPlan['features'], true);
        }
    } catch (Exception $e) {
        $toastMessage = "Error loading pricing plan: " . $e->getMessage();
        $toastType = "danger";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $serviceId = $_POST['service_id'] ?? '';
    
    try {
        if ($action === 'delete') {
            // Delete plan
            $stmt = $pdo->prepare("DELETE FROM service_pricing WHERE service_id = ?");
            $stmt->execute([$serviceId]);
            
            $_SESSION['toastMessage'] = "Pricing plan deleted successfully";
            $_SESSION['toastType'] = "success";
            header("Location: services-pricing.php");
            exit();
            
        } else {
            // Validate input
            $serviceName = trim($_POST['service_name']);
            $price = trim($_POST['price']);
            $duration = $_POST['duration'];
            $isPopular = isset($_POST['is_popular']) ? 1 : 0;
            $status = $_POST['status'];
            
            if (empty($serviceName)) {
                throw new Exception("Service name is required");
            }
            
            // Prepare features array
            $features = [];
            foreach ($featuresList as $feature) {
                $features[] = [
                    'feature' => $feature,
                    'status' => isset($_POST['features'][$feature]) ? 1 : 0
                ];
            }
            
            $featuresJson = json_encode($features);
            
            if ($action === 'edit' && $serviceId) {
                // Update existing plan
                $stmt = $pdo->prepare("UPDATE service_pricing SET 
                    service_name = ?,
                    price = ?,
                    features = ?,
                    duration = ?,
                    is_popular = ?,
                    status = ?,
                    updated_at = NOW()
                    WHERE service_id = ?");
                
                $stmt->execute([
                    $serviceName,
                    $price,
                    $featuresJson,
                    $duration,
                    $isPopular,
                    $status,
                    $serviceId
                ]);
                
                $_SESSION['toastMessage'] = "Pricing plan updated successfully";
                $_SESSION['toastType'] = "success";
                
            } else {
                // Create new plan
                $stmt = $pdo->prepare("INSERT INTO service_pricing (
                    service_id,
                    service_name,
                    price,
                    features,
                    duration,
                    is_popular,
                    status,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NULL)");
                
                $newId = uniqid();
                $stmt->execute([
                    $newId,
                    $serviceName,
                    $price,
                    $featuresJson,
                    $duration,
                    $isPopular,
                    $status
                ]);
                
                $_SESSION['toastMessage'] = "Pricing plan created successfully";
                $_SESSION['toastType'] = "success";
            }
            
            header("Location: services-pricing");
            exit();
        }
        
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "danger";
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
                <h1><i class="bi bi-tags"></i> Services Pricing Management</h1>
                <p>Manage your service pricing plans and features</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Services</a></li>
                <li class="breadcrumb-item"><a href="#">Pricing Management</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
        <?php if ($toastMessage): ?>
        <div class="alert alert-<?= $toastType ?> alert-dismissible fade show" role="alert">
            <?= $toastMessage ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        </script>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Pricing Plans</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                            <i class="bi bi-plus-circle"></i> Add New Plan
                        </button>
                    </div>
                    
                    <div class="tile-body">
                        <?php if (empty($pricingPlans)): ?>
                            <div class="alert alert-info">
                                No pricing plans found. Create your first plan.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Plan Name</th>
                                            <th>Price</th>
                                            <th>Duration</th>
                                            <th>Popular</th>
                                            <th>Status</th>
                                            <th>Features</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pricingPlans as $plan): 
                                            $features = json_decode($plan['features'], true);
                                            $activeFeatures = array_filter($features, function($f) { return $f['status'] == 1; });
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($plan['service_name']) ?></td>
                                                <td><?= htmlspecialchars($plan['price']) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($plan['duration'])) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $plan['is_popular'] ? 'success' : 'secondary' ?>">
                                                        <?= $plan['is_popular'] ? 'Yes' : 'No' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $plan['status'] === 'public' ? 'primary' : 'warning' ?>">
                                                        <?= htmlspecialchars(ucfirst($plan['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= count($activeFeatures) ?> of <?= count($features) ?> features
                                                </td>
                                                <td>
                                                    <a href="services-pricing?plan_id=<?= $plan['service_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="service_id" value="<?= $plan['service_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Delete
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
        
        <!-- Add/Edit Plan Modal -->
        <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?= $currentPlan ? 'edit' : 'add' ?>">
                        <?php if ($currentPlan): ?>
                            <input type="hidden" name="service_id" value="<?= $currentPlan['service_id'] ?>">
                        <?php endif; ?>
                        
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPlanModalLabel">
                                <?= $currentPlan ? 'Edit Pricing Plan' : 'Add New Pricing Plan' ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_name" class="form-label">Plan Name</label>
                                        <input type="text" class="form-control" id="service_name" name="service_name" 
                                               value="<?= $currentPlan['service_name'] ?? '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="text" class="form-control" id="price" name="price" 
                                               value="<?= $currentPlan['price'] ?? '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Duration</label>
                                        <select class="form-select" id="duration" name="duration" required>
                                            <option value="month" <?= ($currentPlan['duration'] ?? '') === 'month' ? 'selected' : '' ?>>Monthly</option>
                                            <option value="year" <?= ($currentPlan['duration'] ?? '') === 'year' ? 'selected' : '' ?>>Yearly</option>
                                            <option value="week" <?= ($currentPlan['duration'] ?? '') === 'week' ? 'selected' : '' ?>>Weekly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_popular" name="is_popular" 
                                            <?= ($currentPlan['is_popular'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_popular">Mark as Popular Plan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="public" <?= ($currentPlan['status'] ?? '') === 'public' ? 'selected' : '' ?>>Public</option>
                                            <option value="private" <?= ($currentPlan['status'] ?? '') === 'private' ? 'selected' : '' ?>>Private</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Features</label>
                                <div class="row">
                                    <?php foreach ($featuresList as $feature): 
                                        $isChecked = false;
                                        if ($currentPlan) {
                                            foreach ($currentPlan['features'] as $f) {
                                                if ($f['feature'] === $feature && $f['status'] == 1) {
                                                    $isChecked = true;
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="feature_<?= preg_replace('/[^a-z0-9]/i', '_', strtolower($feature)) ?>" 
                                                       name="features[<?= htmlspecialchars($feature) ?>]"
                                                       <?= $isChecked ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="feature_<?= preg_replace('/[^a-z0-9]/i', '_', strtolower($feature)) ?>">
                                                    <?= htmlspecialchars($feature) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <?= $currentPlan ? 'Update Plan' : 'Create Plan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // Show modal if editing a plan
        <?php if ($currentPlan): ?>
        $(document).ready(function() {
            $('#addPlanModal').modal('show');
        });
        <?php endif; ?>
    </script>
</body>
</html>