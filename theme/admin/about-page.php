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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Prepare data
        $our_story = $_POST['our_story'] ?? '';
        $our_mission = $_POST['our_mission'] ?? '';
        $our_vision = $_POST['our_vision'] ?? '';
        
        // Process core values (convert from form fields to JSON)
        $core_values = [];
        if (isset($_POST['core_values_title']) && isset($_POST['core_values_desc'])) {
            $titles = $_POST['core_values_title'];
            $descs = $_POST['core_values_desc'];
            
            foreach ($titles as $index => $title) {
                if (!empty($title) && !empty($descs[$index])) {
                    $core_values[] = [
                        'title' => $title,
                        'description' => $descs[$index]
                    ];
                }
            }
        }
        $core_values_json = json_encode($core_values);
        
        $status = $_POST['status'] ?? 'public';
        $updated_by = $_SESSION['username'] ?? 'admin';
        
        // Initialize variables for image handling
        $image_update = false;
        $image_path = null;
        
        // Handle image upload only if a new image was provided
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
            $file_name = 'about-' . uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
                $image_update = true;
                
                // Delete old image if exists
                $stmt = $pdo->prepare("SELECT image FROM about_page WHERE id = 1");
                $stmt->execute();
                $old_image = $stmt->fetchColumn();
                
                if ($old_image && file_exists($old_image)) {
                    unlink($old_image);
                }
            } else {
                throw new Exception("Failed to upload image");
            }
        }
        
        // Prepare the SQL query based on whether we're updating the image or not
        if ($image_update) {
            $stmt = $pdo->prepare("UPDATE about_page SET 
                                our_story = ?, 
                                our_mission = ?, 
                                our_vision = ?, 
                                core_values = ?, 
                                image = ?, 
                                status = ?, 
                                date_updated = NOW(), 
                                updated_by = ?
                                WHERE id = 1");
            $params = [
                $our_story,
                $our_mission,
                $our_vision,
                $core_values_json,
                $image_path,
                $status,
                $updated_by
            ];
        } else {
            $stmt = $pdo->prepare("UPDATE about_page SET 
                                our_story = ?, 
                                our_mission = ?, 
                                our_vision = ?, 
                                core_values = ?, 
                                status = ?, 
                                date_updated = NOW(), 
                                updated_by = ?
                                WHERE id = 1");
            $params = [
                $our_story,
                $our_mission,
                $our_vision,
                $core_values_json,
                $status,
                $updated_by
            ];
        }
        
        $stmt->execute($params);
        
        $toastMessage = "About page updated successfully!";
        $toastType = "success";
        
    } catch (Exception $e) {
        $toastMessage = "Error updating about page: " . $e->getMessage();
        $toastType = "error";
    }
    
    // Store message in session and redirect
    $_SESSION['toastMessage'] = $toastMessage;
    $_SESSION['toastType'] = $toastType;
    header("Location: about-page");
    exit();
}

// Fetch current about page data
try {
    $stmt = $pdo->query("SELECT * FROM about_page WHERE id = 1");
    $about_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Decode core values JSON
    $core_values = [];
    if (!empty($about_data['core_values'])) {
        $core_values = json_decode($about_data['core_values'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding core values: " . json_last_error_msg());
        }
    }
    
    // Ensure we have at least 4 core values (for the form)
    while (count($core_values) < 4) {
        $core_values[] = ['title' => '', 'description' => ''];
    }
    
} catch (Exception $e) {
    $toastMessage = "Error loading about page data: " . $e->getMessage();
    $toastType = "error";
}

// Check for toast message in session
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
                <h1><i class="bi bi-info-circle"></i> About Page Management</h1>
                <p>Edit your website's About page content</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Content</li>
                <li class="breadcrumb-item"><a href="#">About Page</a></li>
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
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Our Story -->
                                    <div class="mb-4">
                                        <label for="our_story" class="form-label fw-bold">Our Story</label>
                                        <textarea class="form-control" id="our_story" name="our_story" rows="8" required><?= 
                                            htmlspecialchars($about_data['our_story'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <!-- Our Mission -->
                                    <div class="mb-4">
                                        <label for="our_mission" class="form-label fw-bold">Our Mission</label>
                                        <textarea class="form-control" id="our_mission" name="our_mission" rows="4" required><?= 
                                            htmlspecialchars($about_data['our_mission'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <!-- Our Vision -->
                                    <div class="mb-4">
                                        <label for="our_vision" class="form-label fw-bold">Our Vision</label>
                                        <textarea class="form-control" id="our_vision" name="our_vision" rows="4" required><?= 
                                            htmlspecialchars($about_data['our_vision'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <!-- Image Upload -->
                                    <div class="mb-4">
                                        <label for="image" class="form-label fw-bold">About Page Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        
                                        <?php if (!empty($about_data['image'])): ?>
                                            <div class="mt-2">
                                                <img src="../WM images/<?= htmlspecialchars($about_data['image']) ?>" alt="Current About Image" class="img-thumbnail" style="max-height: 200px;">
                                                <p class="text-muted mt-1">Current image</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Core Values -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Core Values</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <?php foreach ($core_values as $index => $value): ?>
                                                    <div class="mb-3 core-value-group">
                                                        <label class="form-label">Value <?= $index + 1 ?></label>
                                                        <input type="text" class="form-control mb-2" 
                                                               name="core_values_title[]" 
                                                               value="<?= htmlspecialchars($value['title'] ?? '') ?>" 
                                                               placeholder="Title">
                                                        <textarea class="form-control" 
                                                                  name="core_values_desc[]" 
                                                                  rows="2" 
                                                                  placeholder="Description"><?= 
                                                                      htmlspecialchars($value['description'] ?? '') ?></textarea>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status -->
                                    <div class="mb-4">
                                        <label for="status" class="form-label fw-bold">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="public" <?= ($about_data['status'] ?? '') === 'public' ? 'selected' : '' ?>>Public</option>
                                            <option value="private" <?= ($about_data['status'] ?? '') === 'private' ? 'selected' : '' ?>>Private</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                            </div>
                        </form>
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