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
            iniciarSesion();
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

function iniciarSesion(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user']) || !isset($data['password'])) {
        echo json_encode("Usuario o contraseña no ingresados");
        return;
    }

    $email = $data['user'];
    $password = $data['password'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND password=?");
    $stmt->execute([$email, $password]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo json_encode(["codigo" => 200, "error" => null, "success" => true]);
    } else {
        echo json_encode(["success" => false, 'error' => "Usuario o contraseña incorrectos", 'codigo' => 401]);
    }
}

function bajaUsuario() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        throw new Exception('ID del usuario es obligatorio');
    }

    $id = $data['id'];

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Usuario no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Usuario eliminado correctamente']);
}


function listarUsuarios() {
    global $pdo;

    $correo=(isset($_POST['correo']))?$_POST['correo']:"";
    $password=(isset($_POST['password']))?$_POST['password']:"";

    $stmt = $pdo->query("SELECT * FROM login");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rol=$usuarios['rol'];

    if($usuarios['correo']==$correo && $usuarios['password']==$password){
        switch ($rol){
            case 'admin':
                echo 'admin logeado';
                break;
            case 'alumno':
                echo 'alumno logueado';
                break;
            case 'dptoAlumnos':
                echo 'Departamento de alumos logueado';
                break;
        }

    echo json_encode($usuarios);
}
}



