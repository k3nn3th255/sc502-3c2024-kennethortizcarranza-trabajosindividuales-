<?php
require 'db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

// Funciones para tareas
function obtenerTareas() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM tasks");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function agregarTarea($data) {
    global $pdo;
    $sql = "INSERT INTO tasks (title, description, due_date) VALUES (:title, :description, :due_date)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':due_date' => $data['due_date']
    ]);
}

function eliminarTarea($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

// Funciones para comentarios
function agregarComentario($task_id, $comment) {
    global $pdo;
    $sql = "INSERT INTO comments (task_id, description) VALUES (:task_id, :description)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':task_id' => $task_id, ':description' => $comment]);
}

function obtenerComentarios($task_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE task_id = :task_id");
    $stmt->execute([':task_id' => $task_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function eliminarComentario($comment_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    return $stmt->execute([':id' => $comment_id]);
}

// Manejo de métodos HTTP
switch ($method) {
    case 'GET':
        if (isset($_GET['task_id'])) {
            echo json_encode(obtenerComentarios($_GET['task_id']));
        } else {
            echo json_encode(obtenerTareas());
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['task_id'], $input['comment'])) {
            agregarComentario($input['task_id'], $input['comment']);
            echo json_encode(["message" => "Comentario agregado"]);
        } else if (isset($input['title'], $input['description'], $input['due_date'])) {
            agregarTarea($input);
            echo json_encode(["message" => "Tarea agregada"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['comment_id'])) {
            eliminarComentario($_GET['comment_id']);
            echo json_encode(["message" => "Comentario eliminado"]);
        } else if (isset($_GET['id'])) {
            eliminarTarea($_GET['id']);
            echo json_encode(["message" => "Tarea eliminada"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
