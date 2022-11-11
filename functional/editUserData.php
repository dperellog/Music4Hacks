<?php
// Fitxer que conté les utilitats per modificar les dades d'un usuari.
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
if (!isset($_POST['updateUserData'])) {
    header("Location: ../index.php");
}

require_once '../includes/functions.php';

//Descarregar dades:
unset($_POST['updateUserData']);
$userData = validateData($_POST, 'registerUser');

unset($userData['errors']['passwd']);

//Volcar errors i refills:
$_SESSION['errors'] = $userData['errors'];
$userData = $userData['data'];

if (sizeof($_SESSION['errors']) > 0) {
    header("Location: ../userpage.php");
}else{

    if (getUserData()['id'] != getUserData($userData['email'])['id']) {
        $_SESSION['errors']['modifyUserDataFailed'] = 'Ja hi ha un usuari registrat amb aquest correu!';
        header("Location: ../userpage.php");
    }else{
        //Actualitzar a la BBDD:
        updateDB(array('table' => 'usuaris', 
        'fields' => array('nom' => $userData['name'], 'cognom' => $userData['surname'], 'email' => $userData['email']),
        'where' => array('id' => getUserData()['id'])));

        //Actualitzar la variable Session:
        $userData = getUserData($userData['email']);
        unset($userData['password']);
        $_SESSION['userData'] = $userData;
        
        $_SESSION['errors']['modifyUserDataSuccess'] = true;
        header("Location: ../userpage.php");
    }

}