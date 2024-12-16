<?php

require 'db.php';

function crearTarea($user_id, $title, $description, $due_date)
{
    global $pdo;
    try {
        $sql = "INSERT INTO tasks (user_id, title, description, due_date) values (:user_id, :title, :description, :due_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'due_date' => $due_date
        ]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        logError("Error creando tarea: " . $e->getMessage());
        return 0;
    }
}

function crearComentario($task_id, $comment)
{
    global $pdo;
    try {
        $sql = "INSERT INTO comments (task_id, description) VALUES (:task_id, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'task_id' => $task_id,
            'description' => $comment
        ]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        logError("Error creando comentario: " . $e->getMessage());
        return 0;
    }
}

function obtenerComentariosPorTarea($task_id)
{
    global $pdo;
    try {
        $sql = "SELECT * FROM comments WHERE task_id = :task_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['task_id' => $task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        logError("Error al obtener comentarios: " . $e->getMessage());
        return [];
    }
}

function eliminarComentario($comment_id)
{
    global $pdo;
    try {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $comment_id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        logError("Error al eliminar comentario: " . $e->getMessage());
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
function getJsonInput()
{
    return json_decode(file_get_contents("php://input"), true);
}

session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    switch ($method) {
        case 'GET':
            if (isset($_GET['task_id'])) {
                $comments = obtenerComentariosPorTarea($_GET['task_id']);
                echo json_encode($comments);
            } else {
                $tareas = obtenerTareasPorUsuario($user_id);
                echo json_encode($tareas);
            }
            break;

        case 'POST':
            $input = getJsonInput();
            if (isset($input['task_id'], $input['comment'])) {
                $comment_id = crearComentario($input['task_id'], $input['comment']);
                if ($comment_id > 0) {
                    http_response_code(201);
                    echo json_encode(["message" => "Comentario creado: ID " . $comment_id]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error creando comentario"]);
                }
            } elseif (isset($input['title'], $input['description'], $input['due_date'])) {
                $id = crearTarea($user_id, $input['title'], $input['description'], $input['due_date']);
                if ($id > 0) {
                    http_response_code(201);
                    echo json_encode(["message" => "Tarea creada: ID " . $id]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error creando tarea"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos insuficientes"]);
            }
            break;

        case 'DELETE':
            if (isset($_GET['comment_id'])) {
                $deleted = eliminarComentario($_GET['comment_id']);
                if ($deleted) {
                    http_response_code(200);
                    echo json_encode(["message" => "Comentario eliminado"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error eliminando comentario"]);
                }
            } elseif (isset($_GET['id'])) {
                $deleted = eliminarTarea($_GET['id']);
                if ($deleted) {
                    http_response_code(200);
                    echo json_encode(["message" => "Tarea eliminada"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error eliminando tarea"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos insuficientes"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
            break;
    }
} else {
    http_response_code(401);
    echo json_encode(["error" => "Sesión no activa"]);
}
