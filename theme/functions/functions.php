<?php 
include_once('database/db.php');

function fetchData($table, $rows) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE status=1 LIMIT :rows");
        $stmt->bindParam(':rows', $rows, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return []; 
    }
}
