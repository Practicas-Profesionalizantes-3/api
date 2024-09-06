<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET');

header("Access-Control-Allow-Headers: *");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarCarreras();
            break;
        case 'POST':
            crearCarreras();
            break;
        case 'PUT':
            modificarCarreras();
            break;
        case 'DELETE':
            borrarCarreras();
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

function crearCarreras()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_carrera']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_carrera = $data['id_carrera'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO carreras (id_carrera, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_carrera, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Creado Correctamente!!"]);
}

function modificarCarreras()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_carrera']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_carrera = $data['id_carrera'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE carreras SET descripcion=? WHERE id_carrera=?");
    $stmt->execute([$descripcion, $id_carrera]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Carrera no encontrada']);
        return;
    }

    echo json_encode(['mensaje' => 'Carrera modificada Con Exito!']);
}


function borrarCarreras()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_carrera'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_carrera = $data['id_carrera'];

    $stmt = $pdo->prepare("DELETE FROM carreras WHERE id_carrera=?");
    $stmt->execute([$id_carrera]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Carrera no encontrada']);
        return;
    }

    echo json_encode(['mensaje' => 'Carrera eliminada con Exito!']);
}


function listarCarreras()
{
    global $pdo;

    $id_carrera = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM carreras 
    WHERE 1=1";

    if ($id_carrera != null) {
        $sql .= " AND id_carrera =$id_carrera";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$carreras) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Carreras']);
        return;
    }

    echo json_encode($carreras);
}
