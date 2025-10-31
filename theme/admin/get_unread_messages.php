<?php
session_start();
require_once('db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM support_messages m
        JOIN support_tickets t ON m.ticket_id = t.ticket_id
        WHERE t.user_id = ? 
        AND m.sender_type = 'user' 
        AND m.is_read = FALSE
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['count' => $result['count'] ?? 0]);
} catch (Exception $e) {
    echo json_encode(['count' => 0]);
}