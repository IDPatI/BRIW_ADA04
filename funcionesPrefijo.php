<?php
//$consulta = 'SELECT * FROM products WHERE';

function boleanoAndOr2(string $operador,string $izquierda,string $derecha){
$operando = '';
 if($operador == 'OR'){
    $operando = 'UNION';
 }elseif($operador == 'AND'){
    $operando = 'INTERSECT ';
 }
 return $izquierda. ' '. $operando.' '.$derecha;
}

function not2($cadena){
return "((SELECT id_documento FROM posting_v) EXCEPT". $cadena . ")";
}


function terminoCadena2(string $cadena, array $campos){
    //La palabra "termino" tiene $termino
    $termino = $campos[0];
    $resultado= " (SELECT id_documento FROM posting_v WHERE $termino = '$cadena' )";
    return $resultado;
}

function patron2(string $cadena, array $campos){
    //La palabra "termino" tiene $termino
    $termino = $campos[0];
    $resultado= " (SELECT id_documento FROM posting_v WHERE $termino LIKE '%$cadena%')";
    return $resultado;

}



//CAMPOS-------
//SELECT * FROM $tabla
//Excepcion si hay mas de una tabla
//Hacer antes de campos()
function consultaPrefijo2($campos = NULL){
    $campos= str_replace(' ', '', $campos);
    $resultado = 'SELECT v.id_posting, v.id_vocabulario, v.id_documento, v.termino, v.num_ocurrencias, v.num_palabras,v.frecuencia FROM (';
    if(empty($campos)){      
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

function campos2($campos = null){
    //Preprocesar cadena
    $campos= str_replace(' ', '', $campos);
    $resultado = array();

    //Caso nulo
    if(empty($campos)){
        $resultado = ['termino'];
        return $resultado;
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