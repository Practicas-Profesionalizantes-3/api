<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarAviso_estado();
            break;
        case 'POST':
            crearAviso_estado();
            break;
        case 'PUT':
            modificarAviso_estado();
            break;
        case 'DELETE':
            borrarAviso_estado();
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

function crearAviso_estado()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_estado']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_estado = $data['id_aviso_estado'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO aviso_estado (id_aviso_estado, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_aviso_estado, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Estado de Aviso Nº " . $pdo->lastInsertId() . " Creado Correctamente!!"]);
}

function modificarAviso_estado()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_estado']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_estado = $data['id_aviso_estado'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE aviso_estado SET descripcion=? WHERE id_aviso_estado=?");
    $stmt->execute([$descripcion, $id_aviso_estado]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Aviso de Estado no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Aviso de Estado modificado Con Exito!']);
}


function borrarAviso_estado()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_estado = $data['id_aviso_estado'];

    $stmt = $pdo->prepare("DELETE FROM aviso_estado WHERE id_aviso_estado=?");
    $stmt->execute([$id_aviso_estado]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Aviso de Estado no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Aviso de Estado eliminado Con Exito!']);
}


function listarAviso_estado()
{
    global $pdo;

    $id_aviso_estado = isset($_GET['id_aviso_estado']) ? (int)$_GET['id_aviso_estado'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM aviso_estado 
    WHERE 1=1";

    if ($id_aviso_estado != null) {
        $sql .= " AND id_aviso_estado=$id_aviso_estado";
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
