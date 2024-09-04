<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

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

  if (
    !isset($data['nombre']) || !isset($data['apellido']) || !isset($data['password']) ||
    !isset($data['email']) || !isset($data['id_documento_tipo']) || !isset($data['id_usuario_estado']) || 
    !isset($data['numero_documento']) || !isset($data['carreras']) || !isset($data['id_usuario_tipo'])
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
  $carreras = $data['carreras']; // Debe ser un array con los datos de cada carrera
  $id_usuario_tipo = $data['id_usuario_tipo'];



  $stmt = $pdo->prepare("SELECT 1 FROM usuarios WHERE email like ?");
  $stmt->execute([$email]);
  $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($respuesta) {
    http_response_code(409);
    echo json_encode(['mensaje' => 'Correo existente']);
  } else {
    if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
      // Se guarda la contraseña hash en $password
      $password = password_hash($data['password'], PASSWORD_DEFAULT);

      // Verificar si $id_usuario_tipo es un array y tiene elementos
      if (is_array($id_usuario_tipo) && !empty($id_usuario_tipo)) {
        // Verificar si $carreras es un array y tiene elementos
        if (is_array($carreras) && !empty($carreras)){
          $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, password, email, id_documento_tipo, id_usuario_estado, numero_documento) VALUES (?, ?, ?, ?, ?, ?, ?)");
          $stmt->execute([$nombre, $apellido, $password, $email, $id_documento_tipo, $id_usuario_estado, $numero_documento]);
  
          $id_usuario = $pdo->lastInsertId();
  
          //Inserta el nuevo rol o roles del usuario
          $stmt = $pdo->prepare("INSERT INTO usuario_roles (id_usuario, id_usuario_tipo) VALUES (?, ?)");
          foreach ($id_usuario_tipo as $tipo) {
              // Verificar si $tipo es un valor válido
              if (!empty($tipo)) {
                  $stmt->execute([$id_usuario, $tipo]);
              }
          }
          // Inserta las carreras del usuario
          $stmt = $pdo->prepare("INSERT INTO usuario_carreras (id_usuario, id_carrera, anio, comision) VALUES (?, ?, ?, ?)");
          foreach ($carreras as $carrera) {
              if (isset($carrera['id_carrera']) && isset($carrera['anio']) && isset($carrera['comision'])) {
                  $stmt->execute([$id_usuario, $carrera['id_carrera'], $carrera['anio'], $carrera['comision']]);
              }
          }
          
          http_response_code(201); // Creado
          echo json_encode(['mensaje' => 'Usuario creado correctamente!']);
        } else{
          http_response_code(400); // Código de estado HTTP para una solicitud incorrecta
          echo json_encode(['error' => 'El valor en carreras tiene que estar entre corchetes []']);
        }
      }else{
        http_response_code(400); // Código de estado HTTP para una solicitud incorrecta
        echo json_encode(['error' => 'El valor en id_usuario_tipo tiene que estar entre corchetes []']);
      }
    } else {
      http_response_code(406);
      echo json_encode(['mensaje' => 'El password debe tenes una letra mayuscula y al menos un numero!']);
    }
  }
}

function modificarUsuario()
{
  global $pdo;

  $data = json_decode(file_get_contents('php://input'), true);

  if (
      !isset($data['id_usuario']) || !isset($data['nombre']) || !isset($data['apellido']) ||
      !isset($data['password']) || !isset($data['email']) || !isset($data['id_documento_tipo']) || 
      !isset($data['id_usuario_estado'])|| !isset($data['numero_documento'])
      || !isset($data['carreras']) || !isset($data['id_usuario_tipo'])
  ) {
    throw new Exception('Todos los campos son obligatorios');
  }
  
  $id_usuario = $data['id_usuario'];
  $nombre = $data['nombre'];
  $apellido = $data['apellido'];
  $password = $data['password'];
  $email = $data['email'];
  $id_documento_tipo = $data['id_documento_tipo'];
  $id_usuario_estado = $data['id_usuario_estado'];
  $numero_documento = $data['numero_documento'];
  $carreras = $data['carreras'];
  $id_usuario_tipo = $data['id_usuario_tipo'];

  if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
    // Se guarda la contraseña hash en $password
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Verificar si $id_usuario_tipo es un array y tiene elementos
    if (is_array($id_usuario_tipo) && !empty($id_usuario_tipo)) {
      if (is_array($carreras) && !empty($carreras)){
        //Modifica los campos de la tabla usuarios
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, apellido=?, email=?, password=?, id_documento_tipo=?, id_usuario_estado=?, numero_documento=? WHERE id_usuario=?");
        $stmt->execute([$nombre, $apellido, $email, $password, $id_documento_tipo, $id_usuario_estado, $numero_documento, $id_usuario]);
        
        //Elimina los usuarios_roles que tenia antes
        $stmt = $pdo->prepare("DELETE FROM usuario_roles WHERE id_usuario=?");
        $stmt->execute([$id_usuario]);

        //Inserta el nuevo rol o roles del usuario
        $stmt = $pdo->prepare("INSERT INTO usuario_roles (id_usuario, id_usuario_tipo) VALUES (?, ?)");
        foreach ($id_usuario_tipo as $tipo) {
            // Verificar si $tipo es un valor válido
            if (!empty($tipo)) {
                $stmt->execute([$id_usuario, $tipo]);
            }
        }

        //Elimina los usuario_carreras que tenia antes
        $stmt = $pdo->prepare("DELETE FROM usuario_carreras WHERE id_usuario=?");
        $stmt->execute([$id_usuario]);

        // Inserta las carreras del usuario
        $stmt = $pdo->prepare("INSERT INTO usuario_carreras (id_usuario, id_carrera, anio, comision) VALUES (?, ?, ?, ?)");
        foreach ($carreras as $carrera) {
            if (isset($carrera['id_carrera']) && isset($carrera['anio']) && isset($carrera['comision'])) {
                $stmt->execute([$id_usuario, $carrera['id_carrera'], $carrera['anio'], $carrera['comision']]);
            }
        }

        echo json_encode(['mensaje' => 'El usuario fue modificado exitosamente!']);
      } else{
        http_response_code(400); // Código de estado HTTP para una solicitud incorrecta
        echo json_encode(['error' => 'El valor en carreras tiene que estar entre corchetes []']);
      }
    } else{
      http_response_code(406);
      echo json_encode(['error' => 'El valor en id_usuario_tipo tiene que estar entre corchetes []']);
    }
  }else {
    http_response_code(406);
    echo json_encode(['error' => 'El password debe tenes una letra mayuscula y al menos un numero!']);
  }

}


