<?php
// Fitxer que conté tots els helpers necessaris per treballar.
declare(strict_types=1);

//Si s'accedeix directament al fitxer, redirigir.
$pageRequired = explode('/', $_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
    header("Location: ../index.php");
}

//Constants:
define('POSTS_PER_PAGE', 5);
define('MAX_CHAR_PREVIEW_ENTRY', 300);
define('ADMIN_EMAIL', 'david@localhost');

//Validation-related functions:
function sanitStr(string $str): string {
    //Funció encarregada de sanititzar cadenes

    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);

    return $str;
}


function validateData(array $inputData, string $form): array {
    //Funció que s'encarrega de sanititzar i validar les dades dels formularis.
    //Retorna una array associativa multidimensional amb tots els errors trobats, refills i dades validades.

    $results = array();
    $errors = array();
    $refill = array();

    //Obtenir les funcions per validar els inputs del formulari en questió:
    require_once '../functional/regexp.php';
    $regExp = $regExp[$form];

    //Netejar les dades que s'han de validar:
    foreach ($inputData as $dada => $valor) {
        if (is_string($dada)) {
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
        } else {
            if (!in_array($valor, array('passwd'))) {
                $refill[$camp] = $valor;
            }
            if ($camp == 'passwd') {
                $results['passwd'] = password_hash($results['passwd'], PASSWORD_BCRYPT, ['cost' => 4]);
            }
        }
    }

    //Retrnar l'array amb els errors, refills i resultats.
    return array('data' => $results, 'errors' => $errors, 'refill' => $refill);
}

//User-related functions:
function userExists(string $userEmail): bool {
    //Funció que comprova si un usuari existeix o no.
    return !empty(selectDB(array('table' => 'usuaris', 'fields' => ['email' => $userEmail])));
}

function isLogged(): bool {
    //Funció que comprova si l'usuari actual està loguejat o no.
    return isset($_SESSION['userData']['id']);
}

function getUserID(): int {
    //Funció que retorna la ID de l'usuari actual.
    return $_SESSION['userData']['id'] ?? 0;
}

function getUserData($email = ''): array {
    //Funció que ens retorna un array amb les dades d'un usuari.
    //Per defecte, si no s'especifica correu són de l'usuari actual (extretes de la sessió).

    if ($email != '') {
        $user = selectDB(array('table' => 'usuaris', 'fields' => array('email' => $email, '...')));
        if (!empty($user)) {
            unset($user['password']);
            return $user;
        } else {
            return $user;
        }
    } else {
        return $_SESSION['userData'];
    }
}

//Taxonomies and entries related functions:
function getCategories($args = []): array {
    //Funció que retorna un llistat amb les categories de la BBDD.
    $cats = selectDB(array('table' => 'categories', 'fields' => $args), true);

    return $cats;
}

function categoryExists(string $catName): bool {
    //Funció que retorna si una categoria en particular existeix.
    return !empty(getCategories(array('nombre' => $catName)));
}

function getEntries($pagination = true, $page = 0, $category = ''): array {
    //Funció que retorna totes les entrades segons les condicions dels paràmetres passats.
    //Per defecte retorna totes les categories del blog paginades.

    //Monta l'array dels filtres:
    $fields = array('...');
    !empty($category) ? $fields['categoria_id'] = $category : null;

    //Segons si s'han de paginar o no, fes una consulta o una altra.
    if ($pagination) {
        $entries = selectDB(array('table' => 'entrades', 'fields' => $fields, 'order' => 'DESC', 'pagination' => array($page * POSTS_PER_PAGE, POSTS_PER_PAGE)), true);
    } else {
        $entries = selectDB(array('table' => 'entrades', 'fields' => $fields, 'order' => 'DESC'), true);
    }

    return $entries;
}

