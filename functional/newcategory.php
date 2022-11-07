<?php
// Fitxer que verifica que
declare(strict_types=1);

//If accessed directly, redirect.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

if (!isset($_SESSION)) {
session_start();
}   

if (!isset($_POST['newCategory'])) {
header("Location: ../index.php");
}

require_once '../includes/functions.php';

//Validate Data:
unset($_POST['newCategory']);
$validatedData = validateData($_POST, 'newCategory');

//Volcar errors i refills:
$_SESSION['errors'] = $validatedData['errors'];
$_SESSION['refill'] = $validatedData['refill'];
$catData = $validatedData['data'];


if (!empty($validatedData['errors'])) {
    header("Location: ../categories.php?action=create");
}else{

    if (categoryExists($catData['categoryName'])) {
        $_SESSION['errors']['newCatFailed'] = 'ERROR: La categoria ja existeix!';
        header("Location: ../categories.php?action=create");
    }else{
        $consulta = insertDB(array('table' => 'categories', 'fields' => array('nombre' => $catData['categoryName'])));
    }
    
    //Si resposta segons si hi ha hagut o no error:
    if($consulta){
        $_SESSION['errors']['newCatSuccess'] = true;
    }else{
        $_SESSION['errors']['newCatFailed'] = 'ERROR: No s\'ha pogut crear la categoria.';
    }

    header("Location: ../categories.php?action=create");
    }
