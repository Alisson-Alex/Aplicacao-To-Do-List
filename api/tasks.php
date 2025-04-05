<?php
/*
___________              ________           .____    .__          __   
\__    ___/___           \______ \   ____   |    |   |__| _______/  |_ 
  |    | /  _ \   ______  |    |  \ /  _ \  |    |   |  |/  ___/\   __\
  |    |(  <_> ) /_____/  |    `   (  <_> ) |    |___|  |\___ \  |  |  
  |____| \____/          /_______  /\____/  |_______ \__/____  > |__|  
                                 \/                 \/       \/        


Desafio To-Do List - PHP

Desafio: Desenvolver uma lista de tarefas (To-Do List) utilizando PHP, HTML, JS, CSS e MySQL.

*/

// Incluir o arquivo de configuração
require 'config.php';

// Captura o método da requisição
$metodo = $_SERVER['REQUEST_METHOD'];

// Captura os dados da requisição
$dados = json_decode(file_get_contents('php://input'), true);

// Função para enviar resposta JSON
function respostaJson($dados, $status = 200)
{
    http_response_code($status);
    echo json_encode($dados);
}

// Função para preparar e executar consultas SQL
function executarQuery($sql, $parametros = [])
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    return $stmt;
}

switch ($metodo) {
    case 'GET':
        // Obter todas as tarefas
        $tarefas = executarQuery('SELECT * FROM tasks ORDER BY created_at DESC')->fetchAll();
        respostaJson($tarefas);
        break;

    case 'POST':
        // Adicionar nova tarefa
        $titulo = $dados['title'] ?? '';
        if (empty($titulo)) {
            respostaJson(['erro' => 'O título é obrigatório'], 400);
            break;
        }
        $descricao = $dados['description'] ?? '';
        $stmt = executarQuery('INSERT INTO tasks (title, description) VALUES (?, ?)', [$titulo, $descricao]);
        $id = $pdo->lastInsertId();
        respostaJson(['id' => $id, 'title' => $titulo, 'description' => $descricao, 'complete' => false]);
        break;

    case 'PUT':
        // Atualizar tarefa
        $id = $dados['id'] ?? 0;
        if ($id == 0) {
            respostaJson(['sucesso' => false, 'mensagem' => 'ID inválido'], 400);
            break;
        }

        $titulo = $dados['title'] ?? null;
        $descricao = $dados['description'] ?? null;
        $status = isset($dados['complete']) ? ($dados['complete'] ? 1 : 0) : null;

        // Construir campos de atualização
        $campos = [];
        $parametros = [];
        if ($titulo !== null) {
            $campos[] = "title = ?";
            $parametros[] = $titulo;
        }
        if ($descricao !== null) {
            $campos[] = "description = ?";
            $parametros[] = $descricao;
        }
        if ($status !== null) {
            $campos[] = "status = ?";
            $parametros[] = $status;
        }
        $parametros[] = $id;

        // Monta a consulta SQL
        if ($campos) {
            $sql = 'UPDATE tasks SET ' . implode(', ', $campos) . ' WHERE id = ?';
            executarQuery($sql, $parametros);
            respostaJson(['sucesso' => true]);
        } else {
            respostaJson(['sucesso' => false, 'mensagem' => 'Nenhum campo para atualizar'], 400);
        }
        break;

    case 'DELETE':
        // Excluir tarefa
        $id = $_GET['id'] ?? 0;
        if ($id == 0) {
            respostaJson(['sucesso' => false, 'mensagem' => 'ID inválido'], 400);
            break;
        }
        executarQuery('DELETE FROM tasks WHERE id = ?', [$id]);
        respostaJson(['sucesso' => true]);
        break;

    default:
        respostaJson(['erro' => 'Método não permitido'], 405);
        break;
}
