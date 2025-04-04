const button = document.querySelector('.button-add-task');
const taskTitleinput = document.getElementById('taskTitle');
const taskDescriptionInput = document.getElementById('taskDescription');
const listComplete = document.querySelector('.list-tasks');

// URLs da API
const API_URL = 'php/tasks.php';

// Função para fazer requisições à API
async function fetchData(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Erro na requisição:', error);
        return null;
    }
}

// Adicionar nova tarefa
async function addNewTask() {
    const taskTitle = taskTitleinput.value.trim();
    const taskDescription = taskDescriptionInput.value.trim();

    if (taskTitle === '') {
        alert('Por favor, insira um título para a tarefa.');
        return;
    }
    
    const newTask = await fetchData(API_URL, 'POST', {
        title: taskTitle,
        description: taskDescription
    });
    
    if (newTask) {
        taskTitleinput.value = '';
        taskDescriptionInput.value = '';
        showTasks();
    }
}

// Mostrar tarefas
async function showTasks() {
    const tasks = await fetchData(API_URL);
    
    if (!tasks) return;
    
    let newLi = '';
    
    tasks.forEach((item) => {
        newLi += `
            <li class="task ${item.complete && "done"}" data-id="${item.id}">   
                <div>
                    ${
                        item.editing
                            ? `
                                <input type="text" id="editTitle-${item.id}" value="${item.title}">
                                <textarea id="editDescription-${item.id}">${item.description}</textarea>
                                <button onclick="saveEdit(${item.id})">Salvar</button>
                                <button onclick="cancelEdit(${item.id})">Cancelar</button>
                            `
                            : `
                                <h3>${item.title}</h3>
                                <p>${item.description}</p>
                            `
                    }
                </div>
                <img src="./img/confirm1.png" alt="check-na-tarefa" onclick="completeTask(${item.id}, ${item.complete})">
                <img src="./img/edit-icon.png" alt="editar-tarefa" onclick="editTask(${item.id})">
                <img src="./img/delete1.png" alt="tarefa-para-lixo" onclick="deleteItem(${item.id})">
            </li>            
        `;
    });
    
    listComplete.innerHTML = newLi;
}

// Marcar/desmarcar como completa
async function completeTask(id, currentStatus) {
    await fetchData(API_URL, 'PUT', {
        id: id,
        complete: !currentStatus
    });
    showTasks();
}

// Excluir tarefa
async function deleteItem(id) {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        await fetchData(`${API_URL}?id=${id}`, 'DELETE');
        showTasks();
    }
}

// Editar tarefa
function editTask(id) {
    const taskElement = document.querySelector(`.task[data-id="${id}"]`);
    taskElement.querySelector('div').innerHTML = `
        <input type="text" id="editTitle-${id}" value="${taskElement.querySelector('h3').textContent}">
        <textarea id="editDescription-${id}">${taskElement.querySelector('p').textContent}</textarea>
        <button onclick="saveEdit(${id})">Salvar</button>
        <button onclick="cancelEdit(${id})">Cancelar</button>
    `;
}

// Cancelar edição
function cancelEdit(id) {
    showTasks();
}

// Salvar edição
async function saveEdit(id) {
    const newTitle = document.getElementById(`editTitle-${id}`).value.trim();
    const newDescription = document.getElementById(`editDescription-${id}`).value.trim();
    
    if (newTitle === '') {
        alert('O título não pode estar vazio');
        return;
    }
    
    await fetchData(API_URL, 'PUT', {
        id: id,
        title: newTitle,
        description: newDescription,
        complete: document.querySelector(`.task[data-id="${id}"]`).classList.contains('done')
    });
    
    showTasks();
}

// Carregar tarefas ao iniciar
showTasks();

// Event listeners
button.addEventListener('click', addNewTask);

taskTitleinput.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        addNewTask();
    }
});

taskDescriptionInput.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        addNewTask();
    }
});
