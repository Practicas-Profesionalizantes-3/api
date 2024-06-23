<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarUsuario_estados();
            break;
        case 'POST':
            crearUsuario_estados();
            break;
        case 'PUT':
            modificarUsuario_estados();
            break;
        case 'DELETE':
            borrarUsuario_estados();
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

function crearUsuario_estados()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario_estado']) || !isset($data['permiso_nombre'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario_estado = $data['id_usuario_estado'];
    $permiso_nombre = $data['permiso_nombre'];


    $stmt = $pdo->prepare("INSERT INTO usuario_tipos (id_usuario_estado, permiso_nombre) VALUES (?, ?)");
    $stmt->execute([$id_usuario_estado, $permiso_nombre]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' => " Creado Correctamente!!"]);
}

function modificarUsuario_estados()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario_estado']) || !isset($data['permiso_nombre'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario_estado = $data['id_usuario_estado'];
    $permiso_nombre = $data['permiso_nombre'];


    $stmt = $pdo->prepare("UPDATE usuario_estados SET permiso_nombre=? WHERE id_usuario_estado=?");
    $stmt->execute([$permiso_nombre, $id_usuario_estado]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Estado de Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Estado de Usuario modificado Con Exito!']);
}


function borrarUsuario_estados()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario_estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario_estado = $data['id_usuario_estado'];

    $stmt = $pdo->prepare("DELETE FROM usuario_estados WHERE id_usuario_estado=?");
    $stmt->execute([$id_usuario_estado]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Estado de Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Estado de Usuario eliminado Con Exito!']);
}


function listarUsuario_estados()
{
    global $pdo;

    $id_usuario_estado = isset($_GET['id_usuario_estado']) ? (int)$_GET['id_usuario_estado'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;

    $sql = "SELECT *
    FROM usuario_estados 
    WHERE 1=1";

    if ($id_usuario_estado != null) {
        $sql .= " AND id_usuario_estado=$id_usuario_estado";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(descripcion) like LOWER('%$descripcion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuario_estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuario_estados) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipos de Estados']);
        return;
    }

    echo json_encode($usuario_estados);
}
