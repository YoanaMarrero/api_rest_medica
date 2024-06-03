<?php 
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion {
    
    public function login ($jsonData){
        $_respuestas = new respuestas;
        $data  = json_decode($jsonData, true);

        //if (!isset($data['usuario']) || !isset($data['password'])) {
        if (!array_key_exists('usuario', $data) || !array_key_exists('password', $data)) {
            // Error en los campos pasados
            return $_respuestas->error_400();
        }
        else {
            $usuario = $data['usuario'];
            $password = $data['password'];
            $password = parent::encriptar($password);

            $datos = $this->obtenerDatosUsuario($usuario);
            if ($datos){
                // Si existe el usuario validamos su contraseña
                if ($password === $datos['Password']) {
                    // Validamos el estado del usuario.
                    if ($datos['Estado'] === 'Activo') {
                        // Creación del token
                        $tokenGenerado = $this->insertarToken($datos['UsuarioId']);
                        if ($tokenGenerado){
                            // Si el token se creó satisfactoriamente
                            $result = $_respuestas->response;
                            $result['result'] = array(
                                "token" => $tokenGenerado
                            );
                            return $result;
                        } 
                        else {
                            // Si se produjo algún error y el token no se hizo.
                            return $respuestas->error_500('Error interno, token no generado.');
                        }

                    } else {
                        // Usuario deshabilitado
                        return $_respuestas->error_200('Acceso no autorizado. Usuario deshabilitado.');
                    }

                } else {                    
                    // Password no valido
                    return $_respuestas->error_200('Credenciales no válidas. Password no válido.');
                }

            } else {
                // Usuario desconocido
                return $_respuestas->error_200('Credenciales no válidas. Usuario desconocido.');
            }
        }
    }

    private function obtenerDatosUsuario($correo){
        $query = "SELECT UsuarioId, Password, Estado FROM usuarios WHERE Usuario = '$correo'";
        $datos = parent::obtenerDatos($query);
        if (isset($datos[0]["UsuarioId"])) {
            return $datos[0];
        } else {
            return 0;
        }
    }

    private function insertarToken($userid){
        $val = true;
        /*
        bin2hex -> devuelve un string hexadecimal.
        openssl_random_pseudo_bytes -> genera una cadena de bytes aleatoria.

        Mejoras: 
        - verificar que el usuario no tiene más de un token activo
        - verificar que el token no esté caducado 
        
        */
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        $date = date("Y-m-d H:i");
        $estado = "Activo";
        $query = "INSERT INTO usuarios_token (UsuarioId, Token, Estado, Fecha) VALUES ('$userid', '$token','$estado', '$date')";
        $result = parent::nonQuery($query);
        if ($result){
            return $token;
        } else {
            return 0;
        }
    }
}