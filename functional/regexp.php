<?php
// Fitxer que conté el diccionari de funcions per validar cadenes.
declare(strict_types=1);

//If accessed directly, redirect.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

include_once '../includes/functions.php';

$regExp['registerUser'] = [
    'name' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);},
    'surname' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);},
    'email' => function($v){
        $v = filter_var($v, FILTER_SANITIZE_EMAIL); 
        return filter_var($v, FILTER_VALIDATE_EMAIL);},
    'passwd' => function($v){
        return preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/",$v) ;}
];

$regExp['loginUser'] = [
    'loginEmail' => function($v){
        $v = filter_var($v, FILTER_SANITIZE_EMAIL); 
        return filter_var($v, FILTER_VALIDATE_EMAIL);},
    'loginPasswd' => function($v){
    return strlen($v) > 0 ;}
];

$regExp['newCategory'] = [
    'categoryName' => function($v){
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);}
];

$regExp['newEntry'] = [
    'entryName' => function($v){
        return preg_match("/^[a-zA-Z0-9 ]*$/",$v) && !empty($v);},
    'entryDescription' => function($v){
        return !empty($v);},
    'entryCat' => function($v){
        return in_array($v, array_map(function($cat){ return $cat['id'];},getCategories()));},
];