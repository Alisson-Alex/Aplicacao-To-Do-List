const botaoAdicionar = document.querySelector('.button-add-task');
const campoTituloTarefa = document.getElementById('taskTitle');
const campoDescricaoTarefa = document.getElementById('taskDescription');
const listaTarefas = document.querySelector('.list-tasks');
const seletorFiltro = document.getElementById('filterSelect');  // Captura o select

// URLs da API
const URL_API = 'api/tasks';

// Função para fazer requisições à API
async function buscarDados(url, metodo, dados = null) {
    const opcoes = {
        method: metodo,
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    if (dados) {
        opcoes.body = JSON.stringify(dados);
    }
    
    try {
        const resposta = await fetch(url, opcoes);
        return await resposta.json();
    } catch (erro) {
        console.error('Erro na requisição:', erro);
        return null;
    }
}

// Adicionar nova tarefa
async function adicionarNovaTarefa() {
    const tituloTarefa = campoTituloTarefa.value.trim();
    const descricaoTarefa = campoDescricaoTarefa.value.trim();

    if (tituloTarefa === '') {
        Swal.fire('Erro', 'Por favor, insira um título para a tarefa.', 'error');
        return;
    }
    
    const novaTarefa = await buscarDados(URL_API, 'POST', {
        title: tituloTarefa,
        description: descricaoTarefa
    });
    
    if (novaTarefa) {
        campoTituloTarefa.value = '';
        campoDescricaoTarefa.value = '';
        mostrarTarefas();
    }
}

// Mostrar tarefas
async function mostrarTarefas(filtro) {
    const tarefas = await buscarDados(URL_API);
    
    if (!tarefas) return;
    
    // Filtrando as tarefas com base no valor do filtro
    let tarefasFiltradas = tarefas;
    if (filtro === '0') { // Pendente
        tarefasFiltradas = tarefas.filter(tarefa => tarefa.status === 0); // Filtra status = 0 (pendente)
    } else if (filtro === '1') { // Concluída
        tarefasFiltradas = tarefas.filter(tarefa => tarefa.status === 1); // Filtra status = 1 (completa)
    }
    
    let novaLi = '';
    
    tarefasFiltradas.forEach((item) => {
        novaLi += `
            <li class="task ${item.status === 1 && "done"}" data-id="${item.id}">   
                <div>
                    ${
                        item.editing
                            ? `
                                <input type="text" id="editTitle-${item.id}" value="${item.title}" class="fade-in-input">
                                <textarea id="editDescription-${item.id}" class="fade-in-input">${item.description}</textarea>
                                <button onclick="salvarEdicao(${item.id})">Salvar</button>
                                <button onclick="cancelarEdicao(${item.id})">Cancelar</button>
                            `
                            : `
                                <h3>${item.title}</h3>
                                <p>${item.description}</p>
                            `
                    }
                </div>
                ${item.status === 1 ? '' : `<img src="./img/confirm1.png" alt="check-na-tarefa" onclick="completarTarefa(${item.id}, ${item.status})">`}
                <img src="./img/edit-icon.png" alt="editar-tarefa" onclick="editarTarefa(${item.id})">
                <img src="./img/delete1.png" alt="tarefa-para-lixo" onclick="deletarItem(${item.id})">
            </li>            
        `;
    });
    
    listaTarefas.innerHTML = novaLi;

    // Adicionar a classe 'visible' para os inputs de fade-in
    const fadeInInputs = document.querySelectorAll('.fade-in-input');
    fadeInInputs.forEach(input => {
        input.classList.add('visible');
    });
}

// Marcar/desmarcar como completa
async function completarTarefa(id, statusAtual) {
    await buscarDados(URL_API, 'PUT', {
        id: id,
        complete: !statusAtual
    });
    mostrarTarefas(seletorFiltro.value); // Atualiza as tarefas com o filtro atual
}

// Excluir tarefa
async function deletarItem(id) {
    const resultado = await Swal.fire({
        title: 'Tem certeza?',
        text: 'Você não poderá reverter isso!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    });

    if (resultado.isConfirmed) {
        await buscarDados(`${URL_API}?id=${id}`, 'DELETE');
        Swal.fire('Deletado!', 'A tarefa foi excluída.', 'success');
        mostrarTarefas(seletorFiltro.value); // Atualiza as tarefas com o filtro atual
    }
}

// Editar tarefa
function editarTarefa(id) {
    const tarefaElemento = document.querySelector(`.task[data-id="${id}"]`);
    tarefaElemento.querySelector('div').innerHTML = `
        <input type="text" id="editTitle-${id}" value="${tarefaElemento.querySelector('h3').textContent}" class="fade-in-input">
        <textarea id="editDescription-${id}" class="fade-in-input">${tarefaElemento.querySelector('p').textContent}</textarea>
        <button onclick="salvarEdicao(${id})">Salvar</button>
        <button onclick="cancelarEdicao(${id})">Cancelar</button>
    `;

    // Adiciona a classe 'visible' para o efeito de fade-in no campo de edição
    const fadeInInputs = tarefaElemento.querySelectorAll('.fade-in-input');
    fadeInInputs.forEach(input => {
        input.classList.add('visible');
    });
}

// Cancelar edição
function cancelarEdicao(id) {
    mostrarTarefas(seletorFiltro.value); // Atualiza as tarefas com o filtro atual
}

// Salvar edição
async function salvarEdicao(id) {
    const novoTitulo = document.getElementById(`editTitle-${id}`).value.trim();
    const novaDescricao = document.getElementById(`editDescription-${id}`).value.trim();
    
    if (novoTitulo === '') {
        Swal.fire('Erro', 'O título não pode estar vazio', 'error');
        return;
    }
    
    await buscarDados(URL_API, 'PUT', {
        id: id,
        title: novoTitulo,
        description: novaDescricao,
        complete: document.querySelector(`.task[data-id="${id}"]`).classList.contains('done')
    });
    
    mostrarTarefas(seletorFiltro.value); // Atualiza as tarefas com o filtro atual
}

// Carregar tarefas ao iniciar
mostrarTarefas();

// Event listeners
botaoAdicionar.addEventListener('click', adicionarNovaTarefa);

campoTituloTarefa.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        adicionarNovaTarefa();
    }
});

campoDescricaoTarefa.addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        adicionarNovaTarefa();
    }
});

// Adicionando o evento para o select
seletorFiltro.addEventListener('change', function() {
    mostrarTarefas(seletorFiltro.value); // Atualiza as tarefas com o filtro selecionado
});

// Configuração do Particles.js
particlesJS("particles-js", {
    particles: {
        number: {
            value: 100,
            density: {
                enable: true,
                value_area: 800
            }
        },
        color: {
            value: "#ffffff"
        },
        shape: {
            type: "circle",
            stroke: {
                width: 0,
                color: "#000000"
            }
        },
        opacity: {
            value: 0.5,
            random: true,
            anim: {
                enable: true,
                speed: 1,
                opacity_min: 0.1,
                sync: false
            }
        },
        size: {
            value: 3,
            random: true,
            anim: {
                enable: false
            }
        },
        line_linked: {
            enable: false,
        },
        move: {
            enable: true,
            speed: 1,
            direction: "none",
            random: true,
            straight: false,
            out_mode: "out",
            attract: {
                enable: false
            }
        }
    },
    interactivity: {
        detect_on: "canvas",
        events: {
            onhover: {
                enable: false,
            },
            onclick: {
                enable: false,
            }
        }
    },
    retina_detect: true
});