function showEntry($entry): string {
    //Funció que retorna un HTML amb la previsualització d'una entrada.

    $maxChar = MAX_CHAR_PREVIEW_ENTRY; //Nombre màxim de caràcters a mostrar en la previsualització.

    //Obtenir dades auxiliars de l'entrada.
    $catName = selectDB(array('table' => 'categories', 'fields' => array('id' => $entry['categoria_id'], '...')))['nombre'];
    $authorName = selectDB(array('table' => 'usuaris', 'fields' => array('id' => $entry['usuari_id'], '...')))['nom'];

    $html = '<div class="row entry-preview my-1"><div class="col-sm">
        <div class="header">
        <h4><a href="entrades.php?id=' . $entry['id'] . '">' . $entry['titol'] . '</a></h4>
        <p><a href="categories.php?id=' . $entry['categoria_id'] . '">' . $catName . '</a> - Escrit per ' . $authorName . ' || ' . $entry['data'] . '</p>
        </div>
        <p>';

    $html  .= strlen($entry['descripcio']) > $maxChar ? substr($entry['descripcio'], 0, $maxChar - 3) . '...' : $entry['descripcio'];
    $html .= '</p>
        <a href="entrades.php?id=' . $entry['id'] . '" class="read-more">Llegir més</a>
        </div></div>
    ';
    return $html;
}

function countEntries($catID = 0): int {
    //Funció que comptabilitza les entrades d'una categoria en concret.
    //Per defecte comptabilitza totes les entrades del blog.

    if ($catID != 0) {
        return sizeof(selectDB(array('table' => 'entrades', 'fields' => array('categoria_id' => $catID)), alwaysArray: true));
    } else {
        return sizeof(selectDB(array('table' => 'entrades'), alwaysArray: true));
    }
}

function getPaginationButtons($baseURL, $actualPage, $catID = 0): string {
    //Funció que retorna un HTML amb els botons de paginació.

    //Iniciar variables utilitzades a la funció.
    $postsPerPage = POSTS_PER_PAGE;
    $nPosts = countEntries($catID);
    $page = 1;
    $appendChar = str_contains($baseURL, '?') ? '&' : '?';
    $html = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';

    //Muntar la llista dels botons:
    for ($i = 0; $i <= $nPosts; $i++) {
        if ($page == $actualPage) {
            $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="' . $baseURL . $appendChar . "p=$page" . '">' . $page . '</a></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseURL . $appendChar . "p=$page" . '">' . $page . '</a></li>';
        }
        $i += $postsPerPage;
        $page++;
    }

    $html .= '</ul></nav>';
    return $html;
}

