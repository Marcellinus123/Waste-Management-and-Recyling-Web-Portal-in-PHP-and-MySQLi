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

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-profile'])) {
    // Sanitize and validate form data
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $region = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_STRING);
    
    try {
        // Validate required fields
        if (empty($first_name) || empty($last_name) || empty($email)) {
            throw new Exception("First name, last name and email are required");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            throw new Exception("Email already in use by another account");
        }

        // Update admin profile
        $stmt = $pdo->prepare("UPDATE admins SET 
                            first_name = ?, 
                            last_name = ?, 
                            email = ?, 
                            phone = ?, 
                            address = ?, 
                            city = ?, 
                            zip_code = ?, 
                            country = ?
                            WHERE user_id = ?");
        $stmt->execute([
            $first_name, $last_name, $email, $phone, 
            $address, $city, $zip, $country,
            $_SESSION['user_id']
        ]);
        
        // Update session data
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;
        
        $toastMessage = "Profile updated successfully!";
        $toastType = "success";
    } catch (Exception $e) {
        $toastMessage = "Error updating profile: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {


        if (strlen($new_password) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }
        
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $admin = $stmt->fetch();
        
        if (!$admin || !password_verify($current_password, $admin['password'])) {
            throw new Exception("Current password is incorrect");
        }
        
        if ($new_password !== $confirm_password) {
            throw new Exception("New passwords don't match");
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        
        $toastMessage = "Password updated successfully!";
        $toastType = "success";
    } catch (Exception $e) {
        $toastMessage = "Error changing password: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    try {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        // Validate file
        if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
            throw new Exception("Only JPG, PNG, and GIF images are allowed");
        }
        
        if ($_FILES['profile_image']['size'] > $max_size) {
            throw new Exception("Image size must be less than 2MB");
        }
        
        $uploadDir = 'uploads/profile_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExt = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'admin_' . $_SESSION['user_id'] . '_' . uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            // Delete old image if exists
            $stmt = $pdo->prepare("SELECT avatar FROM admins WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $oldImage = $stmt->fetchColumn();
            
            if ($oldImage && file_exists($oldImage)) {
                unlink($oldImage);
            }
            
            // Update database with new image path
            $stmt = $pdo->prepare("UPDATE admins SET avatar = ? WHERE user_id = ?");
            $stmt->execute([$targetPath, $_SESSION['user_id']]);
            
            // Update session
            $_SESSION['avatar'] = $targetPath;
            
            $toastMessage = "Profile image updated successfully!";
            $toastType = "success";
        } else {
            throw new Exception("Failed to upload image");
        }
    } catch (Exception $e) {
        $toastMessage = "Error uploading profile image: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get current admin data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// If no admin data found, redirect to login
if (!$admin) {
    header("Location: userlogin?error=invalid_session");
    exit();
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
                <h1><i class="bi bi-person-circle"></i> Admin Profile Settings</h1>
                <p>Manage your administrator account information</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item"><a href="#">Profile Settings</a></li>
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
            <div class="clearix"></div>
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <div class="profile-container">
                            <!-- Profile Image Section -->
                            <div class="profile-image-section">
                                <form method="post" enctype="multipart/form-data" id="profile-image-form">
                                    <div class="profile-image-wrapper">
                                        <img src="<?= !empty($admin['avatar']) ? htmlspecialchars($admin['avatar']) : '../assets/images/default-admin.png' ?>" 
                                             alt="Profile Image" class="profile-image rounded-circle" id="profile-image-preview">
                                        <div class="profile-image-overlay">
                                            <label for="profile-image-upload" class="profile-image-label">
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input type="file" class="form-control"  name="profile_image" id="profile-image-upload" accept="image/*" style="display: block;">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary mt-2" style="display: none;" id="save-image-btn">
                                        <i class="fas fa-save"></i> Save Image
                                    </button>
                                </form>
                                <h4 class="profile-name text-center mt-3">
                                    <?= htmlspecialchars($admin['first_name'] ?? '') ?> <?= htmlspecialchars($admin['last_name'] ?? '') ?>
                                </h4>
                                <p class="text-muted text-center"><?= htmlspecialchars($admin['email'] ?? '') ?></p>
                                <p class="text-muted text-center">
                                    <span class="badge bg-success">Administrator</span>
                                </p>
                            </div>
                            
                            <!-- Profile Form Section -->
                            <div class="profile-form-section">
                                <form method="post" id="profile-form">
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="settings-firstname">First Name</label>
                                            <input type="text" name="first_name" id="settings-firstname" class="form-control" 
                                                value="<?= htmlspecialchars($admin['first_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-lastname">Last Name</label>
                                            <input type="text" name="last_name" id="settings-lastname" class="form-control" 
                                                value="<?= htmlspecialchars($admin['last_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-email">Email</label>
                                            <input type="email" name="email" id="settings-email" class="form-control" 
                                                value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-phone">Phone</label>
                                            <input type="tel" name="phone" id="settings-phone" class="form-control" 
                                                value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-address">Address</label>
                                            <input type="text" name="address" id="settings-address" class="form-control" 
                                                value="<?= htmlspecialchars($admin['address'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-city">City</label>
                                            <input type="text" name="city" id="settings-city" class="form-control" 
                                                value="<?= htmlspecialchars($admin['city'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-zip">ZIP Code</label>
                                            <input type="text" name="zip_code" id="settings-zip" class="form-control" 
                                                value="<?= htmlspecialchars($admin['zip_code'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="settings-country">Country</label>
                                            <select name="country" id="settings-country" class="form-control">
                                                <option value="Ghana" <?= ($admin['country'] ?? '') === 'Ghana' ? 'selected' : '' ?>>Ghana</option>
                                                <option value="Other" <?= ($admin['country'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div> 
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="update-profile" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Profile Changes
                                        </button>
                                    </div>
                                </form>
                                
                                <hr class="my-4">
                                
                                <form method="post" id="password-form">
                                    <h5 class="mb-3">Change Password</h5>
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="current-password">Current Password</label>
                                            <input type="password" name="current_password" id="current-password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new-password">New Password</label>
                                            <input type="password" name="new_password" id="new-password" class="form-control" required
                                                   pattern=".{8,}" title="Password must be at least 8 characters">
                                            <small class="form-text text-muted">Minimum 8 characters</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm-password">Confirm New Password</label>
                                            <input type="password" name="confirm_password" id="confirm-password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="update-password" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Update Password
                                        </button>
                                    </div>
                                </form>
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
    
    <style>
        .profile-container {
            display: flex;
            gap: 2rem;
        }
        
        .profile-image-section {
            flex: 0 0 250px;
            text-align: center;
        }
        
        .profile-form-section {
            flex: 1;
        }
        
        .profile-image-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        
        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .profile-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }
        
        .profile-image-wrapper:hover .profile-image-overlay {
            opacity: 1;
        }
        
        .profile-image-label {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        
        .form-actions {
            margin-top: 1.5rem;
            text-align: right;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }
            
            .profile-image-section {
                margin-bottom: 2rem;
            }
        }
    </style>
    
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

        // Handle profile image upload preview
        document.getElementById('profile-image-upload').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('profile-image-preview').src = e.target.result;
                    document.getElementById('save-image-btn').style.display = 'inline-block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Password confirmation validation
        document.getElementById('password-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                showToast("New passwords don't match", "error");
                document.getElementById('confirm-password').focus();
            }
        });
    </script>
</body>
</html>