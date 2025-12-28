document.addEventListener('DOMContentLoaded', function() {
    // Gerenciamento de Tarefas (LocalStorage para persistência simples)
    const taskList = document.getElementById('task-list');
    const newTaskInput = document.getElementById('new-task-input');
    const addTaskBtn = document.getElementById('add-task-btn');

    function loadTasks() {
        let tasks = JSON.parse(localStorage.getItem('dashboard_tasks'));
        
        // Se não houver tarefas, cria algumas sugestões baseadas no contexto
        if (!tasks) {
            tasks = [
                { text: 'Conferir OS finalizadas do dia', completed: false },
                { text: 'Organizar bancada de trabalho', completed: true },
                { text: 'Atualizar status de peças no sistema', completed: false }
            ];
            saveTasks(tasks);
        }
        renderTasks(tasks);
    }

    function saveTasks(tasks) {
        localStorage.setItem('dashboard_tasks', JSON.stringify(tasks));
    }

    function renderTasks(tasks) {
        if (!taskList) return;
        taskList.innerHTML = '';
        tasks.forEach((task, index) => {
            const div = document.createElement('div');
            div.className = `task-item ${task.completed ? 'completed' : ''}`;
            div.innerHTML = `
                <label class="task-checkbox ${task.completed ? 'completed' : ''}">
                    <input type="checkbox" ${task.completed ? 'checked' : ''} data-index="${index}">
                    <span>${task.text}</span>
                </label>
            `;
            taskList.appendChild(div);
        });

        // Adicionar eventos aos checkboxes
        taskList.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const index = this.dataset.index;
                tasks[index].completed = this.checked;
                saveTasks(tasks);
                renderTasks(tasks);
            });
        });
    }

    if (addTaskBtn && newTaskInput) {
        addTaskBtn.addEventListener('click', function() {
            const text = newTaskInput.value.trim();
            if (text) {
                const tasks = JSON.parse(localStorage.getItem('dashboard_tasks') || '[]');
                tasks.push({ text, completed: false });
                saveTasks(tasks);
                renderTasks(tasks);
                newTaskInput.value = '';
            }
        });

        newTaskInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') addTaskBtn.click();
        });
    }

    // Inicializar
    loadTasks();

    // Simulação de Alertas (UX/DX)
    const alertsContainer = document.getElementById('alerts-container');
    if (alertsContainer) {
        const demoAlerts = [
            { id: 2351, type: 'success', message: 'OS 2351 pronta - Avisar cliente' },
            { id: 2348, type: 'warning', message: 'OS 2348 aguardando peças há 2 dias' }
        ];

        function renderAlerts() {
            alertsContainer.innerHTML = '';
            demoAlerts.forEach(alert => {
                const div = document.createElement('div');
                div.className = 'alert-item';
                div.innerHTML = `<strong>🔔 Alerta:</strong> ${alert.message}`;
                alertsContainer.appendChild(div);
            });
        }
        renderAlerts();
    }
});
