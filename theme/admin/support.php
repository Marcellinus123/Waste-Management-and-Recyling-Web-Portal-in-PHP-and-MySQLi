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
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'waste_user') {
    header('Location: userlogin');
    exit();
}

// Check if user_id is set and not null
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: userlogin');
    exit();
}


if (isset($_SESSION['account_status']) && $_SESSION['account_status'] !== 'active') {
    header("Location: userlogin?error=account_inactive");
    exit();
}

// Initialize toast variables
$toastMessage = '';
$toastType = ''; // 'success', 'error', or 'warning'

// Handle support ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-ticket'])) {
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $attachments = $_FILES['attachments'] ?? [];
    
    try {
        // Validate input
        if (empty($subject) || empty($message)) {
            throw new Exception('Subject and message are required');
        }

        // Start transaction
        $pdo->beginTransaction();

        // Insert ticket
        $stmt = $pdo->prepare("INSERT INTO support_tickets 
                            (user_id, subject, title, status) 
                            VALUES (?, ?, ?, 'open')");
        $stmt->execute([
            $_SESSION['user_id'],
            $subject,
            substr($message, 0, 100) // Using first 100 chars as title
        ]);
        
        $ticketId = $pdo->lastInsertId();

        // Insert message
        $stmt = $pdo->prepare("INSERT INTO support_messages 
                            (ticket_id, sender_id, sender_type, message) 
                            VALUES (?, ?, 'user', ?)");
        $stmt->execute([
            $ticketId,
            $_SESSION['user_id'],
            $message
        ]);

        // Handle attachments if any
        if (!empty($attachments['name'][0])) {
            $uploadDir = 'uploads/support/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $attachmentsData = [];
            
            foreach ($attachments['tmp_name'] as $key => $tmpName) {
                $fileName = basename($attachments['name'][$key]);
                $fileType = $attachments['type'][$key];
                $fileSize = $attachments['size'][$key];
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = 'ticket_' . $ticketId . '_' . uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $attachmentsData[] = [
                        'name' => $fileName,
                        'path' => $targetPath,
                        'type' => $fileType,
                        'size' => $fileSize
                    ];
                }
            }

            // Update message with attachments if any were uploaded
            if (!empty($attachmentsData)) {
                $stmt = $pdo->prepare("UPDATE support_messages 
                                    SET attachments = ? 
                                    WHERE message_id = ?");
                $stmt->execute([
                    json_encode($attachmentsData),
                    $pdo->lastInsertId()
                ]);
            }
        }

        $pdo->commit();
        $toastMessage = 'Your support ticket has been submitted successfully!';
        $toastType = 'success';
    } catch (Exception $e) {
        $pdo->rollBack();
        $toastMessage = 'Error submitting ticket: ' . $e->getMessage();
        $toastType = 'error';
    }
}

// Get user's support tickets
$tickets = [];
try {
    $stmt = $pdo->prepare("SELECT t.*, 
                        (SELECT COUNT(*) FROM support_messages m WHERE m.ticket_id = t.ticket_id) as message_count
                        FROM support_tickets t
                        WHERE t.user_id = ?
                        ORDER BY t.updated_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = 'Error loading tickets: ' . $e->getMessage();
    $toastType = 'error';
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
          <h1><i class="bi bi-headset"></i> Support</h1>
          <p>Contact Support</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item"><a href="#">Dashboard / Contact Support</a></li>
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
            <h3 class="tile-title">Complete</h3>
            <div class="tile-body">
              <form id="support-form" method="post" enctype="multipart/form-data" class="row">
                <div class="mb-3 ">
                  <label class="form-label">Subject</label>
                    <select id="support-subject" name="subject" class="form-control" required>
                        <option value="">Select subject</option>
                        <option value="collection">Waste Collection</option>
                        <option value="recycling">Recycling Services</option>
                        <option value="billing">Billing & Payments</option>
                        <option value="account">Account Issues</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Message</label>
                  <textarea id="support-message" name="message" class="form-control" rows="5" 
                                    placeholder="Describe your issue in detail" required></textarea>
                </div> 
                <div class="mb-3">
                  <label class="form-label">Attachment(Optional)</label>
                  <input type="file" id="support-attachments" name="attachments[]" class="form-control" multiple>
                </div> 
                <div class="mb-3 col-md-4 align-self-end">
                  <button type="submit" name="submit-ticket" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                  </button>
                </div>
              </form>
              <div class="profile-section">
                <div class="waste-type-header">
                    <h2>Your Support Tickets</h2>
                    <p>View the status of your previous support requests</p>
                </div>
                <div class="message-list">
                    <?php if (empty($tickets)): ?>
                        <div class="message">
                            <div class="message-content">
                                <div class="message-preview">You haven't submitted any support tickets yet.</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <div class="message">
                                <div class="message-avatar" style="background: 
                                    <?= $ticket['status'] === 'resolved' ? 'var(--success)' : 
                                    ($ticket['status'] === 'in_progress' ? 'var(--warning)' : 'var(--primary)') ?>;">
                                    <i class="fas fa-<?= $ticket['status'] === 'resolved' ? 'check' : 
                                                    ($ticket['status'] === 'in_progress' ? 'clock' : 'envelope') ?>"></i>
                                </div>
                                <div class="message-content">
                                    <div class="message-header">
                                        <div class="message-sender">#<?= htmlspecialchars($ticket['ticket_id']) ?> - 
                                            <?= htmlspecialchars(ucfirst($ticket['subject'])) ?>
                                        </div>
                                        <div class="message-time">
                                            <?= date('M j, Y', strtotime($ticket['updated_at'])) ?> | 
                                            <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?> | 
                                            <?= $ticket['message_count'] ?> message(s)
                                        </div>
                                    </div>
                                    <div class="message-preview"><?= htmlspecialchars($ticket['title']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            toastBody.innerText = message;
            
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
            window.addEventListener('DOMContentLoaded', () => {
                showToast("<?= addslashes($toastMessage) ?>", "<?= $toastType ?>");
            });
        <?php endif; ?>
    </script>
    
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
  </body>
</html>