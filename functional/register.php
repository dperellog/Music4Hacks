<?php
// Fitxer per registrar un nou usuari.
declare(strict_types=1);

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

if (!empty($registerData['errors'])) {
    header("Location: ../index.php");
}else{
    unset($_SESSION['refill']);
    if (userExists($userData['email'])) {
        $_SESSION['errors']['registerFailed'] = "ERROR: Aquest usuari ja existeix!";
    }else{
        require '../includes/connect.php';
        //Preparar la consulta:
        $qry = 'INSERT INTO usuaris (nom,cognom,email,`password`,`data`) VALUES (?,?,?,?,?)';
        $consulta = $conn->prepare($qry);

        //Executar la consulta:
        $resultats['date'] = date("Y-m-d");
        $consulta->bind_param('sssss',$userData['name'], $userData['surname'], $userData['email'], $userData['passwd'], $userData['date']);
        $consulta->execute();
        $consulta->close();
        $conn->close();
        
        //Si resposta segons si hi ha hagut o no error:
        if($consulta->errno == 0){
            $_SESSION['errors']['registerSuccess'] = "Usuari registrat correctament!";
        }else{
            $_SESSION['errors']['registerFailed'] = "ERROR: No s'ha pogut registrar l'usuari.";
        }
    }

    header("Location: ../index.php");
}

