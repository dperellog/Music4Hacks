<?php
// Fitxer que conté el diccionari de funcions per validar cadenes.
declare(strict_types=1);

//Si s'accedeix directament al fitxer, redirigir.
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
$regExp['editCategory'] = [
    'categoryName' => function($v){
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);},
    'catId' => function($v){
        return is_numeric($v);}
];

$regExp['deleteCategory'] = [
    'categoryId' => function($v){
        return is_numeric($v);}
];

$regExp['newEntry'] = [
    'entryName' => function($v){
        return preg_match("/^[a-zA-Z0-9\.\!\,\:\' ]*$/",$v) && !empty($v);},
    'entryDescription' => function($v){
        return !empty($v);},
    'entryCat' => function($v){
        return in_array($v, array_map(function($cat){ return $cat['id'];},getCategories()));},
];

$regExp['editEntry'] = [
    'entryName' => function($v){
        return preg_match("/^[a-zA-Z0-9\.\!\,\:\' ]*$/",$v) && !empty($v);},
    'entryDescription' => function($v){
        return !empty($v);},
    'entryCat' => function($v){
        return in_array($v, array_map(function($cat){ return $cat['id'];},getCategories()));},
    'entryId' => function($v){
        return is_numeric($v);}
];

$regExp['deleteEntry'] = [
    'entryId' => function($v){
        return is_numeric($v);}
];

$regExp['updateUserData'] = [
    'name' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);},
    'surname' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);},
    'email' => function($v){
        $v = filter_var($v, FILTER_SANITIZE_EMAIL); 
        return filter_var($v, FILTER_VALIDATE_EMAIL);}
];

$regExp['contactMsg'] = [
    'contactName' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v) && !empty($v);},
    'contactEmail' => function($v){
        $v = filter_var($v, FILTER_SANITIZE_EMAIL); 
        return filter_var($v, FILTER_VALIDATE_EMAIL);},
    'contactMessage' => function($v){
        return !empty($v);}
];