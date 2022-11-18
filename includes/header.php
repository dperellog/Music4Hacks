<?php
// Fitxer que conté la capalera del blog.
declare(strict_types=1);

//If accessed directly, redirect.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

$pageName = $pageName ?? 'Music4Hacks';

require_once 'functions.php';

if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ca-ES">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
    <title><?= getPageName(); ?></title>
</head>
<body>
    <div class="container-fluid">
    <header class="row py-4 text-bg-dark text-center">
        <div class="row align-items-center slogan">
            <a href="index.php"><h1>Music4Hacks</h1></a>
            <h2 class="small">El blog que mostra com hackejar la música.</h2>
        </div>
        <div class="row px-4">
            <nav class=" navbar navbar-expand-sm navbar-dark px-2">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <?php 
                    foreach (getCategories() as $categoria) {
                        echo '<li class="nav-item"><a href="categories.php?id='.$categoria['id'].'" class="nav-link active">'.$categoria['nombre'].'</a></li>';
                    } 
                    ?>
                    <li class="nav-item"><a href="aboutus.php" class="nav-link">Sobre nosaltres</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contacte</a></li>
                </ul>
            </div>
            </nav>
        </div>
    </header>
    <div class="row my-4 mx-2 page-content">
    
