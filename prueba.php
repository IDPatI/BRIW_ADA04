<?php

include 'consultas.php';

echo '<pre>';
echo var_dump(buscarArchivo('p1'));
echo "</pre>";

/*
//Crea la cola
  $queue= new SplQueue();

  //Añade elementos
  $queue->enqueue('1');
  $queue->enqueue('2');
  $queue->enqueue('3');

  //Muestra el número de elementos de la cola(3)
  //echo $queue->count();

  //Situa el puntero al principio de la cola
  //$queue->rewind();

  //Muestra los elementos (1, 2, 3)
  while( $queue->valid() )
  {
    //echo $queue->current(), PHP_EOL;
    $queue->next();
  }

   //Saca de la cola el primer elemento y lo muestra
    $primero=  $queue->dequeue();
    echo "primero: ".$primero;

    echo "Vacio: ". var_dump($queue->isEmpty());
    $queue->dequeue();
    $queue->dequeue();
    echo "<br>";
    $vacio = $queue->isEmpty();

    echo "Vacio: ". var_dump($vacio);

  //Situa el puntero al principio de la cola

   //Muestra el número de elementos de la cola(2)
    //echo $queue->count();

  //Muestra los elementos (2, 3)
   while( $queue->valid() )
   {
    //echo $queue->current(), PHP_EOL;
    $queue->next();
   }
*/
?>