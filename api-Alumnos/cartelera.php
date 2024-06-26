<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

header("Access-Control-Allow-Headers: X-Requested-With");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            switch ($action) {
                case 'aviso_tipos':
                    listarAvisoTipos();
                    break;
                    // Agrega más casos para otras acciones GET
                default:
                    listarAvisos();
                    break;
            }
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

    // Verifica si se ha enviado un archivo
    if ($_FILES['imagen']['error'] != UPLOAD_ERR_OK) {
        echo json_encode(["mensaje" => "Error en la subida de la imagen"]);
        return;
    }
    if ($_FILES['adjunto']['error'] != UPLOAD_ERR_OK) {
        echo json_encode(["mensaje" => "Error en la subida del archivo"]);
        return;
    }

    // Lee el contenido del archivo
    if (isset($_FILES['imagen'])) {
        $file_content = file_get_contents($_FILES['imagen']['tmp_name']);
    }
    if (isset($_FILES['adjunto'])) {
        $file_content_adjunto = file_get_contents($_FILES['adjunto']['tmp_name']);
    }

    // Resto de los datos recibidos
    $id_aviso_tipo = $_POST['id_aviso_tipo'];
    $id_usuario = $_POST['id_usuario'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $fijado = $_POST['fijado'];
    $id_aviso_estado = $_POST['id_aviso_estado'];

    try {
        $stmt = $pdo->prepare("INSERT INTO `avisos` (id_aviso_tipo, id_usuario, titulo, descripcion, fecha_publicacion, fecha_vencimiento, adjunto, fijado, id_aviso_estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_aviso_tipo, $id_usuario, $titulo, $descripcion, $fecha_publicacion,
            $fecha_vencimiento, isset($_FILES['adjunto']) ? $file_content_adjunto : "", $fijado, $id_aviso_estado, isset($_FILES['imagen']) ? $file_content : ""
        ]);

        http_response_code(201); // Creado

        echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "mensaje" => "Aviso Nº " . $pdo->lastInsertId() . " creado correctamente!"]);
    } catch (Exception $e) {
        echo json_encode(["codigo" => 500, "error" => "No se pudo guardar en la base", "success" => false, "mensaje" => null]);
    }
}

function modificarAviso()
{
    global $pdo;
    $data = array();
    $data = json_decode(file_get_contents('php://input'), true);
    $adjuntoBlob = null;
    $imagenBlob = null;
    if (isset($data['adjunto']) && isset($data['imagen'])) {
        $adjuntoBase64 = $data['adjunto'];
        $imagenBase64 = $data['imagen'];
        
        // Convertir base64 a binario (blob)
        $adjuntoBlob = base64_decode($adjuntoBase64);
        $imagenBlob = base64_decode($imagenBase64);
    }


    // Leer los datos del cuerpo de la solicitud

    // Asegurar que el id_aviso esté presente
    if (!isset($data['id_aviso'])) {
        echo json_encode(["mensaje" => "ID de aviso no proporcionado"]);
        return;
    }

    // Asignar variables con verificación de existencia
    $id_aviso = $data['id_aviso'];
    $id_aviso_tipo = isset($data['id_aviso_tipo']) ? $data['id_aviso_tipo'] : null;
    $id_usuario = isset($data['id_usuario']) ? $data['id_usuario'] : null;
    $titulo = isset($data['titulo']) ? $data['titulo'] : null;
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : null;
    $fecha_publicacion = isset($data['fecha_publicacion']) ? $data['fecha_publicacion'] : null;
    $fecha_vencimiento = isset($data['fecha_vencimiento']) ? $data['fecha_vencimiento'] : null;
    $fijado = isset($data['fijado']) ? $data['fijado'] : null;
    $estado = isset($data['id_aviso_estado']) ? $data['id_aviso_estado'] : null;

    $stmt = $pdo->prepare("UPDATE avisos SET id_aviso_tipo=?, id_usuario=?, titulo=?, descripcion=?, fecha_publicacion=?, fecha_vencimiento=?, adjunto=?, fijado=?, imagen=?, id_aviso_estado=? WHERE id_aviso=?");
    $stmt->execute([$id_aviso_tipo, $id_usuario, $titulo, $descripcion, $fecha_publicacion, $fecha_vencimiento, $adjuntoBlob, $fijado, $imagenBlob, $estado, $id_aviso]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Aviso no encontrado']);
        return;
    }

    echo json_encode(["codigo" => 200, "error" => "No hay error", "success" => true, "mensaje" => "Aviso modificado correctamente!"]);
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
        INNER JOIN aviso_tipo AS at ON a.id_aviso_tipo = at.id_aviso_tipo 
        INNER JOIN usuarios AS u ON a.id_usuario = u.id_usuario 
        INNER JOIN aviso_estado AS e ON e.id_aviso_estado = a.id_aviso_estado
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

    echo json_encode($anuncios);
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
