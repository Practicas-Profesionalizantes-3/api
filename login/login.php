<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarUsuarios();
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



