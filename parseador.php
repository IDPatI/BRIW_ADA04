<?php
require 'lexer.php';
require 'funciones.php';

class nodo{
    public token $token;
    public $izquierda;
    public $derecha;

    function setToken($token){
        $this->token = $token;
    }
    function setIzquierda($nodo){
        $this->izquierda = $nodo;
    }
    
    function setDerecha($nodo){
        $this->derecha = $nodo;
    } 
}

function crearNodo(token $token, nodo $izquierda = null, nodo $derecha = null){
    $regresar = new nodo();
    $regresar->setToken($token);
    $regresar->setIzquierda($izquierda);
    $regresar->setDerecha($derecha);
    return $regresar;
}


//Error si CAMPOS no está al final
//falso si no hay el token CAMPOS
//posicion de CAMPOS si existe 
function checarCAMPOS(array $tokens){
    $final = end($tokens);
    if($final->tipo == tipoToken::tokenError){
        throw new Exception("Hay un error cerda de: ". $final->lexema);
    }

    $tamanio = sizeof($tokens);
    for($i= 0; $i< $tamanio; $i++){
        if($tokens[$i]->tipo== tipoToken::tokenCAMPOS){
            if($tokens[$i+1]->tipo != tipoToken::tokenEOF){
                throw new Exception("CAMPOS() solo puede estar al final");
            }
            return $i;
        }
    }
    return false;
}
//Usar junto a checar campos
//-1-
function preprosesarCAMPOS(array &$tokens){
    $posCAMPOS = checarCAMPOS($tokens);
    if($posCAMPOS=== false){
        return false;
    }
    $retornar = $tokens[$posCAMPOS]->lexema;

    unset($tokens[$posCAMPOS]);
    $tokens = array_values($tokens);
    return $retornar;
}
//-2-
function insertarORs($array) {
    $nuevoArray = [];
    $n = count($array);
    
    for ($i = 0; $i < $n; $i++) {
        $nuevoArray[] = $array[$i];
        if ($i < $n - 1 && esUnario($array[$i]) && esUnario($array[$i + 1])) {
            $nuevoArray[] = crearToken('OR', tipoToken::tokenOR);
        }
    }
    return $nuevoArray;
}

//-3-
function ordenPrefijo(array $tokens, $columnas){
    $pila = [];
    $salida = [];

    for($i=0; $tokens[$i]->tipo != tipoToken::tokenEOF; $i++){
        $token = $tokens[$i];
        switch ($token->tipo){
            case tipoToken::tokenError:
                throw new Exception("Error al contruir la consulta cerca de: ". $token->lexema);
                break;
            case tipoToken::tokenTermino: 
            case tipoToken::tokenCADENA: 
            case tipoToken::tokenPATRON:
                $salida[] = $token;
                /* No se si da lo mismo
                if($i>0){
                    if(esUnario($tokens[$i-1]) && empty($pila)){
                        $salida[] = crearToken('OR', tipoToken::tokenOR);
                    }
                }*/
                break;
            case tipoToken::tokenAND:
            case tipoToken::tokenOR:
                while(!empty($pila)){
                    $salida[] = array_pop($pila);
                }
                array_push($pila, $token);
                break;
            case tipoToken::tokenNOT:
                if(!esUnario($tokens[$i+1])){
                    throw new Exception("Error en NOT : ". $tokens[$i+1]->lexema);
                }
                $nodoEval = crearNodo($tokens[$i+1]);
                $lexema = evaluar($nodoEval, $columnas);
                $token->lexema = $lexema;
                $salida[] = $token;
                $i++;
                break;
            default:
            throw new Exception("Error al evaluar el Orden Prefijo");
        }
    }
    while(!empty($pila)){
        $salida[] = array_pop($pila);
    }
    return $salida;
}

