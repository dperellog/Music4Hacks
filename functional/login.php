<?php
// Fitxer que conté les utilitats per iniciar sessió d'un usuari.
declare(strict_types=1);

//If accessed directly, redirect.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

//Start session:
if (!isset($_SESSION)) {
session_start();
}

//If accessed directly, redirect index.
if (!isset($_POST['loginUser'])) {
    header("Location: ../index.php");
}

require_once '../includes/functions.php';

//Descarregar dades:
unset($_POST['loginUser']);
$userData = validateData($_POST, 'loginUser');

//Volcar errors i refills:
$_SESSION['errors'] = $userData['errors'];
$_SESSION['refill']['loginEmail'] = $userData['refill']['loginEmail'];
$userData = $userData['data'];

if (sizeof($_SESSION['errors']) > 0) {
    header("Location: ../index.php");
}else{

//Obtenir l'usuari de la BBDD:
    $userEmail = $userData['loginEmail'];
    $usuari = selectDB(array('table' => 'usuaris', 'fields' => array('email' => $userEmail, '...')));

    if (!empty($usuari)) {
        //Si existeix un usuari, selecciona'l del array.
        $usuari = $usuari;

        //Check passwd:
        if (password_verify($userData['loginPasswd'], $usuari['password'])) {
            unset($usuari['password']);
            $_SESSION['errors']['loginSuccess'] = true;
            $_SESSION['userData'] = $usuari;
        }else{
            $_SESSION['errors']['loginIncorrect'] = true;
        }
        
    }else{
        $_SESSION['errors']['loginIncorrect'] = true;
    }
    header("Location: ../index.php");
}