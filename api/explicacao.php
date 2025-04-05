<?php

// Incluir o arquivo de configuração que contém as credenciais do banco de dados e outras configurações importantes

require 'config.php';

// config.php
/*
<?php
$host = 'localhost';
$db = 'todo_list';
$user = 'root';
$pass = 'senha_segura';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>
*/

// Você pode ver que a gente está incluindo o arquivo de configuração do banco de dados. Isso é importante porque precisamos conectar ao banco para fazer as operações de CRUD (Criar, Ler, Atualizar e Deletar).

// Captura o método da requisição HTTP (GET, POST, PUT, DELETE)

$metodo = $_SERVER['REQUEST_METHOD']; // O método HTTP da requisição pode ser GET (obter dados), POST (criar), PUT (atualizar) ou DELETE (excluir).

// $_SERVER é uma superglobal do PHP que contém informacoes sobre o servidor e a requisição atual, onde "REQUEST_METHOD" nos dá o método HTTP usado na requisição.

// Captura os dados da requisição. A gente está esperando um corpo de requisição em JSON, então transformamos isso em um array PHP.
$dados = json_decode(file_get_contents('php://input'), true);  // "file_get_contents" é um truque para pegar o que o cliente enviou no corpo da requisição e transformá-lo em algo que a gente pode usar em PHP.

// Basicamente é como se a gente recebesse isso:

/*
{
    "title": "Montar turbao 3kilemei",
    "description": "Pistao forjado por deus"
}
*/

// E transformasse em um array PHP, que é mais fácil de trabalhar com o codigo. Segue exemplo de como ficaria:

/*
Array
(
    [title] => Montar turbao 3kilemei
    [description] => Pistao forjado por deus
)
*/

// O "true" no final do "json_decode" é para garantir que a gente receba um array associativo, e não um objeto. Isso facilita a manipulação dos dados depois.

// Função para enviar uma resposta em formato JSON
function respostaJson($dados, $status = 200)
{
    http_response_code($status);  // Aqui, você define o código de resposta HTTP. 200 é para sucesso, 400 para erro de requisição, 405 para método não permitido, entre outros.
    echo json_encode($dados);     // Depois, pegamos os dados e devolvemos para o cliente. Tudo em formato JSON, porque as APIs funcionam assim.

    // O "json_encode" transforma o array PHP de volta em JSON, que é o formato que o cliente espera receber.

    // Isso é importante porque a maioria dos clientes (como aplicativos web ou mobile) conseguem entender e trabalhar com JSON facilmente.
    // Então, quando o cliente faz uma requisição, ele espera receber uma resposta em JSON. Isso é o que a gente está fazendo aqui.
    // A função "http_response_code" define o código de status HTTP da resposta. Isso é importante para que o cliente saiba se a requisição foi bem-sucedida ou se houve algum erro.
    // O código 200 significa que tudo deu certo, enquanto o 400 indica que houve um erro na requisição (como um campo obrigatório que não foi enviado).
    // O 405 indica que o método HTTP usado não é permitido para essa rota, por exemplo: (Permiti a rota spaturbo.com/api/teste para método GET, e se eu enviar uma requisição do tipo POST para ela vai retornar código 405).

    // O "json_encode" vai fazer a resposta que era assim: 

    /* 
    Array
    (
    [title] => Montar turbao 3kilemei
    [description] => Pistao forjado por deus
    )
    */

    // Virar isso aqui:

    /*
    {
        "title": "Montar turbao 3kilemei",
        "description": "Pistao forjado por deus"
    }
    */
}

// Função para preparar e executar uma consulta SQL
function executarQuery($sql, $parametros = [])
{
    global $pdo;  // A variável $pdo vem do seu arquivo de configuração, aquele que você incluiu lá em cima. Ela nos conecta ao banco de dados.

    // A função `prepare` prepara a consulta SQL. Aqui você está basicamente dizendo: "Banco de dados, prepara uma consulta para nós"
    $stmt = $pdo->prepare($sql);

    // A função `execute` executa a consulta preparada com os parâmetros passados
    $stmt->execute($parametros);  // Os parâmetros são passados para evitar problemas de segurança, como SQL Injection (da uma estudada sobre isso pois é importante).

    return $stmt;  // Após a execução, o objeto $stmt é retornado. Ele pode ser útil para pegar o resultado da consulta, como no caso de um SELECT.
}

