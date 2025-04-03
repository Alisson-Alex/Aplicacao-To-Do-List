const button = document.querySelector('.button-add-task') // document sempre se refere ao arquivo html
const taskTitleinput = document.getElementById('taskTitle')
const taskDescriptionInput = document.getElementById('taskDescription')
const listComplete = document.querySelector('.list-tasks')

let myList = []

function addNewTask(){ // Função que adiciona o 'valor' digitado no campo input e a informação inconcluída.
    const taskTitle = taskTitleinput.value.trim()  // trim() remove espaços em branco extras
    const taskDescription = taskDescriptionInput.value.trim()

    if (taskTitle === ''){ // Verifica se taskValue esta vazio após a remoção dos espaços, se vazio emite um alerta
        alert('Por favor, insira um título para a tarefa.')
        return
    }
    
    myList.push({
        title: taskTitle,
        description: taskDescription,
        complete: false,
        editing: false
})


    taskTitleinput.value = ''
    taskDescriptionInput.value = ''

    showTasks()
}

function showTasks(){ //Função mostrar cada tarefa da lista
    let newLi = ''

    myList.forEach((item, position1) => { //Lê item por item no array
        newLi +=  `
            <li class="task ${item.complete && "done"}">   
                <div>
                    ${
                        item.editing
                            ? `
                                <input type="text" id="editTitle-${position1}" value="${item.title}">
                                <textarea id="editDescription-${position1}">${item.description}</textarea>
                                <button onclick="saveEdit(${position1})">Salvar</button>
                                <button onclick="cancelEdit(${position1})">Cancelar</button>
                            `
                            : `
                                <h3>${item.title}</h3>
                                <p>${item.description}</p>
                            `
                    }
                </div>
                <img src="./img/confirm1.png" alt="check-na-tarefa" onclick="completeTask(${position1})">
                <img src="./img/edit-icon.png" alt="editar-tarefa" onclick="editTask(${position1})">
                <img src="./img/delete1.png" alt="tarefa-para-lixo" onclick="deleteItem(${position1})">
            </li>            
        `
    }) 

    listComplete.innerHTML = newLi
    localStorage.setItem('list', JSON.stringify(myList)) //JSON.stringfy transforma objeto em string
}

function completeTask(position1){ // Funçao que informa ao usuário que a tarefa foi concluida ou não, invertendo seu valor.
    myList[position1].complete = !myList[position1].complete
    showTasks()
}

function deleteItem(position1){
    myList.splice(position1, 1) // Permite deletar dentro do array, informar posição e quantidade de itens
    showTasks()
}

function rechargeTask(){
    const taskLocalStorage = localStorage.getItem('list')

    if(taskLocalStorage){ // So adiciona valor na myList se taskLocalStorage não estiver vazio.
        myList=JSON.parse(taskLocalStorage) // JSON.parce trasnforma string em objeto.
    }

    showTasks()
}

function editTask(position1){
    myList[position1].editing=true
    showTasks()
}

function cancelEdit(position1){
    myList[position1].editing=false
    showTasks()
}

function saveEdit(position1) {
    // Obter os valores dos inputs
    const newTitle = document.getElementById(`editTitle-${position1}`).value;
    const newDescription = document.getElementById(`editDescription-${position1}`).value;
  
    // Atualizar o array myList
    myList[position1].title = newTitle;
    myList[position1].description = newDescription;
  
    // Desativar o modo de edição
    myList[position1].editing = false;
  
    // Atualizar a exibição
    showTasks();
  }



rechargeTask()
button.addEventListener('click', addNewTask) // Atribuindo 'valor' da nova tarefa ao clicar.

taskTitleinput.addEventListener('keyup', function (event){   // Adicionando listeners de evento para "Enter";
    if (event.key === 'Enter'){
        addNewTask()
    }
})

taskDescriptionInput.addEventListener('keyup', function (event){    // Adicionando listeners de evento para "Enter".
    if (event.key === 'Enter'){
        addNewTask()
    }
})
