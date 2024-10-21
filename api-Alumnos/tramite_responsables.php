<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            modificarTramites();
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

function modificarTramites()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    $id_tramite = $data['id_tramite'];
    $id_estado_tramite = $data['id_estado_tramite'];
    $id_usuario_responsable = $data['id_usuario_responsable'];

    $stmt = $pdo->prepare("UPDATE tramites SET id_estado_tramite=?, id_usuario_responsable=? WHERE id_tramite=?");
    $stmt->execute([$id_estado_tramite, $id_usuario_responsable, $id_tramite]);


    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tramite modificado Con Exito!']);
}
