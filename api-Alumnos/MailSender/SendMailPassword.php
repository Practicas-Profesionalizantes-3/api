<?php
require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

function SendMail($sendTo, $asunto, $mensaje)
{
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'EMAIL';
        $mail->Password   = 'CONTRASEÑA';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('EMAIL', 'Departamento de Alumnos');
        $mail->addAddress($sendTo);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = '<h2>Departamento de Alumnos</h2> ' . $mensaje;
        $mail->AltBody = 'Departamento de Alumnos: ' . $mensaje ;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>