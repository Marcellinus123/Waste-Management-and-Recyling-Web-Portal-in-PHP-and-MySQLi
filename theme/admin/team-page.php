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
        if (isset($_POST['add_member'])) {
            // Add new team member
            $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
            $position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            $image = '';
            
            // Process social handles (JSON data)
            $socialHandles = [];
            if (isset($_POST['social_name']) && isset($_POST['profile_link'])) {
                $socialNames = $_POST['social_name'];
                $profileLinks = $_POST['profile_link'];
                
                for ($i = 0; $i < count($socialNames); $i++) {
                    if (!empty(trim($socialNames[$i])) && !empty(trim($profileLinks[$i]))) {
                        $socialHandles[] = [
                            'social_name' => trim($socialNames[$i]),
                            'user_profile_link' => trim($profileLinks[$i])
                        ];
                    }
                }
            }
            
            // Validate required fields
            if (empty($fullname) || empty($position) || empty($status)) {
                throw new Exception("Full name, position and status are required");
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
                    
                    $uploadDir = '../WM images/team/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $newFileName = 'team_' . uniqid() . '.' . $fileExt;
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $image = $targetPath;
                    } else {
                        throw new Exception("Failed to upload image");
                    }
                }
            }
            
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO team_page 
                                  (fullname, position, image, social_handles, status, created_at) 
                                  VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $fullname,
                $position,
                $image,
                json_encode($socialHandles),
                $status
            ]);
            
            $toastMessage = "Team member added successfully!";
            $toastType = "success";
            
        } elseif (isset($_POST['update_member'])) {
            // Update existing team member
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
            $position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            // Process social handles (JSON data)
            $socialHandles = [];
            if (isset($_POST['social_name']) && isset($_POST['profile_link'])) {
                $socialNames = $_POST['social_name'];
                $profileLinks = $_POST['profile_link'];
                
                for ($i = 0; $i < count($socialNames); $i++) {
                    if (!empty(trim($socialNames[$i])) && !empty(trim($profileLinks[$i]))) {
                        $socialHandles[] = [
                            'social_name' => trim($socialNames[$i]),
                            'user_profile_link' => trim($profileLinks[$i])
                        ];
                    }
                }
            }
            
            // Validate required fields
            if (empty($id) || empty($fullname) || empty($position) || empty($status)) {
                throw new Exception("ID, full name, position and status are required");
            }
            
            // Get current image path
            $stmt = $pdo->prepare("SELECT image FROM team_page WHERE id = ?");
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
                
                $uploadDir = '../WM images/team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Delete old image if exists
                if ($currentImage && file_exists($currentImage)) {
                    unlink($currentImage);
                }
                
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newFileName = 'team_' . uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $targetPath;
                } else {
                    throw new Exception("Failed to upload image");
                }
            }
            
            // Update database
            $stmt = $pdo->prepare("UPDATE team_page 
                                  SET fullname = ?, position = ?, image = ?, 
                                  social_handles = ?, status = ?, updated_at = NOW() 
                                  WHERE id = ?");
            $stmt->execute([
                $fullname,
                $position,
                $image,
                json_encode($socialHandles),
                $status,
                $id
            ]);
            
            $toastMessage = "Team member updated successfully!";
            $toastType = "success";
            
        } elseif (isset($_POST['delete_member'])) {
            // Delete team member
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($id)) {
                throw new Exception("Team member ID is required");
            }
            
            // Get image path to delete file
            $stmt = $pdo->prepare("SELECT image FROM team_page WHERE id = ?");
            $stmt->execute([$id]);
            $imagePath = $stmt->fetchColumn();
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM team_page WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete image file if exists
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            $toastMessage = "Team member deleted successfully!";
            $toastType = "success";
        }
    } catch (Exception $e) {
        $toastMessage = "Error: " . $e->getMessage();
        $toastType = "error";
    }
}

