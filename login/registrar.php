<?php
require 'config.php';

header('Content-Type: application/json'); 

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            crearUsuario();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

function crearUsuario(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['nombre']) || !isset($data['apellido']) || !isset($data['dni']) || !isset($data['email']) || !isset($data['password'])) {
        echo json_encode("Todos los campos son obligatorios");
        return;
    }

    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $dni = $data['dni'];
    $email = $data['email'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni=? OR email=?");
    $stmt->execute([$dni, $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo json_encode(["success" => false, 'error' => "El usuario con DNI ".$usuario['dni']." o Correo Electrónico ".$usuario['email']." ya existe", 'codigo' => 401]);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $dni, $email, $password]);
    
    http_response_code(201);
    echo json_encode(["codigo" => 200, "error" => null, "success" => true]);
}

?>