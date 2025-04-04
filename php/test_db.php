<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT 1");
    echo json_encode(['status' => 'success', 'message' => 'Conexão com o banco OK']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
