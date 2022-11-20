<?php
// Arxiu general utilitzat per validar i processar formularis.
declare(strict_types=1);

//# COMPROVACIONS GENERALS #//

//Si s'accedeix directament al fitxer, redirigir.
$pageRequired = explode('/',$_SERVER['SCRIPT_NAME']);
if (end($pageRequired) == basename(__FILE__)) {
header("Location: ../index.php");
}


if (!isset($_SESSION)) {
session_start();
}   

//Definir les accions que pot fer aquest fitxer i la pàgina a redireccionar:
//$actions = ('actionName','Redirect Page').
$actions = [
    'newCategory' => '../categories.php?action=create', 
    'editCategory' => '../categories.php?action=edit',
    'deleteCategory' => '../categories.php?action=create',
    'newEntry' => '../entrades.php?action=create',
    'editEntry' => '../entrades.php?action=edit',
    'deleteEntry' => '../entrades.php?action=create', 
    'registerUser' => '../index.php',
    'loginUser' => '../index.php',
    'updateUserData' => '../userpage.php',
    'contactMsg' => '../contact.php'
];


//Comprovar si hi ha una acció vàlida a realitzar pel POST.
if (!isset($_POST)) {
header("Location: ../index.php");
}else{
    //Extreure del POST l'acció a realitzar i comprovar que es vàlida.
    $action = array_intersect(array_keys($_POST), array_keys($actions));
    
    if (empty($action)) {
        header("Location: ../index.php");
    }else{
        $action = array_shift($action);
    }
}

//# PROGRAMA PRINCIPAL #//

require_once '../includes/functions.php';

//Validar dades:
unset($_POST[$action]);
$validatedData = validateData($_POST, $action);

//Volcar errors i refills a la variable de sessió:
$_SESSION['errors'] = $validatedData['errors'];
$_SESSION['refill'] = $validatedData['refill'];

//Variable que guarda les dades que han estat validades correctament.
$data = $validatedData['data'];

//Afegir la ID a l'enllaç abans de redirigir:
if (in_array($action, array('editEntry', 'editCategory'))) {
    switch ($action) {
        case 'editEntry':
            $actions['editEntry'] .= '&id='.$data['entryId'];
            break;

        case 'editCategory':
            $actions['editCategory'] .= '&id='.$data['catId'];
            break;
    }
}

