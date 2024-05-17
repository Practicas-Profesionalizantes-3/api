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

    if (!isset($data['nombre']) || !isset($data['apellido']) || !isset($data['password']) || 
    !isset($data['email']) || !isset($data['id_documento_tipo']) || !isset($data['id_usuario_estado']) || !isset($data['numero_documento']) 
    || !isset($data['id_carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['id_usuario_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }
    
  
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $password = $data['password'];
    $email = $data['email'];
    $id_documento_tipo = $data['id_documento_tipo'];
    $id_usuario_estado = $data['id_usuario_estado'];
    $numero_documento = $data['numero_documento'];
    $id_carrera = $data['id_carrera'];
    $anio = $data['anio'];
    $comision = $data['comision'];
    $id_usuario_tipo = $data['id_usuario_tipo'];
    

    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email like ?");
    $stmt->execute([$email]);
    $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($respuesta){
        echo json_encode(['mensaje' => 'Correo existente']);
    }else{
        if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, password, email, id_documento_tipo, id_usuario_estado, numero_documento) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $apellido, $password, $email, $id_documento_tipo, $id_usuario_estado, $numero_documento]);

            $id_usuario = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO usuario_roles (id_usuario, id_usuario_tipo) VALUES (?, ?)");
            $stmt->execute([$id_usuario, $id_usuario_tipo]);
            
            $stmt = $pdo->prepare("INSERT INTO usuario_carreras (id_usuario, id_carrera, anio, comision) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_usuario, $id_carrera, $anio, $comision]);

            http_response_code(201); // Creado
            echo json_encode(['mensaje' => 'Usuario creado correctamente!']);
        } else {
            echo json_encode(['mensaje' => 'El password debe tenes una letra mayuscula y al menos un numero!']);
        }  
    }
}

function modificarUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_usuario']) || !isset($data['nombre']) || !isset($data['apellido']) ||  
    !isset($data['email']) || !isset($data['id_documento_tipo']) || !isset($data['id_usuario_estado']) || !isset($data['numero_documento']) 
    || !isset($data['id_carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['id_usuario_tipo'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_usuario = $data['id_usuario'];
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $email = $data['email'];
    $id_documento_tipo = $data['id_documento_tipo'];
    $id_usuario_estado = $data['id_usuario_estado'];
    $numero_documento = $data['numero_documento'];
    $id_carrera = $data['id_carrera'];
    $anio = $data['anio'];
    $comision = $data['comision'];
    $id_usuario_tipo = $data['id_usuario_tipo'];


    $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, id_documento_tipo=?, id_usuario_estado=?, numero_documento=? WHERE id_usuario=?");
            $stmt->execute([$nombre, $apellido, $email, $id_documento_tipo, $id_usuario_estado, $numero_documento, $id_usuario]);

    $stmt = $pdo->prepare("UPDATE usuario_roles SET id_usuario=?, id_usuario_tipo=? WHERE id_usuario=?");
            $stmt->execute([$id_usuario, $id_usuario_tipo, $id_usuario]);
            
    $stmt = $pdo->prepare("UPDATE usuario_carreras SET id_usuario=?, id_carrera=?, anio=?, comision=? WHERE id_usuario=?");
            $stmt->execute([$id_usuario, $id_carrera, $anio, $comision, $id_usuario]);


    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Usuario modificado correctamente!']);
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

    $id_usuario = isset($_GET['id_usuario'])? (int)$_GET['id_usuario'] : null;

    $stmt = $pdo->prepare("UPDATE usuarios SET id_usuario_estado = '3' WHERE id_usuario=?");
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

    $id_usuario = isset($_GET['id_usuario'])? (int)$_GET['id_usuario'] : null;
    $nombre = isset($_GET['nombre'])? $_GET['nombre'] : null;
    $apellido = isset($_GET['apellido'])? $_GET['apellido'] : null;
    $email = isset($_GET['email'])? $_GET['email'] : null;
    $id_documento_tipo = isset($_GET['id_documento_tipo'])? $_GET['id_documento_tipo'] : null;
    $id_usuario_estado = isset($_GET['id_usuario_estado'])? (int)$_GET['id_usuario_estado'] : null;
    $numero_documento = isset($_GET['numero_documento'])? $_GET['numero_documento'] : null;
    

    if ($id_usuario) {
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        u.nombre, 
        u.apellido, 
        u.email, 
        dt.descripcion AS documento_tipo, 
        ue.descripcion AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
        u.id_usuario = ?");
        $stmt->execute([$id_usuario]);
    } elseif($nombre) {
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        LOWER(u.nombre) AS nombre, 
        LOWER(u.apellido) AS apellido, 
        LOWER(u.email) AS email, 
        LOWER(dt.descripcion) AS documento_tipo, 
        LOWER(ue.descripcion) AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
        u.nombre like LOWER(?)");
        $stmt->execute(["%$nombre%"]);
    } elseif($apellido){
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        LOWER(u.nombre) AS nombre, 
        LOWER(u.apellido) AS apellido, 
        LOWER(u.email) AS email, 
        LOWER(dt.descripcion) AS documento_tipo, 
        LOWER(ue.descripcion) AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
        u.apellido like LOWER(?)");
        $stmt->execute([$apellido]);
    } elseif($email){
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        LOWER(u.nombre) AS nombre, 
        LOWER(u.apellido) AS apellido, 
        LOWER(u.email) AS email, 
        LOWER(dt.descripcion) AS documento_tipo, 
        LOWER(ue.descripcion) AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
        u.email like LOWER(?)");
        $stmt->execute(["%$email%"]);
    } elseif($id_documento_tipo){
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        LOWER(u.nombre) AS nombre, 
        LOWER(u.apellido) AS apellido, 
        LOWER(u.email) AS email, 
        LOWER(dt.descripcion) AS documento_tipo, 
        LOWER(ue.descripcion) AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
        dt.descripcion like LOWER(?)");
        $stmt->execute([$id_documento_tipo]);
    } elseif($id_usuario_estado){
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        LOWER(u.nombre) AS nombre, 
        LOWER(u.apellido) AS apellido, 
        LOWER(u.email) AS email, 
        LOWER(dt.descripcion) AS documento_tipo, 
        LOWER(ue.descripcion) AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
        u.id_usuario_estado like ?");
        $stmt->execute([$id_usuario_estado]);
    } elseif($numero_documento){
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        LOWER(u.nombre) AS nombre, 
        LOWER(u.apellido) AS apellido, 
        LOWER(u.email) AS email, 
        LOWER(dt.descripcion) AS documento_tipo, 
        LOWER(ue.descripcion) AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      WHERE 
      u.numero_documento like ?");
        $stmt->execute(["%$numero_documento%"]);
    }else{
        $stmt = $pdo->prepare("SELECT 
        u.id_usuario, 
        u.nombre, 
        u.apellido, 
        u.email, 
        dt.descripcion AS documento_tipo, 
        ue.descripcion AS usuario_estado, 
        u.numero_documento 
      FROM 
        usuarios AS u 
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo 
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      ");
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


