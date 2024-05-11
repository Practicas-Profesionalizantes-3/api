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

    if (!isset($data['nombre']) || !isset($data['apellido']) || !isset($data['password']) || !isset($data['email']) || !isset($data['dni']) || !isset($data['carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['estado']) || !isset($data['firma_digital']) || !isset($data['id_tipo_usuario'])) {
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
    $firma_digital = $data['firma_digital'];
    $id_tipo_usuario = $data['id_tipo_usuario'];

    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email like ?");
    $stmt->execute([$email]);
    $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($respuesta){
        echo json_encode(['mensaje' => 'Correo existente']);
    }else{
        if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, dni, carrera, anio, comision, estado, firma_digital) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $apellido, $email, $password, $dni, $carrera, $anio, $comision, $estado, $firma_digital ]);

            $id_user = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO usuario_roles (id_usuario, id_usuario_tipo) VALUES (?, ?)");
            $stmt->execute([$id_user, $id_tipo_usuario]);

            http_response_code(201); // Creado
            echo json_encode(['mensaje' => 'Usuario creado correctamente!']);
        } else {
            echo json_encode(['mensaje' => 'El password debe tenes una letra mayuscula y al meos un numero!']);
        }  
    }
}

function modificarUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario']) || !isset($data['nombre']) || !isset($data['apellido']) || !isset($data['email']) || !isset($data['dni']) ||  !isset($data['carrera']) || !isset($data['anio']) || !isset($data['comision'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id_usuario'];
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $email = $data['email'];
    $dni = $data['dni'];
    $carrera = $data['carrera'];
    $anio = $data['anio'];
    $comision = $data['comision'];
    $usuario_tipo = $data['id_usuario_tipo'];

    $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, carrera=?, anio=?, comision=? WHERE id_usuario=?");
    $stmt->execute([$nombre, $apellido, $dni, $email, $carrera, $anio, $comision, $id]);

    $stmt = $pdo->prepare("UPDATE usuario_roles SET id_usuario_tipo = ? WHERE id_usuario=?");
    $stmt->execute([$usuario_tipo, $id]);


    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Usuario modificado correctamente!']);
}
 
     
function bajaUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario'])){
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario = $data['id_usuario'];
       

    $stmt = $pdo->prepare("UPDATE usuarios SET estado='baja' WHERE id_usuario=?");
    $stmt->execute([$id_usuario]);

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
    $id_usuario = isset($_GET['id_usuario'])? (int)$_GET['id_usuario'] : null;
    $dni = isset($_GET['dni'])? (int)$_GET['dni'] : null;
    $email = isset($_GET['email'])? $_GET['email'] : null;
    $carrera = isset($_GET['carrera'])? $_GET['carrera'] : null;
    $anio = isset($_GET['anio'])? (int)$_GET['anio'] : null;
    $comision = isset($_GET['comision'])? $_GET['comision'] : null;
    $estado = isset($_GET['estado'])? $_GET['estado'] : null;

    if ($id_usuario) {
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE u.id_usuario = ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute([$id_usuario]);
    } elseif($apellido) {
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE apellido LIKE ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute(["%$apellido%"]);
    } elseif($dni){
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE dni = ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute([$dni]);
    } elseif($email){
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE email LIKE ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute(["%$email%"]);
    } elseif($carrera){
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE carrera LIKE ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute(["%$carrera%"]);
    } elseif($anio){
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE anio = ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute([$anio]);
    } elseif($comision){
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE comision LIKE ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute(["%$comision%"]);
    } elseif($estado){
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE estado LIKE ? and u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo");
        $stmt->execute(["%$estado%"]);
    }else{
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.dni, u.carrera, u.anio, u.comision, u.estado, ut.permiso_nombre AS tipo_usuario FROM usuarios AS u, usuario_tipos AS ut, usuario_roles AS ur WHERE u.id_usuario = ur.id_usuario and ur.id_usuario_tipo = ut.id_usuario_tipo;");
        $stmt->execute();
    }

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}


