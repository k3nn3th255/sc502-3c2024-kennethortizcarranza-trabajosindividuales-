const API_URL_TASKS = 'backend/tasks.php';
const API_URL_COMMENTS = 'backend/comments.php';

// Función para agregar un comentario
document.getElementById('comment-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const commentId = document.getElementById('comment-id').value;
    const comment = document.getElementById('task-comment').value;
    const taskId = parseInt(document.getElementById('comment-task-id').value);

    let response;
    if (commentId) {
        // Modo edición: actualizar el comentario existente
        response = await fetch(`${API_URL_COMMENTS}?id=${commentId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ task_id: taskId, description: comment }),
            credentials: 'include'
        });
    } else {
        // Modo creación: agregar un nuevo comentario
        response = await fetch(API_URL_COMMENTS, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ task_id: taskId, description: comment }),
            credentials: 'include'
        });
    }

    if (response.ok) {
        loadTasks(); // Recargar tareas y comentarios
    } else {
        console.error("Error al guardar el comentario");
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById('commentModal'));
    modal.hide();
});

// Función para eliminar un comentario
document.querySelectorAll('.remove-comment').forEach(function (button) {
    button.addEventListener('click', async function (e) {
        const commentId = parseInt(e.target.dataset.commentid);

        const response = await fetch(`${API_URL_COMMENTS}?id=${commentId}`, { method: 'DELETE', credentials: 'include' });

        if (response.ok) {
            loadTasks(); // Recargar tareas y comentarios
        } else {
            console.error("Error al eliminar el comentario");
        }
    });
});

// Añadir manejadores de eventos para editar comentarios
document.querySelectorAll('.edit-comment').forEach(function (button) {
    button.addEventListener('click', async function (e) {
        const commentId = parseInt(e.target.dataset.commentid);
        const taskId = parseInt(e.target.closest('.card').querySelector('.add-comment').dataset.id); // Obtener ID de la tarea asociada

        // Cargar el comentario a editar
        const response = await fetch(`${API_URL_COMMENTS}?id=${commentId}`, { method: 'GET', credentials: 'include' });
        if (response.ok) {
            const commentData = await response.json();
            document.getElementById('task-comment').value = commentData.description; // Cargar texto del comentario
            document.getElementById('comment-id').value = commentId; // Establecer el ID del comentario
            document.getElementById('comment-task-id').value = taskId; // Establecer el ID de la tarea

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById("commentModal"));
            modal.show();
        } else {
            console.error("Error al obtener el comentario");
        }
    });
});


