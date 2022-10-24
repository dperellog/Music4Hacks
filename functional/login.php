<?php
// Fitxer que conté les utilitats per iniciar sessió d'un usuari.
declare(strict_types=1);

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
    require_once '../includes/connect.php';

    //Preparar la consulta:
    $qry = 'SELECT * FROM usuaris WHERE email = ?';
    $consulta = $conn->prepare($qry);

    //Executar la consulta:
    $consulta->bind_param('s',$userData['loginEmail']);
    $consulta->execute();
    $results = $consulta->get_result();
    $consulta->close();

    if ($results->num_rows > 0) {
        $usuari = $results->fetch_assoc();

        //Check passwd:
        if (password_verify($userData['loginPasswd'], $usuari['password'])) {
            unset($usuari['password']);
            $_SESSION['errors']['loginSuccess'] = true;
            $_SESSION['userData'] = $usuari;
        }else{
            $_SESSION['errors']['loginIncorrect'] = true;
        }
        
    }else{
        echo "not found";
        $_SESSION['errors']['loginIncorrect'] = true;
    }
    header("Location: ../index.php");
}