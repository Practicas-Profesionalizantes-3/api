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

    $data = json_decode(file_get_contents('php://input'), true);
    if (
        !isset($data['id_aviso_tipo']) || !isset($data['id_usuario']) || !isset($data['titulo'])
        || !isset($data['descripcion']) || !isset($data['fecha_publicacion']) || !isset($data['fecha_vencimiento'])
        || !isset($data['adjunto']) || !isset($data['fijado']) || !isset($data['id_aviso_estado']) || !isset($data['ubicacion_imagen'])
    ) {
        echo json_encode(["mensaje" => "Hay un campo vacio"]);
        return;
    }

    $id_aviso_tipo = $data['id_aviso_tipo'];
    $id_usuario = $data['id_usuario'];
    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $fecha_publicacion = $data['fecha_publicacion'];
    $fecha_vencimiento = $data['fecha_vencimiento'];
    $adjunto = $data['adjunto'];
    $fijado = $data['fijado'];
    $id_aviso_estado = $data['id_aviso_estado'];

    try {

        $stmt = $pdo->prepare("INSERT INTO `avisos` (id_aviso_tipo, id_usuario, titulo, descripcion, 
    fecha_publicacion, fecha_vencimiento, adjunto, fijado, id_aviso_estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_aviso_tipo, $id_usuario, $titulo, $descripcion, $fecha_publicacion,
            $fecha_vencimiento, $adjunto, $fijado, $id_aviso_estado
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

    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !isset($data['id_aviso']) || !isset($data['id_aviso_tipo']) || !isset($data['id_usuario']) || !isset($data['titulo'])
        || !isset($data['descripcion']) || !isset($data['fecha_publicacion']) || !isset($data['fecha_vencimiento'])
        || !isset($data['adjunto']) || !isset($data['fijado']) || !isset($data['id_aviso_estado']) || !isset($data['ubicacion_imagen'])
    ) {
        throw new Exception('Todos los campos son obligatorios');
    }


    $id_aviso = $data['id_aviso'];
    $id_aviso_tipo = $data['id_aviso_tipo'];
    $id_usuario = $data['id_usuario'];
    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $fecha_publicacion = $data['fecha_publicacion'];
    $fecha_vencimiento = $data['fecha_vencimiento'];
    $adjunto = $data['adjunto'];
    $fijado = $data['fijado'];
    $estado = $data['id_aviso_estado'];

    $stmt = $pdo->prepare("UPDATE avisos SET id_aviso_tipo=?, id_usuario=?, titulo=?, descripcion=?, fecha_publicacion=?, fecha_vencimiento=?, adjunto=?, fijado=?, id_aviso_estado=? WHERE id_aviso=?");
    $stmt->execute([$id_aviso_tipo, $id_usuario, $titulo, $descripcion, $fecha_publicacion, $fecha_vencimiento, $adjunto, $fijado, $estado, $id_aviso]);

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
    $adjunto = isset($_GET['adjunto']) ? $_GET['adjunto'] : null;
    $fijado = isset($_GET['fijado']) ? $_GET['fijado'] : null;

    $sql = "SELECT 
        a.id_aviso, 
        a.titulo, 
        a.descripcion, 
        a.fecha_publicacion, 
        a.fecha_vencimiento, 
        a.adjunto, 
        a.fijado, 
        a.ubicacion_imagen,
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
    if ($adjunto != null) {
        $sql .= " AND LOWER(a.adjunto) like LOWER('%$adjunto%')";
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
