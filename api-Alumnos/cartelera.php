<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

header("Access-Control-Allow-Headers: X-Requested-With");

date_default_timezone_set('America/Argentina/Buenos_Aires');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarAvisos();
            break;
        case 'POST':
            crearAviso();
            break;
        case 'PUT':
            modificarAviso();
            break;
        case 'DELETE':
            borrarAviso();
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

function crearAviso()
{
    global $pdo;

    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Manejar archivos
    $file_content = isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK
                    ? file_get_contents($_FILES['imagen']['tmp_name'])
                    : null;

    $file_content_adjunto = isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] == UPLOAD_ERR_OK
                            ? file_get_contents($_FILES['adjunto']['tmp_name'])
                            : null;

    // Resto de los datos
    $id_aviso_tipo = $_POST['id_aviso_tipo'];
    $id_usuario = $_POST['id_usuario'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $fijado = $_POST['fijado'];
    $id_aviso_estado = $_POST['id_aviso_estado'];
    $fecha_publicacion = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO `avisos` (id_aviso_tipo, id_usuario, titulo, descripcion, fecha_publicacion, fecha_vencimiento, adjunto, fijado, id_aviso_estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_aviso_tipo, $id_usuario, $titulo, $descripcion, $fecha_publicacion,
            $fecha_vencimiento, $file_content_adjunto, $fijado, $id_aviso_estado, $file_content
        ]);

        $lastInsertId = $pdo->lastInsertId();

        http_response_code(201); // Creado
        echo json_encode([
            "codigo" => 200,
            "error" => "No hay error",
            "success" => true,
            "mensaje" => "Aviso Nº " . $lastInsertId . " creado correctamente!",
            "id_aviso" => $lastInsertId
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "codigo" => 500,
            "error" => "No se pudo guardar en la base",
            "success" => false,
            "mensaje" => null
        ]);
    }
}



function modificarAviso()
{
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    // Asegurar que el id_aviso esté presente
    if (!isset($data['id_aviso'])) {
        echo json_encode(["mensaje" => "ID de aviso no proporcionado"]);
        return;
    }

    $id_aviso = $data['id_aviso'];

    try {
        // Verificar si el aviso existe
        $stmt = $pdo->prepare("SELECT id_aviso FROM avisos WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404); // No encontrado
            echo json_encode(['error' => 'Aviso no encontrado']);
            return;
        }

        // Convertir base64 a binario (blob) si están presentes
        $adjuntoBlob = isset($data['adjunto']) ? base64_decode($data['adjunto']) : null;
        $imagenBlob = isset($data['imagen']) ? base64_decode($data['imagen']) : null;

        // Asignar variables con verificación de existencia
        $id_aviso_tipo = $data['id_aviso_tipo'] ?? null;
        $id_usuario = $data['id_usuario'] ?? null;
        $titulo = $data['titulo'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $fecha_vencimiento = $data['fecha_vencimiento'] ?? null;
        $fijado = $data['fijado'] ?? null;
        $estado = $data['id_aviso_estado'] ?? null;

        // Preparar y ejecutar la consulta de actualización
        $stmt = $pdo->prepare("UPDATE avisos SET id_aviso_tipo=?, id_usuario=?, titulo=?, descripcion=?, fecha_vencimiento=?, adjunto=?, fijado=?, imagen=?, id_aviso_estado=? WHERE id_aviso=?");
        $stmt->execute([$id_aviso_tipo, $id_usuario, $titulo, $descripcion, $fecha_vencimiento, $adjuntoBlob, $fijado, $imagenBlob, $estado, $id_aviso]);

        echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "mensaje" => "Aviso modificado correctamente!"]);
    } catch (Exception $e) {
        http_response_code(500); // Error interno del servidor
        echo json_encode(["codigo" => 500, "error" => "Error en la actualización", "success" => false, "mensaje" => $e->getMessage()]);
    }
}


function borrarAviso()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_aviso'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_aviso = $data['id_aviso'];

    $stmt = $pdo->prepare("UPDATE avisos SET id_aviso_estado='2' WHERE id_aviso=?");
    $stmt->execute([$id_aviso]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Aviso no encontrado']);
        return;
    }

    echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "mensaje" => "Aviso eliminado de manera logica!"]);
}


function listarAvisos()
{
    global $pdo;

    $id_aviso = isset($_GET['id_aviso']) ? (int)$_GET['id_aviso'] : null;
    $id_aviso_tipo = isset($_GET['id_aviso_tipo']) ? (int)$_GET['id_aviso_tipo'] : null;
    $titulo = isset($_GET['titulo']) ? $_GET['titulo'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;
    $fecha_publicacion = isset($_GET['fecha_publicacion']) ? $_GET['fecha_publicacion'] : null;
    $fecha_vencimiento = isset($_GET['fecha_vencimiento']) ? $_GET['fecha_vencimiento'] : null;
    $fijado = isset($_GET['fijado']) ? $_GET['fijado'] : null;

    $sql = "SELECT 
        a.id_aviso, 
        a.titulo, 
        a.descripcion, 
        a.fecha_publicacion, 
        a.fecha_vencimiento, 
        a.adjunto, 
        a.fijado, 
        a.imagen,
        e.descripcion as estado,
        at.descripcion AS aviso_tipo, 
        u.nombre AS usuario,
        u.id_usuario AS id_usuario
        FROM 
        avisos AS a 
        LEFT JOIN aviso_tipo AS at ON a.id_aviso_tipo = at.id_aviso_tipo 
        LEFT JOIN usuarios AS u ON a.id_usuario = u.id_usuario 
        LEFT JOIN aviso_estado AS e ON e.id_aviso_estado = a.id_aviso_estado
        WHERE 
        1=1";

    if ($id_aviso != null) {
        $sql .= " AND a.id_aviso = $id_aviso";
    }
    if ($id_aviso_tipo != null) {
        $sql .= " AND a.id_aviso_tipo=$id_aviso_tipo";
    }
    if ($titulo != null) {
        $sql .= " AND LOWER(a.titulo) like LOWER('%$titulo%')";
    }
    if ($descripcion != null) {
        $sql .= " AND LOWER(a.descripcion) like LOWER('%$descripcion%')";
    }
    if ($fijado == 'si') {
        $sql .= " AND LOWER(a.fijado) like LOWER('%$fijado%')";
    }
    if ($fijado == 'no') {
        $sql .= " AND LOWER(a.fijado) like LOWER('%$fijado%')";
    }
    if ($fecha_publicacion != null) {
        $sql .= " AND a.fecha_publicacion BETWEEN '$fecha_publicacion' AND '$fecha_vencimiento'";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$anuncios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron anuncios']);
        return;
    }

    // Convierte el contenido BLOB en base64 para incluir en la respuesta JSON
    foreach ($anuncios as &$anuncio) {
        if (!is_null($anuncio['imagen'])) {
            $anuncio['imagen'] = base64_encode($anuncio['imagen']);
        }
        if (!is_null($anuncio['adjunto'])) {
            $anuncio['adjunto'] = base64_encode($anuncio['adjunto']);
        }
    }

    echo json_encode(["codigo" => 200, "error" => null, "success" => true, "mensaje" => "Anuncios obtenidos con exito!", "data" => $anuncios]);
}


function listarAvisoTipos()
{
    global $pdo;

    $sql = "SELECT 
        a.id_aviso_tipo,
        a.descripcion
        FROM 
        aviso_tipo as a
        WHERE 
        1=1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cartelera = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartelera) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron tipos de cartelera']);
        return;
    }

    echo json_encode($cartelera);
}
