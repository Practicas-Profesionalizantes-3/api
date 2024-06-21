<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarUsuario_tipos();
            break;
        case 'POST':
            crearUsuario_tipos();
            break;
        case 'PUT':
            modificarUsuario_tipos();
            break;
        case 'DELETE':
            borrarUsuario_tipos();
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

function crearUsuario_tipos()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario_tipo = $data['id_usuario_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("INSERT INTO usuario_tipos (id_usuario_tipo, descripcion) VALUES (?, ?)");
    $stmt->execute([$id_usuario_tipo, $descripcion]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => "Tipo de Usuario Nº " . $pdo->lastInsertId() . " Creado Correctamente!!"]);
}

function modificarUsuario_tipos()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario_tipo']) || !isset($data['descripcion'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario_tipo = $data['id_usuario_tipo'];
    $descripcion = $data['descripcion'];


    $stmt = $pdo->prepare("UPDATE usuario_tipos SET id_usuario_tipo=?, descripcion=? WHERE id_usuario_tipo=?");
    $stmt->execute([$id_usuario_tipo, $descripcion, $id_usuario_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Usuario modificado Con Exito!']);
}


function borrarUsuario_tipos()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario_tipo = $data['id_usuario_tipo'];

    $stmt = $pdo->prepare("DELETE FROM usuario_tipos WHERE id_usuario_tipo=?");
    $stmt->execute([$id_usuario_tipo]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tipo de Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tipo de Usuario eliminado Con Exito!']);
}


function listarUsuario_tipos()
{
    global $pdo;

    $id_usuario_tipo = isset($_GET['id_usuario_tipo']) ? (int)$_GET['id_usuario_tipo'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM usuario_tipos 
    WHERE 1=1";

    if ($id_usuario_tipo != null) {
        $sql .= " AND id_usuario_tipo=$id_usuario_tipo";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuario_tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuario_tipos) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Usuarios']);
        return;
    }

    echo json_encode($usuario_tipos);
}
