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
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Faltan datos obligatorios']);
        return;
    }

    $id_tramite = $data['id_tramite'];
    $comentarios = $data['comentarios'];

    // Seleccionar el registro original
    $stmt = $pdo->prepare("SELECT * FROM tramites WHERE id_tramite = ?");
    $stmt->execute([$id_tramite]);
    $tramite = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tramite) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Trámite no encontrado']);
        return;
    }

    // Obtener los valores desde el registro existente
    $id_usuario_creacion = $tramite['id_usuario_creacion'];
    $id_usuario_responsable = $tramite['id_usuario_responsable'];
    $id_tramite_tipo = $tramite['id_tramite_tipo'];
    $id_estado_tramite = $tramite['id_estado_tramite'];
    $descripcion = $tramite['descripcion'];

    // Establecer la zona horaria de Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha_creacion = date("Y-m-d H:i:s");

    // Insertar el nuevo trámite en la base de datos, incluyendo el id_tramite
    $stmtInsert = $pdo->prepare("INSERT INTO tramites (id_tramite, id_usuario_creacion, id_usuario_responsable, id_tramite_tipo, id_estado_tramite, descripcion, comentarios, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmtInsert->execute([$id_tramite, $id_usuario_creacion, $id_usuario_responsable, $id_tramite_tipo, $id_estado_tramite, $descripcion, $comentarios, $fecha_creacion])) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error al insertar el nuevo trámite']);
        return;
    }

    $nuevo_id_tramite = $pdo->lastInsertId();

    // Crear notificación
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/api/notificaciones/crearNotificacion.php");
    curl_setopt($ch, CURLOPT_POST, 1);

    $notificacionData = json_encode([
        "id_tramite" => $nuevo_id_tramite,
        "id_aviso" => null,
        "id_notificacion_tipo" => 2,
        "id_notificacion_estado" => 1
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $notificacionData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Verificar respuesta de la API de notificaciones
    $responseDecoded = json_decode($response, true);
    if (isset($responseDecoded['mensaje'])) {
        echo json_encode(["mensaje" => "Comentario agregado y trámite duplicado con éxito"]);
    } else {
        echo json_encode(["codigo" => 500, "success" => false, "mensaje" => "Error al crear la notificación."]);
    }
}