//-4-
function construirArbol(array $tokens){
    $pila = [];

    for($i=0; $i<sizeof($tokens); $i++){
        $token = $tokens[$i];
        switch ($token->tipo){
            case tipoToken::tokenError:
                throw new Exception("Error al contruir la consulta cerca de: ". $token->lexema);
                break;
            case tipoToken::tokenAND:
            case tipoToken::tokenOR: 
                $nodo = crearNodo($token);
                $nodo->derecha = array_pop($pila);
                $nodo->izquierda = array_pop($pila);
                array_push($pila, $nodo);
                break;
            case tipoToken::tokenNOT:
                array_push($pila, crearNodo($token));
                break;
            case tipoToken::tokenTermino:
            case tipoToken::tokenCADENA:
            case tipoToken::tokenPATRON:
                array_push($pila, crearNodo($token));
                break;
            default:
            throw new Exception("Error al evalur la consulta");
        }
    }
    echo "<pre>";
    //var_dump($pila);
    echo "</pre>";

    if(sizeof($pila)!== 1){
        throw new Exception("Consulta inválida");
    }
    return $pila[0];
}

//true si es operacion unaria
function esUnario(token $token){
    switch ($token->tipo){
        case tipoToken::tokenPATRON:
        case tipoToken::tokenCADENA:
        case tipoToken::tokenTermino:
            return true;
        default:
        return false;
    }
}
//-5-
function evaluar($nodo, $columnas){
    if(is_null($nodo)){
        return '';
    }
    $token = $nodo->token;
    switch ($token->tipo){
        case tipoToken::tokenTermino: 
        case tipoToken::tokenCADENA: 
            return terminoCadena($token->lexema, $columnas);
        break;
        case tipoToken::tokenPATRON:
            return patron($token->lexema, $columnas);
        case tipoToken::tokenAND:
        case tipoToken::tokenOR: 
            return boleanoAndOr($token->lexema,evaluar($nodo->izquierda, $columnas), evaluar($nodo->derecha, $columnas));
        break;
        case tipoToken::tokenNOT:
            return not($token->lexema);
            break;
        default:
        throw new Exception("Error al evalur el arbol binario");
    }
}

//$cadena = 'CAMPOS(suppliers.company)';


function obtenerSQL($cadena){
    //Obtener
    $arregloTokens = tokenArray($cadena);


//Dentro de la funcion se modifica el arregloTokens para eliminar el token de CAMPOS y devolver su valor
    $valCampos = preprosesarCAMPOS($arregloTokens);

    $arregloTokens = insertarORs($arregloTokens);


//Prefijo "SELECT * FROM  $tabla WHERE
    $prefijoConsulta = consultaPrefijo($valCampos);


//Camposo a buscar [name, category,...]
    $columnas = campos($valCampos);

    $prefijo = ordenPrefijo($arregloTokens, $columnas);
    $arbol = construirArbol($prefijo);
    $consulta = evaluar($arbol, $columnas);
    $consulta = $prefijoConsulta.$consulta;
    return $consulta;
}
/*
//Obtener
$arregloTokens = tokenArray($cadena);


//Dentro de la funcion se modifica el arregloTokens para eliminar el token de CAMPOS y devolver su valor
$valCampos = preprosesarCAMPOS($arregloTokens);

$arregloTokens = insertarORs($arregloTokens);


//Prefijo "SELECT * FROM  $tabla WHERE
$prefijoConsulta = consultaPrefijo($valCampos);


//Camposo a buscar [name, category,...]
$columnas = campos($valCampos);

$arbolPrueba;
$consulta;

$prefijo = ordenPrefijo($arregloTokens, $columnas);
$arbol = construirArbol($prefijo);
$consulta = evaluar($arbol, $columnas);

$consulta = $prefijoConsulta.$consulta;

echo "<pre>";
//var_dump($nodoActual);
//var_dump($arregloTokens);
//var_dump($valCampos);
//var_dump($prefijo);
//var_dump($arbolPrueba);
echo ($consulta);
echo "</pre>";
*/




?>