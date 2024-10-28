<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            obtenerComentariosTramites();
            break;
        case 'POST':
            crearComentariosTramites();
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

//Mofica los comentarios de los tramites
function crearComentariosTramites()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    if (!isset($data['id_tramite']) || !isset($data['comentario'])) {
        throw new Exception('Faltan datos obligatorios');
    }

    $id_tramite = $data['id_tramite'];
    $comentario = $data['comentario'];
    $fecha_comentario = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO tramite_comentarios (id_tramite, comentario, fecha_comentario) VALUES (?, ?, ?)");
    $stmt->execute([$id_tramite, $comentario, $fecha_comentario]);

    http_response_code(201); // Creado

    echo json_encode(['mensaje' =>  " Creado Correctamente!!"]);
}

function obtenerComentariosTramites()
{
    global $pdo;

    // Obtener y validar el id_tramite
    $id_tramite = isset($_GET['id_tramite']) ? (int)$_GET['id_tramite'] : null;
    
    // Verificar que el id_tramite sea un número válido
    if ($id_tramite === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Se debe proporcionar un id_tramite']);
        return;
    }

    // Inicializar la consulta básica
    $sql = "SELECT comentario, fecha_comentario FROM tramite_comentarios WHERE id_tramite = ?";
    $params = [$id_tramite]; // Array para los parámetros de ejecución

    // Condiciones dinámicas
    if (isset($_GET['comentario']) && $_GET['comentario'] !== '') {
        $sql .= " AND LOWER(comentario) LIKE LOWER(?)";
        $params[] = "%" . strtolower($_GET['comentario']) . "%"; // Añadir el comentario a los parámetros
    }
    if (isset($_GET['fecha_comentario']) && $_GET['fecha_comentario'] !== '') {
        $sql .= " AND LOWER(fecha_comentario) LIKE LOWER(?)";
        $params[] = "%" . strtolower($_GET['fecha_comentario']) . "%"; // Añadir la fecha a los parámetros
    }

    // Agregar el ordenamiento por fecha_comentario
    $sql .= " ORDER BY fecha_comentario DESC";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tramite_comentario = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tramite_comentario) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron comentarios']);
        return;
    }

    echo json_encode($tramite_comentario);
}
