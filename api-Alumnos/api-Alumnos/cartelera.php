<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
                listarAnuncios();
            break;
        case 'POST':
            crearAnuncio();
            break;
        case 'PUT':
                modificarAnuncio();
            break;
        case 'DELETE':
            borrarAnuncio();
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

function crearAnuncio() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['titulo']) || !isset($data['descripcion']) || !isset($data['imagen']) || !isset($data['fechaDesde']) || 
    !isset($data['fechaHasta']) || !isset($data['carrera']) || !isset($data['anio']) || !isset($data['comision']) || !isset($data['estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $imagen = $data['imagen'];
    $fechaDesde = $data['fechaDesde'];
    $fechaHasta = $data['fechaHasta'];
    $carrera = $data['carrera'];
    $anio = $data['anio'];
    $comision = $data['comision'];
    $estado = $data['estado'];

    $stmt = $pdo->prepare("INSERT INTO anuncios (titulo, descripcion, imagen, fechaDesde, fechaHasta, carrera, anio, comision, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $imagen, $fechaDesde, $fechaHasta, $carrera, $anio, $comision, $estado]);

    http_response_code(201); // Creado
   
    echo json_encode(['mensaje' => "Anuncio Nº ".$pdo->lastInsertId()." creado correctamente!"]);
}

function modificarAnuncio() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['titulo']) || !isset($data['descripcion']) || !isset($data['imagen']) || 
    !isset($data['fechaDesde']) ||  !isset($data['fechaHasta']) || !isset($data['carrera']) || !isset($data['anio']) 
    || !isset($data['comision']) || !isset($data['estado'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];
    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $imagen = $data['imagen'];
    $fechaDesde = $data['fechaDesde'];
    $fechaHasta = $data['fechaHasta'];
    $carrera = $data['carrera'];
    $anio = $data['anio'];
    $comision = $data['comision'];
    $estado = $data['estado'];

    $stmt = $pdo->prepare("UPDATE anuncios SET titulo=?, descripcion=?, imagen=?, fechaDesde=?, fechaHasta=?, carrera=?, anio=?, comision=?, estado=? WHERE id=?");
    $stmt->execute([$titulo, $descripcion, $imagen, $fechaDesde, $fechaHasta, $carrera, $anio, $comision, $estado, $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Anuncio no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Anuncio modificado correctamente!']);
}


function borrarAnuncio() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])){
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];

    $stmt = $pdo->prepare("DELETE FROM anuncios WHERE id=?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Anuncio no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Anuncio eliminado correctamente!']);
}


function listarAnuncios() {
    global $pdo;

    $titulo = isset($_GET['titulo'])? $_GET['titulo'] : null;
    $id = isset($_GET['id'])? (int)$_GET['id'] : null;
    $descripcion = isset($_GET['descripcion'])? $_GET['descripcion'] : null;
    $carrera = isset($_GET['carrera'])? $_GET['carrera'] : null;
    $anio = isset($_GET['anio'])? (int)$_GET['anio'] : null;
    $comision = isset($_GET['comision'])? $_GET['comision'] : null;
    $estado = isset($_GET['estado'])? $_GET['estado'] : null;
    $fechaDesde = isset($_GET['fechaDesde'])? $_GET['fechaDesde'] : null;
    $fechaHasta = isset($_GET['fechaHasta'])? $_GET['fechaHasta'] : null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE id =?");
        $stmt->execute([$id]);
    } elseif($titulo) {
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE titulo LIKE?");
        $stmt->execute(["%$titulo%"]);
    } elseif($descripcion){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE descripcion LIKE ?");
        $stmt->execute(["%$descripcion%"]);
    } elseif($carrera){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE carrera LIKE ?");
        $stmt->execute(["%$carrera%"]);
    } elseif($anio){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE anio=?");
        $stmt->execute([$anio]);
    } elseif($comision){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE comision LIKE ?");
        $stmt->execute(["%$comision%"]);
    } elseif($estado){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE estado LIKE ?");
        $stmt->execute(["%$estado%"]);
    }elseif($fechaDesde){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE fechaDesde LIKE ?");
        $stmt->execute(["%$fechaDesde%"]);
    }elseif($fechaHasta){
        $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE fechaHasta LIKE ?");
        $stmt->execute(["%$fechaHasta%"]);
    }

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$anuncios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron anuncios$anuncios']);
        return;
    }

    echo json_encode($anuncios);
}


