<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: POST, PUT');

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
        echo json_encode("Usuario o contraseña no ingresados");
        return;
    }

    $email = $data['user'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE email=?");
    $stmt->execute([$email]);
    $hashed_password = $stmt->fetchColumn();

    if (password_verify($password, $hashed_password)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email=?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            session_start();
            echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "data" => json_encode($usuario)]);
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
    require "MailSender/SendMailPassword.php";

    $data = json_decode(file_get_contents('php://input'), true);

    $id_usuario = $data['id_usuario'];
    $password = $data['password'];
    $current_password = $data['current_password'];

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
    if (!password_verify($current_password, $hashed_password)) {
        echo json_encode(["codigo" => 401, "error" => "Contraseña actual incorrecta", "success" => false, "data" => null]);
        return;
    }

    // Verificar que la nueva contraseña cumpla con el patrón
    if (preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE usuarios SET password=? WHERE id_usuario=?");
        $stmt->execute([$password, $id_usuario]);
    }

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(["codigo" => 404, "error" => "No se pudo actualizar la contraseña", "success" => false, "data" => null]);
        return;
    }

    if (SendMail($email, "Cambio de contraseña", "Usuario, le notificamos que su contraseña ha sido modificada.")) {
        echo json_encode(["codigo" => 200, "error" => null, "success" => true, "data" => null]);
    } else {
        echo json_encode(["codigo" => 200, "error" => "No se pudo enviar el mail", "success" => false, "data" => null]);
    }
}
