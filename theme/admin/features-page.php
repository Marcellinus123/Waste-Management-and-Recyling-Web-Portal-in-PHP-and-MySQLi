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
    // Add/Update Feature
    if (isset($_POST['save_feature'])) {
        try {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            $updated_by = $_SESSION['username'] ?? 'admin';
            
            // Validate required fields
            if (empty($title) || empty($content) || empty($category)) {
                throw new Exception("Title, content and category are required");
            }
            
            // Handle image upload
            $image_name = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    throw new Exception("Only JPG, PNG, GIF, and WebP images are allowed");
                }
                
                if ($_FILES['image']['size'] > $max_size) {
                    throw new Exception("Image size must be less than 5MB");
                }
                
                $upload_dir = '../WM images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = 'feature-' . uniqid() . '.' . $file_ext;
                $target_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_name = $file_name;
                    
                    // If updating, delete old image if new one was uploaded
                    if ($id > 0) {
                        $stmt = $pdo->prepare("SELECT image FROM features_page WHERE id = ?");
                        $stmt->execute([$id]);
                        $old_image = $stmt->fetchColumn();
                        
                        if ($old_image && file_exists($upload_dir . $old_image)) {
                            unlink($upload_dir . $old_image);
                        }
                    }
                } else {
                    throw new Exception("Failed to upload image");
                }
            }
            
            if ($id > 0) {
                // Update existing feature
                if (!empty($image_name)) {
                    $stmt = $pdo->prepare("UPDATE features_page SET 
                                        title = ?, 
                                        content = ?, 
                                        category = ?,
                                        image = ?,
                                        status = ?,
                                        updated_by = ?,
                                        created_at = NOW()
                                        WHERE id = ?");
                    $stmt->execute([$title, $content, $category, $image_name, $status, $updated_by, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE features_page SET 
                                        title = ?, 
                                        content = ?, 
                                        category = ?,
                                        status = ?,
                                        updated_by = ?,
                                        created_at = NOW()
                                        WHERE id = ?");
                    $stmt->execute([$title, $content, $category, $status, $updated_by, $id]);
                }
                
                $toastMessage = "Feature updated successfully!";
            } else {
                // Insert new feature
                $stmt = $pdo->prepare("INSERT INTO features_page 
                                    (title, content, category, image, status, updated_by, created_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $content, $category, $image_name, $status, $updated_by]);
                
                $toastMessage = "Feature added successfully!";
            }
            
            $toastType = "success";
        } catch (Exception $e) {
            $toastMessage = "Error saving feature: " . $e->getMessage();
            $toastType = "error";
        }
    }
    
    // Delete Feature
    if (isset($_POST['delete_feature'])) {
        try {
            $id = intval($_POST['id']);
            
            // Get image path before deleting
            $stmt = $pdo->prepare("SELECT image FROM features_page WHERE id = ?");
            $stmt->execute([$id]);
            $image_name = $stmt->fetchColumn();
            
            // Delete the record
            $stmt = $pdo->prepare("DELETE FROM features_page WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete the image file if exists
            if ($image_name && file_exists('../WM images/' . $image_name)) {
                unlink('../WM images/' . $image_name);
            }
            
            $toastMessage = "Feature deleted successfully!";
            $toastType = "success";
        } catch (Exception $e) {
            $toastMessage = "Error deleting feature: " . $e->getMessage();
            $toastType = "error";
        }
    }
    
    // Redirect to prevent form resubmission
    if (!empty($toastMessage)) {
        $_SESSION['toastMessage'] = $toastMessage;
        $_SESSION['toastType'] = $toastType;
        header("Location: features-page.php");
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

// Fetch all features
$features = [];
try {
    $stmt = $pdo->query("SELECT * FROM features_page ORDER BY id ASC");
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error fetching features: " . $e->getMessage();
    $toastType = "error";
}

// Fetch a single feature for editing
$edit_feature = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM features_page WHERE id = ?");
        $stmt->execute([$id]);
        $edit_feature = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $toastMessage = "Error fetching feature for editing: " . $e->getMessage();
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
                <h1><i class="bi bi-star"></i> Manage Features</h1>
                <p>Create and manage your platform features</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Content</li>
                <li class="breadcrumb-item"><a href="#">Features</a></li>
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
                                        <h5><i class="bi bi-pencil-square"></i> <?= $edit_feature ? 'Edit' : 'Add'; ?> Feature</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $edit_feature ? $edit_feature['id'] : ''; ?>">
                                            
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="title" name="title" 
                                                       value="<?= $edit_feature ? htmlspecialchars($edit_feature['title']) : ''; ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="content" class="form-label">Content</label>
                                                <textarea class="form-control" id="content" name="content" rows="5" required><?= 
                                                    $edit_feature ? htmlspecialchars($edit_feature['content']) : ''; ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-select" id="category" name="category" required>
                                                    <option value="Home" <?= ($edit_feature && $edit_feature['category'] === 'Home') ? 'selected' : '' ?>>Home</option>
                                                    <option value="Business" <?= ($edit_feature && $edit_feature['category'] === 'Business') ? 'selected' : '' ?>>Business</option>
                                                    <option value="Community" <?= ($edit_feature && $edit_feature['category'] === 'Community') ? 'selected' : '' ?>>Community</option>
                                                    <option value="Other" <?= ($edit_feature && $edit_feature['category'] === 'Other') ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Feature Image</label>
                                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                                <?php if ($edit_feature && !empty($edit_feature['image'])): ?>
                                                    <div class="mt-2">
                                                        <img src="../WM images/<?= htmlspecialchars($edit_feature['image']) ?>" alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                                                        <p class="text-muted mt-1">Current image: <?= htmlspecialchars($edit_feature['image']) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="public" <?= ($edit_feature && $edit_feature['status'] === 'public') ? 'selected' : '' ?>>Public</option>
                                                    <option value="private" <?= ($edit_feature && $edit_feature['status'] === 'private') ? 'selected' : '' ?>>Private</option>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" name="save_feature" class="btn btn-primary">
                                                <i class="bi bi-save"></i> <?= $edit_feature ? 'Update' : 'Save'; ?> Feature
                                            </button>
                                            
                                            <?php if ($edit_feature): ?>
                                                <a href="features-page.php" class="btn btn-secondary">Cancel</a>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Features List -->
                            <div class="col-lg-7">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5><i class="bi bi-list-ul"></i> Features List</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($features)): ?>
                                            <p class="text-muted">No features found. Add your first feature using the form.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Title</th>
                                                            <th>Category</th>
                                                            <th>Image</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($features as $feature): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($feature['title']) ?></td>
                                                                <td><?= htmlspecialchars($feature['category']) ?></td>
                                                                <td>
                                                                    <?php if (!empty($feature['image'])): ?>
                                                                        <img src="../WM images/<?= htmlspecialchars($feature['image']) ?>" alt="Feature image" style="max-height: 50px;">
                                                                    <?php else: ?>
                                                                        <span class="text-muted">No image</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-<?= $feature['status'] === 'public' ? 'success' : 'secondary' ?>">
                                                                        <?= htmlspecialchars($feature['status']) ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="features-page.php?edit=<?= $feature['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="id" value="<?= $feature['id'] ?>">
                                                                        <button type="submit" name="delete_feature" class="btn btn-sm btn-outline-danger" 
                                                                                onclick="return confirm('Are you sure you want to delete this feature?')">
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