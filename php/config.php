<?php
// Adicione isso no início para mostrar erros (apenas para desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Altere estas variáveis conforme seu ambiente
$host = 'localhost';  // ou o IP do seu servidor MySQL
$db   = 'todo_list';  // nome do banco criado
$user = 'root';       // usuário do MySQL
$pass = '';           // senha do MySQL (deixe vazio se não tiver)
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Mostra o erro específico
    die(json_encode([
        'error' => 'Erro de conexão com o banco de dados',
        'details' => $e->getMessage()
    ]));
}
?>
