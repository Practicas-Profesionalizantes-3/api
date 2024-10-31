<?php
require 'config.php';

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarTramites();
            break;
        case 'POST':
            crearTramites();
            break;
        case 'PUT':
            modificarTramites();
            break;
        case 'DELETE':
            borrarTramites();
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
function crearTramites()
{
    global $pdo;

    // Inicializar un array para los campos faltantes
    $missingFields = [];

    // Comprobar si los campos obligatorios están presentes en $_POST
    $requiredFields = [
        'id_usuario_creacion',
        'id_usuario_responsable',
        'id_tramite_tipo',
        'descripcion'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    // Si hay campos faltantes, lanzar excepción con detalles
    if (!empty($missingFields)) {
        throw new Exception('Los siguientes campos son obligatorios: ' . implode(', ', $missingFields));
    }

    // Asignar valores a variables
    $id_usuario_creacion = $_POST['id_usuario_creacion'];
    $id_usuario_responsable = $_POST['id_usuario_responsable'];
    $id_tramite_tipo = $_POST['id_tramite_tipo'];
    $id_estado_tramite = 1; // Este valor puede ser fijo o depender de la lógica de tu aplicación
    $descripcion = $_POST['descripcion'];

    // Establecer la zona horaria de Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha_creacion = date("Y-m-d H:i:s"); // Obtener la fecha y hora actual en la zona horaria correcta

    try {
        // Insertar el trámite en la base de datos
        $stmt = $pdo->prepare("INSERT INTO tramites (id_usuario_creacion, id_usuario_responsable, id_tramite_tipo, id_estado_tramite, descripcion, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_usuario_creacion,
            $id_usuario_responsable,
            $id_tramite_tipo,
            $id_estado_tramite,
            $descripcion,
            $fecha_creacion
        ]);

        // Obtener el ID del trámite creado
        $id_tramite = $pdo->lastInsertId();

        $response = [
            "codigo" => 200,
            "success" => true,
            "mensaje" => "Trámite creado correctamente!",
            "id_tramite" => $id_tramite
        ];

        // Verificar si se recibió un archivo adjunto
        if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
            // Obtener información del archivo
            $fileTmpPath = $_FILES['adjunto']['tmp_name'];

            // Leer el archivo como un blob
            $fileData = file_get_contents($fileTmpPath);

            // Insertar en la tabla tramite_adjuntos
            $stmtAdjunto = $pdo->prepare("INSERT INTO tramite_adjuntos (id_tramite, archivo) VALUES (?, ?)");
            $stmtAdjunto->execute([
                $id_tramite,
                $fileData // Guardamos el contenido como un blob
            ]);

            $response['mensaje_archivo'] = "Se subió el archivo";
        } else {
            $response['mensaje_archivo'] = "NO se subió el archivo";
        }

        echo json_encode($response);
    } catch (Exception $ex) {
        // Manejo de errores: Puedes hacer un log del error si es necesario
        echo json_encode([
            "codigo" => 500,
            "success" => false,
            "mensaje" => "No se pudo crear el trámite! " . $ex->getMessage()
        ]);
    }
}


