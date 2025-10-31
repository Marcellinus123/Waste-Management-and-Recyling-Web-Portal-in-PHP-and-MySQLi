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

// Check user authentication
if (!isset($_SESSION['loggedin'])) {
    header("Location: userlogin");
    exit();
}

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'waste_driver') {
    header('Location: userlogin');
    exit();
}

if (isset($_SESSION['account_status']) && $_SESSION['account_status'] !== 'active') {
    header("Location: userlogin?error=account_inactive");
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$tickets = [];
$messages = [];
$currentTicket = null;

// Get all support tickets for this user
try {
    $stmt = $pdo->prepare("SELECT t.*, 
                          (SELECT COUNT(*) FROM support_messages m WHERE m.ticket_id = t.ticket_id) as message_count
                          FROM support_tickets t
                          WHERE t.user_id = ?
                          ORDER BY t.updated_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $toastMessage = "Error loading tickets: " . $e->getMessage();
    $toastType = "error";
}


// Mark messages as read when viewing a ticket
if (isset($_GET['ticket_id'])) {
    $ticketId = (int)$_GET['ticket_id'];
    
    try {
        // Mark all admin messages in this ticket as read
        $stmt = $pdo->prepare("
            UPDATE support_messages m
            JOIN support_tickets t ON m.ticket_id = t.ticket_id
            SET m.is_read = TRUE
            WHERE t.ticket_id = ? 
            AND t.user_id = ?
            AND m.sender_type = 'admin'
        ");
        $stmt->execute([$ticketId, $_SESSION['user_id']]);
        
        // Update the notification count via WebSocket if possible
        echo "<script>
            if (typeof messageSocket !== 'undefined' && messageSocket.readyState === WebSocket.OPEN) {
                messageSocket.send(JSON.stringify({
                    type: 'messages_read',
                    ticket_id: $ticketId
                }));
            }
        </script>";
    } catch (Exception $e) {
        error_log("Error marking messages as read: " . $e->getMessage());
    }
}

// Handle viewing a specific ticket
if (isset($_GET['ticket_id'])) {
    $ticketId = (int)$_GET['ticket_id'];
    
    try {
        // Verify the ticket belongs to this user
        $stmt = $pdo->prepare("SELECT t.* FROM support_tickets t
                              WHERE t.ticket_id = ? AND t.user_id = ?");
        $stmt->execute([$ticketId, $_SESSION['user_id']]);
        $currentTicket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentTicket) {
            throw new Exception("Ticket not found or access denied");
        }
        
        // Get all messages for this ticket
        $stmt = $pdo->prepare("SELECT m.*, 
                              CASE 
                                WHEN m.sender_type = 'admin' THEN 'Admin'
                                ELSE 'You'
                              END as sender_name
                              FROM support_messages m
                              WHERE m.ticket_id = ?
                              ORDER BY m.created_at");
        $stmt->execute([$ticketId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $toastMessage = "Error loading ticket: " . $e->getMessage();
        $toastType = "error";
    }
}


// Handle sending a reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $ticketId = (int)$_POST['ticket_id'];
    $message = trim($_POST['message']);
    $attachments = $_FILES['attachments'] ?? [];
    
    try {
        // Validate input
        if (empty($message)) {
            throw new Exception("Message cannot be empty");
        }
        
        // Verify the ticket belongs to this user
        $stmt = $pdo->prepare("SELECT t.* FROM support_tickets t
                              WHERE t.ticket_id = ? AND t.user_id = ?");
        $stmt->execute([$ticketId, $_SESSION['user_id']]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$ticket) {
            throw new Exception("Ticket not found or access denied");
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Save message to database
        $stmt = $pdo->prepare("INSERT INTO support_messages 
                              (ticket_id, sender_id, sender_type, message, created_at)
                              VALUES (?, ?, 'user', ?, NOW())");
        $stmt->execute([$ticketId, $_SESSION['user_id'], $message]);
        $messageId = $pdo->lastInsertId();
        
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
                    $messageId
                ]);
            }
        }
        
        // Update ticket status and timestamp
        $stmt = $pdo->prepare("UPDATE support_tickets 
                              SET status = 'open', updated_at = NOW()
                              WHERE ticket_id = ?");
        $stmt->execute([$ticketId]);
        
        $pdo->commit();
        
        // Send email notification to admin
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'emittyarts@gmail.com'; // SMTP username
            $mail->Password   = 'rmrediquunflofll';     // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            
            // Recipients
            $mail->setFrom('emittyarts@gmail.com', 'Waste Wizard Support');
            $mail->addAddress('admin@wastewizard.com', 'Waste Wizard Admin');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Reply on Support Ticket #' . $ticketId;
            
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
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Waste Wizard Support</h2>
                    </div>
                    <div class="content">
                        <p>Hello Admin,</p>
                        <p>A user has replied to support ticket <strong>#' . $ticketId . '</strong> regarding:</p>
                        <h3>' . htmlspecialchars($ticket['title']) . '</h3>
                        
                        <div class="message">
                            ' . nl2br(htmlspecialchars($message)) . '
                        </div>
                        
                        <p>Please log in to the admin panel to respond.</p>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' Waste Wizard. All rights reserved.</p>
                        <p>This is an automated message.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $mail->Body = $emailBody;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $emailBody));
            
            $mail->send();
            
            $toastMessage = "Reply sent successfully and admin has been notified";
            $toastType = "success";
            
            // Refresh the current ticket data
            header("Location: messages?ticket_id=$ticketId");
            exit();
            
        } catch (Exception $e) {
            // Email failed but message was saved to database
            $toastMessage = "Reply saved but email notification could not be sent: " . $mail->ErrorInfo;
            $toastType = "warning";
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $toastMessage = "Error sending reply: " . $e->getMessage();
        $toastType = "error";
    }
}

// Handle creating a new ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket'])) {
    $subject = $_POST['subject'] ?? '';
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $attachments = $_FILES['attachments'] ?? [];
    
    try {
        // Validate input
        if (empty($subject) || empty($title) || empty($message)) {
            throw new Exception('Subject, title and message are required');
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
            $title
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
        $messageId = $pdo->lastInsertId();

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
                    $messageId
                ]);
            }
        }

        $pdo->commit();
        
        // Send email notification to admin
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
            $mail->addAddress('admin@wastewizard.com', 'Waste Wizard Admin');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Support Ticket #' . $ticketId . ' - ' . $subject;
            
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
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Waste Wizard Support</h2>
                    </div>
                    <div class="content">
                        <p>Hello Admin,</p>
                        <p>A new support ticket has been created:</p>
                        
                        <h3>Ticket #' . $ticketId . ': ' . htmlspecialchars($title) . '</h3>
                        <p><strong>Category:</strong> ' . htmlspecialchars(ucfirst($subject)) . '</p>
                        
                        <div class="message">
                            ' . nl2br(htmlspecialchars($message)) . '
                        </div>
                        
                        <p>Please log in to the admin panel to respond.</p>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' Waste Wizard. All rights reserved.</p>
                        <p>This is an automated message.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $mail->Body = $emailBody;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $emailBody));
            
            $mail->send();
            
            $toastMessage = "Support ticket created successfully and admin has been notified";
            $toastType = "success";
            
            // Redirect to view the new ticket
            header("Location: messages.php?ticket_id=$ticketId");
            exit();
            
        } catch (Exception $e) {
            // Email failed but ticket was created
            $toastMessage = "Ticket created but email notification could not be sent: " . $mail->ErrorInfo;
            $toastType = "warning";
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $toastMessage = "Error creating ticket: " . $e->getMessage();
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
                <h1><i class="bi bi-envelope-fill"></i> Support Messages</h1>
                <p>View and manage your support requests</p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
                <li class="breadcrumb-item"><a href="#">Support</a></li>
            </ul>
        </div>
        
        <!-- Toast Notification -->
        <?php if ($toastMessage): ?>
        <div class="alert alert-<?php echo $toastType === 'success' ? 'success' : ($toastType === 'warning' ? 'warning' : 'danger'); ?> alert-dismissible fade show" role="alert">
            <?php echo $toastMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Tickets List -->
            <div class="col-md-4">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Your Support Tickets</h3>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                            <i class="bi bi-plus-circle"></i> New Ticket
                        </button>
                    </div>
                    <div class="list-group">
                        <?php foreach ($tickets as $ticket): ?>
                        <a href="messages?ticket_id=<?php echo $ticket['ticket_id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo $currentTicket && $currentTicket['ticket_id'] == $ticket['ticket_id'] ? 'active' : ''; ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($ticket['title']); ?></h6>
                                <small><?php echo date('M j, g:i a', strtotime($ticket['created_at'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars(ucfirst($ticket['subject'])); ?></p>
                            <small>
                                <span class="badge bg-<?php 
                                    switch($ticket['status']) {
                                        case 'open': echo 'primary'; break;
                                        case 'in_progress': echo 'warning'; break;
                                        case 'resolved': echo 'success'; break;
                                        case 'closed': echo 'secondary'; break;
                                        default: echo 'info';
                                    }
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                </span>
                                <span class="text-muted ms-2">
                                    <?php echo $ticket['message_count'] ?> message(s)
                                </span>
                            </small>
                        </a>
                        <?php endforeach; ?>
                        <?php if (empty($tickets)): ?>
                        <div class="list-group-item">
                            <p class="mb-0 text-muted">No support tickets found</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Conversation or New Ticket Form -->
            <div class="col-md-8">
                <?php if ($currentTicket): ?>
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="title">Ticket #<?php echo $currentTicket['ticket_id']; ?></h3>
                        <div class="btn-group">
                            <span class="badge bg-<?php 
                                switch($currentTicket['status']) {
                                    case 'open': echo 'primary'; break;
                                    case 'in_progress': echo 'warning'; break;
                                    case 'resolved': echo 'success'; break;
                                    case 'closed': echo 'secondary'; break;
                                    default: echo 'info';
                                }
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $currentTicket['status'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="tile-body">
                        <div class="conversation-container">
                            <div class="conversation-header">
                                <h4><?php echo htmlspecialchars($currentTicket['title']); ?></h4>
                                <p class="text-muted">
                                    Subject: <?php echo htmlspecialchars(ucfirst($currentTicket['subject'])); ?>
                                </p>
                                <p class="text-muted">
                                    Created: <?php echo date('F j, Y \a\t g:i a', strtotime($currentTicket['created_at'])); ?>
                                </p>
                            </div>
                            
                            <div class="conversation-messages">
                                <?php if (!empty($messages)): ?>
                                    <?php foreach ($messages as $message): ?>
                                        <div class="message <?= $message['sender_type'] === 'admin' ? 'admin-message' : 'user-message' ?>">
                                            <div class="message-header">
                                                <strong><?= htmlspecialchars($message['sender_name']) ?></strong>
                                                <small class="text-muted">
                                                    <?= date('M j, g:i a', strtotime($message['created_at'])) ?>
                                                </small>
                                            </div>
                                            
                                            <div class="message-body">
                                                <?= nl2br(htmlspecialchars($message['message'])) ?>

                                                <?php if (!empty($message['attachments'])): ?>
                                                    <?php 
                                                        $attachments = json_decode($message['attachments'], true);
                                                        if ($attachments && is_array($attachments)): 
                                                    ?>
                                                        <div class="attachments mt-2">
                                                            <small class="text-muted">Attachments:</small>
                                                            <?php foreach ($attachments as $attachment): ?>
                                                                <div class="attachment">
                                                                    <a href="<?= htmlspecialchars($attachment['path']) ?>" target="_blank">
                                                                        <i class="bi bi-paperclip"></i> <?= htmlspecialchars($attachment['name']) ?>
                                                                    </a>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        No messages in this conversation yet.
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($currentTicket['status'] !== 'closed' && $currentTicket['status'] !== 'resolved'): ?>
                            <div class="conversation-reply">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="ticket_id" value="<?php echo $currentTicket['ticket_id']; ?>">
                                    <div class="form-group">
                                        <label for="reply-message">Your Reply</label>
                                        <textarea class="form-control" id="reply-message" name="message" rows="4" required></textarea>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="reply-attachments">Attachments (Optional)</label>
                                        <input type="file" class="form-control" id="reply-attachments" name="attachments[]" multiple>
                                    </div>
                                    <div class="form-actions mt-3">
                                        <button type="submit" name="send_reply" class="btn btn-primary">
                                            <i class="bi bi-send-fill"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-secondary mt-3">
                                This ticket is closed. To reopen it, please create a new ticket.
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
                            <h4 class="mt-3">Select a ticket to view messages</h4>
                            <p class="text-muted">Choose a support ticket from the list to view the conversation</p>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                                <i class="bi bi-plus-circle"></i> Create New Ticket
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- New Ticket Modal -->
        <div class="modal fade" id="newTicketModal" tabindex="-1" aria-labelledby="newTicketModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newTicketModalLabel">Create New Support Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="ticket-subject" class="form-label">Subject</label>
                                <select id="ticket-subject" name="subject" class="form-select" required>
                                    <option value="">Select subject</option>
                                    <option value="collection">Waste Collection</option>
                                    <option value="recycling">Recycling Services</option>
                                    <option value="billing">Billing & Payments</option>
                                    <option value="account">Account Issues</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ticket-title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="ticket-title" name="title" required 
                                       placeholder="Brief summary of your issue">
                            </div>
                            <div class="mb-3">
                                <label for="ticket-message" class="form-label">Message</label>
                                <textarea class="form-control" id="ticket-message" name="message" rows="5" required
                                          placeholder="Describe your issue in detail"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="ticket-attachments" class="form-label">Attachments (Optional)</label>
                                <input type="file" class="form-control" id="ticket-attachments" name="attachments[]" multiple>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="create_ticket" class="btn btn-primary">Submit Ticket</button>
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
    
    <style>
        .conversation-container {
            display: flex;
            flex-direction: column;
            height: calc(130vh - 300px);
        }
        
        .conversation-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        
        .conversation-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 5px;
            max-width: 80%;
        }
        
        .user-message {
            background-color: #e9ecef;
            margin-right: auto;
        }
        
        .admin-message {
            background-color: #d4edda;
            margin-left: auto;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .message-body {
            line-height: 1.5;
        }
        
        .attachments {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        
        .attachment {
            margin-top: 5px;
        }
        
        .attachment a {
            color: #007bff;
            text-decoration: none;
        }
        
        .conversation-reply {
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            // Scroll to bottom of messages
            $('.conversation-messages').scrollTop($('.conversation-messages')[0].scrollHeight);
            
            // Focus on reply textarea when viewing a ticket
            <?php if ($currentTicket): ?>
            $('#reply-message').focus();
            <?php endif; ?>
            
            // Initialize modal if there was an error creating a ticket
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket']) && $toastType === 'error'): ?>
            $('#newTicketModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html>