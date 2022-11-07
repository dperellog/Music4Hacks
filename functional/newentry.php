<?php
// Fitxer que verifica que
declare(strict_types=1);

//If accessed directly, redirect.
// $pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
// if (end($pageRequired) == basename(__FILE__)) {
// header("Location: ../index.php");
// }

if (!isset($_SESSION)) {
session_start();
}   

if (!isset($_POST['newEntry'])) {
header("Location: ../index.php");
}

require_once '../includes/functions.php';

//Validate Data:
unset($_POST['newEntry']);
$validatedData = validateData($_POST, 'newEntry');

//Volcar errors i refills:
$_SESSION['errors'] = $validatedData['errors'];
$_SESSION['refill'] = $validatedData['refill'];
$entryData = $validatedData['data'];


if (!empty($validatedData['errors'])) {
    header("Location: ../entrades.php?action=create");

}else{

    $consulta = insertDB(array('table' => 'entrades', 
    'fields' => array(
        'usuari_id' => getUserID(), 
        'categoria_id' => $entryData['entryCat'],
        'titol' => $entryData['entryName'],
        'descripcio' => $entryData['entryDescription'],
        'data' => date("Y-m-d")
        )
    ));

    //Si resposta segons si hi ha hagut o no error:
    if($consulta){
        $_SESSION['errors']['newEntrySuccess'] = true;
    }else{
        $_SESSION['errors']['newEntryFailed'] = 'ERROR: No s\'ha pogut crear l\'entrada.';
    }

    header("Location: ../entrades.php?action=create");
}

