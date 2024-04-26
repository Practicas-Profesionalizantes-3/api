
function listarUsuarios() {
    global $pdo;

    $stmt = $pdo->query("SELECT * FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);
}

function filtrarUsuariosPorNombre() {
    global $pdo;

    $nombre = $_GET['nombre'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre LIKE ?");
    $stmt->execute(["%$nombre%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorApellido() {
    global $pdo;

    $apellido = $_GET['apellido'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE apellido LIKE ?");
    $stmt->execute(["%$apellido%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorDni() {
    global $pdo;

    $dni = $_GET['dni'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni LIKE ?");
    $stmt->execute(["%$dni%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorEmail() {
    global $pdo;

    $email = $_GET['email'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email LIKE ?");
    $stmt->execute(["%$email%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}
function filtrarUsuariosPorCarrera() {
    global $pdo;

    $carrera = $_GET['carrera'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE carrera LIKE ?");
    $stmt->execute(["%$carrera%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorAnio() {
    global $pdo;

    $anio = $_GET['anio'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE anio LIKE ?");
    $stmt->execute(["%$anio%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorComision() {
    global $pdo;

    $comision = $_GET['comision'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE comision LIKE ?");
    $stmt->execute(["%$comision%"]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}

function filtrarUsuariosPorEstado() {
    global $pdo;

    $estado = $_GET['estado'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE estado = ?");
    $stmt->execute([$estado]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$usuarios) {
        http_response_code(404); // No encontrado
        echo json_encode(['error' => 'No se encontraron usuarios']);
        return;
    }

    echo json_encode($usuarios);
}