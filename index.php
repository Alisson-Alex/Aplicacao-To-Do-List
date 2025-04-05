<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">
    <title>Desafio To-Do List</title>
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/iconoir-icons/iconoir@main/css/iconoir.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div id="particles-js"></div>

    <div class="navBox">
        <!-- Título adicionado aqui -->
        <h2 class="nav-title">Lista de Tarefas</h2>
        <nav>
            <ul>
                <label for="filterSelect" class="nav-link">
                    Filtrar por status
                    <i class="iconoir-long-arrow-down-right"></i>
                </label>
                <select id="filterSelect" class="filter-select">
                    <option value="all">Todas Tarefas</option>
                    <option value="0">Pendente</option>
                    <option value="1">Concluída</option>
                </select>
            </ul>
        </nav>
    </div>

    <div class="input-container">
        <ul>
            <input type="text" id="taskTitle" placeholder="Título da tarefa..." class="input-title">
            <textarea id="taskDescription" placeholder="Descrição da tarefa..." class="input-description"></textarea>
            <button class="button-add-task">Adicionar</button>
        </ul>
    </div>

    <div class="box-tasks">
        <ul class="list-tasks"></ul>
    </div>

    <script src="scripts.js"></script>
</body>

</html>