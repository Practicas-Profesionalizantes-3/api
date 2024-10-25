<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

header("Access-Control-Allow-Headers: *");

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


function altaUsuario()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    // Validación de campos obligatorios
    if (
        !isset($data['nombre']) || !isset($data['apellido']) || !isset($data['password']) ||
        !isset($data['email']) || !isset($data['id_documento_tipo']) || !isset($data['id_usuario_estado']) || !isset($data['numero_documento']) ||
        !isset($data['id_carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['id_usuario_tipo'])
    ) {
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

    // Validación de correo existente
    $stmt = $pdo->prepare("SELECT 1 FROM usuarios WHERE email LIKE ?");
    $stmt->execute([$email]);
    $respuesta_email = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($respuesta_email) {
        http_response_code(409);
        echo json_encode(['mensaje' => 'Correo existente']);
        return; // Salir de la función si el correo ya existe
    }

    // Validación de número de documento existente
    $stmt = $pdo->prepare("SELECT 1 FROM usuarios WHERE numero_documento = ?");
    $stmt->execute([$numero_documento]);
    $respuesta_documento = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($respuesta_documento) {
        http_response_code(409);
        echo json_encode(['mensaje' => 'Número de documento ya existente']);
        return; // Salir de la función si el número de documento ya existe
    }

    // Validación de la contraseña
    if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insertar el usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, password, email, id_documento_tipo, id_usuario_estado, numero_documento) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellido, $password, $email, $id_documento_tipo, $id_usuario_estado, $numero_documento]);

        $id_usuario = $pdo->lastInsertId();

        // Insertar roles de usuario
        $stmt = $pdo->prepare("INSERT INTO usuario_roles (id_usuario, id_usuario_tipo) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $id_usuario_tipo]);

        // Insertar carrera de usuario
        $stmt = $pdo->prepare("INSERT INTO usuario_carreras (id_usuario, id_carrera, anio, comision) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $id_carrera, $anio, $comision]);

        http_response_code(201); // Creado
        echo json_encode(["codigo" => 201, "error" => "No hay error", "success" => true, "mensaje" => "Usuario creado con éxito!"]);
    } else {
        http_response_code(406);
        echo json_encode(['mensaje' => 'El password debe tener una letra mayúscula y al menos un número!']);
    }
}


function modificarUsuario()
{
  global $pdo;

  $data = json_decode(file_get_contents('php://input'), true);

  if (
    !isset($data['id_usuario']) || !isset($data['nombre']) || !isset($data['apellido']) ||
    !isset($data['email']) || !isset($data['id_documento_tipo']) || !isset($data['id_usuario_estado']) || !isset($data['numero_documento'])
    || !isset($data['id_carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['id_usuario_tipo'])
  ) {
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

  $stmt1 = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, id_documento_tipo=?, id_usuario_estado=?, numero_documento=? WHERE id_usuario=?");
  $stmt1->execute([$nombre, $apellido, $email, $id_documento_tipo, $id_usuario_estado, $numero_documento, $id_usuario]);

  $stmt2 = $pdo->prepare("DELETE FROM usuario_roles WHERE id_usuario=?");
  $stmt2->execute([$id_usuario]);

  $stmt3 = $pdo->prepare("INSERT INTO usuario_roles (id_usuario, id_usuario_tipo) VALUES (?, ?)");
  $stmt3->execute([$id_usuario, $id_usuario_tipo]);

  $stmt4 = $pdo->prepare("UPDATE usuario_carreras SET id_carrera=?, anio=?, comision=? WHERE id_usuario=?");
  $stmt4->execute([$id_carrera, $anio, $comision, $id_usuario]);

  if ($stmt1->rowCount() === 0 && $stmt2->rowCount() === 0 && $stmt3->rowCount() === 0 && $stmt4->rowCount() === 0) {
    http_response_code(404); // No encontrado
    echo json_encode(["codigo" => 404, "error" => "Usuario no encontrado", "success" => false, "mensaje" => "No se pudo modificar el usuario"]);
    return;
  }

  echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "mensaje" => "Usuario modificado con exito!"]);
}


function bajaUsuario()
{
  global $pdo;

  $data = json_decode(file_get_contents('php://input'), true);

  if (!isset($data['id_usuario'])) {
    throw new Exception('Todos los campos son obligatorios');
  }

  $id_usuario = $data['id_usuario'];

  $stmt = $pdo->prepare("UPDATE usuarios SET usuarios.id_usuario_estado=2 WHERE usuarios.id_usuario=?");
  $stmt->execute([$id_usuario]);

  if ($stmt->rowCount() === 0) {
    http_response_code(404); // No encontrado
    echo json_encode(['error' => 'Usuario no encontrado, ID ' . $data["id_usuario"]]);
    return;
  }

  echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "mensaje" => "Aviso eliminado de manera logica!"]);
}

