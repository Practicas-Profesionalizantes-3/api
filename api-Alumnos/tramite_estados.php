<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarTramite_estados();
            break;
        case 'POST':
            crearTramites_estados();
            break;
        case 'PUT':
            modificarTramite_estados();
            break;
        case 'DELETE':
            borrarTramite_estados();
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

function crearTramites_estados()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_estado_tramite']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_estado_tramite = $data['id_estado_tramite'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO tramite_estados (id_estado_tramite, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_estado_tramite, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' =>  " Creado Correctamente!!"]);
}

function modificarTramite_estados()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_estado_tramite']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_estado_tramite = $data['id_estado_tramite'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE tramite_estados SET id_estado_tramite=?, descripcion=? WHERE id_estado_tramite=?");
    $stmt->execute([$id_estado_tramite, $descripcion, $id_estado_tramite]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Estado de Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Estado de Tramite modificado Con Exito!']);
}


function borrarTramite_estados()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_estado_tramite'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_estado_tramite = $data['id_estado_tramite'];

    $stmt = $pdo->prepare("DELETE FROM tramite_estados WHERE id_estado_tramite=?");
    $stmt->execute([$id_estado_tramite]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Estado de Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Estado de Tramite eliminado Con Exito!']);
}


function listarTramite_estados()
{
    global $pdo;

    $id_estado_tramite = isset($_GET['id_estado_tramite']) ? (int)$_GET['id_estado_tramite'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM tramite_estados 
    WHERE 1=1";

    if ($id_estado_tramite != null) {
        $sql .= " AND id_estado_tramite=$id_estado_tramite";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tramite_estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tramite_estados) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($tramite_estados);
}
