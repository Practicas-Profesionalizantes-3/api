<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarAviso_tipo();
            break;
        case 'POST':
            crearAviso_tipo();
            break;
        case 'PUT':
            modificarAviso_tipo();
            break;
        case 'DELETE':
            borrarAviso_tipo();
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

function crearAviso_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_tipo ']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_tipo  = $data['id_aviso_tipo '];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO aviso_tipo (id_aviso_tipo , descripcion) VALUES (?, ?)");
    $stmt->execute([$id_aviso_tipo, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Tipo de Aviso Nº " . $pdo->lastInsertId() . " Creado Correctamente!!"]);
}

function modificarAviso_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_tipo ']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_tipo  = $data['id_aviso_tipo '];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE aviso_tipo SET id_aviso_tipo =?, descripcion=? WHERE id_aviso_tipo =?");
    $stmt->execute([$id_aviso_tipo, $descripcion, $id_aviso_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Aviso no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Aviso modificado Con Exito!']);
}


function borrarAviso_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_tipo '])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_tipo  = $data['id_aviso_tipo '];

    $stmt = $pdo->prepare("DELETE FROM aviso_tipo WHERE id_aviso_tipo =?");
    $stmt->execute([$id_aviso_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Aviso no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Aviso eliminado con Exito!']);
}


function listarAviso_tipo()
{
    global $pdo;

    $id_aviso_tipo  = isset($_GET['id_aviso_tipo ']) ? (int)$_GET['id_aviso_tipo '] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT * 
    FROM aviso_tipo 
    WHERE 1=1";

    if ($id_aviso_tipo  != null) {
        $sql .= " AND id_aviso_tipo =$id_aviso_tipo ";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $aviso_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$aviso_tipo) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipo de Aviso']);
        return;
    }

    echo json_encode($aviso_tipo);
}
