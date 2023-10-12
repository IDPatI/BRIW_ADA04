<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADA03</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <form action="index.php" method="post">
        <label for="consulta"> <b>Bucar en Archivos</b></label>
        <input type=" text" name="consulta" title="consulta" size="80" <?php if(isset($_POST['consulta'])){echo 'value="'.$_POST['consulta'].'"';} ?>>
        <input type="submit" value="Bucar">
    </form>
    <br>
    <a href="subirArchivo.php"> Subir archivo </a>
    <?php
    require "parseador.php";
    require 'tf-idf.php';
    $sql;
    if(!isset($_POST['consulta'])){return;}
        try{
        $sql = obtenerSQL($_POST['consulta']);
        imprimirConsulta($sql);
        //$sql = "SELECT DISTINCT termino, num_ocurrencias FROM  ($sql) AS resultado ";
        //$resultado = $coneccion->query($sql);
        //imprimirResultado($resultado);
    }catch(Exception $e){
        echo '<b>Consulta Invalida </b>';
    }
    if(isset($sql)){
        echo '<p id="consulta">'.$sql.'</p>';
    }
    $coneccion->close();
    ?>
</body>
</html>

