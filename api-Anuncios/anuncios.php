<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                obtenerAnuncio();
            } elseif (isset($_GET['titulo'])) {
                filtrarAnunciosPorTitulo();
            }elseif (isset($_GET['fechaDesde'])) {
                filtrarAnunciosPorFechaDesde();
            } else {
                listarAnuncios();
            }
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

    if (!isset($data['titulo']) || !isset($data['descripcion']) || !isset($data['imagen']) || !isset($data['fechaDesde']) || !isset($data['fechaHasta'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $imagen = $data['imagen'];
    $fechaDesde = $data['fechaDesde'];
    $fechaHasta = $data['fechaHasta'];

    $stmt = $pdo->prepare("INSERT INTO anuncios (titulo, descripcion, imagen, fechaDesde, fechaHasta) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $imagen, $fechaDesde, $fechaHasta]);

    http_response_code(201); // Creado
    echo json_encode(['mensaje' => 'anuncio creado correctamente']);
}

function modificarAnuncio() {
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || !isset($data['titulo']) || !isset($data['descripcion']) || !isset($data['imagen']) || !isset($data['fechaDesde']) ||  !isset($data['fechaHasta'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id = $data['id'];
    $titulo = $data['titulo'];
    $descripcion = $data['descripcion'];
    $imagen = $data['imagen'];
    $fechaDesde = $data['fechaDesde'];
    $fechaHasta = $data['fechaHasta'];

    $stmt = $pdo->prepare("UPDATE anuncios SET titulo=?, descripcion=?, imagen=?, fechaDesde=?, fechaHasta=? WHERE id=?");
    $stmt->execute([$titulo, $descripcion, $imagen, $fechaDesde, $fechaHasta, $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Anuncio no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Anuncio modificado correctamente']);
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

    echo json_encode(['mensaje' => 'Anuncio eliminado correctamente']);
}

function obtenerAnuncio() {
    global $pdo;

    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE id = ?");
    $stmt->execute([$id]);

    $anuncio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$anuncio) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Anuncio no encontrado']);
        return;
    }

    echo json_encode($anuncio);
}

function listarAnuncios() {
    global $pdo;

    $stmt = $pdo->query("SELECT * FROM anuncios");
    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($anuncios);
}

function filtrarAnunciosPorTitulo() {
    global $pdo;

    $titulo = $_GET['titulo'];

    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE titulo LIKE ?");
    $stmt->execute(["%$titulo%"]);

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$anuncios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($anuncios);
}

function filtrarAnunciosPorFechaDesde() {
    global $pdo;

    $fechaDesde = $_GET['fechaDesde'];

    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE fechaDesde LIKE ?");
    $stmt->execute(["%$fechaDesde%"]);

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$anuncios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($anuncios);
}

function filtrarAnunciosPorFechaHasta() {
    global $pdo;

    $fechaHasta = $_GET['fechaHasta'];

    $stmt = $pdo->prepare("SELECT * FROM anuncios WHERE fechaHasta LIKE ?");
    $stmt->execute(["%$fechaHasta%"]);

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$anuncios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($anuncios);
}

