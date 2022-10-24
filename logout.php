<?php
// Fiter per fer Logout:
declare(strict_types=1);

if (!isset($_SESSION)) {
session_start();
}

session_destroy();

header("Location: index.php");