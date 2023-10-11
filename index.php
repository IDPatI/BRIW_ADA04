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
        <label for="consulta"> <b>Bucar en Northwind</b></label>
        <input type=" text" name="consulta" title="consulta" size="80" <?php if(isset($_POST['consulta'])){echo 'value="'.$_POST['consulta'].'"';} ?>>
        <input type="submit" value="Bucar">
    </form>
    <br>
    <?php
    include "consultas.php";
    if(isset($sql)){
        echo '<p id="consulta">'.$sql.'</p>';
    }
    ?>
</body>
</html>

