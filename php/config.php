<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configurações para GitHub Actions (sobrescreve local)
$host = getenv('MYSQL_HOST') ?: 'localhost';
$db   = getenv('MYSQL_DATABASE') ?: 'todo_list';
$user = getenv('MYSQL_USER') ?: 'root';
$pass = getenv('MYSQL_PASSWORD') ?: '';

// Restante do código...
?>
