<?php
// Fitxer per carregar la pagina de categories.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

include_once 'includes/functions.php';

$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'create':
        $pageName = 'Crear categories';
        break;

    case 'edit':
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = 'Modificant '.$categoryData['nombre'];
        break;
    
    default:
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = $categoryData['nombre'];
        break;
}


if ($action == 'create') {
    //Download errors:
    $errors = $_SESSION['errors'] ?? array();
    $refill = $_SESSION['refill'] ?? array();

    unset($_SESSION['errors']);
    unset($_SESSION['refill']);
}

include_once 'includes/header.php';
?>

<div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">

    <?php 
    //Formulari d'afegir nova categoria:
    if ($action == 'create' || $action == 'edit'): ?>
    <div class="row create-categories">
        <div class="row text-center mb-4">
            <h2>Gestionar les categories:</h2>
            <h2 class="h6"><?= $action == 'create' ? 'Afegir noves categories al blog.' : 'Editant '.$categoryData['nombre'] ?></h2>
            <hr class="mt-2">
        </div>
        <div class="row">
            <?= isset($errors['ActionSuccess']) ? '<div class="alert alert-success" role="alert">' . $errors['ActionSuccess'] . '</div>' : null ?>
            <?= isset($errors['ActionFailed']) ? '<div class="alert alert-danger" role="alert">' . $errors['ActionFailed'] . '</div>' : null ?>
        </div>

        <div class="row">
            <div class="col-sm-8 content-box">
                <h4><?= $action == 'create' ? 'Crear nova categoria:' : 'Editant '.$categoryData['nombre'] ?></h4>
                <form action="functional/newcategory.php" method="post" class="content">
                    <div class="mb-2">
                        <label for="category-name" class="form-label">Nom de la categoria:</label>
                        <input type="text" name="categoryName" class="form-control" value="<?= isset($categoryData) ? $categoryData['nombre'] : null; ?>">
                        <?= isset($errors['categoryName']) ? '<p class="alert alert-danger mt-2">El nom de la categoria ha de cumplir els requisits!</p>' : null ?>
                        <div class="form-text">Intordueix un nom amb només lletres i espais.</div>
                    </div>
                    <input type="submit" name="newCategory" value="Crear categoria" class="btn btn-primary mt-2">
                </form>
            </div>
            <div class="col-sm ms-2 content-box">
                <h4>Modificar Categories:</h4>
                <div class="content">
                    <?php 
                    $cats = getCategories();

                    if ($cats) {
                        echo '<ul>';
                        foreach ($cats as $categoria) {
                            echo'<li class=""><a href="categories.php?action=edit&id='.$categoria['id'].'" class="nav-link active">'.$categoria['nombre'].'</a></li>';;
                        }
                        echo '</ul>';
                    } else {
                        echo '<p class="alert alert-warning">Alerta! No s\'ha creat encara cap categoria.</p>';
                    }
                    
                    
                    
                    
                    
                    
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php 
    //Carregar els posts de la categoria:
    if ($action == 'view'): ?>
        <div class="container mt-4">
            <h2>Posts de <?= $categoryData['nombre']?>:</h2>
            <?php  ?>
            <div class="row">
                <div class="col mt-2 mx-3">
                <?php
                    $entrades = getEntries(category : $categoryData['id']);

                    if ($entrades){
                        foreach ($entrades as $entry) {
                            echo showEntry($entry);
                        }
                    }else{
                        echo '<h4 class="text-center text-warning">No hi han entrades creades!</h4>';
                        //echo '<img src="assets/img/no-entries.png"  alt="No hi han entrades.">';
                    }
                    ?>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>
<?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
