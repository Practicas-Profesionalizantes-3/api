<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                obtenerUsuario();
            } elseif (isset($_GET['nombre'])) {
                filtrarUsuariosPorNombre();
            }elseif (isset($_GET['apellido'])) {
                filtrarUsuariosPorApellido();
            }elseif (isset($_GET['dni'])) {
                filtrarUsuariosPorDni();
            }elseif (isset($_GET['email'])) {
                filtrarUsuariosPorEmail();
            }elseif (isset($_GET['carrera'])) {
                filtrarUsuariosPorCarrera();
            }elseif (isset($_GET['año'])) {
                filtrarUsuariosPorAnio();
            }elseif (isset($_GET['comision'])) {
                filtrarUsuariosPorComision();
            }elseif(isset($_GET['estado'])) {
                filtrarUsuariosPorEstado();
            } else {
                listarUsuarios();
            }
            break;
        case 'POST':
            altaUsuario();
            break;
        case 'PUT':
                modificarUsuario();
            break;
        case 'DELETE':
            bajaUsuario();
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

function altaUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['nombre']) || !isset($data['apellido']) || !isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $email, $password]);

    http_response_code(201); // Creado
    echo json_encode(['mensaje' => 'Usuario creado correctamente']);
}

function modificarUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['nombre']) || !isset($data['apellido']) || !isset($data['email']) || !isset($data['dni']) ||  !isset($data['carrera']) || !isset($data['anio']) || !isset($data['comision'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $email = $data['email'];
    $dni = $data['dni'];
    $carrera = $data['carrera'];
    $year = $data['anio'];
    $comision = $data['comision'];

    $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, dni=?, carrera=?, anio=?, comision=? WHERE id=?");
    $stmt->execute([$nombre, $apellido, $email, $dni, $carrera, $year, $comision, $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Usuario modificado correctamente']);
}


function bajaUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])){
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];
    $estado = $data['estado'];

    $stmt = $pdo->prepare("UPDATE usuarios SET estado=? WHERE id=?");
    $stmt->execute([$estado, $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Usuario modificado correctamente']);
}

function obtenerUsuario() {
    global $pdo;

    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }

    echo json_encode($usuario);
}

function listarUsuarios() {
    global $pdo;

    $stmt = $pdo->query("SELECT * FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);
}

function filtrarUsuariosPorNombre() {
    global $pdo;

    $nombre = $_GET['nombre'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre LIKE ?");
    $stmt->execute(["%$nombre%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorApellido() {
    global $pdo;

    $apellido = $_GET['apellido'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE apellido LIKE ?");
    $stmt->execute(["%$apellido%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorDni() {
    global $pdo;

    $dni = $_GET['dni'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni LIKE ?");
    $stmt->execute(["%$dni%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorEmail() {
    global $pdo;

    $email = $_GET['email'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email LIKE ?");
    $stmt->execute(["%$email%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}
function filtrarUsuariosPorCarrera() {
    global $pdo;

    $carrera = $_GET['carrera'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE carrera LIKE ?");
    $stmt->execute(["%$carrera%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorAnio() {
    global $pdo;

    $anio = $_GET['anio'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE anio LIKE ?");
    $stmt->execute(["%$anio%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorComision() {
    global $pdo;

    $comision = $_GET['comision'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE comision LIKE ?");
    $stmt->execute(["%$comision%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorEstado() {
    global $pdo;

    $estado = $_GET['estado'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE estado = ?");
    $stmt->execute([$estado]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}