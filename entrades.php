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

$pageName = $action == 'create' ? 'Crear nova entrada' : 'Entrades del blog';


//Download errors:
$errors = $_SESSION['errors'] ?? array();
$refill = $_SESSION['refill'] ?? array();

unset($_SESSION['errors']);
unset($_SESSION['refill']);

include_once 'includes/header.php';
?>

<div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">
    <div class="row text-center mb-4">
        <h2>Gestionar les entrades:</h2>
        <h2 class="h6">Afegir noves entrades al blog.</h2>
        <hr class="mt-2">
    </div>
        <?= isset($errors['newEntrySuccess']) ? '<p class="alert alert-success row">Entrada creada exitosament!</p>' : null ?>
        <?= isset($errors['newEntryFailed']) ? '<p class="alert alert-danger row">' . $errors['newEntryFailed'] . '</p>' : null ?>

    <div class="row">
        <div class="col-sm content-box">
            <h4>Crear nova entrada:</h4>
            <form action="functional/newentry.php" method="post" class="content">
                <div class="mb-2">
                    <label for="entryName" class="form-label">Nom de l'entrada:</label>
                    <input type="text" name="entryName" class="form-control" <?= isset($refill['entryName']) ? 'value="'.$refill['entryName'].'"' : null ?>>
                    <?= isset($errors['entryName']) ? '<p class="alert alert-danger mt-2">El nom de l\'entrada ha de cumplir els requisits!</p>' : null ?>
                    <div class="form-text">Intordueix un nom amb només lletres i espais.</div>
                </div>
                <div class="mb-2">
                    <label for="entryDescription" class="form-label">Descripció:</label>
                    <textarea class="form-control" name="entryDescription"><?= isset($refill['entryDescription']) ? $refill['entryDescription'] : null ?></textarea>
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

</div>
<?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