function bajaUsuario()
{
  global $pdo;

  $data = json_decode(file_get_contents('php://input'), true);

  if (!isset($data['numero_documento'])) {
    throw new Exception('Todos los campos son obligatorios');
  }

  $id_usuario = isset($_GET['id_usuario']) ? (int) $_GET['id_usuario'] : null;

  $stmt = $pdo->prepare("UPDATE usuarios SET id_usuario_estado = '2' WHERE id_usuario=?");
  $stmt->execute([$id_usuario]);

  if ($stmt->rowCount() === 0) {
    http_response_code(404); // No encontrado
    echo json_encode(['error' => 'Usuario no encontrado']);
    return;
  }

  echo json_encode(['mensaje' => 'Usuario eliminado correctamente']);
}

function listarUsuarios()
{
  global $pdo;

  $id_usuario = isset($_GET['id_usuario']) ? (int) $_GET['id_usuario'] : null;
  $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
  $apellido = isset($_GET['apellido']) ? $_GET['apellido'] : null;
  $email = isset($_GET['email']) ? $_GET['email'] : null;
  $password = isset($_GET['password']) ? $_GET['password'] : null;
  $id_documento_tipo = isset($_GET['id_documento_tipo']) ? $_GET['id_documento_tipo'] : null;
  $id_usuario_estado = isset($_GET['id_usuario_estado']) ? (int) $_GET['id_usuario_estado'] : null;
  $numero_documento = isset($_GET['numero_documento']) ? $_GET['numero_documento'] : null;
 

  $sql = "SELECT
        u.id_usuario,
        u.nombre,
        u.apellido,
        u.email,
        u.password,
        dt.descripcion AS documento_tipo,
        ue.descripcion AS usuario_estado,
        u.numero_documento,
        c.descripcion AS carrera,
        uc.anio,
        uc.comision,
        ut.descripcion AS rol_usuario
      FROM
        usuarios AS u
        INNER JOIN documento_tipos AS dt ON u.id_documento_tipo = dt.id_documento_tipo
        INNER JOIN usuario_estados AS ue ON u.id_usuario_estado = ue.id_usuario_estado
        INNER JOIN usuario_carreras AS uc ON u.id_usuario = uc.id_usuario 
        INNER JOIN carreras AS c ON uc.id_carrera = c.id_carrera  
        INNER JOIN usuario_roles AS ur ON u.id_usuario = ur.id_usuario
        INNER JOIN usuario_tipos AS ut ON ur.id_usuario_tipo = ut.id_usuario_tipo

      WHERE
        1=1";
  if ($id_usuario != null) {
    $sql .= " AND u.id_usuario=$id_usuario";
  }
  if ($nombre != null) {
    $sql .= " AND LOWER(u.nombre) like LOWER('%$nombre%')";
  }
  if ($apellido != null) {
    $sql .= " AND LOWER(u.apellido) like LOWER('%$apellido%')";
  }
  if ($email != null) {
    $sql .= " AND LOWER(u.email) like LOWER('%$email%')";
  }
  if ($id_documento_tipo != null) {
    $sql .= " AND LOWER(dt.descripcion) like LOWER('%$id_documento_tipo%')";
  }
  if ($id_usuario_estado != null) {
    $sql .= " AND LOWER(u.id_usuario_estado) like LOWER('%$id_usuario_estado%')";
  }
  if ($numero_documento != null) {
    $sql .= " AND u.numero_documento=$numero_documento";
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!$usuario) {
    http_response_code(404); // No encontrado
    echo json_encode(['error' => 'No se encontraron usuarios']);
    return;
  }

  echo json_encode($usuario);
}