function modificarTramites()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (
        !isset($data['id_tramite']) || !isset($data['id_usuario_creacion']) || !isset($data['id_usuario_responsable'])
        || !isset($data['id_tramite_tipo']) || !isset($data['id_estado_tramite']) || !isset($data['descripcion'])
    ) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_tramite = $data['id_tramite'];
    $id_usuario_creacion = $data['id_usuario_creacion'];
    $id_usuario_responsable = $data['id_usuario_responsable'];
    $id_tramite_tipo = $data['id_tramite_tipo'];
    $id_estado_tramite = $data['id_estado_tramite'];
    $descripcion = $data['descripcion'];
    $fecha_creacion = date("Y-m-d H:i:s");

    $stmt = $pdo->prepare("UPDATE tramites SET id_usuario_creacion=?, id_usuario_responsable=?, id_tramite_tipo=?,
    id_estado_tramite=?, descripcion=?, fecha_creacion=? WHERE id_tramite=?");
    $stmt->execute([$id_usuario_creacion, $id_usuario_responsable, $id_tramite_tipo, $id_estado_tramite, $descripcion, $fecha_creacion, $id_tramite]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tramite modificado Con Exito!']);
}

function borrarTramites()
{
    global $pdo;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_tramite'])) {
        throw new Exception('Todos los campos son obligatorios');
    }

    $id_tramite = $data['id_tramite'];

    $stmt = $pdo->prepare("DELETE FROM tramites WHERE id_tramite=?");
    $stmt->execute([$id_tramite]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'Tramite no encontrado']);
        return;
    }

    echo json_encode(['mensaje' => 'Tramite eliminado Con Exito!']);
}


function listarTramites()
{
    global $pdo;

    $id_tramite = isset($_GET['id_tramite']) ? $_GET['id_tramite'] : null;
    $id_usuario_creacion = isset($_GET['id_usuario_creacion']) ? (int)$_GET['id_usuario_creacion'] : null;
    $id_usuario_responsable = isset($_GET['id_usuario_responsable']) ? $_GET['id_usuario_responsable'] : null;
    $id_tramite_tipo = isset($_GET['id_tramite_tipo']) ? $_GET['id_tramite_tipo'] : null;
    $id_estado_tramite = isset($_GET['id_estado_tramite']) ? $_GET['id_estado_tramite'] : null;
    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;
    $fecha_creacion = isset($_GET['fecha_creacion']) ? $_GET['fecha_creacion'] : null;

    $sql = "SELECT
    t.id_tramite,
    uc.nombre AS nombre,
    uc.apellido AS apellido,
    ur.nombre AS responsable,
    ur.apellido AS responsable_apellido,
    tt.descripcion AS tipo_tramite,
    te.descripcion AS estado_tramite,
    t.descripcion,
    t.fecha_creacion,
    ta.archivo AS archivo
FROM
    tramites AS t
    LEFT JOIN tramites_tipo AS tt ON t.id_tramite_tipo = tt.id_tramite_tipo
    LEFT JOIN tramite_estados AS te ON t.id_estado_tramite = te.id_estado_tramite
    LEFT JOIN tramite_adjuntos AS ta ON t.id_tramite = ta.id_tramite
    LEFT JOIN usuarios AS uc ON t.id_usuario_creacion = uc.id_usuario
    LEFT JOIN usuarios AS ur ON t.id_usuario_responsable = ur.id_usuario
WHERE
    1=1
";

// El resto de tu código de filtros...


    if ($id_tramite != null) {
        $sql .= " AND id_tramite =$id_tramite ";
    }
    if ($id_usuario_creacion != null) {
        $sql .= " AND LOWER(id_usuario_creacion) like LOWER('%$id_usuario_creacion%')";
    }
    if ($id_usuario_responsable != null) {
        $sql .= " AND LOWER(id_usuario_responsable) like LOWER('%$id_usuario_responsable%')";
    }
    if ($id_tramite_tipo != null) {
        $sql .= " AND LOWER(id_tramite_tipo) like LOWER('%$id_tramite_tipo%')";
    }
    if ($id_estado_tramite != null) {
        $sql .= " AND LOWER(id_estado_tramite) like LOWER('%$id_estado_tramite%')";
    }
    if ($fecha_creacion != null) {
        $sql .= " AND LOWER(fecha_creacion) like LOWER('%$fecha_creacion%')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tramites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$tramites) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Tipo de tramite']);
        return;
    }

    // Convierte el contenido BLOB en base64 para incluir en la respuesta JSON
    foreach ($tramites as &$tramite) {
        if (!is_null($tramite['archivo'])) {
            $tramite['archivo'] = base64_encode($tramite['archivo']);
        }
    }
    
    echo json_encode($tramites);
}
