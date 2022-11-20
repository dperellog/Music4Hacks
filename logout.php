<?php
// Fitxer per fer Logout:
declare(strict_types=1);

//Destruir sessió i redirigir al header.
if (!isset($_SESSION)) {
    session_start();
}

session_destroy();

header("Location: index.php");
