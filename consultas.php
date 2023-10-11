<?php
//IMPORTANTE, AQUI SOLO REFERENCIAR A UN DOCUMENTO
require 'parseador.php';

//Coneccion al server
$servidor = 'localhost';
$usuario = 'root';
$contraseña = '';
$bdnombre = 'indice';

$coneccion = new mysqli($servidor, $usuario, $contraseña, $bdnombre);
if($coneccion->connect_error){
    die("Erro de conección".$coneccion->connect_error );
}
//-----------

$sql;


function sql($query){
    $coneccion = $GLOBALS['coneccion'];
    return $coneccion->query($query);
}

function buscarArchivo($nombre){
    global $servidor, $usuario, $contraseña, $bdnombre;

    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL buscar_documento('$nombre')";
    $consulta =$coneccion->query($query);
    $respuesta = $consulta->fetch_assoc();
    $coneccion->close();
    return $respuesta;
}

function guardarArchivo($url, $nombre, $previo, $num_palabras){
    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL insertar_documento('$url','$nombre','$previo','$num_palabras')";
    $consulta =$coneccion->query($query);
    $coneccion->close();
    return true;
}

function buscarTermino($termino){
    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL buscar_vocabulario('$termino')";
    $consulta =$coneccion->query($query);
    $resultado = $consulta->fetch_assoc();
    $coneccion->close();
    return $resultado;
}

function guardarTermino($termino){
    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL 	insertar_vocabulario('$termino')";
    $consulta =$coneccion->query($query);
    $coneccion->close();
    return true;
}

function guardarTupla($id_vocabulario,$id_documento ){

    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL insertar_posting('$id_vocabulario', '$id_documento')";
    $consulta =$coneccion->query($query);
    $coneccion->close();
    return true;
}

function sumarTupla($nombre,$termino ){
    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL sumar_posting('$termino', '$nombre')";
    $consulta =$coneccion->query($query);
    $coneccion->close();
    return true;
}

function buscarTupla($id_vocabulario, $id_documento){
    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL 	buscar_posting('$id_vocabulario', '$id_documento')";
    $consulta =$coneccion->query($query);
    $resultado = $consulta->fetch_assoc();
    $coneccion->close();
    return $resultado;
}

function sumarFrecuencia($termino){
    global $servidor, $usuario, $contraseña, $bdnombre;
    $coneccion = new mysqli( $servidor, $usuario, $contraseña, $bdnombre);
    $query = "CALL 	sumar_vocabulario('$termino')";
    $coneccion->query($query);
    $coneccion->close();
    return true;
}

if(!isset($_POST['consulta'])){return;}
try{
    $sql = obtenerSQL($_POST['consulta']);
    //$resultado = $coneccion->query($sql);
    //imprimirResultado($resultado);
}catch(Exception $e){
    echo '<b>'.$e->getMessage(). '</b>';
}

function clearStoredResults(){
    $mysqli = $GLOBALS['coneccion'];
    
    do {
         if ($res = $mysqli->store_result()) {
           $res->free();
         }
    } while ($mysqli->more_results() && $mysqli->next_result());        
    
}

?>