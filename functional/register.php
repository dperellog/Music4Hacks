<?php
// Fitxer per registrar un nou usuari.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

require_once '../includes/functions.php';

if (!isset($_POST['registerUser'])) {
    header("Location: ../index.php");
}

//Funcions per validar els inputs:
$regExp = [
    'name' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v);},
    'surname' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v);},
    'email' => function($v){
        $v = filter_var($v, FILTER_SANITIZE_EMAIL); 
        return filter_var($v, FILTER_VALIDATE_EMAIL);},
    'passwd' => function($v){
        return preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/",$v) ;}
];


//Descarregar resultats i neteja:
$resultats = [];
foreach ($_POST as $dada => $valor) {
    if (is_string($dada)){
        $resultats[sanitStr($dada)] = sanitStr($valor);
    }
}

$_SESSION['errors'] = array();

//Comprovar que hi ha informació de tots els inputs:
    foreach (array_keys($regExp) as $clau) {
        if (!in_array($clau, array_keys($resultats))) {
            $_SESSION['errors'][$clau] = true;
        }
    }

//Comprovar que tots els inputs són correctes:
foreach ($resultats as $camp => $valor) {
    if (in_array($camp, array_keys($regExp)) && !$regExp[$camp]($valor) || empty($valor)) {
       $_SESSION['errors'][$camp] = true;
    }else{
        if (!in_array($valor, array('passwd'))){
            $_SESSION['refill'][$camp] = $valor;
        }
        if ($camp == 'passwd'){
            $resultats['passwd'] = password_hash($resultats['passwd'], PASSWORD_BCRYPT, ['cost'=>4]);
        }
    }
    
}

if (!empty($_SESSION['errors'])) {
    header("Location: ../index.php");
}else{
    $_SESSION['errors']['registerStatus'] = registerUser($resultats);
    header("Location: ../index.php");
}

