<?php

include_once 'usuario.php';
include_once 'response.php';

$request_method=$_SERVER['REQUEST_METHOD'];
$path_info=$_SERVER['PATH_INFO']??'';
$response = new Response();

switch ($path_info) {
    case '/signin':
        switch ($request_method) {
            case 'POST':                            //1.recibe email, clave, nombre, apellido, telefono y tipo (user, admin) y lo guarda en un archivo.
                $email=$_POST['email']??'';
                $clave=$_POST['clave']??'';
                $nombre=$_POST['nombre']??'';
                $apellido=$_POST['apellido']??'';
                $telefono=$_POST['telefono']??'';
                $tipo=$_POST['tipo']??'';
                if($email=='' || $clave=='' || $nombre=='' || $apellido==''|| $telefono==''|| $tipo==''){
                    $response->status='fail';
                    $response->data='Faltan datos'; 
                }elseif($tipo!='user' && $tipo!='admin'){
                    $response->status='fail';
                    $response->data='Tipo de usuario no valido'; 
                }else{
                    $usr = new Usuario($email, $clave, $nombre, $apellido, $telefono, $tipo);
                    $result = $usr->signin();
                    if($result){
                        $response->status='success';   
                        $response->data='Usuario creado';                        
                    }else{
                        $response->data='El usuario ya existe'; 
                        $response->status='error';                          
                    }
                }
            break;
            case 'GET':
                $response->data ="405 method not allowed";
                $response->status='fail';
            break;
        }
    break;
    case '/login':                              //2.recibe email y clave y chequea que existan, si es así retorna un JWT de lo contrario informa el error (si el email o la clave están equivocados) .
        switch ($request_method) {
            case 'POST':
                $email=$_POST['email']??'';
                $clave=$_POST['clave']??'';
                if($email=='' || $clave==''){
                    $response->status='fail';
                    $response->data='Faltan datos'; 
                }else{
                    $result = Usuario::login($email, $clave);
                    if($result){
                        $response->status='success';   
                        $response->data=$result;     //jwt                   
                    }else{
                        $response->data="Credenciales incorrectas"; 
                        $response->status='fail'; 
                    }
                }
            break;
            case 'GET':
                $response->data ="405 method not allowed";
                $response->status='fail';
            break;
        }
    break;
    case '/detalle':                                  //3.GET detalle: Muestra todos los datos del usuario actual.
        switch ($request_method) {
            case 'GET':
                $all_headers=getallheaders();
                $jwt = $all_headers['Authorization']?? '';

                if(empty($jwt)){
                    $response->status='fail';
                    $response->data='Unauthorized: Debe iniciar sesion para acceder a este recurso';
                }else{
                    try {
                        if($jwtDecoded = Usuario::checkLoggedIn($jwt)){
                            $response->data=Usuario::getUser($jwtDecoded->data->email);
                            $response->status='success';                       
                        }
                        else{
                            $response->data ='Error de sesion: Usuario no encontrado';
                            $response->status='error';                             
                        }
                    } catch (Exception $ex) {
                        $response->data ='Error de sesion: '.$ex->getMessage();
                        $response->status='error';
                    }            
                }
            break;
            case 'POST':
                $response->data ="405 method not allowed";
                $response->status='fail';
            break;
        }
    break;
    case '/lista':                                          //4.GET lista: Si el usuario es admin muestra todos los usuarios, si es user solo los del tipo user.
        switch ($request_method) {
            case 'GET':
                $all_headers=getallheaders();
                $jwt = $all_headers['Authorization']?? '';

                if(empty($jwt)){
                    $response->status='fail';
                    $response->data='Unauthorized: Debe iniciar sesion para acceder a este recurso';
                }else{
                    try {
                        if($jwtDecoded = Usuario::checkLoggedIn($jwt)){
                            $response->data=Usuario::getAllUsers($jwtDecoded->data->tipo);
                            $response->status='success';                       
                        }
                        else{
                            $response->data ='Error de sesion: Usuario no encontrado';
                            $response->status='error';                             
                        }
                    } catch (Exception $ex) {
                        $response->data ='Error de sesion: '.$ex->getMessage();
                        $response->status='error';
                    }            
                }
            break; 
            case 'POST':
                $response->data ="405 method not allowed";
                $response->status='fail';
            break;
        }
    break;
    default:
        $response->data ="404 Not Found";
        $response->status='fail';
        break;

}
echo json_encode($response);