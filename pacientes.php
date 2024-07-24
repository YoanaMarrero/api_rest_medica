<?php 
require_once 'clases/respuestas.class.php';
require_once 'clases/pacientes.class.php';

$_respuyestas = new respuestas;
$_pacientes = new pacientes;

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'POST':
        // CREATE (CRUD)
        
        // Recibimos los datos enviados
        $postBody = file_get_contents("php://input");

        // Enviamos los datos al manejador
        $response = $_pacientes->post($postBody);

        // Devolvemos una respuesta
        header('Content-Type: application/json');
        $responseCode = (isset($datosArray['result']['error_id'])) ? $datosArray['result']['error_id'] : 200;
        http_response_code($responseCode);
        echo json_encode($response);
        break;

    case 'GET':
        // READ (CRUD)
        if (isset($_GET['page'])) {

            // Recibimos los datos enviados
            $pagina = is_numeric($_GET['page']) ? $_GET['page'] : 1;

            // Enviamos los datos al manejador
            $response = $_pacientes->listaPacientes($pagina);
            
            // Devolvemos una respuesta
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($response);
        } 
        elseif (isset($_GET['id'])) {
            
            // Recibimos los datos enviados
            $pacienteId = $_GET['id'];

            // Enviamos los datos al manejador
            $response = $_pacientes->obtenerPaciente($pacienteId);

            // Devolvemos una respuesta
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($response);
        }
        break;

    case 'PUT':
        // UPDATE (CRUD)

        // Recibimos los datos enviados
        $postBody = file_get_contents("php://input");

        // Enviamos los datos al manejador
        $response = $_pacientes->put($postBody);
        
        // Devolvemos una respuesta
        header('Content-Type: application/json');
        $responseCode = (isset($datosArray['result']['error_id'])) ? $datosArray['result']['error_id'] : 200;
        http_response_code($responseCode);
        echo json_encode($response);
        break;

    case 'DELETE':
        // DELETE (CRUD)

        // Recibimos los datos enviados
        $postBody = file_get_contents("php://input");

        // Enviamos los datos al manejador
        $response = $_pacientes->delete($postBody);
        
        // Devolvemos una respuesta
        header('Content-Type: application/json');
        $responseCode = (isset($datosArray['result']['error_id'])) ? $datosArray['result']['error_id'] : 200;
        http_response_code($responseCode);
        echo json_encode($response);
        break;

    default:
        // Enviamos los datos al manejador
        $response = $_respuestas->error_405();

        // Devolvemos una respuesta
        header('Content-Type: application/json');
        http_response_code(405);
        echo json_encode($response);
        break;
}

?>