if (!empty($validatedData['errors'])) {
    //Si hi ha errors, redirigir a la pàgina que correspón.
    header("Location: ".$actions[$action]);
}else{

    //Si no hi ha errors, realitzar l'acció corresponent:
    switch ($action) {

        case 'newCategory':
            //Comprovar si la categoria existeix.
            if (categoryExists($data['categoryName'])) {
                $response = array('actionFailed' => 'ERROR: La categoria ja existeix!');
                header("Location: ".$actions[$action]);
            }else{
                //Si la categoria no existeix, afegir-la a la BBDD:
                $consulta = insertDB(array('table' => 'categories', 'fields' => array('nombre' => $data['categoryName'])));

                //Genera el missatge de resposta a l'usuari.
                $response = $consulta 
                ? array('actionSuccess' => 'Nova categoria creada satisfactoriament!')
                : array('actionFailed' => 'ERROR: No s\'ha pogut crear la categoria.');
            }
            break;
        
        case 'editCategory':
            //Comprovar si existeix la categoria:
            if (categoryExists($data['categoryName'])) {
                $response = array('actionFailed' => 'Ja existeix una categoria amb aquest nom!');
            }else{
                $consulta = updateDB(array('table'=>'categories', 'fields'=>array('nombre' => $data['categoryName']), 'where'=>array('id' => $data['catId'])));
                
                //Genera el missatge de resposta a l'usuari.
                $response = $consulta 
                ? array('actionSuccess' => 'Categoria editada satisfactoriament!')
                : array('actionFailed' => 'ERROR: No s\'ha pogut editar la categoria.');
                
            }
            break;
        
        case 'deleteCategory':
            //Comprovem si la categoria conté entrades.
            if (sizeof(getEntries(category : $data['categoryId'])) != 0){
                $response = array('actionFailed' => 'ERROR: No es pot borrar la categoria perquè encara hi han entrades!');
            }else{
                $consulta = deleteDB(array('table' => 'categories', 'where' => array('id' => $data['categoryId'])));
                
                //Genera el missatge de resposta a l'usuari.
                $response = $consulta 
                ? array('actionSuccess' => 'Categoria eliminada satisfactoriament!')
                : array('actionFailed' => 'ERROR: No s\'ha pogut eliminar la categoria.');
            }
            break;
        
        case 'newEntry':
            //Afegir l'entrada directament:
            $consulta = insertDB(array('table' => 'entrades', 
            'fields' => array(
                'usuari_id' => getUserID(), 
                'categoria_id' => $data['entryCat'],
                'titol' => $data['entryName'],
                'descripcio' => $data['entryDescription'],
                'data' => date("Y-m-d")
                )
            ));

            if ($consulta) {
                //Si s'ha pogut crear l'entrada correctament, no reemplenar res.
                $_SESSION['refill'] = array();
                $response = array('actionSuccess' => 'Entrada creada exitosament!');
            }else{
                array('actionFailed' => 'ERROR: No s\'ha pogut crear l\'entrada.');
            }
            break;
        
        case 'editEntry':
            $consulta = updateDB(array('table'=>'entrades', 'fields'=>array(
                'titol' => $data['entryName'],
                'descripcio' => $data['entryDescription'],
                'categoria_id' => $data['entryCat']),
                 'where'=>array('id' => $data['entryId'])));

            //Genera el missatge de resposta a l'usuari.
            $response = $consulta 
            ? array('actionSuccess' => 'Entrada editada satisfactoriament!')
            : array('actionFailed' => 'ERROR: No s\'ha pogut editar la entrada.');
            break;

        case 'deleteEntry':
            //Eliminem directament l'entrada.
            $consulta = deleteDB(array('table' => 'entrades', 'where' => array('id' => $data['entryId'])));

            //Genera el missatge de resposta a l'usuari.
            $response = $consulta 
            ? array('actionSuccess' => 'Entrada eliminada satisfactoriament!')
            : array('actionFailed' => 'ERROR: No s\'ha pogut eliminar l\'entrada.');
            break;

        case 'registerUser':
            //Comprovar si l'usuari existeix o no.
            if (userExists($data['email'])) {
                $response = array('actionFailed' => 'ERROR: Aquest usuari ja existeix!');
            }else{

                //Executar la consulta:
                $data['date'] = date("Y-m-d");

                $consulta = insertDB(
                    array('table' => 'usuaris', 
                    'fields' => array(
                        'nom' => $data['name'], 
                        'cognom' => $data['surname'], 
                        'email' => $data['email'], 
                        'password' => $data['passwd'], 
                        'data' => $data['date'])
                        )
                    );
                
                if ($consulta) {
                    //Si l'usuri s'ha pogut registrar, eliminar les dades a emplenar del formulari:
                    unset($_SESSION['refill']);
                }

                //Genera el missatge de resposta a l'usuari.
                $response = $consulta 
                    ? array('actionSuccess' => 'Usuari registrat correctament!')
                    : array('actionFailed' => 'ERROR: No s\'ha pogut registrar l\'usuari.');
            }
            break;

        case 'loginUser':
            //Obtenir l'usuari de la BBDD:
            $userEmail = $data['loginEmail'];
            $usuari = selectDB(array('table' => 'usuaris', 'fields' => array('email' => $userEmail, '...')));

            //Si l'usuari existeix:
            if (!empty($usuari)) {
                //Check passwd:
                if (password_verify($data['loginPasswd'], $usuari['password'])) {
                    unset($usuari['password']);
                    
                    //Generar resposta per l'usuari.
                    $response = array('actionSuccess' => 'Usuari loguejat correctament!');

                    //Carregar les dades a la sessió:
                    $_SESSION['userData'] = $usuari;
                }else{
                    $response = array('actionFailed' => 'Credencials incorrectes!');
                }
            }else{
                $response = array('actionFailed' => 'Credencials incorrectes!');
            }
            break;

        case 'updateUserData':
            //Compovar si ja existeix un usuri amb el mateix correu:
            $usuariDB = getUserData($data['email'])['id'];

            if (!empty($usuariDB) && (getUserData()['id'] != $usuariDB)) {
                $response = array('actionFailed' => 'Ja hi ha un usuari registrat amb aquest correu!');
            }else{
                //Actualitzar a la BBDD:
                updateDB(array('table' => 'usuaris', 
                'fields' => array('nom' => $data['name'], 'cognom' => $data['surname'], 'email' => $data['email']),
                'where' => array('id' => getUserData()['id'])));
        
                //Actualitzar la variable Session:
                $userData = getUserData($data['email']);
                unset($userData['password']);
                $_SESSION['userData'] = $userData;
                
                //Genera el missatge de resposta a l'usuari.
                $response = array('actionSuccess' => 'Dades actualitzades exitosament!');
            }
            break;

        case 'contactMsg':
            //Muntar les capçaleres del correu a enviar al administrador.
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            //Muntar el cos del missatge.
            $email = '
            <html>
                <h1>Nou missatge del blog!</h1>
                <p>
            ';

            $email .= "Remitent: ".$data['contactEmail']."<br>
                        Nom: ".$data['contactName']."<br><br>
                        Missatge: <br>".$data['contactMessage']."</p></html>";
            
            //Enviar el correu a l'administrador.            
            $result = mail(ADMIN_EMAIL, "Nou missatge des del blog!", wordwrap($email,70), $headers);

            //Si s'ha enviat correctament, no reemplenar cap camp.
            if ($result) {
                $_SESSION['refill'] = array();
            }

            //Genera el missatge de resposta a l'usuari.
            $response = $result 
                    ? array('actionSuccess' => 'Missatge enviat correctament!')
                    : array('actionFailed' => 'ERROR: No s\'ha pogut enviar el missatge.');
            break;
    }
    
    //Carregar a la variable de sessió els errors.
    $_SESSION['errors'] = array($action => $response);

    //Redirigir l'usuari a la pàgina de destí.
    header("Location: ".$actions[$action]);
    }