function listarUsuarios()
{
    global $pdo;

    $id_usuario = isset($_GET['id_usuario']) ? (int) $_GET['id_usuario'] : null;
    $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
    $apellido = isset($_GET['apellido']) ? $_GET['apellido'] : null;
    $email = isset($_GET['email']) ? $_GET['email'] : null;
    $id_documento_tipo = isset($_GET['id_documento_tipo']) ? $_GET['id_documento_tipo'] : null;
    $id_usuario_estado = isset($_GET['id_usuario_estado']) ? (int) $_GET['id_usuario_estado'] : null;
    $numero_documento = isset($_GET['numero_documento']) ? $_GET['numero_documento'] : null;
    $permiso_nombre = isset($_GET['permiso_nombre']) ? $_GET['permiso_nombre'] : null;

    $sql = "SELECT
        u.id_usuario,
        u.nombre,
        u.apellido,
        u.email,
        dt.descripcion AS documento_tipo,
        ue.descripcion AS usuario_estado,
        u.numero_documento,
        ut.descripcion AS usuario_tipo,
        ut.id_usuario_tipo As id_usuario_tipo,
        c.id_carrera AS id_carrera,
        c.descripcion AS carrera,
        uc.anio AS anio,
        uc.comision AS comision
      FROM
        usuarios AS u
      INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo
      INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
      LEFT JOIN usuario_roles AS ur ON u.id_usuario = ur.id_usuario
      LEFT JOIN usuario_tipos AS ut ON ur.id_usuario_tipo = ut.id_usuario_tipo
      LEFT JOIN usuario_carreras AS uc ON u.id_usuario = uc.id_usuario
      LEFT JOIN carreras AS c ON c.id_carrera = uc.id_carrera
      WHERE 1=1";

    $params = [];

    if ($id_usuario != null) {
        $sql .= " AND u.id_usuario = :id_usuario";
        $params[':id_usuario'] = $id_usuario;
    }
    if ($nombre != null) {
        $sql .= " AND LOWER(u.nombre) LIKE LOWER(:nombre)";
        $params[':nombre'] = "%$nombre%";
    }
    if ($apellido != null) {
        $sql .= " AND LOWER(u.apellido) LIKE LOWER(:apellido)";
        $params[':apellido'] = "%$apellido%";
    }
    if ($email != null) {
        $sql .= " AND LOWER(u.email) LIKE LOWER(:email)";
        $params[':email'] = "%$email%";
    }
    if ($id_documento_tipo != null) {
        $sql .= " AND LOWER(dt.descripcion) LIKE LOWER(:id_documento_tipo)";
        $params[':id_documento_tipo'] = "%$id_documento_tipo%";
    }
    if ($id_usuario_estado != null) {
        $sql .= " AND u.id_usuario_estado = :id_usuario_estado";
        $params[':id_usuario_estado'] = $id_usuario_estado;
    }
    if ($numero_documento != null) {
        $sql .= " AND u.numero_documento = :numero_documento";
        $params[':numero_documento'] = $numero_documento;
    }
    if ($permiso_nombre != null) {
        $sql .= " AND LOWER(ut.permiso_nombre) LIKE LOWER(:permiso_nombre)";
        $params[':permiso_nombre'] = "%$permiso_nombre%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404); // No encontrado
        echo json_encode(["codigo" => 404, "error" => "No se encontraron usuarios", "success" => false, "mensaje" => "Error", "data" => null]);
        return;
    }
 
    echo json_encode(["codigo" => 200, "error" => null, "success" => true, "mensaje" => "Usuario/s obtenido/s con exito", "data" => $usuario]);
}