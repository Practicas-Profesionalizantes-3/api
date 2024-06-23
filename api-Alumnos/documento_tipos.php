<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarDocumento_tipo();
            break;
        case 'POST':
            crearDocumento_tipo();
            break;
        case 'PUT':
            modificarDocumento_tipo();
            break;
        case 'DELETE':
            borrarDocumento_tipo();
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

function crearDocumento_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_documento_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_documento_tipo = $data['id_documento_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO documento_tipos (id_documento_tipo, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_documento_tipo, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => " Creado Correctamente!!"]);
}

function modificarDocumento_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_documento_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_documento_tipo = $data['id_documento_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE documento_tipos SET descripcion=? WHERE id_documento_tipo=?");
    $stmt->execute([$descripcion, $id_documento_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Documento no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Documento modificado Con Exito!']);
}


function borrarDocumento_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_documento_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_documento_tipo = $data['id_documento_tipo'];

    $stmt = $pdo->prepare("DELETE FROM documento_tipos WHERE id_documento_tipo=?");
    $stmt->execute([$id_documento_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Documento no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Documento eliminado con Exito!']);
}


function listarDocumento_tipo()
{
    global $pdo;

    $id_documento_tipo = isset($_GET['id_documento_tipo']) ? (int)$_GET['id_documento_tipo'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT * 
    FROM documento_tipos 
    WHERE 1=1";

    if ($id_documento_tipo != null) {
        $sql .= " AND id_documento_tipo=$id_documento_tipo ";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $documento_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$documento_tipo) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipo de Aviso']);
        return;
    }

    echo json_encode($documento_tipo);
}
