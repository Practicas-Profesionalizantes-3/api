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

    if (!isset($data['id_aviso_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_tipo  = $data['id_aviso_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO aviso_tipo (id_aviso_tipo, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_aviso_tipo, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Tipo de Aviso Nº " . $pdo->lastInsertId() . " Creado Correctamente!!"]);
}

function modificarAviso_tipo()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_tipo  = $data['id_aviso_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE aviso_tipo SET descripcion=? WHERE id_aviso_tipo =?");
    $stmt->execute([$descripcion, $id_aviso_tipo]);

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

    if (!isset($data['id_aviso_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso_tipo  = $data['id_aviso_tipo'];

    $stmt = $pdo->prepare("DELETE FROM aviso_tipo WHERE id_aviso_tipo=?");
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

    $sql = "SELECT 
        a.id_aviso_tipo,
        a.descripcion
        FROM 
        aviso_tipo as a
        WHERE 
        1=1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cartelera = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartelera) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron tipos de cartelera']);
        return;
    }

    echo json_encode($cartelera);
}
