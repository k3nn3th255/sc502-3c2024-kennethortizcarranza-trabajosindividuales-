document.addEventListener('DOMContentLoaded', function () {
    const API_URL = 'backend/tasks.php';
    const taskList = document.getElementById('task-list');

    async function loadTasks() {
        try {
            const response = await fetch(API_URL);
            if (response.ok) {
                const tasks = await response.json();
                renderTasks(tasks);
            }
        } catch (error) {
            console.error("Error al cargar tareas:", error);
        }
    }

    function renderTasks(tasks) {
        taskList.innerHTML = '';
        tasks.forEach(task => {
            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${task.title}</h5>
                        <p class="card-text">${task.description}</p>
                        <p class="card-text"><small>Due: ${task.due_date}</small></p>
                        <h6>Comments:</h6>
                        <ul id="comments-${task.id}"></ul>
                        <input type="text" placeholder="Add comment" class="form-control mb-2" id="comment-input-${task.id}">
                        <button class="btn btn-primary btn-sm" onclick="addComment(${task.id})">Add Comment</button>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-danger btn-sm" onclick="deleteTask(${task.id})">Delete Task</button>
                    </div>
                </div>
            `;
            taskList.appendChild(taskCard);
            loadComments(task.id);
        });
    }

    async function loadComments(taskId) {
        try {
            const response = await fetch(`${API_URL}?task_id=${taskId}`);
            if (response.ok) {
                const comments = await response.json();
                const commentList = document.getElementById(`comments-${taskId}`);
                commentList.innerHTML = comments.map(c => `
                    <li>${c.description} <button class="btn btn-danger btn-sm" onclick="deleteComment(${c.id})">X</button></li>
                `).join('');
            }
        } catch (error) {
            console.error("Error al cargar comentarios:", error);
        }
    }

    async function addComment(taskId) {
        const input = document.getElementById(`comment-input-${taskId}`);
        const comment = input.value.trim();
        if (comment) {
            await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ task_id: taskId, comment: comment })
            });
            loadComments(taskId);
            input.value = '';
        }
    }

    async function deleteComment(commentId) {
        await fetch(`${API_URL}?comment_id=${commentId}`, { method: 'DELETE' });
        loadTasks();
    }

    async function deleteTask(taskId) {
        await fetch(`${API_URL}?id=${taskId}`, { method: 'DELETE' });
        loadTasks();
    }

    loadTasks();
});
