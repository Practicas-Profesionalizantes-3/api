<?php
require 'config.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET');

header("Access-Control-Allow-Headers: *");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            listarMateriaCarrera();
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


function listarMateriaCarrera()
{
    global $pdo;

    $id_carrera = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : null;

    $sql = "SELECT mc.*, m.materia
    FROM materia_carreras AS mc
    INNER JOIN  materias AS m on mc.id_materia = m.id_materia
    WHERE 1=1";

    if ($id_carrera != null) {
        $sql .= " AND id_carrera =$id_carrera";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$carreras) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron Carreras']);
        return;
    }

    echo json_encode($carreras);
}
