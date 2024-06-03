<?php 

class conexion {
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;

    function __construct() {
        $datos = $this->datosConexion();

        foreach ($datos as $key => $value) {
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }

        $this->conexion = new mysqli($this->server, $this->user, $this->password, $this->database, $this->port);
        if ($this->conexion->connect_errno){
            exit('Error de conexion con la base de datos');
        }
    }

    // Obtenemos los datos del archivo de configuración
    private function datosConexion(){       
        $direccion = dirname( __FILE__ );
        $jsondata = file_get_contents($direccion .'/' .'config');
        return json_decode($jsondata, true);
    }

    private function convertirUTF8($array){
        array_walk_recursive($array, function(&$item, $key){
            if (!mb_detect_encoding($item, "utf-8", true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    // select 
    public function obtenerDatos($sqlstr){
        $results = $this->conexion->query($sqlstr);
        $resultArray = array();
        foreach ($results as $key){
            $resultArray[] = $key;
        }
        return $this->convertirUTF8($resultArray);
    }

    // update o delete
    public function nonQuery($sqlstr){
        $results = $this->conexion->query($sqlstr);
        return $this->conexion->affected_rows;
    }

    // insert into
    public function nonQueryId($sqlstr){
        $results = $this->conexion->query($sqlstr);
        $filas = $this->conexion->affected_rows;
        if ($filas >= 1){
            return $this->conexion->insert_id;
        } else {
            return 0;
        }
    }

    // encriptar
    protected function encriptar($string){
        return md5($string);
    }
}