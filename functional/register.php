<?php
// Fitxer per registrar un nou usuari.
declare(strict_types=1);

//If accessed directly, redirect.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

//Start Session.
if (!isset($_SESSION)) {
    session_start();
}

//If accessed directly.
if (!isset($_POST['registerUser'])) {
    header("Location: ../index.php");
}

require_once '../includes/functions.php';

//Validate Data:
unset($_POST['registerUser']);
$registerData = validateData($_POST, 'registerUser');

//Volcar errors i refills:
$_SESSION['errors'] = $registerData['errors'];
$_SESSION['refill'] = $registerData['refill'];
$userData = $registerData['data'];

//Si hi han errors de validació, redirigir al index.
if (!empty($registerData['errors'])) {
    header("Location: ../index.php");
}else{
    unset($_SESSION['refill']);

    //Comprovar si l'usuari existeix o no.
    if (userExists($userData['email'])) {
        $_SESSION['errors']['registerFailed'] = "ERROR: Aquest usuari ja existeix!";
    }else{

        //Executar la consulta:
        $userData['date'] = date("Y-m-d");

        $consulta = insertDB(
            array('table' => 'usuaris', 
            'fields' => array(
                'nom' => $userData['name'], 
                'cognom' => $userData['surname'], 
                'email' => $userData['email'], 
                'password' => $userData['passwd'], 
                'data' => $userData['date'])
                )
            );
        
        //Si resposta segons si hi ha hagut o no error:
        if($consulta){
            $_SESSION['errors']['registerSuccess'] = "Usuari registrat correctament!";
        }else{
            $_SESSION['errors']['registerFailed'] = "ERROR: No s'ha pogut registrar l'usuari.";
        }
    }
    

    header("Location: ../index.php");
}

