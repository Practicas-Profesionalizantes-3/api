<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarNotificacion();
            break;
        case 'POST':
            crearNotificacion();
            break;
        case 'PUT':
            modificarNotificacion();
            break;
        case 'DELETE':
            borrarNotificacion();
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

function crearNotificacion()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    $id_aviso = $data['id_aviso'] ?? null;
    $id_tramite = $data['id_tramite'] ?? null; // Puedes recibirlo o mantenerlo como null
    $id_notificacion_tipo = $data['id_notificacion_tipo'];
    $id_notificacion_estado = $data['id_notificacion_estado'];

    $stmt = $pdo->prepare("INSERT INTO notificaciones (id_aviso, id_tramite, id_notificacion_tipo, fecha_envio_notificacion, id_notificacion_estado) VALUES (?, ?, ?, NOW(), ?)");
    $stmt->execute([$id_aviso, $id_tramite, $id_notificacion_tipo, $id_notificacion_estado]);

    http_response_code(201); // Creado
    echo json_encode(['mensaje' => "Notificación creada correctamente!"]);
}


function modificarNotificacion()
{
    global $pdo;

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405); // Método no permitido
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Obtener el cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificar campos obligatorios
        if (!isset($data['id_notificacion']) || !isset($data['id_notificacion_estado']) ||
            !is_numeric($data['id_notificacion']) || !is_numeric($data['id_notificacion_estado'])) {
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(['error' => 'Todos los campos son obligatorios y deben ser numéricos']);
            return;
        }

        $id_notificacion = (int)$data['id_notificacion'];
        $id_notificacion_estado = (int)$data['id_notificacion_estado'];

        // Actualizar la base de datos
        $stmt = $pdo->prepare("UPDATE notificaciones SET id_notificacion_estado=? WHERE id_notificacion=?");
        $stmt->execute([$id_notificacion_estado, $id_notificacion]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404); // No encontrado
            echo json_encode(['error' => 'Notificación no encontrada o sin cambios']);
            return;
        }

        // Respuesta exitosa
        echo json_encode(['mensaje' => 'Notificación modificada correctamente!']);
    } catch (Exception $e) {
        http_response_code(500); // Error interno del servidor
        echo json_encode(['error' => 'Error en la API: ' . $e->getMessage()]);
    }
}

function borrarNotificacion()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion = $data['id_notificacion'];

    $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id_notificacion = ?");
    $stmt->execute([$id_notificacion]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Notificacion no encontrada']);
        return;
    }

    echo json_encode(['mensaje' => 'Notificacion eliminada correctamente!']);
}


function listarNotificacion()
{
    global $pdo;

    $stmt = null;

    $id_notificacion = isset($_GET['id_notificacion']) ? $_GET['id_notificacion'] : null;
    $id_aviso = isset($_GET['id_aviso']) ? (int)$_GET['id_aviso'] : null;
    $id_tramite = isset($_GET['id_tramite']) ? $_GET['id_tramite'] : null;
    $id_notificacion_tipo = isset($_GET['id_notificacion_tipo']) ? $_GET['id_notificacion_tipo'] : null;
    $fecha_envio_notificacion = isset($_GET['fecha_envio_notificacion']) ? $_GET['fecha_envio_notificacion'] : null;
    $id_notificacion_estado = isset($_GET['id_notificacion_estado']) ? $_GET['id_notificacion_estado'] : null;

    $sql = "SELECT
    n.id_notificacion,
    n.id_aviso,
    a.descripcion as id_aviso_descripcion,
    n.id_tramite,
    t.descripcion AS id_tramite_descripcion,
    tn.descripcion AS id_notificacion_tipo,
    n.fecha_envio_notificacion,
    n.id_notificacion_estado
FROM
    notificaciones AS n
    LEFT JOIN avisos AS a ON n.id_aviso = a.id_aviso
    LEFT JOIN tramites AS t ON n.id_tramite = t.id_tramite
    LEFT JOIN tipo_notificaciones AS tn ON n.id_notificacion_tipo = tn.id_notificacion_tipo
    WHERE 1=1";

    if ($id_notificacion != null) {
        $sql .= " AND n.id_notificacion=$id_notificacion";
    }
    if ($id_aviso != null) {
        $sql .= " AND n.id_aviso=$id_aviso";
    }
    if ($id_tramite != null) {
        $sql .= " AND n.id_tramite=$id_tramite";
    }
    if ($id_notificacion_tipo != null) {
        $sql .= " AND n.id_notificacion_tipo=$id_notificacion_tipo";
    }
    if ($fecha_envio_notificacion != null) {
        $sql .= " AND n.fecha_envio_notificacion=$fecha_envio_notificacion";
    }


    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$notificaciones) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($notificaciones);
}
