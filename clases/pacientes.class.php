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

    }

    private function insertarPaciente(){
        $query = "INSERT INTO " .$this->tabla ." (DNI, Nombre, Direccion, CodigoPostal, Telefono, Genero, 
            FechaNacimiento, Correo) VALUES ('" .$this->dni ."','" .$this->nombre ."','" .$this->direccion ."','"
            .$this->codigoPostal ."','" .$this->telefono ."','" .$this->genero ."','" .$this->fechaNacimiento 
            ."','" .$this->correo ."')";
        $result = parent::nonQueryId($query);
        if ($result)
            return $result;
        else
            return 0;
    }

    public function put($jsonData){
        $_respuestas = new respuestas;
        $data = json_decode($jsonData, true);

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
    }

    private function actualizarPaciente(){
        $query = "UPDATE " .$this->tabla ." WHERE PacienteId=" .$this->pacienteid;
        $result = parent::nonQuery($query);
        if ($result)
            return $result;
        else
            return 0;
    }

    public function delete($jsonData){
        $_respuestas = new respuestas;
        $data = json_decode($jsonData, true);

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
    }

    private function eliminarPaciente(){
        $query = "DELETE FROM " .$this->tabla ." WHERE PacienteId=" .$this->pacienteid;
        $result = parent::nonQuery($query);
        if ($result)
            return $result;
        else
            return 0;
    }
}