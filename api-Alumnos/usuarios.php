<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarUsuarios();
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

    if (!isset($data['nombre']) || !isset($data['apellido']) || !isset($data['password']) || !isset($data['email']) || !isset($data['dni']) || !isset($data['carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['estado']) || !isset($data['rol'])) {
        throw new Exception('Todos los campos son obligatorios');
    }
    

    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $password = $data['password'];
    $email = $data['email'];
    $dni = $data['dni'];
    $carrera = $data['carrera'];
    $anio = $data['anio'];
    $comision = $data['comision'];
    $estado = $data['estado'];
    $rol = $data['rol'];
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email like ?");
    $stmt->execute([$email]);
    $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($respuesta){
        echo json_encode(['mensaje' => 'Correo existente']);
    }else{
        if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, dni, carrera, anio, comision, estado, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $apellido, $email, $password, $dni, $carrera, $anio, $comision, $estado, $rol ]);

         http_response_code(201); // Creado
         echo json_encode(['mensaje' => 'Usuario creado correctamente']);
        } else {
            echo json_encode(['mensaje' => 'Password Invalido']);
        }  
    }
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



function listarUsuarios() {
    global $pdo;

    $apellido = isset($_GET['apellido'])? $_GET['apellido'] : null;
    $id = isset($_GET['id'])? (int)$_GET['id'] : null;
    $dni = isset($_GET['dni'])? (int)$_GET['dni'] : null;
    $email = isset($_GET['email'])? $_GET['email'] : null;
    $carrera = isset($_GET['carrera'])? $_GET['carrera'] : null;
    $anio = isset($_GET['anio'])? (int)$_GET['anio'] : null;
    $comision = isset($_GET['comision'])? $_GET['comision'] : null;
    $estado = isset($_GET['estado'])? $_GET['estado'] : null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id =?");
        $stmt->execute([$id]);
    } elseif($apellido) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE apellido LIKE?");
        $stmt->execute(["%$apellido%"]);
    } elseif($dni){
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni=?");
        $stmt->execute([$dni]);
    } elseif($email){
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email LIKE ?");
        $stmt->execute(["%$email%"]);
    } elseif($carrera){
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE carrera LIKE ?");
        $stmt->execute(["%$carrera%"]);
    } elseif($anio){
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE anio=?");
        $stmt->execute([$anio]);
    } elseif($comision){
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE comision LIKE ?");
        $stmt->execute(["%$comision%"]);
    } else{
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE estado LIKE ?");
        $stmt->execute(["%$estado%"]);
    }

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}


