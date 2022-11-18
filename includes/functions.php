<?php
// Fitxer que conté tots els helpers necessaris per treballar.
declare(strict_types=1);

//If accessed directly, redirect.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}

//Constants:
define('POSTS_PER_PAGE', 5);

//Validations:
function sanitStr(string $str) : string {

    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);

    return $str;
}

/**
 * Funció que valida les dades d'un formulari i retorna un array amb errors, refills i dades.
 *
 * @param array $inputData Dades del formulari amb l'estil de $_POST.
 * @param string $form Nom del formulari utilitzat per validar dades.
 * @return array Retorna les següents claus: 'data', 'errors', 'refill'.
 * 
 */ 
function validateData(array $inputData, string $form) : array {

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

//User-related functions:
/**
 * Funció que valida les dades d'un formulari i retorna un array amb errors, refills i dades.
 *
 * @param string $userEmail Dades del formulari amb l'estil de $_POST.
 * @param string $form Nom del formulari utilitzat per validar dades.
 * 
 */ 
function userExists(string $userEmail) : bool{
    return !empty(selectDB(array('table' => 'usuaris', 'fields' => ['email' => $userEmail])));
}

function isLogged() : bool {
    return isset($_SESSION['userData']['id']);
}

function getUserID() : int {
    return $_SESSION['userData']['id'] ?? 0;
}

function getUserData($email = '') : array {
    if ($email != '') {
        $user = selectDB(array('table' => 'usuaris', 'fields' => array('email' => $email, '...')));
        if (!empty($user)) {
            unset($user['password']);
            return $user;
        }else{
            return $user;
        }
    }else{
        return $_SESSION['userData'];
    }
}

//Taxonomies and entries related functions:
function getCategories($args = []) : array {
    $cats = selectDB(array('table' => 'categories', 'fields' => $args));

    //Si només dona un resultat.
    if (!empty($cats)) {
        if (count($cats) == count($cats, COUNT_RECURSIVE)) {
            $cats = array($cats);
        }
    }
    return $cats;
}

function categoryExists(string $catName) : bool{
    return !empty(getCategories(array('nombre' => $catName)));
}

function getEntries($pagination = true, $page = 0, $category = '') : array{
    $postsPerPage = POSTS_PER_PAGE;
    $fields = array('...');
    !empty($category) ? $fields['categoria_id'] = $category : null;

    if ($pagination) {
        $entries = selectDB(array('table' => 'entrades', 'fields' => $fields, 'order' => 'DESC', 'pagination' => array($page*$postsPerPage, $postsPerPage)));
    }else{
        $entries = selectDB(array('table' => 'entrades', 'fields' => $fields, 'order' => 'DESC'));
    }

    //Si només dona un resultat.
    if (!empty($entries)) {
        if (count($entries) == count($entries, COUNT_RECURSIVE)) {
            $entries = array($entries);
        }
    }
    return $entries;
}

function showEntry($entry) : string{

    $maxChar = 150;
    $catName = selectDB(array('table' => 'categories', 'fields' => array('id' => $entry['categoria_id'], '...')))['nombre'];
    $authorName = selectDB(array('table' => 'usuaris', 'fields' => array('id' => $entry['usuari_id'], '...')))['nom'];
    
    $html = '<div class="row entry-preview my-1"><div class="col-sm">
        <div class="header">
        <h4><a href="entrades.php?id='.$entry['id'].'">'.$entry['titol'].'</a></h4>
        <p><a href="categories.php?id='.$entry['categoria_id'].'">'.$catName.'</a> - Escrit per '.$authorName.' || '.$entry['data'].'</p>
        </div>
        <p>';
    
    $html  .= strlen($entry['descripcio']) > $maxChar ? substr($entry['descripcio'], 0, $maxChar-3) . '...' : $entry['descripcio'];
    $html.= '</p>
        <a href="entrades.php?id='.$entry['id'].'" class="read-more">Llegir més</a>
        </div></div>
    ';
    return $html;
}

function countEntries($catID = 0) : int{
    if ($catID != 0) {
        return sizeof(selectDB(array('table'=>'entrades', 'fields' => array('categoria_id' => $catID))));
    }else{
        return sizeof(selectDB(array('table'=>'entrades')));
    }
}

function getPaginationButtons($baseURL, $actualPage, $catID = 0) : string {
    $postsPerPage = POSTS_PER_PAGE;
    $nPosts = countEntries($catID);
    $page = 1;

    $appendChar = str_contains($baseURL, '?') ? '&' : '?';

    $html = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';
    for ($i=0; $i <= $nPosts; $i++) { 
        if ($page == $actualPage) {
            $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="'.$baseURL.$appendChar."p=$page".'">'.$page.'</a></li>';
        }else{
            $html .= '<li class="page-item"><a class="page-link" href="'.$baseURL.$appendChar."p=$page".'">'.$page.'</a></li>';
        }
        $i+=$postsPerPage;
        $page++;
    }

    $html .= '</ul></nav>';
    return $html;
}

//Database related functions:
function selectDB($args = ['table' => '', 'fields' => [], 'order' => 'DESC', 'pagination' => array(0, 0), 'operator' => '=']) : array{
    /*
    ->  Aquesta funció fa d'interfície per realitzar selects a la base de dades.
        - Permet especificar quins camps vols obtenir i filtrar directament el resultat.
        
    $args = [
        table = $tablename (string),
        *fields = $fields => $where (array),
        order = ASC|DESC (string),
        pagination = array(offset, number of elements)
    ]

    *fields => If $where is empty, there will be no "where" condition. Use '...' to select all fields.

    */

    //Array a retornar:
    $data = [];

    //Function variables:
    $fields = '*';
    $where = '';
    $whereValues = ['types' => [], 'values' => []];
    $order = $args['order'] ?? 'ASC';
    $pagination = isset($args['pagination']) ? 'LIMIT '.implode(", ", $args['pagination']) : '';
    $op = $args['operator'] ?? '=';

    //If there's no table, return void array.
    if (empty($args['table'])) {
        return $data;
    }


    $table = $args['table'];

    //Obtenir paràmetres SELECT i WHERE:
    if (!empty($args['fields'])){

        //Obtenir el paràmetre del SELECT:
        if (!in_array('...',array_values($args['fields']))) {
            $fields = implode(', ',array_keys($args['fields']));
        }else{
            unset($args['fields'][array_search('...', $args['fields'])]);
        }
        

        //Obtenir el paràmetre WHERE:
        foreach ($args['fields'] as $key => $value) {
            if (!empty($value)){

                //Muntar la WHERE condition de la query:
                $where.= empty($where) ? 'WHERE ' : ' AND ';
                $where.= "`$key` $op ?";

                //Muntar una array per lligar els paràmetres (fer el BIND).
                if (gettype($value) == 'integer') {
                    array_push($whereValues['types'], 'i');
                    array_push($whereValues['values'], intval($value));
                }else{
                    array_push($whereValues['types'], str_contains($key, 'id') ? 'i' : 's');
                    array_push($whereValues['values'], $value);
                }
                
            }
        }
    }

    //REALITZAR LA CONSULTA:
    if(isset($conn)){
        global $conn;
    }else{
        require 'connect.php';
    }

    //Muntar la query:
    $qry = "SELECT $fields FROM $table $where ORDER BY `id` $order $pagination";

    //Executar consulta:    
    try {
        $consulta = $conn->prepare($qry);
        !empty($where) ? $consulta->bind_param(implode($whereValues['types']),...$whereValues['values']) : null;
        $consulta->execute();
    } catch (mysqli_sql_exception $e){
        print_r($e);
        $conn->close();
        return $data;
    }
    
    $result = $consulta->get_result();
    $conn->close();

    //Crear array amb les dades:
    $dades = array();
    while ($reg = $result->fetch_assoc()) {  
        
        array_push($dades, $reg);    
    }

    return sizeof($dades) == 1 ? $dades[0] : $dades;

}

function insertDB($args = ['table' => '', 'fields' => []]) : bool{
  /*
    ->  Aquesta funció fa d'interfície per insertar registres a la base de dades.
        - Retorna booleà segons si ha pogut insertar o no.
        
    $args = [
        table = $tablename (string),
        *fields = $field => $value (array),
    ]

    *fields => Si falten camps, la funció retorna fals.

    */


    //Function variables:

    //If there's no table, return false.
    if (empty($args['table'])) {
        return false;
    }

    $table = $args['table'];

    //Si no tenim dades a insertar, retorna fals.
    if (empty($args['fields'])){
        return false;
    }

    //Muntar la relació camp-valor:
    $columns = array_keys($args['fields']);
    $valueTypes = ['types' => [], 'values' => []];
    foreach ($args['fields'] as $key => $value) {

        //Muntar una array per lligar els paràmetres (fer el BIND).
        if (gettype($value) == 'integer') {
            array_push($valueTypes['types'], 'i');
            array_push($valueTypes['values'], intval($value));
        }else{
            array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
            array_push($valueTypes['values'], $value);
        }
            
    }


    //REALITZAR EL INSERT:
    if(isset($conn)){
        global $conn;
    }else{
        require 'connect.php';
    }

    //Muntar la query:
    $qry = "INSERT INTO $table (".implode(", ",$columns).") VALUES (".implode(", ",array_map(function(){return '?';},$columns)).")";
    
    //Executar consulta:    
    try {
        $consulta = $conn->prepare($qry);
        $consulta->bind_param(implode($valueTypes['types']),...$valueTypes['values']);
        $consulta->execute();
    } catch (mysqli_sql_exception){
        $conn->close();
        return false;
    }
    $conn->close();
    return true;
    
}

function updateDB($args = ['table' => '', 'fields' => [], 'where' => []]) : bool{
    /*
      ->  Aquesta funció fa d'interfície per insertar registres a la base de dades.
          - Retorna booleà segons si ha pogut insertar o no.
          
      $args = [
          table = $tablename (string),
          *fields = $field => $value (array),
      ]
  
      *fields => Si falten camps, la funció retorna fals.
  
      */
  
  
      //Function variables:
  
      //If there's no table, return false.
      if (empty($args['table']) || empty($args['where'])) {
          return false;
      }
  
      $table = $args['table'];
      $where = $args['where'];
  
      //Si no tenim dades a actualitzar, retorna fals.
      if (empty($args['fields'])){
          return false;
      }
  
      //Muntar la relació camp-valor:
      $columns = array_keys($args['fields']);
      $valueTypes = ['types' => [], 'values' => []];

      foreach ($args['fields'] as $key => $value) {
  
          //Muntar una array per lligar els paràmetres (fer el BIND).
          if (gettype($value) == 'integer') {
              array_push($valueTypes['types'], 'i');
              array_push($valueTypes['values'], intval($value));
          }else{
              array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
              array_push($valueTypes['values'], $value);
          }
              
      }

      foreach ($where as $key => $value) {
  
        //Muntar una array per lligar els paràmetres del WHERE.
        if (gettype($value) == 'integer') {
            array_push($valueTypes['types'], 'i');
            array_push($valueTypes['values'], intval($value));
        }else{
            array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
            array_push($valueTypes['values'], $value);
        }
            
    }
  
  
      //REALITZAR EL INSERT:
      if(isset($conn)){
          global $conn;
      }else{
          require 'connect.php';
      }

  
      //Muntar la query:
      $qry = "UPDATE $table SET ".implode(", ",array_map(function($col){return "$col = ?";}, $columns))." WHERE ".implode(" AND ",array_map(function($key){return "$key = ?";}, array_keys($where)))."";

      //Executar consulta:    
       try {
           $consulta = $conn->prepare($qry);
           $consulta->bind_param(implode($valueTypes['types']),...$valueTypes['values']);
           $consulta->execute();
       } catch (mysqli_sql_exception $e){
        print_r($e);
           $conn->close();
           return false;
       }
       $conn->close();
       return true;
      
}

function deleteDB($args = ['table' => '', 'where' => []]) : bool{
    /*
      ->  Aquesta funció fa d'interfície per insertar registres a la base de dades.
          - Retorna booleà segons si ha pogut insertar o no.
          
      $args = [
          table = $tablename (string),
          *fields = $field => $value (array),
      ]
  
      *fields => Si falten camps, la funció retorna fals.
  
      */
  
  
      //Function variables:
  
      //If there's no table, return false.
      if (empty($args['table']) || empty($args['where'])) {
          return false;
      }
  
      $table = $args['table'];
      $where = $args['where'];
  
  
      //Muntar la relació camp-valor:
      $columns = array_keys($args['where']);
      $valueTypes = ['types' => [], 'values' => []];

      foreach ($args['where'] as $key => $value) {
  
          //Muntar una array per lligar els paràmetres (fer el BIND).
          if (gettype($value) == 'integer') {
              array_push($valueTypes['types'], 'i');
              array_push($valueTypes['values'], intval($value));
          }else{
              array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
              array_push($valueTypes['values'], $value);
          }
              
      }
  
  
      //REALITZAR EL DELETE:
      if(isset($conn)){
          global $conn;
      }else{
          require 'connect.php';
      }
  
      //Muntar la query:
      $qry = "DELETE FROM $table WHERE ".implode(" AND ",array_map(function($key){return "$key = ?";}, array_keys($where)))."";

      //Executar consulta:    
       try {
           $consulta = $conn->prepare($qry);
           $consulta->bind_param(implode($valueTypes['types']),...$valueTypes['values']);
           $consulta->execute();
       } catch (mysqli_sql_exception $e){
        print_r($e);
           $conn->close();
           return false;
       }
       $conn->close();
       return true;
      
}

//Miscelanea:
function getPageName() : string{
    //Funció que retorna el nom de la pàgina.
    global $pageName;

    return "$pageName - Music4Hacks";
}

function getErrorsAlert($errors) : string{
    //Funció que retorna un HTML amb errors.
    $html = '<div class="row">';

    foreach ($errors as $error => $msg) {
        switch ($error) {
            case 'actionSuccess':
                $html .= '<div class="alert alert-success" role="alert">' . $msg . '</div>';
                break;
            case 'actionFailed':
                $html .= '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                break;
        }
    }
    
    return $html .= '</div>';
}

function translateKeys($array, $keys) : array{
    //$keys = [oldKey] => 'newKey';

    $arrReturn = array();
    foreach ($keys as $oldKey => $newKey) {
        $arrReturn[$newKey] = $array[$oldKey];
    }
    return $arrReturn;
}