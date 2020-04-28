<?php
include_once 'datos.php';
include_once 'auth.php';

class Usuario{
    public $email;
    public $clave;
    public $nombre;
    public $apellido;
    public $telefono;
    public $tipo;

    public function __construct($email, $clave, $nombre, $apellido, $telefono, $tipo)
    {   
        $this->email=$email;
        $this->clave=$clave;
        $this->nombre=$nombre;
        $this->apellido=$apellido;
        $this->telefono=$telefono;
        $this->tipo=$tipo;
    }
    public function signin(){                                   //registrar
        $usuarios= Datos::deserializar('usuarios.txt');
        if($usuarios){
            foreach($usuarios as $user){
                if($this->email == $user->email)
                    return false;            
            }     
        }else{
            $usuarios = array();
        }
        array_push($usuarios,$this);
        return Datos::serializar('usuarios.txt',$usuarios);
    }

    public static function login($email, $clave){
        $usuarios= Datos::deserializar('usuarios.txt');
        if($usuarios){
            foreach($usuarios as $user){
                if($email == $user->email && $clave==$user->clave)
                    return Auth::crearJWT(Auth::generarPayload(array("email"=>$user->email, "tipo"=>$user->tipo)));
            }     
        }
        return false;
    }

    public static function checkLoggedIn($jwt){   
        try {
            return Auth::autentificar($jwt);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public static function getUser($email){
        $usuarios= Datos::deserializar('usuarios.txt');
        if($usuarios){
            foreach($usuarios as $user){
                if($email == $user->email)
                    return $user;           
            }     
        }
        return 'El usuario no existe';
    }
    public static function getAllUsers($tipo){
        $usuarios= Datos::deserializar('usuarios.txt');
        if($tipo=='user'){
            $usuarios= array_filter($usuarios,"Usuario::isNotAdmin");
        }
        return $usuarios;
    }

    private static function isNotAdmin($user){
        if($user->tipo!='admin')
            return true;
        return false;
    }

}