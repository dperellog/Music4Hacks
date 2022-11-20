<?php

//Si s'accedeix directament al fitxer, redirigir.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

//Dades per accedir a la BBDD:
$servername = "localhost";
$username = "blog";
$password = "rLgIFGlfhxUuLxLX";
$dbname = "blog";

//Defineixo un controlador per obtenir sempre que hi hagi un error una excepció de tipus "mysqli_sql_exception".
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
