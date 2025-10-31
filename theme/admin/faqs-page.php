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
$faqs = [];
$currentFaq = null;

// Get all FAQs
try {
    $stmt = $pdo->query("SELECT * FROM faqs ORDER BY created_at DESC");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading FAQs: " . $e->getMessage();
    $toastType = "danger";
}

// Handle viewing/editing a specific FAQ
if (isset($_GET['faq_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
        $stmt->execute([$_GET['faq_id']]);
        $currentFaq = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $toastMessage = "Error loading FAQ: " . $e->getMessage();
        $toastType = "danger";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $faqId = $_POST['faq_id'] ?? '';
    
    try {
        if ($action === 'delete') {
            // Delete FAQ
            $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
            $stmt->execute([$faqId]);
            
            $_SESSION['toastMessage'] = "FAQ deleted successfully";
            $_SESSION['toastType'] = "success";
            header("Location: faqs-page");
            exit();
            
        } else {
            // Validate input
            $question = trim($_POST['question']);
            $answer = trim($_POST['answer']);
            $link = trim($_POST['link']);
            $status = $_POST['status'];
            
            if (empty($question) || empty($answer)) {
                throw new Exception("Question and answer are required");
            }
            
            if ($action === 'edit' && $faqId) {
                // Update existing FAQ
                $stmt = $pdo->prepare("UPDATE faqs SET 
                    question = ?,
                    answer = ?,
                    link = ?,
                    status = ?,
                    updated_at = NOW()
                    WHERE id = ?");
                
                $stmt->execute([
                    $question,
                    $answer,
                    $link,
                    $status,
                    $faqId
                ]);
                
                $_SESSION['toastMessage'] = "FAQ updated successfully";
                $_SESSION['toastType'] = "success";
                
            } else {
                // Create new FAQ
                $stmt = $pdo->prepare("INSERT INTO faqs (
                    question,
                    answer,
                    link,
                    status,
                    created_at
                ) VALUES (?, ?, ?, ?, NOW())");
                
                $stmt->execute([
                    $question,
                    $answer,
                    $link,
                    $status
                ]);
                
                $_SESSION['toastMessage'] = "FAQ created successfully";
                $_SESSION['toastType'] = "success";
            }
            
            header("Location: faqs-page");
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
                <h1><i class="bi bi-question-circle"></i> FAQs Management</h1>
                <p>Manage frequently asked questions and answers</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Content</a></li>
                <li class="breadcrumb-item"><a href="#">FAQs</a></li>
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
                        <h3 class="title">FAQs List</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faqModal">
                            <i class="bi bi-plus-circle"></i> NEW/UPDATE
                        </button>
                        
                    </div>
                    
                    <div class="tile-body">
                        <?php if (empty($faqs)): ?>
                            <div class="alert alert-info">
                                No FAQs found. Create your first FAQ.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($faqs as $faq): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($faq['question']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $faq['status'] === 'public' ? 'primary' : 'warning' ?>">
                                                        <?= htmlspecialchars(ucfirst($faq['status'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($faq['created_at'])) ?></td>
                                                <td>
                                                    <a href="faqs-page?faq_id=<?= $faq['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="faq_id" value="<?= $faq['id'] ?>">
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
        
        <!-- FAQ Modal -->
        <div class="modal fade" id="faqModal" tabindex="-1" aria-labelledby="faqModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?= $currentFaq ? 'edit' : 'add' ?>">
                        <?php if ($currentFaq): ?>
                            <input type="hidden" name="faq_id" value="<?= $currentFaq['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="modal-header">
                            <h5 class="modal-title" id="faqModalLabel">
                                <?= $currentFaq ? 'Edit FAQ' : 'Add New FAQ' ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="question" class="form-label">Question</label>
                                <input type="text" class="form-control" id="question" name="question" 
                                       value="<?= htmlspecialchars($currentFaq['question'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="answer" class="form-label">Answer</label>
                                <textarea class="form-control" id="answer" name="answer" rows="5" required><?= htmlspecialchars($currentFaq['answer'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="link" class="form-label">Link (Optional)</label>
                                        <input type="url" class="form-control" id="link" name="link" 
                                               value="<?= htmlspecialchars($currentFaq['link'] ?? '') ?>">
                                        <small class="text-muted">Add a relevant link if needed</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="public" <?= ($currentFaq['status'] ?? '') === 'public' ? 'selected' : '' ?>>Public</option>
                                            <option value="private" <?= ($currentFaq['status'] ?? '') === 'private' ? 'selected' : '' ?>>Private</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <?= $currentFaq ? 'Update FAQ' : 'Create FAQ' ?>
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
        // Initialize CKEditor for answer textarea
        ClassicEditor
            .create(document.querySelector('#answer'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Heading', class: 'ck-heading_heading2' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });

        // Show modal if editing a FAQ
        <?php if ($currentFaq): ?>
        $(document).ready(function() {
            $('#faqModal').modal('show');
        });
        <?php endif; ?>
    </script>
</body>
</html>