<?php

include 'tf-idf.php';
$coneccion = new mysqli($servidor, $usuario, $contraseña, $bdnombre);
if($coneccion->connect_error){
    die("Erro de conección".$coneccion->connect_error );
}

//Nu
$consulta = 'SELECT * FROM posting_v WHERE (termino ="robots") OR (termino ="y") OR (termino ="salud")';

imprimirConsulta($consulta);



?>