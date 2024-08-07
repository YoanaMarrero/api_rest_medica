<?php
class respuestas{
    public $response = [
        "status" => "ok",
        "result" => array()
    ];


    // No existe como código de error como tal lo utilizaremos 
    // para controlar diferentes cuestiones
    public function error_200($string = 'Datos incorrectos.'){
        $this->response["status"] = "error";
        $this->response["result"] = array(
            "error_id" => "200",
            "error_msg" => $string
        );
        return $this->response;
    }

    public function error_400(){
        $this->response["status"] = "error";
        $this->response["result"] = array(
            "error_id" => "400",
            "error_msg" => 'Datos enviados incompletos o con formato incorrecto.'
        );
        return $this->response;
    }

    public function error_401($string = 'Acceso no autorizado.'){
        $this->response["status"] = "error";
        $this->response["result"] = array(
            "error_id" => "401",
            "error_msg" => $string
        );
        return $this->response;
    }

    // Para cuando remiten un metodo no aceptado
    public function error_405(){
        $this->response["status"] = "error";
        $this->response["result"] = array(
            "error_id" => "405",
            "error_msg" => "Método no permitido."
        );
        return $this->response;
    }

    public function error_500($string = 'Error interno del servidor.'){
        $this->response["status"] = "error";
        $this->response["result"] = array(
            "error_id" => "500",
            "error_msg" => $string
        );
        return $this->response;
    }
}