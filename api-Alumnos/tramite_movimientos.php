<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarTramite_movimientos();
            break;
        case 'POST':
            crearTramite_movimientos();
            break;
        case 'DELETE':
            borrarTramite_movimientos();
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

function crearTramite_movimientos()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !isset($data['id_tramite']) || !isset($data['fecha_movimiento']) || !isset($data['id_usuario']) || !isset($data['observacion'])
        || !isset($data['id_estado_tramite'])
    ) {
        throw new Exception('Todos los campos son obligatorios');
    }

    // Asegúrate de que estás recibiendo el ID del trámite
    $id_tramite = $data['id_tramite'] ?? null;
    $fecha_movimiento = $data['fecha_movimiento'];
    $id_usuario = $data['id_usuario'] ?? null;
    $observacion = $data['observacion'] ?? '';
    $id_estado_tramite = $data['id_estado_tramite'] ?? null;

    // Verificar que todos los campos necesarios estén presentes
    if (!$id_tramite || !$id_usuario || !$id_estado_tramite) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos los campos son obligatorios']);
        return;
    }

    // Establecer la zona horaria a Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Fecha actual
    $fecha_movimiento = date('Y-m-d H:i:s'); // Formato compatible con MySQL

    // Crear un nuevo registro sin verificar si ya existe
    $stmt = $pdo->prepare("INSERT INTO tramite_movimientos (id_tramite, fecha_movimiento, id_usuario, 
    observacion, id_estado_tramite) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_tramite, $fecha_movimiento, $id_usuario, $observacion, $id_estado_tramite]);

    http_response_code(201); // Creado
    echo json_encode(['mensaje' => "Movimiento creado correctamente."]);
}




function borrarTramite_movimientos()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_tramite'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_tramite = $data['id_tramite'];

    $stmt = $pdo->prepare("DELETE FROM tramite_movimientos WHERE id_tramite=?");
    $stmt->execute([$id_tramite]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Movimiento no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Movimiento eliminado Con Exito!']);
}


function listarTramite_movimientos()
{
    global $pdo;

    $id_tramite = isset($_GET['id_tramite']) ? (int)$_GET['id_tramite'] : null;
    $fecha_movimiento = isset($_GET['fecha_movimiento']) ? $_GET['fecha_movimiento'] : null;
    $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;
    $observacion = isset($_GET['observacion']) ? $_GET['observacion'] : null;
    $id_estado_tramite = isset($_GET['id_estado_tramite']) ? $_GET['id_estado_tramite'] : null;

    $sql = "SELECT 
  tm.id_tramite, 
  tm.fecha_movimiento,
  u.nombre AS id_usuario, 
  tm.observacion,
  te.descripcion AS id_estado_tramite
FROM 
  tramite_movimientos tm 
  INNER JOIN usuarios u ON tm.id_usuario = u.id_usuario 
  INNER JOIN tramite_estados te ON tm.id_estado_tramite = te.id_estado_tramite
WHERE  1=1
  AND tm.id_tramite = tm.id_tramite  
  AND u.id_usuario = tm.id_usuario 
  AND te.id_estado_tramite = tm.id_estado_tramite";

    if ($id_tramite != null) {
        $sql .= " AND tm.id_tramite=$id_tramite";
    }
    if ($fecha_movimiento != null) {
        $sql .= " AND tm.fecha_movimiento=$fecha_movimiento";
    }
    if ($id_usuario != null) {
        $sql .= " AND tm.id_usuario=$id_usuario";
    }
    if ($observacion != null) {
        $sql .= " AND LOWER(observacion) like LOWER('%$observacion%')";
    }
    if ($id_estado_tramite != null) {
        $sql .= " AND te.id_estado_tramite=$id_estado_tramite";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tramite_movimiento = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tramite_movimiento) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($tramite_movimiento);
}
