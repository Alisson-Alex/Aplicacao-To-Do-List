<?php
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obter todas as tarefas
        $stmt = $pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
        $tasks = $stmt->fetchAll();
        echo json_encode($tasks);
        break;
        
    case 'POST':
        // Adicionar nova tarefa
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['error' => 'O título é obrigatório']);
            exit;
        }
        
        $stmt = $pdo->prepare('INSERT INTO tasks (title, description) VALUES (?, ?)');
        $stmt->execute([$title, $description]);
        $id = $pdo->lastInsertId();
        
        echo json_encode([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'complete' => false
        ]);
        break;
        
    case 'PUT':
        // Atualizar tarefa (editar ou marcar como completa)
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $complete = $data['complete'] ?? false;
        
        $stmt = $pdo->prepare('UPDATE tasks SET title = ?, description = ?, complete = ? WHERE id = ?');
        $stmt->execute([$title, $description, $complete, $id]);
        
        echo json_encode(['success' => true]);
        break;
        
    case 'DELETE':
        // Excluir tarefa
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
?>