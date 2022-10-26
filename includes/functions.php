<?php
// Fitxer que conté tots els helpers necessaris per treballar.
declare(strict_types=1);

function getPageName() : string{
    global $pageName;

    return "$pageName - Music4Hacks";
}

function sanitStr($str) : string {

    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);

    return $str;
}

function validateData($inputData, $form) : array {
    $results = array();
    $errors = array();
    $refill = array();

    //Funcions per validar els inputs:
    require_once '../functional/regexp.php';

    $regExp = $regExp[$form]; 
    //Descarregar resultats i neteja:
    foreach ($inputData as $dada => $valor) {
        if (is_string($dada)){
            $results[sanitStr($dada)] = sanitStr($valor);
        }
    }

    //Comprovar que hi ha informació de tots els inputs:
        foreach (array_keys($regExp) as $clau) {
            if (!in_array($clau, array_keys($inputData))) {
                $errors[$clau] = true;
            }
        }

    //Comprovar que tots els inputs són correctes:
    foreach ($inputData as $camp => $valor) {
        if (in_array($camp, array_keys($regExp)) && !$regExp[$camp]($valor)) {
        $errors[$camp] = true;
        }else{
            if (!in_array($valor, array('passwd'))){
                $refill[$camp] = $valor;
            }
            if ($camp == 'passwd'){
                $results['passwd'] = password_hash($results['passwd'], PASSWORD_BCRYPT, ['cost'=>4]);
            }
        }
    }

    return array('data' => $results, 'errors' => $errors, 'refill' => $refill);
}


function userExists($userEmail) : bool{
    if(isset($conn)){
        global $conn;
    }else{
        require 'connect.php';
    }

    //Preparar consulta dels usuaris
    $qry = 'SELECT * FROM usuaris WHERE email = ?';
    $consulta = $conn->prepare($qry);

    //Fer consulta:
    $consulta->bind_param('s',$userEmail);
    $consulta->execute();
    $result = $consulta->get_result();
    $conn->close();

    //Resultat:
    return $result->num_rows != 0;
}

function isLogged() : bool {
    return isset($_SESSION['userData']['id']);
}

function getCategories() : array {
    if(isset($conn)){
        global $conn;
    }else{
        require 'connect.php';
    }

    //Preparar consulta dels usuaris
    $qry = 'SELECT * FROM categories';
    $consulta = $conn->prepare($qry);

    //Fer consulta:
    $consulta->execute();
    $result = $consulta->get_result();
    $conn->close();

    //Crear array:
    $dades = array();
    while ($reg = $result->fetch_assoc()) {
        array_push($dades, $reg);
    }

    return $dades;
}