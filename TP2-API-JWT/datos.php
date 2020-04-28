<?php

class Datos{

    //Guardar------------------------------------------------------------------------
    public static function guardarTxt($archivo, $datos){
        $myfile = fopen($archivo, "a") or die("Unable to open file!");
        $rta = fwrite($myfile, $datos);	
        fclose($myfile);

        return $rta;       
    }

    public static function guardarJSON($archivo, $objeto){
        $arrayJson=self::leerJSON($archivo);
        if($arrayJson==null)
            $arrayJson=array();
            
        if(array_push($arrayJson,$objeto)>0){
            $myfile = fopen($archivo, "w") or die("Unable to open file!");
            $rta = fwrite($myfile, json_encode($arrayJson));
            fclose($myfile);            
        }

        return $rta;       
    }

    public static function serializar($archivo, $datos){   
        $myfile = fopen($archivo, "w") or die("Unable to open file!");
        $rta = fwrite($myfile, serialize($datos));	
        fclose($myfile);

        return $rta;       
    }

    //Leer-----------------------------------------------------------------------------------
    public static function leerJSON($archivo){
        if(file_exists($archivo) && filesize($archivo)>0){
            $myfile = fopen($archivo, "r") or die("Unable to open file!");
            $arrayJson = json_decode(fread($myfile,filesize($archivo)));
            fclose($myfile);
            return $arrayJson;                
        }
        return null;
    }


    public static function leerTodoRaw($path){
        if(file_exists($path) && filesize($path)>0){
            $myfile = fopen($path, "r") or die("Unable to open file!");
            $rta= fread($myfile,filesize($path));	      
            fclose($myfile); 
            return $rta;                      
        }
        return null;
    }

    public static function leerTodoTxt($path){
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $retorno=array();
        while(!feof($myfile)) {		
            $linea=explode('@',fgets($myfile));  
            if($linea>1)                          
                array_push($retorno,$linea);         
        }
        fclose($myfile);
        return $retorno;
    }

    public static function deserializar($path){
        $datosDeserializados= unserialize(self::leerTodoRaw($path));   
        return $datosDeserializados;  
    }
}


