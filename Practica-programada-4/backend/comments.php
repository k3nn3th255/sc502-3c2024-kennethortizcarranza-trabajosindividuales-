<?php
require 'db.php';

// Crear un nuevo comentario
function crearComentario($task_id, $description)
{
    global $pdo;
    try {
        $sql = "INSERT INTO comments (task_id, description) VALUES (:task_id, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['task_id' => $task_id, 'description' => $description]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        logError("Error creando comentario: " . $e->getMessage());
        return 0;
    }
}

// Obtener comentarios por tarea
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

// Eliminar un comentario por id
function eliminarComentario($id)
{
    global $pdo;
    try {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0; // true si se elimina algo
    } catch (Exception $e) {
        logError("Error al eliminar comentario: " . $e->getMessage());
        return false;
    }
}

// Manejo de solicitudes HTTP
$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

switch ($method) {
    case 'GET':
        if (isset($_GET['task_id'])) {
            // Obtener comentarios de una tarea específica
            $comentarios = obtenerComentariosPorTarea($_GET['task_id']);
            echo json_encode($comentarios);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de tarea no proporcionado"]);
        }
        break;

    case 'POST':
        // Crear un nuevo comentario
        $input = json_decode(file_get_contents("php://input"), true);

        if (isset($input['task_id'], $input['description'])) {
            $id = crearComentario($input['task_id'], $input['description']);
            if ($id > 0) {
                http_response_code(201);
                echo json_encode(["message" => "Comentario creado: ID:" . $id]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error general creando el comentario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Datos insuficientes"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            // Eliminar un comentario
            if (eliminarComentario($_GET['id'])) {
                http_response_code(200);
                echo json_encode(['message' => "Comentario eliminado"]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Sucedió un error al eliminar el comentario']);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de comentario no proporcionado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
        
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (isset($input['task_id'], $input['description']) && isset($_GET['id'])) {
            $updated = actualizarComentario($_GET['id'], $input['task_id'], $input['description']);
            if ($updated) {
                http_response_code(200);
                echo json_encode(["message" => "Comentario actualizado"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando el comentario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Datos insuficientes"]);
        }
        break;

        // Función para actualizar un comentario
        function actualizarComentario($id, $task_id, $description)
        {
            global $pdo;
            try {
                $sql = "UPDATE comments SET task_id = :task_id, description = :description WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute(['task_id' => $task_id, 'description' => $description, 'id' => $id]);
            } catch (Exception $e) {
                logError("Error actualizando comentario: " . $e->getMessage());
                return false;
            }
        }
}
