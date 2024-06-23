<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarNotificacion_estado();
            break;
        case 'POST':
            crearNotificacion_estado();
            break;
        case 'PUT':
            modificarNotificacion_estado();
            break;
        case 'DELETE':
            borrarNotificacion_estado();
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

function crearNotificacion_estado()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion_estado']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion_estado = $data['id_notificacion_estado'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO notificacion_estado (id_notificacion_estado, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_notificacion_estado, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Estado de Notificacion Nº " . $pdo->lastInsertId() . " Creado Correctamente!!"]);
}

function modificarNotificacion_estado()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion_estado']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion_estado = $data['id_notificacion_estado'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE notificacion_estado SET descripcion=? WHERE id_notificacion_estado=?");
    $stmt->execute([$descripcion, $id_notificacion_estado]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Estado de Notificacion no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Estado de Notificacion modificado Con Exito!']);
}


function borrarNotificacion_estado()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_notificacion_estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_notificacion_estado = $data['id_notificacion_estado'];

    $stmt = $pdo->prepare("DELETE FROM notificacion_estado WHERE id_notificacion_estado=?");
    $stmt->execute([$id_notificacion_estado]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Estado de Notificacion no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Estado de Notificacion eliminado Con Exito!']);
}


function listarNotificacion_estado()
{
    global $pdo;

    $id_notificacion_estado = isset($_GET['id_notificacion_estado']) ? (int)$_GET['id_notificacion_estado'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM notificacion_estado 
    WHERE 1=1";

    if ($id_notificacion_estado != null) {
        $sql .= " AND id_notificacion_estado=$id_notificacion_estado";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $notificacion_estado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$notificacion_estado) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($notificacion_estado);
}
