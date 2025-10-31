<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once('db.php');
require '../libs/PHPMailer/src/PHPMailer.php';
require '../libs/PHPMailer/src/SMTP.php';
require '../libs/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check admin authentication
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

// Initialize variables
$toastMessage = '';
$toastType = '';
$contacts = [];
$currentContact = null;

// Get all contact messages
try {
    $stmt = $pdo->prepare("SELECT * FROM site_contacts ORDER BY date_contacted DESC");
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading contacts: " . $e->getMessage();
    $toastType = "error";
}

// Handle viewing a specific contact
if (isset($_GET['contact_id'])) {
    $contactId = (int)$_GET['contact_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM site_contacts WHERE id = ?");
        $stmt->execute([$contactId]);
        $currentContact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Mark as read/processed if needed
        $stmt = $pdo->prepare("UPDATE site_contacts SET status = 'processed' WHERE id = ? AND status = 'new'");
        $stmt->execute([$contactId]);
        
    } catch (Exception $e) {
        $toastMessage = "Error loading contact: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle sending a reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $contactId = (int)$_POST['contact_id'];
    $message = trim($_POST['message']);
    
    try {
        // Validate input
        if (empty($message)) {
            throw new Exception("Message cannot be empty");
        }
        
        // Get contact info
        $stmt = $pdo->prepare("SELECT * FROM site_contacts WHERE id = ?");
        $stmt->execute([$contactId]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$contact) {
            throw new Exception("Contact not found");
        }
        
        // Send email to the contact
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'emittyarts@gmail.com'; 
            $mail->Password   = 'rmrediquunflofll';     
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            
            // Recipients
            $mail->setFrom('emittyarts@gmail.com', 'Waste Wizard Support');
            $mail->addAddress($contact['email'], $contact['fullname']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Re: Your Contact Form Submission';
            
            // Email template
            $emailBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 15px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 12px; color: #777; }
                    .message { background-color: white; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
                    .original-message { background-color: #f0f0f0; padding: 10px; border-left: 3px solid #ccc; margin: 10px 0; font-size: 0.9em; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Waste Wizard Support</h2>
                    </div>
                    <div class="content">
                        <p>Hello ' . htmlspecialchars($contact['fullname']) . ',</p>
                        <p>Thank you for contacting us. Here is our response to your inquiry:</p>
                        
                        <div class="message">
                            ' . nl2br(htmlspecialchars($message)) . '
                        </div>
                        
                        <div class="original-message">
                            <p><strong>Your original message:</strong></p>
                            <p>' . nl2br(htmlspecialchars($contact['message'])) . '</p>
                        </div>
                        
                        <p>If you have any further questions, please don\'t hesitate to contact us again.</p>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' Waste Wizard. All rights reserved.</p>
                        <p>This is an automated message, please do not reply directly to this email.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $mail->Body = $emailBody;
            $mail->AltBody = "Hello " . $contact['fullname'] . ",\n\n" .
                            "Thank you for contacting us. Here is our response to your inquiry:\n\n" .
                            $message . "\n\n" .
                            "Your original message:\n" .
                            $contact['message'] . "\n\n" .
                            "If you have any further questions, please don't hesitate to contact us again.\n\n" .
                            "© " . date('Y') . " Waste Wizard. All rights reserved.";
            
            $mail->send();
            
            // Update contact status
            $stmt = $pdo->prepare("UPDATE site_contacts SET status = 'responded', responded_at = NOW() WHERE id = ?");
            $stmt->execute([$contactId]);
            
            $toastMessage = "Response sent successfully to " . htmlspecialchars($contact['email']);
            $toastType = "success";
            
            // Refresh the current contact data
            header("Location: contacts?contact_id=$contactId");
            exit();
            
        } catch (Exception $e) {
            throw new Exception("Email could not be sent: " . $mail->ErrorInfo);
        }
        
    } catch (Exception $e) {
        $toastMessage = "Error sending reply: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle deleting a contact message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $contactId = (int)$_POST['contact_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM site_contacts WHERE id = ?");
        $stmt->execute([$contactId]);
        
        $toastMessage = "Message deleted successfully";
        $toastType = "success";
        
        header("Location: contacts");
        exit();
        
    } catch (Exception $e) {
        $toastMessage = "Error deleting message: " . $e->getMessage();
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
                <h1><i class="bi bi-envelope-fill"></i> Contact Messages</h1>
                <p>Manage messages from the website contact form</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Contacts</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
        <?php if ($toastMessage): ?>
        <div class="alert alert-<?php echo $toastType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo $toastMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Messages List -->
            <div class="col-md-4">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Contact Messages</h3>
                        <div class="btn-group">
                            <a href="contacts" class="btn btn-primary btn-sm">Refresh</a>
                        </div>
                    </div>
                    <div class="list-group">
                        <?php foreach ($contacts as $contact): ?>
                        <a href="contacts?contact_id=<?php echo $contact['id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo $currentContact && $currentContact['id'] == $contact['id'] ? 'active' : ''; ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($contact['subject']); ?></h6>
                                <small><?php echo date('M j, g:i a', strtotime($contact['date_contacted'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($contact['fullname']); ?></p>
                            <small><?php echo htmlspecialchars($contact['email']); ?></small>
                            <div class="mt-1">
                                <span class="badge bg-<?php 
                                    switch($contact['status']) {
                                        case 'new': echo 'primary'; break;
                                        case 'processed': echo 'info'; break;
                                        case 'responded': echo 'success'; break;
                                        default: echo 'secondary';
                                    }
                                ?>">
                                    <?php echo ucfirst($contact['status']); ?>
                                </span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                        <?php if (empty($contacts)): ?>
                        <div class="list-group-item">
                            <p class="mb-0 text-muted">No contact messages found</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Message Details and Reply -->
            <div class="col-md-8">
                <?php if ($currentContact): ?>
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Message from <?php echo htmlspecialchars($currentContact['fullname']); ?></h3>
                        <div class="btn-group">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="contact_id" value="<?php echo $currentContact['id']; ?>">
                                <button type="submit" name="delete_message" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash-fill"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="tile-body">
                        <div class="message-details">
                            <div class="mb-3">
                                <h5><?php echo htmlspecialchars($currentContact['subject']); ?></h5>
                                <p class="text-muted">
                                    From: <?php echo htmlspecialchars($currentContact['fullname']); ?>
                                    &lt;<?php echo htmlspecialchars($currentContact['email']); ?>&gt;
                                </p>
                                <p class="text-muted">
                                    Received: <?php echo date('F j, Y \a\t g:i a', strtotime($currentContact['date_contacted'])); ?>
                                </p>
                                <?php if ($currentContact['status'] === 'responded' && $currentContact['responded_at']): ?>
                                <p class="text-muted">
                                    Responded: <?php echo date('F j, Y \a\t g:i a', strtotime($currentContact['responded_at'])); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="message-content p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($currentContact['message'])); ?>
                            </div>
                            
                            <?php if ($currentContact['status'] !== 'responded'): ?>
                            <div class="reply-form mt-4">
                                <h5>Send Response</h5>
                                <form method="POST">
                                    <input type="hidden" name="contact_id" value="<?php echo $currentContact['id']; ?>">
                                    <div class="form-group">
                                        <label for="reply-message">Your Response</label>
                                        <textarea class="form-control" id="reply-message" name="message" rows="5" required></textarea>
                                    </div>
                                    <div class="form-actions mt-3">
                                        <button type="submit" name="send_reply" class="btn btn-primary">
                                            <i class="bi bi-send-fill"></i> Send Response
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-success mt-4">
                                <i class="bi bi-check-circle-fill"></i> A response has already been sent to this message.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="tile">
                    <div class="tile-body">
                        <div class="text-center py-5">
                            <i class="bi bi-envelope-open fs-1 text-muted"></i>
                            <h4 class="mt-3">Select a message to view</h4>
                            <p class="text-muted">Choose a contact message from the list to view details and respond</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <style>
        .message-details {
            max-height: calc(100vh - 300px);
            overflow-y: auto;
        }
        
        .message-content {
            white-space: pre-wrap;
            border-left: 4px solid #28a745;
        }
        
        .reply-form {
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .list-group-item.active .badge {
            background-color: white !important;
            color: #28a745;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            // Focus on reply textarea when viewing a message
            <?php if ($currentContact && $currentContact['status'] !== 'responded'): ?>
            $('#reply-message').focus();
            <?php endif; ?>
        });
    </script>
</body>
</html>