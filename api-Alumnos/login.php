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
