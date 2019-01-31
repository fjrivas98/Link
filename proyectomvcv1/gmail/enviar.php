<?php
require_once '../classes/autoload.php';
require_once '../classes/vendor/autoload.php';

use izv\tools\Session;

$session = new SeSsion();


$origen = "franciscojavierrivasbascon@gmail.com";
$alias = "Curso DWE IZV";
$destino = "nacho.pena1985@gmail.com";
$asunto = "Prueba de correo";
$mensaje = "¿Llegará?";
$cliente = new Google_Client();

$cliente->setApplicationName('CorreoWeb2');
$cliente->setClientId('848266974673-n9gork5r94kprbk4p5jfululaj0hfq4a.apps.googleusercontent.com');
$cliente->setClientSecret('eizWxpt5Bvo5Z45KWaiXbBOl');

$cliente->setAccessToken(file_get_contents('token.conf'));

if ($cliente->getAccessToken()) {
    $service = new Google_Service_Gmail($cliente);
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->From = $origen;
        $mail->FromName = $alias;
        $mail->AddAddress($destino);
        $mail->AddReplyTo($origen, $alias);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $mail->preSend();
        $mime = $mail->getSentMIMEMessage();
        $mime = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');
        $mensaje = new Google_Service_Gmail_Message();
        $mensaje->setRaw($mime);
        $service->users_messages->send('me', $mensaje);
        echo "Correo enviado correctamente";
    } catch (Exception $e) {
        echo ("Error en el envío del correo: " . $e->getMessage());
    }
} else {
    echo "No conectado con gmail";
}