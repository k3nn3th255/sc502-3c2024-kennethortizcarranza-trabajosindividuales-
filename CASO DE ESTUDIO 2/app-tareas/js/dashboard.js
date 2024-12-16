document.addEventListener('DOMContentLoaded', function () {
    const API_URL = 'backend/tasks.php';

    async function loadTasks() {
        try {
            const response = await fetch(API_URL, {
                method: 'GET',
                credentials: 'include'
            });
            if (response.ok) {
                const tasks = await response.json();
                renderTasks(tasks);
            } else if (response.status === 401) {
                window.location.href = 'index.html';
            } else {
                console.error("Error al obtener tareas");
            }
        } catch (err) {
            console.error(err);
        }
    }

    function renderTasks(tasks) {
        const taskList = document.getElementById('task-list');
        taskList.innerHTML = '';
        tasks.forEach(task => {
            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${task.title}</h5>
                        <p class="card-text">${task.description}</p>
                        <ul>
                            ${task.comments ? task.comments.map(comment => `
                                <li>${comment.description} 
                                    <button data-commentid="${comment.id}" class="btn btn-sm btn-danger remove-comment">Remove</button>
                                </li>
                            `).join('') : ''}
                        </ul>
                        <button data-id="${task.id}" class="btn btn-primary add-comment">Add Comment</button>
                    </div>
                </div>
            `;
            taskList.appendChild(taskCard);
        });

        document.querySelectorAll('.add-comment').forEach(btn => btn.addEventListener('click', handleAddComment));
        document.querySelectorAll('.remove-comment').forEach(btn => btn.addEventListener('click', handleRemoveComment));
    }

    async function handleAddComment(e) {
        const taskId = e.target.dataset.id;
        const comment = prompt("Enter your comment:");
        if (comment) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ task_id: taskId, comment: comment }),
                    credentials: 'include'
                });
                if (response.ok) {
                    loadTasks();
                } else {
                    console.error("Error adding comment");
                }
            } catch (err) {
                console.error(err);
            }
        }
    }

    async function handleRemoveComment(e) {
        const commentId = e.target.dataset.commentid;
        try {
            const response = await fetch(`${API_URL}?comment_id=${commentId}`, {
                method: 'DELETE',
                credentials: 'include'
            });
            if (response.ok) {
                loadTasks();
            } else {
                console.error("Error removing comment");
            }
        } catch (err) {
            console.error(err);
        }
    }

    loadTasks();
});
