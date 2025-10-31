<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once('db.php');

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: userlogin");
    exit();
}

// Check if usertype is not 'waste_user'
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'waste_driver') {
    header('Location: userlogin');
    exit();
}

// Check if user_id is set and not null
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: userlogin');
    exit();
}

// Initialize toast variables
$toastMessage = '';
$toastType = ''; // 'success', 'error', or 'warning'

// Handle Profile Image Upload FIRST (before other form processing)
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    try {
        $uploadDir = 'uploads/profile_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $fileExt = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;
        
        // Check if file is an image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Only JPG, PNG, and GIF files are allowed");
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            // Update database with new image path
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE user_id = ?");
            $stmt->execute([$targetPath, $_SESSION['user_id']]);
            
            // Update session if needed
            $_SESSION['profile_image'] = $targetPath;
            
            $toastMessage = "Profile image updated successfully!";
            $toastType = "success";
            
            $_SESSION['toastMessage'] = $toastMessage;
            $_SESSION['toastType'] = $toastType;
            
            header("Location: profile");
            exit();
        } else {
            throw new Exception("Failed to upload image");
        }
    } catch (Exception $e) {
        $toastMessage = "Error uploading profile image: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle Profile Update
if (isset($_POST['update-profile'])) {
    // Get form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $country = $_POST['country'] ?? '';
    $region = $_POST['region'] ?? '';
    
    try {
        // Update user profile
        $stmt = $pdo->prepare("UPDATE users SET 
                            first_name = ?, 
                            last_name = ?, 
                            email = ?, 
                            phone = ?, 
                            address = ?, 
                            city = ?, 
                            zip = ?, 
                            country = ?, 
                            region = ? 
                            WHERE user_id = ?");
        $stmt->execute([
            $first_name, $last_name, $email, $phone, 
            $address, $city, $zip, $country, $region,
            $_SESSION['user_id']
        ]);
        
        // Update session data
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        
        $toastMessage = "Profile updated successfully!";
        $toastType = "success";
        
        // Store in session for redirect
        $_SESSION['toastMessage'] = $toastMessage;
        $_SESSION['toastType'] = $toastType;
        
        header("Location: profile");
        exit();
    } catch (Exception $e) {
        $toastMessage = "Error updating profile: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle Password Change
if (isset($_POST['update-password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!password_verify($current_password, $user['password'])) {
            throw new Exception("Current password is incorrect");
        }
        
        if ($new_password !== $confirm_password) {
            throw new Exception("New passwords don't match");
        }
        
        if (strlen($new_password) < 8) {
            throw new Exception("Password must be at least 8 characters");
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        
        $toastMessage = "Password updated successfully!";
        $toastType = "success";
        
        // Store in session for redirect
        $_SESSION['toastMessage'] = $toastMessage;
        $_SESSION['toastType'] = $toastType;
        
        header("Location: profile");
        exit();
    } catch (Exception $e) {
        $toastMessage = "Error changing password: " . $e->getMessage();
        $toastType = "error";
    }
}

// Check for toast messages in session
if (isset($_SESSION['toastMessage'])) {
    $toastMessage = $_SESSION['toastMessage'];
    $toastType = $_SESSION['toastType'];
    unset($_SESSION['toastMessage']);
    unset($_SESSION['toastType']);
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Set default profile image if none exists
if (empty($user['avatar'])) {
    $user['avatar'] = 'https://via.placeholder.com/150';
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
          <h1><i class="bi bi-person-circle"></i> Profile Settings</h1>
          <p>Manage your account information</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item"><a href="#">Dashboard / Profile Settings</a></li>
        </ul>
      </div>
      <!-- Toast Container for showing form submission status -->
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
                <!-- Profile Image Section - Simplified without overlay -->
                <div class="profile-image-section">
                  <form method="post" enctype="multipart/form-data" id="profile-image-form">
                    <div class="text-center mb-3">
                      <img src="<?= htmlspecialchars($user['avatar']) ?>" 
                           alt="Profile Image" class="profile-image rounded-circle" id="profile-image-preview"
                           style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    </div>
                    <div class="text-center">
                      <label for="profile-image-upload" class="btn btn-sm btn-primary">
                        <i class="fas fa-camera"></i> Change Photo
                      </label>
                      <input type="file" name="profile_image" id="profile-image-upload" accept="image/*" style="display: none;">
                    </div>
                  </form>
                  <h4 class="profile-name text-center mt-3">
                    <?= htmlspecialchars($user['first_name'] ?? '') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?>
                  </h4>
                  <p class="text-muted text-center"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                  <?php
                  $ac_bal = htmlspecialchars($user['account_balance'] ?? '0.00');
                  ?>
                  <p class="text-muted text-center">Account: <?= number_format($ac_bal, 2) ?></p>
                </div>
                
                <!-- Profile Form Section -->
                <div class="profile-form-section">
                  <form method="post" id="profile-form" enctype="multipart/form-data">
                    <div class="form-grid">
                      <div class="form-group">
                        <label for="settings-firstname">First Name</label>
                        <input type="text" name="first_name" id="settings-firstname" class="form-control" 
                            value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-lastname">Last Name</label>
                        <input type="text" name="last_name" id="settings-lastname" class="form-control" 
                            value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-email">Email</label>
                        <input type="email" name="email" id="settings-email" class="form-control" 
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-phone">Phone</label>
                        <input type="tel" name="phone" id="settings-phone" class="form-control" 
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-address">Address</label>
                        <input type="text" name="address" id="settings-address" class="form-control" 
                            value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-city">City</label>
                        <input type="text" name="city" id="settings-city" class="form-control" 
                            value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-zip">ZIP Code</label>
                        <input type="text" name="zip" id="settings-zip" class="form-control" 
                            value="<?= htmlspecialchars($user['zip'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label for="settings-country">Country</label>
                        <select name="country" id="settings-country" class="form-control">
                          <option value="Ghana" <?= ($user['country'] ?? '') === 'Ghana' ? 'selected' : '' ?>>Ghana</option>
                        </select>
                      </div> 
                      <div class="form-group">
                        <label for="settings-region">Region</label>
                        <select name="region" id="settings-region" class="form-control">
                          <?php
                          $ghanaRegions = [
                              'Ashanti', 'Brong-Ahafo', 'Central', 'Eastern', 'Greater Accra', 
                              'Northern', 'Upper East', 'Upper West', 'Volta', 'Western',
                              'Ahafo', 'Bono East', 'North East', 'Oti', 'Savannah', 'Western North'
                          ];
                          foreach ($ghanaRegions as $region) {
                              $selected = ($user['region'] ?? '') === $region ? 'selected' : '';
                              echo "<option value=\"$region\" $selected>$region</option>";
                          }
                          ?>
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
                        <input type="password" name="new_password" id="new-password" class="form-control" required>
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
        const toastEl = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000 // 5 seconds
        });

        function showToast(message, type = 'info') {
            const toastHeader = toastEl.querySelector('.toast-header');
            const toastBody = toastEl.querySelector('.toast-body');
            
            toastBody.innerText = message;
            
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

        <?php if (!empty($toastMessage)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showToast("<?= addslashes($toastMessage) ?>", "<?= $toastType ?>");
            });
        <?php endif; ?>


        document.getElementById('profile-image-upload').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('profile-image-preview').src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
                
                document.getElementById('profile-image-form').submit();
            }
        });
    </script>
  </body>
</html>