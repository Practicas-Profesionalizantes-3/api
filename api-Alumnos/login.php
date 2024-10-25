<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT');

header("Access-Control-Allow-Headers: X-Requested-With");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            iniciarSesion();
            break;
        case 'PUT':
            modificarPassword();
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
function iniciarSesion()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
 
    if (!isset($data['user']) || !isset($data['password'])) {
        echo json_encode(["error" => "Usuario o contraseña no ingresados"]);
        echo json_encode(["error" => "Usuario o contraseña no ingresados"]);
        return;
    }
 
    $email = $data['user'];
    $password = $data['password'];
 
    // Verificar la contraseña
    $stmt = $pdo->prepare("SELECT password, id_usuario_estado FROM usuarios WHERE email=?");
    $stmt = $pdo->prepare("SELECT password, id_usuario_estado FROM usuarios WHERE email=?");
    $stmt->execute([$email]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$userData) {
        echo json_encode(["success" => false, "error" => "Usuario o contraseña incorrectos", "codigo" => 401]);
        return;
    }
    $hashed_password = $userData['password'];
    $usuarioEstado = $userData['id_usuario_estado']; // Estado del usuario
    // Verificar si el usuario está activo
    if ($usuarioEstado != 1) { // Asumiendo que 1 significa activo
        echo json_encode(["success" => false, "error" => "El usuario está inactivo", "codigo" => 403]);
        return;
    }
 
    if (password_verify($password, $hashed_password)) {
        // Obtener los datos del usuario
        $stmt = $pdo->prepare("SELECT u.*, c.id_carrera, c.descripcion AS carrera FROM usuarios AS u INNER JOIN usuario_carreras AS uc ON u.id_usuario = uc.id_usuario INNER JOIN carreras AS c ON uc.id_carrera = c.id_carrera WHERE u.email=?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Obtener el tipo de rol del usuario desde usuario_roles y usuario_tipos
            $stmtRol = $pdo->prepare("
                SELECT ut.id_usuario_tipo
                FROM usuario_roles ur
                JOIN usuario_tipos ut ON ur.id_usuario_tipo = ut.id_usuario_tipo
                WHERE ur.id_usuario = ?
            ");
            $stmtRol->execute([$usuario['id_usuario']]);
            $rol = $stmtRol->fetchColumn();

            if ($rol) {
                // Agregar el rol a la respuesta
                $usuario['id_usuario_tipo'] = $rol;
                session_start();
                echo json_encode([
                    "codigo" => 200,
                    "error" => "No hay error",
                    "success" => true,
                    "data" => json_encode($usuario)
                ]);
            } else {
                echo json_encode(["success" => false, 'error' => "No se encontró el rol del usuario", 'codigo' => 403]);
            }
        } else {
            echo json_encode(["success" => false, 'error' => "Usuario o contraseña incorrectos", 'codigo' => 401]);
        }
    } else {
        echo json_encode(["success" => false, 'error' => "Usuario o contraseña incorrectos", 'codigo' => 402]);
    }
}

function modificarPassword()
{
    global $pdo;
    require "MailSender/SendMail.php";

    $data = json_decode(file_get_contents('php://input'), true);

    $id_usuario = $data['id_usuario'];
    $password = $data['password'];
    $current_password = $data['current_password']?? null;

    // Obtener la contraseña actual almacenada en la base de datos
    $stmt = $pdo->prepare("SELECT u.password, u.email FROM usuarios AS u WHERE id_usuario=?");
    $stmt->execute([$id_usuario]); // Falta ejecutar la consulta
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    // Verificar si se obtuvo un resultado
    if (!$result) {
        http_response_code(404); // No encontrado
        echo json_encode(["codigo" => 404, "error" => "Usuario no encontrado", "success" => false, "data" => null]);
        return;
    }

    $hashed_password = $result->password;
    $email = $result->email;

    // Verificar que la contraseña actual sea correcta
    if($current_password != null){
        if (!password_verify($current_password, $hashed_password)) {
            echo json_encode(["codigo" => 401, "error" => "Contraseña actual incorrecta", "success" => false, "data" => null]);
            return;
        }
    }

    // Verificar que la nueva contraseña cumpla con el patrón
    if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE usuarios SET password=? WHERE id_usuario=?");
        $stmt->execute([$password, $id_usuario]);
    }
    else{
        echo json_encode(["codigo" => 400, "error" => "La nueva contraseña no cumple con los requisitos", "success" => false, "data" => null]);
        return;
    }

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(["codigo" => 404, "error" => "No se pudo actualizar la contraseña", "success" => false, "data" => null]);
        return;
    }

    if (SendMail($email, "Cambio de contraseña", "Usuario, le notificamos que su contraseña ha sido modificada.", true)) {
        http_response_code(200);
        echo json_encode(["codigo" => 200, "error" => null, "success" => true, "mensaje" => "Contraseña modificada", "data" => null]);
    } else {
        http_response_code(200);
        echo json_encode(["codigo" => 200, "error" => "No se pudo enviar el mail", "success" => false, "data" => null]);
    }
}
