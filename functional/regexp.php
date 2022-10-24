<?php
// Fitxer que conté el diccionari de funcions per validar cadenes.
declare(strict_types=1);

$regExp['registerUser'] = [
    'name' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v);},
    'surname' => function($v){ 
        return preg_match("/^[a-zA-Z ]*$/",$v);},
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