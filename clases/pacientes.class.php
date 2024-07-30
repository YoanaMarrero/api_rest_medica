<?php 
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class pacientes extends conexion {

    private $tabla = 'pacientes';
    private $pacienteid = '';
    private $dni = '';
    private $nombre = ''; 
    private $direccion = '';
    private $codigoPostal = '';
    private $genero = '';
    private $telefono = '';
    private $fechaNacimiento = '0000-00-00';
    private $correo = '';
    private $cantidadPagina = 100;
    private $token = '';
    private $imagen = '';
    
    private function buscarToken(){
        $query = "SELECT TokenId, UsuarioId, Estado from usuarios_token 
            WHERE Token = '" .$this->token ."' AND Estado = 'Activo'";
        $result = parent::obtenerDatos($query);
        if ($result)
            return $result;
        else
            return 0;
    }

    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $result = parent::nonQuery($query);
        if ($result)
            return $result;
        else
            return 0;
    }

    private function insertarPaciente(){
        $query = "INSERT INTO " .$this->tabla ." (DNI, Nombre, Direccion, CodigoPostal, Telefono, Genero, 
            FechaNacimiento, Correo, Imagen) VALUES ('" .$this->dni ."','" .$this->nombre ."','" .$this->direccion ."','"
            .$this->codigoPostal ."','" .$this->telefono ."','" .$this->genero ."','" .$this->fechaNacimiento 
            ."','" .$this->correo  ."','" .$this->imagen ."')";
        $result = parent::nonQueryId($query);
        if ($result)
            return $result;
        else
            return 0;
    }
    
    private function actualizarPaciente(){
        $query = "UPDATE " .$this->tabla ." SET DNI='" .$this->dni ."', Nombre='" .$this->nombre ."', 
            Direccion='" .$this->direccion ."', CodigoPostal='" .$this->codigoPostal ."', 
            Telefono='" .$this->telefono ."', Genero='" .$this->genero ."', FechaNacimiento ='" .$this->fechaNacimiento 
            ."', Correo = '" .$this->correo ."' WHERE PacienteId=" .$this->pacienteid;
        $result = parent::nonQuery($query);
        if ($result)
            return $result;
        else
            return 0;
    }

    private function eliminarPaciente(){
        $query = "DELETE FROM " .$this->tabla ." WHERE PacienteId=" .$this->pacienteid;
        $result = parent::nonQuery($query);
        if ($result)
            return $result;
        else
            return 0;
    }

    private function procesarImagen($img){
        $direccion = dirname(__DIR__) ."\public\imagenes\\";
        $partes = explode(";base64,", $img);
        $extension = explode('/', mime_content_type($img))[1];
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion .uniqid() .'.' .$extension;
        file_put_contents($file, $imagen_base64);
        $fileBD = str_replace('\\', '/', $file);
        // En este punto podríamos redimensionar la imagen.
        return $fileBD;
    }

    public function listaPacientes($pagina = 1){
        $inicio = 0;
        $cantidad = $this->cantidadPagina;
        if ($pagina > 1) {
            $inicio = ($cantidad * ($pagina -1)) + 1;
            $cantidad = $cantidad * $pagina;
        }

        $query = 'SELECT PacienteId, Nombre, DNI, Telefono, Correo FROM '.$this->tabla ." limit $inicio, $cantidad";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }

    public function obtenerPaciente($id){
        $query = 'SELECT * FROM ' .$this->tabla ." WHERE PacienteId ='$id'";
        return parent::obtenerDatos($query);
    }

    public function post($jsonData){
        $_respuestas = new respuestas;
        $data = json_decode($jsonData, true);
        if (!isset($data['token'])){
            return $_respuestas->error_401();
        } else {
            $this->token = $data['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken){
                // Procedemos a validar si los datos que nos remiten son los necesarios para realizar
                // un insert into.
                // En nuestro ejemplo los campos requeridos serán dni, nombre, correo
                if (!isset($data['nombre']) || !isset($data['dni']) || !isset($data['correo'])) {
                    return $_respuestas->error_400();
                }
                else {
                    $this->nombre = $data['nombre'];
                    $this->dni = $data['dni'];
                    $this->correo = $data['correo'];   
                    if (isset($data['direccion'])) $this->direccion = $data['direccion'];
                    if (isset($data['codigoPostal'])) $this->codigoPostal = $data['codigoPostal'];
                    if (isset($data['genero'])) $this->genero = $data['genero'];
                    if (isset($data['telefono'])) $this->telefono = $data['telefono'];
                    if (isset($data['fechaNacimiento'])) $this->fechaNacimiento = $data['fechaNacimiento'];
                    if (isset($data['imagen'])) {
                        $this->imagen = $this->procesarImagen($data['imagen']);
                    }

                    $pacienteGenerado =  $this->insertarPaciente();
                    if ($pacienteGenerado){
                        $result = $_respuestas->response;
                        $result['result'] = array(
                            "pacienteId" => $pacienteGenerado
                        );
                        return $result;
                    } 
                    else {
                        // Si se produjo algún error y el registro no se hizo.
                        return $respuestas->error_500('Error interno, paciente no registrado.');
                    }
                }
            } else {
                return $_respuestas->error_401("Acceso no autorizado. Token inválido o caducado.");
            }
        }
    }

    public function put($jsonData){
        $_respuestas = new respuestas;
        $data = json_decode($jsonData, true);

        if (!isset($data['token'])){
            return $_respuestas->error_401();
        } else {
            $this->token = $data['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken){
                // Procedemos a validar si los datos que nos remiten son los necesarios para realizar
                // un insert into.
                // En nuestro ejemplo los campos requeridos serán dni, nombre, correo
                if (!isset($data['pacienteid'])) {
                    return $_respuestas->error_400();
                }
                else {
                    $this->pacienteid = $data['pacienteid'];
                    if (isset($data['nombre'])) $this->nombre = $data['nombre'];
                    if (isset($data['dni'])) $this->dni = $data['dni'];
                    if (isset($data['correo']))  $this->correo = $data['correo'];   
                    if (isset($data['direccion'])) $this->direccion = $data['direccion'];
                    if (isset($data['codigoPostal'])) $this->codigoPostal = $data['codigoPostal'];
                    if (isset($data['genero'])) $this->genero = $data['genero'];
                    if (isset($data['telefono'])) $this->telefono = $data['telefono'];
                    if (isset($data['fechaNacimiento'])) $this->fechaNacimiento = $data['fechaNacimiento'];

                    $pacienteActualizado =  $this->actualizarPaciente();
                    if ($pacienteActualizado){
                        $result = $_respuestas->response;
                        $result['result'] = array(
                            "pacienteId" => $this->pacienteid
                        );
                        return $result;
                    } 
                    else {
                        // Si se produjo algún error y el registro no se hizo.
                        return $_respuestas->error_500('Error interno, paciente no actualizado.');
                    }
                }
            } else {
                return $_respuestas->error_401("Acceso no autorizado. Token inválido o caducado.");
            }
        }
    }

    public function delete($jsonData){
        $_respuestas = new respuestas;
        $data = json_decode($jsonData, true);

        if (!isset($data['token'])){
            return $_respuestas->error_401();
        } else {
            $this->token = $data['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken){
                // Procedemos a validar si los datos que nos remiten son los necesarios para realizar
                // un insert into.
                // En nuestro ejemplo los campos requeridos serán dni, nombre, correo
                if (!isset($data['pacienteid'])) {
                    return $_respuestas->error_400();
                }
                else {
                    $this->pacienteid = $data['pacienteid'];           

                    $pacienteEliminado =  $this->eliminarPaciente();
                    if ($pacienteEliminado){
                        $result = $_respuestas->response;
                        $result['result'] = array(
                            "pacienteId" => $this->pacienteid
                        );
                        return $result;
                    } 
                    else {
                        // Si se produjo algún error y el registro no se hizo.
                        return $_respuestas->error_500('Error interno, paciente no actualizado.');
                    }
                } 
            } else {
                return $_respuestas->error_401("Acceso no autorizado. Token inválido o caducado.");
            }
        }
    }

   

    

}