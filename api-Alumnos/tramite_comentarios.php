<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            modificarComentariosTramites();
            break;
        default:
            http_response_code(405); // Método no permitido
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500); // Error del servidor
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(['error' => $e->getMessage()]);
}

//Mofica los comentarios de los tramites
function modificarComentariosTramites()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_tramite']) || !isset($data['comentarios'])) {
        throw new Exception('Faltan datos obligatorios');
    }

    $id_tramite = $data['id_tramite'];
    $comentarios = $data['comentarios'];

    $stmt = $pdo->prepare("UPDATE tramites SET comentarios=? WHERE id_tramite=?");
    $stmt->execute([$comentarios, $id_tramite]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Trámite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Comentario agregado con éxito']);
}
