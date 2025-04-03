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
        complete: false
})


    taskTitleinput.value = ''
    taskDescriptionInput.value = ''

    showTasks()
}

function showTasks(){ //Função mostrar cada tarefa da lista

    let newLi = ''

    myList.forEach((item, position1) => { //Lê item por item no array
        newLi = newLi + `

            <li class="task ${item.complete && "done"}">   
                <div>
                    <h3>${item.title}</h3>
                    <p>${item.description}</p>
                </div>
                    <img src="./img/confirm1.png" alt="check-na-tarefa" onclick="completeTask(${position1})">
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
