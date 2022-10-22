<?php
// Fitxer que conté la capalera del blog.
declare(strict_types=1);

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
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <title><?= getPageName(); ?></title>
</head>
<body>
    <header class="container-fluid py-4 text-bg-dark text-center">
        <div class="container-fluid slogan">
            <h1>Music4Hacks</h1>
            <h2 class="small">El blog que mostra com hackejar la música.</h2>
        </div>
        <nav class="navbar navbar-expand-sm navbar-dark">
            <ul class="nav nav-sm">
                <li class="nav-item"><a href="#" class="nav-link active text-light">Inici</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-light">Tutorials</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-light">Partitures</a></li>
                <li class="nav-item"><a href="#" class="nav-link disabled">Sobre nosaltres</a></li>
                <li class="nav-item"><a href="#" class="nav-link disabled">Contacte</a></li>
            </ul>
        </nav>
    </header>
    