// Aqui a coisa começa a ficar interessante! Vamos lidar com o que o cliente nos pediu, com base no método HTTP que ele usou.
switch ($metodo) {
    case 'GET':  // Se o método for GET, isso significa que o cliente quer pegar dados do servidor.
        // Vamos buscar todas as tarefas no banco de dados, ordenadas pela data de criação, do mais recente para o mais antigo.
        $tarefas = executarQuery('SELECT * FROM tasks ORDER BY created_at DESC')->fetchAll();  // O "fetchAll" pega todos os registros da consulta.
        respostaJson($tarefas);  // Responde com todas as tarefas em formato JSON.
        break;

    case 'POST':  // Se o método for POST, estamos criando uma nova tarefa.
        $titulo = $dados['title'] ?? '';  // A gente pega o título da tarefa que o cliente enviou. Se não houver, o título será uma string vazia. O "?? ''" é um operador para garantir que não seja null.

        // Se o título estiver vazio, vamos retornar um erro 400 (requisição errada).
        if (empty($titulo)) {
            respostaJson(['erro' => 'O título é obrigatório'], 400);  // 400 é o código de erro que significa "Bad Request". Se o título for vazio, a requisição está errada.
            break;
        }

        $descricao = $dados['description'] ?? '';  // A descrição é opcional, então, se não vier, a variável será vazia.

        // Agora vamos adicionar a nova tarefa ao banco de dados.
        $stmt = executarQuery('INSERT INTO tasks (title, description) VALUES (?, ?)', [$titulo, $descricao]);

        // Aqui pegamos o ID da nova tarefa que foi criada no banco.
        $id = $pdo->lastInsertId();  // "lastInsertId" é uma função que retorna o ID da última linha inserida no banco. Isso é útil para saber qual foi o ID da tarefa recém-criada.

        // A resposta vai ser a tarefa que acabamos de criar, incluindo o ID gerado e o título.
        respostaJson(['id' => $id, 'title' => $titulo, 'description' => $descricao, 'complete' => false]);  // Retornamos a tarefa para o cliente, com "complete" como falso (pois ainda não foi marcada como concluída).
        break;

    case 'PUT':  // Se o método for PUT, estamos atualizando uma tarefa existente.
        $id = $dados['id'] ?? 0;  // O ID da tarefa que queremos atualizar. Se não vier, o valor será 0 (o que indica que a requisição está errada, e vamos mostrar um erro).
        if ($id == 0) {  // Se o ID for 0, significa que você não passou um ID válido, então vamos mandar um erro 400.
            respostaJson(['sucesso' => false, 'mensagem' => 'ID inválido'], 400);
            break;
        }

        // Aqui você pode atualizar o título, a descrição e o status da tarefa.
        $titulo = $dados['title'] ?? null;  // O título é opcional na atualização, por isso pode ser null.
        $descricao = $dados['description'] ?? null;  // A descrição também pode ser null.

        // Se o cliente nos mandar "complete" como true, vamos marcar como concluído (1); se não, vai ser 0 (não concluído).
        $status = isset($dados['complete']) ? ($dados['complete'] ? 1 : 0) : null;

        // A gente monta dinamicamente os campos da query SQL para atualizar só o que o cliente informou.
        $campos = [];
        $parametros = [];

        if ($titulo !== null) {
            $campos[] = "title = ?";  // Se o título foi enviado, adiciona ele à query.
            $parametros[] = $titulo;  // E adiciona o valor do título nos parâmetros para a query.
        }
        if ($descricao !== null) {
            $campos[] = "description = ?";  // Se a descrição foi enviada, adiciona ela à query.
            $parametros[] = $descricao;  // E o valor da descrição nos parâmetros.
        }
        if ($status !== null) {
            $campos[] = "status = ?";  // Se o status foi enviado, a gente também adiciona à query.
            $parametros[] = $status;   // E o valor do status.
        }
        $parametros[] = $id;  // Nunca se esqueça de passar o ID no final, porque a gente vai precisar disso para achar a tarefa correta no banco.

        // Se algum campo foi enviado para atualização, a query vai ser montada e executada.
        if ($campos) {
            $sql = 'UPDATE tasks SET ' . implode(', ', $campos) . ' WHERE id = ?';  // Monta a query de atualização dinamicamente.
            executarQuery($sql, $parametros);  // Executa a consulta de atualização.
            respostaJson(['sucesso' => true]);  // Retorna sucesso.
        } else {
            respostaJson(['sucesso' => false, 'mensagem' => 'Nenhum campo para atualizar'], 400);  // Se não houve atualização, manda erro.
        }
        break;

    case 'DELETE':  // Se o método for DELETE, estamos excluindo uma tarefa.
        $id = $_GET['id'] ?? 0;  // Pegamos o ID da tarefa para excluir. O ID vem via query string, por exemplo: `?id=1`.
        if ($id == 0) {  // Se o ID for inválido (0), mostramos um erro.
            respostaJson(['sucesso' => false, 'mensagem' => 'ID inválido'], 400);
            break;
        }

        // Exclui a tarefa do banco de dados.
        executarQuery('DELETE FROM tasks WHERE id = ?', [$id]);

        respostaJson(['sucesso' => true]);  // Retorna que deu certo a exclusão.
        break;

    default:  // Se o método não for GET, POST, PUT ou DELETE, vamos responder com erro 405 (Método não permitido).
        respostaJson(['erro' => 'Método não permitido'], 405);  // 405 é quando o método não é aceito pela API.
        break;

        /* Qualquer duvida me manda, ou se precisar de mais explicações sobre alguma parte do código, é só avisar! */
}
