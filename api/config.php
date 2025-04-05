<?php

// Exibição de erros (em produção, desabilite a exibição de erros e logue-os)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Defina um ambiente para evitar a exibição de erros em produção
// Exemplo: em produção, altere ini_set para não exibir erros, mas logá-los
// if ($_SERVER['APPLICATION_ENV'] === 'production') {
//     ini_set('display_errors', 0);
//     ini_set('log_errors', 1);
//     ini_set('error_log', '/path/to/php-error.log');
// }

// Configuração de Cabeçalhos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permitir qualquer origem (ou restringir com URLs específicas)
header('Access-Control-Allow-Methods: *'); // Métodos permitidos, * para permitir todos os métodos ou especificar (GET, POST, etc.)
header('Access-Control-Allow-Headers: Content-Type'); // Cabeçalhos permitidos (Content-Type, Authorization, etc.)

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'to-do');
define('DB_USER', 'root');
define('DB_PASS', '');

// Função para realizar a conexão com o banco de dados
function conectarBancoDeDados()
{
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['error' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()]));
    }
}

// Conectar ao banco de dados
$pdo = conectarBancoDeDados();