//Database related functions:
function selectDB($args = ['table' => '', 'fields' => [], 'order' => 'DESC', 'pagination' => array(0, 0), 'operator' => '='], $alwaysArray = false): array {
    /*
    ->  Aquesta funció fa d'interfície per realitzar selects a la base de dades.
        - Permet especificar quins camps vols obtenir i filtrar directament el resultat.
        - Retorna una array buida si no troba resultats.
        
    $args = [
        table = $tablename (string),
        *fields = $fields => $where (array),
        order = ASC|DESC (string),
        pagination = array(offset, number of elements)
        operator = MySQL operators (string)
    ]

    *fields => If $where is empty, there will be no "where" condition. Use '...' to select all fields:

    Com funciona el paràmetre de "fields"?
        - Per defecte ens selecciona tots els camps que ESTIGUIN dins de l'array de fields.
            - Si volem obtenir tots els camps i a part aplicar un filtre WHERE, podem incloure un valor sense clau
              identic a '...'.
        - La funció utilitza la combinació de clau-valor de fields per muntar el WHERE.
            - Si especifiquem una clau dins l'array "fields" que no conté cap valor, no ens aplicarà a cap WHERE, però igualment ens seleccionarà
              el camp per mostrar-lo.

    */

    //Array a retornar:
    $data = [];

    //Iniciem variables:
    $select = '*';
    $where = '';
    $order = $args['order'] ?? 'ASC';
    $pagination = isset($args['pagination']) ? 'LIMIT ' . implode(", ", $args['pagination']) : '';
    $op = $args['operator'] ?? '=';

    $fields = $args['fields'] ?? array();
    $whereValues = ['types' => [], 'values' => []];
    
    

    //Si no hi ha cap taula a cercar, no podem fer la consulta.
    if (empty($args['table'])) {
        return $data;
    }else{
        $table = $args['table'];
    }

    //Obtenir valors SELECT i WHERE:

    //Si l'usuari ens ha passat camps:
    if (!empty($fields)) {

        //Si hem de seleccionar uns camps específics, modificar el valor del SELECT:
        if (!in_array('...', array_values($fields))) {
            $select = implode(', ', array_keys($fields));
        } else {
            //Si hem de seleccionar tots els camps, eliminem '...' per evitar tractar-lo més endavant.
            unset($fields[array_search('...', $fields)]);
            //El valor del SELECT segueix sent '*'.
        }

        //Obtenir el valor del WHERE:
        foreach ($fields as $key => $value) {
            //Per cada camp, si té valor, afegir-lo al WHERE.
            if (!empty($value)) {

                //Muntar el valor de la WHERE condition de la query:
                $where .= empty($where) ? 'WHERE ' : ' AND ';
                $where .= "`$key` $op ?";

                //Muntar una array per lligar els paràmetres (fer el BIND).
                if (gettype($value) == 'integer') {
                    array_push($whereValues['types'], 'i');
                    array_push($whereValues['values'], intval($value));
                } else {
                    array_push($whereValues['types'], str_contains($key, 'id') ? 'i' : 's');
                    array_push($whereValues['values'], $value);
                }
            }
        }
    }

    //REALITZAR LA CONSULTA:
    if (isset($conn)) {
        global $conn;
    } else {
        require 'connect.php';
    }

    //Muntar la query:
    $qry = "SELECT $select FROM $table $where ORDER BY `id` $order $pagination";

    //Executar consulta:    
    try {
        $consulta = $conn->prepare($qry);
        !empty($where) ? $consulta->bind_param(implode($whereValues['types']), ...$whereValues['values']) : null;
        $consulta->execute();
    } catch (mysqli_sql_exception $e) {
        print_r($e);
        $conn->close();
        return $data;
    }

    //Obtenir els registres que ha retornat el select.
    $result = $consulta->get_result();
    $conn->close();

    //Crear array amb les dades:
    $dades = array();
    while ($reg = $result->fetch_assoc()) {
        array_push($dades, $reg);
    }

    //Si l'usuari ens ha forçat a rebre les dades sempre dins d'un array "contenidor":
    if ($alwaysArray) {
        return $dades;
    } else {
        //Si només tenim un resultat, retornar una array directament amb el resultat (sense array contenidor).
        return sizeof($dades) == 1 ? $dades[0] : $dades;
    }
}

function insertDB($args = ['table' => '', 'fields' => []]): bool {
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

    //Si no tenim la taula o els registres a insertar, no podem fer la consulta.
    if (empty($args['table']) || empty($args['fields'])) {
        return false;
    }

    //Descarreguem les dades del paràmetre $args.
    $table = $args['table'];
    $fields = $args['fields'];

    //Muntar la relació valor-tipus:
    $columns = array_keys($fields);
    $valueTypes = ['types' => [], 'values' => []];

    //Muntar una array per lligar els tipus (fer el BIND).
    foreach ($fields as $key => $value) {
        if (gettype($value) == 'integer') {
            array_push($valueTypes['types'], 'i');
            array_push($valueTypes['values'], intval($value));
        } else {
            array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
            array_push($valueTypes['values'], $value);
        }
    }


    //REALITZAR EL INSERT:
    if (isset($conn)) {
        global $conn;
    } else {
        require 'connect.php';
    }

    //Muntar la query:
    $qry = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", array_map(function () {
        return '?';
    }, $columns)) . ")";

    //Executar consulta:    
    try {
        $consulta = $conn->prepare($qry);
        $consulta->bind_param(implode($valueTypes['types']), ...$valueTypes['values']);
        $consulta->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        print_r($e);
        return false;
    }finally{
        $conn->close();
    }  
}

