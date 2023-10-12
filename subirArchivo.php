<!DOCTYPE html>
<html>
<body>
<a href="index.php">Atras</a>
<form action="subirArchivo.php" method="post" enctype="multipart/form-data">
 <h2> Subir archivos</h2> <br>
  <input type="file" name="archivos[]" accept="file/txt" multiple>
  <br>
  <br>
  <input type="submit" value="Subir archivos" name="subir">
</form>

<?php 
if(!(isset($_FILES["archivos"]) && !empty($_FILES["archivos"]["name"][0]))){
   return;
}

//Quitar cuando se termine
/*
echo '<pre>';
echo var_dump($_FILES["archivos"]);
echo "</pre>";
*/
//--------

?>
</body>
</html>

<?php
if(!(isset($_FILES["archivos"]) && !empty($_FILES["archivos"]["name"][0]))){
  return;
}
include 'consultas.php';

$directorio = 'archivos/';
$archivos = guardarArchivos($directorio);

indexarArchivos($archivos, $directorio);
echo "Archivos indexados";

function guardarArchivos($directorio){
  $archivos = [];
  $cantidad = sizeof($_FILES["archivos"]["name"]);
  for($i =0; $i<$cantidad; $i++){
    $nombre = nombreArchivo( $_FILES["archivos"]["name"][$i]);
    if($_FILES["archivos"]["type"][$i]!=="text/plain"){
      echo "No es un archivo de texto";
      return;
    }
    move_uploaded_file($_FILES["archivos"]["tmp_name"][$i], $directorio.$nombre);
    $archivos[] = $nombre;
  }
  return $archivos;
}

function indexarArchivos($archivos, $directorio){
  //$invertedIndex = [];
  foreach($archivos as $archivo){
    $contenido = file_get_contents($directorio.$archivo);
    if($contenido === false) die('Unable to read file: ' . $archivo);
    $previo = substr($contenido, 0, 50);
    $contenido  = limpiar($contenido);
    $palabras = explode(" ", $contenido);


    $num_palabras = sizeof($palabras);
    $url = $archivo;
    $nombre = substr($archivo, 0, -4);
    guardarArchivo($url, $nombre, $previo, $num_palabras);
    $id_documento = buscarArchivo($nombre)['id_documento'];


    foreach($palabras as $palabra){
      $id_vocabulario = '';
      if(buscarTermino($palabra)=== null){
        guardarTermino($palabra);
      }
      $id_vocabulario = buscarTermino($palabra)['id_vocabulario'];
      
      if(buscarTupla($id_vocabulario, $id_documento)=== null){
        guardarTupla($id_vocabulario, $id_documento);
        sumarFrecuencia($palabra);
      }else{
        sumarTupla($id_vocabulario, $id_documento);
      }
      /*
      if(!array_key_exists($palabra, $invertedIndex)) $invertedIndex[$palabra] = [];
      if(!array_key_exists($archivo, $invertedIndex[$palabra])){

        $invertedIndex[$palabra][$archivo] = 1;
      }else{
        $invertedIndex[$palabra][$archivo] ++;
      }*/
    }

  }
  //return $invertedIndex;
}

function nombreArchivo(string $nombre){
  $actual = 0;
  $archivo = explode('.',$nombre);
  $devolver = $archivo[0];

  while(file_exists($GLOBALS['directorio'].$devolver.'.'.$archivo[1])){
    $devolver=$archivo[0].$actual;
    $actual ++;
  };
  return $devolver.'.'.$archivo[1];
}

function limpiar($var) {
  return strtolower(preg_replace('/\s+/', ' ', preg_replace('/[^a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s]+/u', '', $var)));
}
?>