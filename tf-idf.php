<?php
require 'consultas.php';

function TF_IDF($sqlConsulta){
    //IDF
    
    $consulta = "SELECT COUNT(*) as total FROM documentos";

    $resultado = sql($consulta);
    $numDocumentos =(int)$resultado->fetch_assoc()['total'];
    //palabra, #documentos que tienen la palabra 
    $sqlIDF = "SELECT DISTINCT id_vocabulario, num_ocurrencias FROM  ($sqlConsulta) AS resultado ";
    $tablaP_ND = sql($sqlIDF);

    $IDF = [];
    //[id_vocalubario]->idf;
    while($fila = $tablaP_ND->fetch_assoc()){
        $IDF[(int)$fila['id_vocabulario']] = log10($numDocumentos/$fila['num_ocurrencias']);
    }
    
    //-----------

    //TF_IDF
    $sqlDocumentos = "SELECT id_vocabulario,id_documento,frecuencia, num_palabras  FROM  ($sqlConsulta) AS resultado ";
    $resultado = sql($sqlDocumentos);
    $TF_IDF = [];
    $documento=0;$palabra=0;$frecuencia=0;$numPalabras =0;
    while($fila = $resultado->fetch_assoc()){
        $documento = (int)$fila['id_documento'];
        $palabra = (int)$fila['id_vocabulario'];
        $frecuencia = (int)$fila['frecuencia'];
        $numPalabras = (int)$fila['num_palabras'];
        if(!array_key_exists($documento, $TF_IDF)) $TF_IDF[$documento] = [];
        $TF_IDF[$documento][$palabra] = ($frecuencia /$numPalabras) * $IDF[$palabra];
    }

    //-------
    //[id_documentod]->[id_vocabulario]->ITF_DF
    return $TF_IDF;
}

function ordenarTF_IDF(array $TF_IDF, $sqlConsulta){
    $documentos = array_keys($TF_IDF);

    $tf_idfConsulta = TF_IDFQuery($sqlConsulta);

    //Peso TF_IDF, id_documento
    $ordenar = [];
    for($i = 0; $i<sizeof($documentos); $i++ ){
        $valores = $TF_IDF[$documentos[$i]];
        $suma = 0;
        foreach($valores as $valor){
            $suma+= $valor;
        }
        $Cos = similitudCoseno($valores, $tf_idfConsulta);
        //Intercambiar $Cos y $suma para diferente orden
        $ordenar[$documentos[$i]] = [$suma,$Cos ,$documentos[$i]];
    }
    rsort($ordenar);
    return $ordenar;

}

function TF_IDFQuery($sqlConsulta){
    $consulta = "SELECT COUNT(*) as total FROM documentos";

    $resultado = sql($consulta);
    $numDocumentos =(int)$resultado->fetch_assoc()['total'];
    //palabra, #documentos que tienen la palabra 
    $sqlIDF = "SELECT DISTINCT id_vocabulario, num_ocurrencias FROM  ($sqlConsulta) AS resultado ";
    $tablaP_ND = sql($sqlIDF);

    $IDF = [];
    //[id_vocalubario]->idf;
    $filas = mysqli_num_rows($tablaP_ND);
    while($fila = $tablaP_ND->fetch_assoc()){
        $IDF[(int)$fila['id_vocabulario']] = log10($numDocumentos/$fila['num_ocurrencias']) * (1/$filas);
    }
    return $IDF;
}

function similitudCoseno(array $documento, array $consulta){
    $palabras = array_keys($consulta);
    $tamanio = sizeof($palabras);

    $numerador = 0;
    $denominadorD= 0;
    $denominadorQ= 0;
    for($i = 0; $i< $tamanio; $i++){
       $query= $consulta[$palabras[$i]];
       $doc=0;
       if(isset($documento[$palabras[$i]])){
        $doc= $documento[$palabras[$i]];
       }
       $numerador += $query * $doc;
       $denominadorD += $doc * $doc;
       $denominadorQ += $query *$query;
    }
    return $numerador / sqrt($denominadorD * $denominadorQ);
}



function imprimirConsulta($sqlConsulta){
    $tf_idf = TF_IDF($sqlConsulta);
    $orden = ordenarTF_IDF($tf_idf, $sqlConsulta);
    for($i=0; $i< sizeof($orden); $i++){
        $actual = $orden[$i][2];
        $consulta = "SELECT * FROM documentos WHERE id_documento = $actual";
        $resultado = sql($consulta);
        $documento = $resultado->fetch_assoc();
        echo "<p><b> (".$orden[$i][1].")".$documento['nombre']. "</b>";
        echo '<a href= "archivos/'.$documento['url'].'" target="_blank"> Abrir</a></p>';
        echo '<p>'.$documento['previo']. '</p>';
    }
    return;
}
