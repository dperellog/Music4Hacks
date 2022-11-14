<?php
// Fitxer per carregar la pagina de categories.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

include_once 'includes/functions.php';

if (!isLogged()) {
header("Location: index.php");
}

$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'create':
        $pageName = 'Crear nova entrada';
        break;

    case 'edit':
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = 'Modificant '.$entryData['titol'];
        break;
    
    default:
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = $entryData['titol'];
        break;
}

//Download errors:
$errors = $_SESSION['errors'] ?? array();

$refill = isset($errors['newEntrySuccess']) ? array() : $_SESSION['refill'] ?? array();


unset($_SESSION['errors']);
unset($_SESSION['refill']);

include_once 'includes/header.php';


?>

<div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">

    <?php
    //Formulari d'afegir nova categoria:
    if ($action == 'create' || $action == 'edit'): 
        if ($action == 'edit') {
            $refill = translateKeys($entryData, array('titol' => 'entryName', 'descripcio' => 'entryDescription', 'categoria_id' => 'entryCat'));
        }
    ?>
    <div class="row text-center mb-4">
        <h2>Gestionar les entrades:</h2>
        <h2 class="h6"><?= $action == 'create' ? 'Afegir noves entrades al blog.' : 'Editant '.$entryData['titol'] ?></h2>
        <hr class="mt-2">
    </div>
    
    <?= isset($errors['newEntry']) ? getErrorsAlert($errors['newEntry']) : null ?>

    <div class="row">
        <div class="col-sm content-box">
            <h4><?= $action == 'create' ? 'Crear nova entrada:' : 'Editant '.$entryData['titol'] ?></h4>
            <form action="functional/actionForm.php" method="post" class="content">
                <div class="mb-2">
                    <label for="entryName" class="form-label">Nom de l'entrada:</label>
                    <input type="text" name="entryName" class="form-control" <?= isset($refill['entryName']) ? 'value="'.$refill['entryName'].'"' : null ?>>
                    <?= isset($errors['entryName']) ? '<p class="alert alert-danger mt-2">El nom de l\'entrada ha de cumplir els requisits!</p>' : null ?>
                    <div class="form-text">Intordueix un nom amb només lletres, números i espais.</div>
                </div>
                <div class="mb-2">
                    <label for="entryDescription" class="form-label">Descripció:</label>
                    <textarea class="form-control" name="entryDescription" style="min-height: 10em;"><?= isset($refill['entryDescription']) ? $refill['entryDescription'] : null ?></textarea>
                    <?= isset($errors['entryDescription']) ? '<p class="alert alert-danger mt-2">La descripció no pot estar buida!</p>' : null ?>
                </div>
                <div class="mb-2">
                    <label for="entryCategory" class="form-label">Categoria:</label>
                    <select class="form-select" aria-label="Categories existents." name="entryCat">
                        <?php  
                        
                        $refillCat = $refill['entryCat'] ?? 0;
                        foreach (getCategories() as $cat) {
                            if ($cat['id'] == $refillCat){
                                echo '<option value="'.$cat['id'].'" selected>'.$cat['nombre'].'</option>';
                            }else{
                                echo '<option value="'.$cat['id'].'">'.$cat['nombre'].'</option>';
                            }
                            
                        } ?>
                    </select>
                    <?= isset($errors['entryCat']) ? '<p class="alert alert-danger mt-2">Has de seleccionar una categoria existent!</p>' : null ?>
                </div>
                <input type="submit" name="newEntry" value="Crear entrada" class="btn btn-primary mt-2">
            </form>
        </div>
        
    </div>
    <?php endif;

    //Carregar els posts de la categoria:
    if ($action == 'view'): 
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $entryData['categoria_id'], '...')));
        $userData = selectDB(array('table' => 'usuaris', 'fields' => array('id' => $entryData['usuari_id'], '...')))
        ?>
        
        <div class="container mt-4">
            <div class="row">
                <div class="header entry-header col mt-2 mx-3">
                <h2><?= $entryData['titol']?>:</h2>
                <p><a href="categories.php?id=<?= $entryData['categoria_id']?>"><?= $categoryData['nombre'] ?></a> - Escrit per <?= $userData['nom']?> || <?= $entryData['data']?></p>
                <hr class="mt-2">
                </div>
            </div>
            <div class="row">
                <div class="col mt-2 mx-3">
                    <?= $entryData['descripcio']?>
                </div>
            </div>



                
            
        </div>



<?php endif; ?>

</div>
<?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
