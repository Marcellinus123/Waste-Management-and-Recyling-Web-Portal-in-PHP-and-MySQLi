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
            // Add new feature highlight
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            $image = '';
            
            // Process sub features (JSON data)
            $subFeatures = [];
            if (isset($_POST['sub_features']) && is_array($_POST['sub_features'])) {
                foreach ($_POST['sub_features'] as $feature) {
                    if (!empty(trim($feature))) {
                        $subFeatures[] = ['feature' => trim($feature)];
                    }
                }
            }
            
            // Validate required fields
            if (empty($title) || empty($status)) {
                throw new Exception("Title and status are required");
            }
            
            // Handle image upload
            if (isset($_FILES['image'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                        throw new Exception("Only JPG, PNG, and GIF images are allowed");
                    }
                    
                    if ($_FILES['image']['size'] > $maxSize) {
                        throw new Exception("Image size must be less than 2MB");
                    }
                    
                    $uploadDir = '../WM images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $newFileName = 'feature_' . uniqid() . '.' . $fileExt;
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $image = $targetPath;
                    } else {
                        throw new Exception("Failed to upload image");
                    }
                }
            }
            
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO feature_highlights 
                                  (title, sub_features, image, status, created_at) 
                                  VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $title,
                json_encode($subFeatures),
                $image,
                $status
            ]);
            
            $toastMessage = "Feature highlight added successfully!";
            $toastType = "success";
            
        } elseif (isset($_POST['update_feature'])) {
            // Update existing feature highlight
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            // Process sub features (JSON data)
            $subFeatures = [];
            if (isset($_POST['sub_features']) && is_array($_POST['sub_features'])) {
                foreach ($_POST['sub_features'] as $feature) {
                    if (!empty(trim($feature))) {
                        $subFeatures[] = ['feature' => trim($feature)];
                    }
                }
            }
            
            // Validate required fields
            if (empty($id) || empty($title) || empty($status)) {
                throw new Exception("ID, title and status are required");
            }
            
            // Get current image path
            $stmt = $pdo->prepare("SELECT image FROM feature_highlights WHERE id = ?");
            $stmt->execute([$id]);
            $currentImage = $stmt->fetchColumn();
            $image = $currentImage;
            
            // Handle image upload if new image provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                    throw new Exception("Only JPG, PNG, and GIF images are allowed");
                }
                
                if ($_FILES['image']['size'] > $maxSize) {
                    throw new Exception("Image size must be less than 2MB");
                }
                
                $uploadDir = 'uploads/feature_images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Delete old image if exists
                if ($currentImage && file_exists($currentImage)) {
                    unlink($currentImage);
                }
                
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newFileName = 'feature_' . uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $targetPath;
                } else {
                    throw new Exception("Failed to upload image");
                }
            }
            
            // Update database
            $stmt = $pdo->prepare("UPDATE feature_highlights 
                                  SET title = ?, sub_features = ?, image = ?, status = ? 
                                  WHERE id = ?");
            $stmt->execute([
                $title,
                json_encode($subFeatures),
                $image,
                $status,
                $id
            ]);
            
            $toastMessage = "Feature highlight updated successfully!";
            $toastType = "success";
            
        } elseif (isset($_POST['delete_feature'])) {
            // Delete feature highlight
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($id)) {
                throw new Exception("Feature ID is required");
            }
            
            // Get image path to delete file
            $stmt = $pdo->prepare("SELECT image FROM feature_highlights WHERE id = ?");
            $stmt->execute([$id]);
            $imagePath = $stmt->fetchColumn();
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM feature_highlights WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete image file if exists
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            $toastMessage = "Feature highlight deleted successfully!";
            $toastType = "success";
        }
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get all feature highlights for listing
$features = $pdo->query("SELECT * FROM feature_highlights ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Function to format sub features for display
function formatSubFeatures($json) {
    $subFeatures = json_decode($json, true);
    $output = '';
    if (is_array($subFeatures)) {
        foreach ($subFeatures as $item) {
            if (isset($item['feature'])) {
                $output .= '<li>' . htmlspecialchars($item['feature']) . '</li>';
            }
        }
    }
    return $output ? '<ul>' . $output . '</ul>' : '';
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
                <h1><i class="bi bi-stars"></i> Feature Highlights Management</h1>
                <p>Manage the feature highlights displayed on the website</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item"><a href="#">Feature Highlights</a></li>
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
                        <!-- Add New Feature Form -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><?= isset($_GET['edit']) ? 'Edit' : 'Add New' ?> Feature Highlight</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" enctype="multipart/form-data">
                                    <?php if (isset($_GET['edit'])): 
                                        $editId = $_GET['edit'];
                                        $editFeature = $pdo->prepare("SELECT * FROM feature_highlights WHERE id = ?");
                                        $editFeature->execute([$editId]);
                                        $feature = $editFeature->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                        <input type="hidden" name="id" value="<?= $feature['id'] ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= isset($feature) ? htmlspecialchars($feature['title']) : '' ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sub_features" class="form-label">Sub Features (One per line)</label>
                                        <textarea class="form-control" id="sub_features" name="sub_features[]" rows="5"><?php 
                                            if (isset($feature)) {
                                                $subFeatures = json_decode($feature['sub_features'], true);
                                                if (is_array($subFeatures)) {
                                                    foreach ($subFeatures as $item) {
                                                        if (isset($item['feature'])) {
                                                            echo htmlspecialchars($item['feature']) . "\n";
                                                        }
                                                    }
                                                }
                                            }
                                        ?></textarea>
                                        <small class="text-muted">Enter each sub feature on a new line</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Feature Image</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                        <?php if (isset($feature) && !empty($feature['image'])): ?>
                                            <div class="mt-2">
                                                <img src="../WM images/<?= htmlspecialchars($feature['image']) ?>" alt="Current Image" style="max-height: 100px;">
                                                <p class="small text-muted mt-1">Current image</p>
                                            </div>
                                        <?php endif; ?>
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
                                            <a href="feature-highlights" class="btn btn-secondary">Cancel</a>
                                        <?php else: ?>
                                            <button type="submit" name="add_feature" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Add Feature
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Feature Highlights List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Feature Highlights</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Sub Features</th>
                                                <th>Image</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($features as $feature): ?>
                                                <tr>
                                                    <td><?= $feature['id'] ?></td>
                                                    <td><?= htmlspecialchars($feature['title']) ?></td>
                                                    <td><?= formatSubFeatures($feature['sub_features']) ?></td>
                                                    <td>
                                                        <?php if (!empty($feature['image'])): ?>
                                                            <img src="../WM images/<?= htmlspecialchars($feature['image']) ?>" alt="Feature Image" style="max-height: 50px;">
                                                        <?php else: ?>
                                                            <span class="text-muted">No image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= $feature['status'] === 'public' ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= ucfirst($feature['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($feature['created_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="feature-highlights?edit=<?= $feature['id'] ?>" class="btn btn-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this feature highlight?');">
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