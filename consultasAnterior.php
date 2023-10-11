<?php
//IMPORTANTE, AQUI SOLO REFERENCIAR A UN DOCUMENTO
require 'parseador.php';

//Conneccion al server
$servidor = 'localhost';
$usuario = 'root';
$contraseña = '';
$bdnombre = 'northwind';

$coneccion = new mysqli($servidor, $usuario, $contraseña, $bdnombre);
if($coneccion->connect_error){
    die("Erro de conección".$coneccion->connect_error );
}
//-----------
$sql;
if(!isset($_POST['consulta'])){return;}

try{
    $sql = obtenerSQL($_POST['consulta']);
    $resultado = $coneccion->query($sql);
    imprimirResultado($resultado);
}catch(Exception $e){
    echo '<b>'.$e->getMessage(). '</b>';
}









function imprimirResultado($resultado){
    $numColumnas = mysqli_num_fields($resultado);
    $nombreColumnas = array();

    //Encabezado
    echo '<table>';
    echo '<tr>';
    for($i = 0; $i< $numColumnas; $i++){
        $columna = $resultado->fetch_field();
        echo '<th>'. $columna->name. '</th>';
        $nombreColumnas[$i] = $columna->name;
    }
    echo '</tr>';

    //Contenido
    while($fila = $resultado->fetch_assoc()){
        echo '<tr>';
        for($i = 0; $i< $numColumnas; $i++){
            echo '<td>'. $fila[$nombreColumnas[$i]]. '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}





$coneccion->close();
?>