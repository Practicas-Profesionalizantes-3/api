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

function crearNotificacion() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['tipo_notificacion']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $tipo_notificacion = $data['tipo_notificacion'];
    $descripcion = $data['descripcion'];

    //DATE('y-m-d/TH:i:sP')
    $stmt = $pdo->prepare("INSERT INTO notificaciones (tipo_notificacion, descripcion, fecha, hora) VALUES (?, ?, now() , time(now()) )");
    $stmt->execute([$tipo_notificacion, $descripcion]);

    http_response_code(201); // Creado
   
    echo json_encode(['mensaje' => "Notificación Nº ".$pdo->lastInsertId()." creado correctamente."]);
}

function modificarNotificacion() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['tipo_notificacion']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];
    $tipo_notificacion = $data['tipo_notificacion'];
    $descripcion = $data['descripcion'];

    $stmt = $pdo->prepare("UPDATE notificaciones SET tipo_notificacion=?, descripcion=? WHERE id=?");
    $stmt->execute([$tipo_notificacion, $descripcion, $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'notificacion no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Anuncio modificado correctamente']);
}


function borrarNotificacion() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])){
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];

    $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Notificacion no encontrada']);
        return;
    }

    echo json_encode(['mensaje' => 'Notificacion eliminado correctamente']);
}


function listarNotificacion() {
    global $pdo;

    $stmt = null;

    $tipo_notificacion = isset($_GET['tipo_notificacion'])? $_GET['tipo_notificacion'] : null;
    $id = isset($_GET['id'])? (int)$_GET['id'] : null;
    $descripcion = isset($_GET['descripcion'])? $_GET['descripcion'] : null;
    $fecha = isset($_GET['fecha'])? $_GET['fecha'] : null;
    $hora = isset($_GET['hora'])? $_GET['hora'] : null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE id = ?");
        $stmt->execute([$id]);
    } elseif($tipo_notificacion) {
        $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE tipo_notificacion LIKE ?");
        $stmt->execute(["%$tipo_notificacion%"]);
    } elseif($descripcion){
        $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE descripcion LIKE ?");
        $stmt->execute(["%$descripcion%"]);
    } elseif($fecha){
        $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE fecha = ?");
        $stmt->execute([$fecha]);
    } elseif($hora){
        $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE hora=?");
        $stmt->execute([$hora]);
    } else{
        $stmt = $pdo->prepare("SELECT * FROM notificaciones");
        $stmt->execute();
    }

    if ($stmt === null) {
        http_response_code(400); // Solicitud incorrecta
        echo json_encode(['error' => 'Parámetros de solicitud inválidos']);
        return;
    }

    $notificacion = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($notificacion);
}