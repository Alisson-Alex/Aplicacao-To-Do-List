<?php
require 'config.php';

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'tasks'");
    $result = $stmt->fetch();

    if ($result) {
        echo "✅ Tabela 'tasks' existe no banco de dados!";
    } else {
        echo "❌ Tabela 'tasks' NÃO encontrada. Execute o SQL de criação primeiro.";
    }
} catch (PDOException $e) {
    die("Erro ao verificar tabela: " . $e->getMessage());
}
?>
