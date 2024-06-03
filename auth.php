<?php
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

$_auth = new auth;
$_respuestas = new respuestas;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recibir los datos
    $postBody = file_get_contents("php://input");

    // Enviamos los datos al manejador de la api
    $datosArray = $_auth->login($postBody);

    // Devolvemos una respuesta
    header('Content-Type: application/json');
    $responseCode = (isset($datosArray['result']['error_id'])) ? $datosArray['result']['error_id'] : 200;
    http_response_code($responseCode);

} 
else {
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    http_response_code(405);
}
echo json_encode($datosArray);

?>