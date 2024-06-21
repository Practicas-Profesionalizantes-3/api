<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarTipo_notificaciones();
            break;
        case 'POST':
            crearTipo_notificaciones();
            break;
        case 'PUT':
            modificarTipo_notificaciones();
            break;
        case 'DELETE':
            borrarTipo_notificaciones();
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

function crearTipo_notificaciones()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion_tipo = $data['id_notificacion_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO tipo_notificaciones (id_notificacion_tipo, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_notificacion_tipo, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Tipo de Notificacion Nº " . $pdo->lastInsertId() . " Creado Correctamente!!"]);
}

function modificarTipo_notificaciones()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion_tipo = $data['id_notificacion_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE tipo_notificaciones SET id_notificacion_tipo=?, descripcion=? WHERE id_notificacion_tipo=?");
    $stmt->execute([$id_notificacion_tipo, $descripcion, $id_notificacion_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Notificacion no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Notificacion modificado Con Exito!']);
}


function borrarTipo_notificaciones()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion_tipo = $data['id_notificacion_tipo'];

    $stmt = $pdo->prepare("DELETE FROM tipo_notificaciones WHERE id_notificacion_tipo=?");
    $stmt->execute([$id_notificacion_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Notificacion no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Notificacion eliminado Con Exito!']);
}


function listarTipo_notificaciones()
{
    global $pdo;

    $id_notificacion_tipo = isset($_GET['id_notificacion_tipo']) ? (int)$_GET['id_notificacion_tipo'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM tipo_notificaciones 
    WHERE 1=1";

    if ($id_notificacion_tipo != null) {
        $sql .= " AND id_notificacion_tipo=$id_notificacion_tipo";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $aviso_estado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$aviso_estado) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($aviso_estado);
}
