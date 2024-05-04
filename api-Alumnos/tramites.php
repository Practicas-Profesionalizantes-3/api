<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
                listarTramites();
            break;
        case 'POST':
            crearTramites();
            break;
        case 'PUT':
                modificarTramites();
            break;
        case 'DELETE':
            borrarTramites();
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

function crearTramites() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['tipo_tramite']) || !isset($data['descripcion']) || !isset($data['estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $tipo_tramite = $data['tipo_tramite'];
    $descripcion = $data['descripcion'];
    $estado = $data['estado'];

    $stmt = $pdo->prepare("INSERT INTO tramites (tipo_tramite, descripcion, estado) VALUES (?, ?, ?)");
    $stmt->execute([$tipo_tramite, $descripcion, $estado]);

    http_response_code(201); // Creado
   
    echo json_encode(['mensaje' => "tramite Nº ".$pdo->lastInsertId()." creado correctamente"]);
}

function modificarTramites() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['tipo_tramite']) || !isset($data['descripcion']) || !isset($data['estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];
    $tipo_tramite = $data['tipo_tramite'];
    $descripcion = $data['descripcion'];
    $estado = $data['estado'];

    $stmt = $pdo->prepare("UPDATE tramites SET tipo_tramite=?, descripcion=?, estado=? WHERE id=?");
    $stmt->execute([$tipo_tramite, $descripcion, $estado, $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tramite modificado correctamente']);
}


function borrarTramites() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])){
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];

    $stmt = $pdo->prepare("DELETE FROM tramites WHERE id=?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tramite eliminado correctamente']);
}


function listarTramites() {
    global $pdo;

    $id = isset($_GET['id'])? (int)$_GET['id'] : null;
    $tipo_tramite = isset($_GET['tipo_tramite'])? $_GET['tipo_tramite'] : null;
    $descripcion = isset($_GET['descripcion'])? $_GET['descripcion'] : null;
    $estado = isset($_GET['estado'])? $_GET['estado'] : null;
    
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM tramites WHERE id =?");
        $stmt->execute([$id]);
    } elseif($tipo_tramite) {
        $stmt = $pdo->prepare("SELECT * FROM tramites WHERE tipo_tramite LIKE?");
        $stmt->execute(["%$tipo_tramite%"]);
    } elseif($descripcion){
        $stmt = $pdo->prepare("SELECT * FROM tramites WHERE descripcion LIKE ?");
        $stmt->execute(["%$descripcion%"]);
    } else{
        $stmt = $pdo->prepare("SELECT * FROM tramites WHERE estado LIKE ?");
        $stmt->execute(["%$estado%"]);
    } 

    $tramite = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tramite) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tramite']);
        return;
    }

    echo json_encode($tramite);
}