function updateDB($args = ['table' => '', 'fields' => [], 'where' => []]): bool {
    /*
      ->  Aquesta funció fa d'interfície per actualitzar registres a la base de dades.
          - Retorna booleà segons si ha pogut insertar o no.
          
      $args = [
          table = $tablename (string),
          *fields = $field => $value (array),
          *where = $field => $value (array),
      ]
  
      * => Si falten camps, la funció retorna fals.
  
      */


    //Si no tenim taula o condició, no podem executar la consulta.
    if (empty($args['table']) || empty($args['where'])) {
        return false;
    }

    //Descarregar els valors del paràmetre $args.
    $table = $args['table'];
    $where = $args['where'];
    $fields = $args['fields'];
    
    //Si no tenim dades a actualitzar, retorna fals.
    if (empty($fields)) {
        return false;
    }else{
        $columns = array_keys($fields);
    }

    //Muntar les relacions valor-tipus:
    $valueTypes = ['types' => [], 'values' => []];

    //Muntar una array per lligar els tipus a actualitzar (fer el BIND).
    foreach ($fields as $key => $value) {
        if (gettype($value) == 'integer') {
            array_push($valueTypes['types'], 'i');
            array_push($valueTypes['values'], intval($value));
        } else {
            array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
            array_push($valueTypes['values'], $value);
        }
    }

    //Muntar una array per lligar els tipus del WHERE.
    foreach ($where as $key => $value) {
        if (gettype($value) == 'integer') {
            array_push($valueTypes['types'], 'i');
            array_push($valueTypes['values'], intval($value));
        } else {
            array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
            array_push($valueTypes['values'], $value);
        }
    }


    //REALITZAR EL INSERT:
    if (isset($conn)) {
        global $conn;
    } else {
        require 'connect.php';
    }


    //Muntar la query:
    $qry = "UPDATE $table SET " . implode(", ", array_map(function ($col) {
        return "$col = ?";
    }, $columns)) . " WHERE " . implode(" AND ", array_map(function ($key) {
        return "$key = ?";
    }, array_keys($where))) . "";

    //Executar consulta:    
    try {
        $consulta = $conn->prepare($qry);
        $consulta->bind_param(implode($valueTypes['types']), ...$valueTypes['values']);
        $consulta->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        print_r($e);
        return false;
    }finally{
        $conn->close();
    }
}

function deleteDB($args = ['table' => '', 'where' => []]): bool {
    /*
      ->  Aquesta funció fa d'interfície per eliminar registres a la base de dades.
          - Retorna booleà segons si s'ha pogut eliminar o no.
          
      $args = [
          table = $tablename (string),
          *where = $field => $value (array),
      ]
  
      *where => Si falten camps, la funció retorna fals.
  
      */

    //Si no ens han especificat una taula o una condició, no podem realitzar la consulta.
    if (empty($args['table']) || empty($args['where'])) {
        return false;
    }

    //Descarregar els valors del paràmetre $args.
    $table = $args['table'];
    $where = $args['where'];


    //Muntar la relació camp-valor:
    $valueTypes = ['types' => [], 'values' => []];

    //Muntar una array per lligar els tipus del WHERE (fer el BIND).
    foreach ($where as $key => $value) {      
        if (gettype($value) == 'integer') {
            array_push($valueTypes['types'], 'i');
            array_push($valueTypes['values'], intval($value));
        } else {
            array_push($valueTypes['types'], str_contains($key, 'id') ? 'i' : 's');
            array_push($valueTypes['values'], $value);
        }
    }


    //REALITZAR EL DELETE:
    if (isset($conn)) {
        global $conn;
    } else {
        require 'connect.php';
    }

    //Muntar la query:
    $qry = "DELETE FROM $table WHERE " . implode(" AND ", array_map(function ($key) {
        return "$key = ?";
    }, array_keys($where))) . "";

    //Executar consulta:    
    try {
        $consulta = $conn->prepare($qry);
        $consulta->bind_param(implode($valueTypes['types']), ...$valueTypes['values']);
        $consulta->execute();
        return true;
    } catch (mysqli_sql_exception $e) {
        print_r($e);
        return false;
    } finally {
        $conn->close();
    }
}

//Miscelanea:
function getPageName(): string {
    //Funció que retorna el nom de la pàgina.
    global $pageName;
    return "$pageName - Music4Hacks";
}

function getErrorsAlert($errors): string {
    //Funció que formateja amb un HTML els errors.
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

function translateKeys($array, $keys): array {
    //Aquesta funció ens permet traduir les claus d'una array associativa.
    //L'utilitzo per traduir els noms dels meus inputs amb els noms dels camps de la BBDD.

    //$keys = [oldKey] => 'newKey';
    $arrReturn = array();
    foreach ($keys as $oldKey => $newKey) {
        $arrReturn[$newKey] = $array[$oldKey];
    }
    return $arrReturn;
}
