<?php
require __DIR__ . '/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET');

header("Access-Control-Allow-Headers: X-Requested-With");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            SendMail(null, null, null, false);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500); // Error del servidor
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(['error' => $e->getMessage()]);
}

function SendMail($sendTo, $asunto, $mensaje, $fromApi)
{
    $mail = new PHPMailer(true);
    $sendTo = isset($_GET['sendTo']) ? $_GET['sendTo'] : $sendTo;
    $asunto = isset($_GET['asunto']) ? $_GET['asunto'] : $sendTo;
    $mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : $sendTo;

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'santiagopruebas22@gmail.com';
        $mail->Password   = 'qeke ljtu tked uacc';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('santiagopruebas22@gmail.com', 'Departamento de Alumnos');
        $mail->addAddress($sendTo);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = '<h2>Departamento de Alumnos</h2> ' . $mensaje;
        $mail->AltBody = 'Departamento de Alumnos: ' . $mensaje ;
        $mail->send();

        if($fromApi){
            return true;
        }
        else{
            http_response_code(200);
            echo json_encode(["codigo" => 200, "error" => null, "success" => true, "mensaje" => "Email enviado!", "data" => null]);
        }
    } catch (Exception $e) {
        return false;
    }
}

?>