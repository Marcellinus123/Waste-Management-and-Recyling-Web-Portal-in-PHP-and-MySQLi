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
$toastType = ''; // 'success', 'error', or 'warning'

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_feature'])) {
            // Add new feature comparison
            $feature = filter_input(INPUT_POST, 'feature', FILTER_SANITIZE_STRING);
            $basic = filter_input(INPUT_POST, 'basic', FILTER_SANITIZE_STRING);
            $pro = filter_input(INPUT_POST, 'pro', FILTER_SANITIZE_STRING);
            $enterprise = filter_input(INPUT_POST, 'enterprise', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            // Validate required fields
            if (empty($feature) || empty($status)) {
                throw new Exception("Feature name and status are required");
            }
            
            // Set default values if empty
            $basic = empty($basic) ? '0' : $basic;
            $pro = empty($pro) ? '0' : $pro;
            $enterprise = empty($enterprise) ? '0' : $enterprise;
            
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO feature_comparison 
                                  (feature, basic, pro, enterprise, status, created_at) 
                                  VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $feature,
                $basic,
                $pro,
                $enterprise,
                $status
            ]);
            
            $toastMessage = "Feature comparison added successfully!";
            $toastType = "success";
            
        } elseif (isset($_POST['update_feature'])) {
            // Update existing feature comparison
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $feature = filter_input(INPUT_POST, 'feature', FILTER_SANITIZE_STRING);
            $basic = filter_input(INPUT_POST, 'basic', FILTER_SANITIZE_STRING);
            $pro = filter_input(INPUT_POST, 'pro', FILTER_SANITIZE_STRING);
            $enterprise = filter_input(INPUT_POST, 'enterprise', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            // Validate required fields
            if (empty($id) || empty($feature) || empty($status)) {
                throw new Exception("ID, feature name and status are required");
            }
            
            // Set default values if empty
            $basic = empty($basic) ? '0' : $basic;
            $pro = empty($pro) ? '0' : $pro;
            $enterprise = empty($enterprise) ? '0' : $enterprise;
            
            // Update database
            $stmt = $pdo->prepare("UPDATE feature_comparison 
                                  SET feature = ?, basic = ?, pro = ?, enterprise = ?, status = ? 
                                  WHERE id = ?");
            $stmt->execute([
                $feature,
                $basic,
                $pro,
                $enterprise,
                $status,
                $id
            ]);
            
            $toastMessage = "Feature comparison updated successfully!";
            $toastType = "success";
            
        } elseif (isset($_POST['delete_feature'])) {
            // Delete feature comparison
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($id)) {
                throw new Exception("Feature ID is required");
            }
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM feature_comparison WHERE id = ?");
            $stmt->execute([$id]);
            
            $toastMessage = "Feature comparison deleted successfully!";
            $toastType = "success";
        }
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get all feature comparisons for listing
$features = $pdo->query("SELECT * FROM feature_comparison ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Function to format feature availability
function formatAvailability($value) {
    if ($value === '1') {
        return '<i class="bi bi-check-circle-fill text-success"></i>';
    } elseif ($value === '0') {
        return '<i class="bi bi-x-circle-fill text-danger"></i>';
    }
    return htmlspecialchars($value);
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
                <h1><i class="bi bi-list-check"></i> Feature Comparison Management</h1>
                <p>Manage the feature comparison table displayed on the website</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item"><a href="#">Feature Comparison</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
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
                    <div class="tile-body">
                        <!-- Add/Edit Feature Form -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><?= isset($_GET['edit']) ? 'Edit' : 'Add New' ?> Feature Comparison</h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <?php if (isset($_GET['edit'])): 
                                        $editId = $_GET['edit'];
                                        $editFeature = $pdo->prepare("SELECT * FROM feature_comparison WHERE id = ?");
                                        $editFeature->execute([$editId]);
                                        $feature = $editFeature->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                        <input type="hidden" name="id" value="<?= $feature['id'] ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="feature" class="form-label">Feature Name</label>
                                        <input type="text" class="form-control" id="feature" name="feature" 
                                               value="<?= isset($feature) ? htmlspecialchars($feature['feature']) : '' ?>" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="basic" class="form-label">Basic Plan</label>
                                                <select class="form-select" id="basic" name="basic">
                                                    <option value="0" <?= (isset($feature) && $feature['basic'] === '0') ? 'selected' : '' ?>>Not Available</option>
                                                    <option value="1" <?= (isset($feature) && $feature['basic'] === '1') ? 'selected' : '' ?>>Available</option>
                                                    <option value="Limited" <?= (isset($feature) && $feature['basic'] === 'Limited') ? 'selected' : '' ?>>Limited</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="pro" class="form-label">Pro Plan</label>
                                                <select class="form-select" id="pro" name="pro">
                                                    <option value="0" <?= (isset($feature) && $feature['pro'] === '0') ? 'selected' : '' ?>>Not Available</option>
                                                    <option value="1" <?= (isset($feature) && $feature['pro'] === '1') ? 'selected' : '' ?>>Available</option>
                                                    <option value="Limited" <?= (isset($feature) && $feature['pro'] === 'Limited') ? 'selected' : '' ?>>Limited</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="enterprise" class="form-label">Enterprise Plan</label>
                                                <select class="form-select" id="enterprise" name="enterprise">
                                                    <option value="0" <?= (isset($feature) && $feature['enterprise'] === '0') ? 'selected' : '' ?>>Not Available</option>
                                                    <option value="1" <?= (isset($feature) && $feature['enterprise'] === '1') ? 'selected' : '' ?>>Available</option>
                                                    <option value="Limited" <?= (isset($feature) && $feature['enterprise'] === 'Limited') ? 'selected' : '' ?>>Limited</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="public" <?= (isset($feature) && $feature['status'] === 'public') ? 'selected' : '' ?>>Public</option>
                                            <option value="private" <?= (isset($feature) && $feature['status'] === 'private') ? 'selected' : '' ?>>Private</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <?php if (isset($_GET['edit'])): ?>
                                            <button type="submit" name="update_feature" class="btn btn-primary me-2">
                                                <i class="bi bi-save"></i> Update Feature
                                            </button>
                                            <a href="feature-comparison" class="btn btn-secondary">Cancel</a>
                                        <?php else: ?>
                                            <button type="submit" name="add_feature" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Add Feature
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Feature Comparisons List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Feature Comparisons</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Feature</th>
                                                <th>Basic</th>
                                                <th>Pro</th>
                                                <th>Enterprise</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($features as $feature): ?>
                                                <tr>
                                                    <td><?= $feature['id'] ?></td>
                                                    <td><?= htmlspecialchars($feature['feature']) ?></td>
                                                    <td><?= formatAvailability($feature['basic']) ?></td>
                                                    <td><?= formatAvailability($feature['pro']) ?></td>
                                                    <td><?= formatAvailability($feature['enterprise']) ?></td>
                                                    <td>
                                                        <span class="badge <?= $feature['status'] === 'public' ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= ucfirst($feature['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($feature['created_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="feature-comparison?edit=<?= $feature['id'] ?>" class="btn btn-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this feature comparison?');">
                                                                <input type="hidden" name="id" value="<?= $feature['id'] ?>">
                                                                <button type="submit" name="delete_feature" class="btn btn-danger">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
            toastBody.textContent = message;
            
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
            document.addEventListener('DOMContentLoaded', () => {
                showToast("<?= addslashes($toastMessage) ?>", "<?= $toastType ?>");
            });
        <?php endif; ?>
    </script>
</body>
</html>