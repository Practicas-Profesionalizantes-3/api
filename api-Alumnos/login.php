<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            iniciarSesion();
            break;
        case 'PUT':
            modificarUsuario();
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
    // echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => $data['user'], "data" => $data['password']]);
    // return;
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
        echo json_encode(["success" => false, 'error' => "Usuario o contraseña incorrectos", 'codigo' => 401]);
    }
}

function modificarUsuario()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    $id_usuario = $data['id_usuario'];
    $password = $data['password'];
    $current_password = $data['current_password'];

     // Obtener la contraseña actual almacenada en la base de datos
     $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario=?");
     $stmt->execute([$id_usuario]);
     $hashed_password = $stmt->fetchColumn();
 
     // Verificar que la contraseña actual sea correcta
     if (!password_verify($current_password, $hashed_password)) {
         http_response_code(401); // No autorizado
         echo json_encode(['error' => 'Contraseña actual incorrecta']);
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
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Usuario modificado Con Exito!']);
}