<?php

class token{
  public tipoToken $tipo; 
  public string $lexema;

  function setTipo(tipoToken $tipo){
    $this->tipo = $tipo;
  }

  function setLexema(string $lexema){
    $this->lexema = $lexema;
  }
  function getTipo(){
    return $this->tipo;
  }
}
enum tipoToken{
    case tokenEOF;
    case tokenError;
    case tokenTermino;

    case tokenAND;
    case tokenOR;
    case tokenNOT;

    case tokenPATRON;
    case tokenCADENA;
    case tokenCAMPOS;

}

function crearToken(string $lexema, tipoToken $tipo){
    $regresar = new token();
    $regresar->setLexema($lexema);
    $regresar->setTipo($tipo);
    return $regresar;
}


//AND &
//OR  |
//NOT !
//---
//CADENA $
//PATRON #
//CAMPOS %

function preposesarCadena(string $cadena){
    $cadena = trim($cadena);
    $original = array('AND', 'OR', 'NOT', 'CADENA', 'PATRON','CAMPOS');
    $operadores = array('&', '|', '!', '$', '#','%');
    $remplazo = str_replace($original, $operadores, $cadena);
    $remplazo = strtolower($remplazo);
    $cadenaSeparada = str_split($remplazo);

    $regresar = new SplQueue();
    foreach($cadenaSeparada as $caracter){
      $regresar->enqueue($caracter);
    }
    return $regresar;
}

class lexer{
    public string $actual;
    public SplQueue $cadena;

    function setCadena(string $cadena){
        $this->cadena =preposesarCadena($cadena); 
        $this->actual = $this->cadena->dequeue();
    }
    function sinCaracter(){
      return $this->cadena->isEmpty();
    }
    function siguiente(){
      if(!$this->sinCaracter()){
        $this->actual = $this->cadena->dequeue();
      }
    }
}

function lexerTermino(lexer $lexer){
  $lexema = '';
  
  while($lexer->actual!=' '&& !$lexer->sinCaracter()){
    $lexema.=  $lexer->actual;
    $lexer->siguiente();
  }
  if($lexer->sinCaracter()){
    $lexema.=  $lexer->actual;
  }
  return crearToken($lexema, tipoToken::tokenTermino);
}

function lexerParentesis(lexer $lexer,  tipoToken $tipo){
  $error = '';
  switch($tipo){
    case tipoToken::tokenCADENA:
      $error = 'CADENA(';
      break;
    case tipoToken::tokenPATRON:
      $error = 'PATRON(';
      break;
    case tipoToken::tokenCAMPOS:
      $error = 'CAMPOS(';
      break;
  }

  $lexer->siguiente();
  if($lexer->actual != '('){
    return crearToken($error,tipoToken::tokenError);
  }
  $lexer->siguiente();


  $lexema = '';
  while($lexer->actual!= ')' && !$lexer->sinCaracter()){
    $lexema.= $lexer->actual;
    $lexer->siguiente();
  }
  if($lexer->actual!= ')'){
    return crearToken($error,tipoToken::tokenError);
  }
  $lexer->siguiente();
  return crearToken($lexema, $tipo);
}

function lexerTokenSiguiente(lexer $lexer){
if($lexer->sinCaracter()){return crearToken("", tipoToken::tokenEOF);}
while($lexer->actual ==' '){$lexer->siguiente();}
 
switch($lexer->actual){
 case '&':$lexer->siguiente(); return crearToken('AND', tipoToken::tokenAND); break;
 case '|':$lexer->siguiente(); return crearToken('OR', tipoToken::tokenOR); break;
 case '!':$lexer->siguiente(); return crearToken('NOT', tipoToken::tokenNOT); break;
 case '$':return lexerParentesis($lexer, tipoToken::tokenCADENA); break;
 case '#':return lexerParentesis($lexer, tipoToken::tokenPATRON); break;
 case '%':return lexerParentesis($lexer, tipoToken::tokenCAMPOS); break;
 default:
  return lexerTermino($lexer);
}
}

function tokenArray(string $cadena){
  $lexer = new lexer();
  $lexer->setCadena($cadena);
  $resultado = array();

  while(true){
    $token = lexerTokenSiguiente($lexer);
    $resultado[] = $token;
    if($token->getTipo() == tipoToken::tokenEOF || $token->getTipo()== tipoToken::tokenError){
      break;
    }
  }
  return $resultado;
}

//Prueba
/*
$cadena = 'CADENA(ASDF)';
$lexer = new lexer();
$lexer->setCadena($cadena);
$resultado = array();



while(true){
  $token = lexerTokenSiguiente($lexer);
  $resultado[] = $token;
  if($token->getTipo() == tipoToken::tokenEOF || $token->getTipo()== tipoToken::tokenError){
    break;
  }
}


echo '<pre>';
var_dump($resultado);
echo '</pre>';
*/


?>