// Get all team members for listing
$teamMembers = $pdo->query("SELECT * FROM team_page ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Function to format social handles for display
function formatSocialHandles($json) {
    $socialHandles = json_decode($json, true);
    $output = '';
    if (is_array($socialHandles)) {
        foreach ($socialHandles as $item) {
            if (isset($item['social_name']) && isset($item['user_profile_link'])) {
                $output .= '<li><a href="' . htmlspecialchars($item['user_profile_link']) . '" target="_blank">' . 
                          htmlspecialchars($item['social_name']) . '</a></li>';
            }
        }
    }
    return $output ? '<ul class="list-unstyled">' . $output . '</ul>' : '';
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
                <h1><i class="bi bi-people-fill"></i> Team Page Management</h1>
                <p>Manage the team members displayed on the website</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item"><a href="#">Team Page</a></li>
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
                        <!-- Add/Edit Team Member Form -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><?= isset($_GET['edit']) ? 'Edit' : 'Add New' ?> Team Member</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" enctype="multipart/form-data">
                                    <?php if (isset($_GET['edit'])): 
                                        $editId = $_GET['edit'];
                                        $editMember = $pdo->prepare("SELECT * FROM team_page WHERE id = ?");
                                        $editMember->execute([$editId]);
                                        $member = $editMember->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                    <?php endif; ?>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fullname" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" id="fullname" name="fullname" 
                                                       value="<?= isset($member) ? htmlspecialchars($member['fullname']) : '' ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="position" class="form-label">Position</label>
                                                <input type="text" class="form-control" id="position" name="position" 
                                                       value="<?= isset($member) ? htmlspecialchars($member['position']) : '' ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active" <?= (isset($member) && $member['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= (isset($member) && $member['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Profile Image</label>
                                                <input type="file" class="form-control" id="image" name="image">
                                                <?php if (isset($member) && !empty($member['image'])): ?>
                                                    <div class="mt-2">
                                                        <img src="<?= htmlspecialchars($member['image']) ?>" alt="Current Image" style="max-height: 100px;">
                                                        <p class="small text-muted mt-1">Current image</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Social Media Handles</label>
                                        <div id="social-handles-container">
                                            <?php if (isset($member) && !empty($member['social_handles'])):
                                                $socialHandles = json_decode($member['social_handles'], true);
                                                if (is_array($socialHandles)):
                                                    foreach ($socialHandles as $handle): ?>
                                                        <div class="row mb-2 social-handle-row">
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" name="social_name[]" 
                                                                       placeholder="Platform (e.g. Facebook)" 
                                                                       value="<?= htmlspecialchars($handle['social_name'] ?? '') ?>">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="url" class="form-control" name="profile_link[]" 
                                                                       placeholder="Profile URL" 
                                                                       value="<?= htmlspecialchars($handle['user_profile_link'] ?? '') ?>">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger remove-handle">Remove</button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach;
                                                endif;
                                            else: ?>
                                                <div class="row mb-2 social-handle-row">
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control" name="social_name[]" 
                                                               placeholder="Platform (e.g. Facebook)">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="url" class="form-control" name="profile_link[]" 
                                                               placeholder="Profile URL">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-handle">Remove</button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" id="add-social-handle" class="btn btn-sm btn-secondary mt-2">
                                            <i class="bi bi-plus"></i> Add Social Media
                                        </button>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <?php if (isset($_GET['edit'])): ?>
                                            <button type="submit" name="update_member" class="btn btn-primary me-2">
                                                <i class="bi bi-save"></i> Update Member
                                            </button>
                                            <a href="team-page" class="btn btn-secondary">Cancel</a>
                                        <?php else: ?>
                                            <button type="submit" name="add_member" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Add Member
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Team Members List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Team Members</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Social Media</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($teamMembers as $member): ?>
                                                <tr>
                                                    <td><?= $member['id'] ?></td>
                                                    <td>
                                                        <?php if (!empty($member['image'])): ?>
                                                            <img src="<?= htmlspecialchars($member['image']) ?>" alt="Team Member" style="max-height: 50px;">
                                                        <?php else: ?>
                                                            <span class="text-muted">No image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($member['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($member['position']) ?></td>
                                                    <td><?= formatSocialHandles($member['social_handles']) ?></td>
                                                    <td>
                                                        <span class="badge <?= $member['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= ucfirst($member['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($member['created_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="team-page?edit=<?= $member['id'] ?>" class="btn btn-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                                                <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                                                <button type="submit" name="delete_member" class="btn btn-danger">
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

        // Add new social media handle field
        document.getElementById('add-social-handle').addEventListener('click', function() {
            const container = document.getElementById('social-handles-container');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2 social-handle-row';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="social_name[]" placeholder="Platform (e.g. Facebook)">
                </div>
                <div class="col-md-5">
                    <input type="url" class="form-control" name="profile_link[]" placeholder="Profile URL">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-handle">Remove</button>
                </div>
            `;
            container.appendChild(newRow);
        });

        // Remove social media handle field
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-handle')) {
                const row = e.target.closest('.social-handle-row');
                // Don't remove the last row
                if (document.querySelectorAll('.social-handle-row').length > 1) {
                    row.remove();
                }
            }
        });
    </script>
</body>
</html>