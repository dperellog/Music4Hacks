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

function registerUser($usuari){
    
    if(isset($conn)){
        global $conn;
    }else{
        require 'connect.php';
    }

    if (userExists($usuari['email'])) {
        return false;
    }else{
    //Preparar la consulta:
    $qry = 'INSERT INTO usuaris (nom,cognom,email,`password`,`data`) VALUES (?,?,?,?,?)';
    $consulta = mysqli_prepare($conn, $qry);

    //Executar la consulta:
    $usuari['date'] = date("Y-m-d");
    mysqli_stmt_bind_param($consulta,'sssss',$usuari['name'], $usuari['surname'], $usuari['email'], $usuari['passwd'], $usuari['date']);
    mysqli_stmt_execute($consulta);
    mysqli_close($conn);
    
    return mysqli_stmt_get_result($consulta) == '';
    }
}

function userExists($userEmail){
    if(isset($conn)){
        global $conn;
    }else{
        require 'connect.php';
    }


    //Preparar consulta dels usuaris
    $qry = 'SELECT * FROM usuaris WHERE email = ?';
    $consulta = mysqli_prepare($conn, $qry);

    //Fer consulta:
    mysqli_stmt_bind_param($consulta,'s',$userEmail);
    mysqli_stmt_execute($consulta);
    $result = (mysqli_stmt_get_result($consulta));
    mysqli_close($conn);

    //Resultat:
    return $result->num_rows != 0;
}