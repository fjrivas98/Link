<?php
session_start();
require_once '../classes/vendor/autoload.php';
$cliente = new Google_Client();
$cliente->setApplicationName('CorreoWeb2');
$cliente->setClientId('848266974673-n9gork5r94kprbk4p5jfululaj0hfq4a.apps.googleusercontent.com');
$cliente->setClientSecret('eizWxpt5Bvo5Z45KWaiXbBOl');
$cliente->setRedirectUri('https://proj1-ciscowow.c9users.io/proyecto/gmail/obtenercredenciales.php');

$cliente->setScopes('https://www.googleapis.com/auth/gmail.compose');
$cliente->setAccessType('offline');
if (isset($_GET['code'])) {
    $cliente->authenticate($_GET['code']);
    $_SESSION['token'] = $cliente->getAccessToken();
    $archivo = "token.conf";
    $fh = fopen($archivo, 'w') or die("error");
    fwrite($fh, json_encode($cliente->getAccessToken()));
    fclose($fh);
    header("Location: finalizartoken.php?code=" . $_GET['code']);
}