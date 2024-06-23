<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarTramites_tipo();
            break;
        case 'POST':
            crearTramites_tipo();
            break;
        case 'PUT':
            modificarTramites_tipo();
            break;
        case 'DELETE':
            borrarTramites_tipo();
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

function crearTramites_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_tramite_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_tramite_tipo = $data['id_tramite_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO tramites_tipo (id_tramite_tipo, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_tramite_tipo, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => " Creado Correctamente!!"]);
}

function modificarTramites_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_tramite_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_tramite_tipo = $data['id_tramite_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE tramites_tipo SET id_tramite_tipo=?, descripcion=? WHERE id_tramite_tipo=?");
    $stmt->execute([$id_tramite_tipo, $descripcion, $id_tramite_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Tramite modificado Con Exito!']);
}


function borrarTramites_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_tramite_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_tramite_tipo = $data['id_tramite_tipo'];

    $stmt = $pdo->prepare("DELETE FROM tramites_tipo WHERE id_tramite_tipo=?");
    $stmt->execute([$id_tramite_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Tramite eliminado Con Exito!']);
}


function listarTramites_tipo()
{
    global $pdo;

    $id_tramite_tipo = isset($_GET['id_tramite_tipo']) ? (int)$_GET['id_tramite_tipo'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM tramites_tipo 
    WHERE 1=1";

    if ($id_tramite_tipo != null) {
        $sql .= " AND id_tramite_tipo=$id_tramite_tipo";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tramites_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tramites_tipo) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($tramites_tipo);
}
