<?php
//$consulta = 'SELECT * FROM products WHERE';

function boleanoAndOr(string $operador,string $izquierda,string $derecha){
 return $izquierda. ' '. 'OR'.' '.$derecha;
}

function not($cadena){
return ' NOT '. $cadena;
}


function terminoCadena(string $cadena, array $campos){
    $resultado= ' ('.$campos[0]. ' ="'.$cadena.'"';

    $tamanio = sizeof($campos);
    for($i= 1; $i<$tamanio; $i++ ){
    $resultado.= ' OR ';
    $resultado.= $campos[$i]. ' ="'.$cadena.'"';
    }
    $resultado.=')';
    return $resultado;
}

function patron(string $cadena, array $campos){
    $resultado = ' ('. $campos[0]. ' LIKE "%'. $cadena. '%"';

    $tamanio = sizeof($campos);
    for($i= 1; $i<$tamanio; $i++ ){
        $resultado.= ' OR ';
        $resultado.= $campos[$i]. ' LIKE "%'. $cadena. '%"';
    }
    $resultado.=')';
    return $resultado;

}



//CAMPOS-------
//SELECT * FROM $tabla
//Excepcion si hay mas de una tabla
//Hacer antes de campos()
function consultaPrefijo($campos = NULL){
    $campos= str_replace(' ', '', $campos);
    $resultado = 'SELECT * FROM ';
    if(empty($campos)){
        $resultado.= ' posting_v WHERE';
        return $resultado;
    }
    $camposArray = explode(",", $campos);

    $tamanio = sizeof($camposArray);
    $tabla_columa = explode('.', $camposArray[0], 2);
    for($i= 1; $i < $tamanio; $i++ ){
        $tabla_columaActual = explode('.', $camposArray[$i], 2);
        if(trim($tabla_columaActual[0]) != trim($tabla_columa[0])){
            return new Exception("Solo puede haber una tabla en CAMPOS(): ".$tabla_columaActual[0] );
        }
    }
    return $resultado.= $tabla_columa[0]. " WHERE";
}

//array de Campos de CAMPOS()
function campos($campos = null){
    //Preprocesar cadena
    $campos= str_replace(' ', '', $campos);
    $resultado = array();

    //Caso nulo
    if(empty($campos)){
        $resultado = ['termino'];
        return $resultado;
    }

    //Separar por comas
    $camposArray = explode(",", $campos);

    //Recorrer y separar en busca de los puntos
    $tamanio = sizeof($camposArray);
    for($i= 0; $i < $tamanio; $i++ ){
        $tabla_columaActual = explode('.', $camposArray[$i], 2);
        //Si no se encuentra un punto error
        if(empty($tabla_columaActual[1])){
            return new Exception("No se encuentra la columna en CAMPOS(): ". $tabla_columaActual[0]);
        }
        $resultado[] = trim($tabla_columaActual[1]);
    }
    return $resultado;
}
//*-------------

/*
$p = 'Potato Chips';
$consulta = terminoCadena($p, campos());
echo '<pre>';
var_dump($consulta);
echo '</pre>';
*/
?>