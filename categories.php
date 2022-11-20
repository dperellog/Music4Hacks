<?php
// Fitxer per carregar la pagina de categories.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

//Codi de capçalera:

include_once 'includes/functions.php';

//Obtindre l'acció a realitzar:
$action = $_GET['action'] ?? 'view';

//Si hem de mostrar una categoria i no tenim la ID, redirigir a pàgina no trobada.
//(Aquesta situació només es pot causar manipulant l'enllaç).
if ($action != 'create' && !isset($_GET['id'])) {
    header("Location: 404.php");
}

//Depenent del que haguem de realitzar, inicialitzem variables.
switch ($action) {
    case 'create':
        $pageName = 'Crear categories';
        break;

    case 'edit':
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = 'Modificant ' . $categoryData['nombre'];
        break;

    case 'delete':
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = 'Eliminar ' . $categoryData['nombre'];
        break;

    default:
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = $categoryData['nombre'];
        $currentPage = countEntries($categoryData['id']) > POSTS_PER_PAGE ? $_GET['p'] ?? 1 : false;
        break;
}

//Si ens han entrat una ID que no correspón a cap entrada, redirigir a pàgina no trobada.
//(Aquesta situació es pot donar quan tenim un enllaç a una entrada prèviament borrada).
if (isset($categoryData) && !$categoryData) {
    header("Location: 404.php");
}


if ($action == 'create' || $action == 'edit') {
    //Si no s'està visualitzant l'entrada, comprovar que estigui loguejat.
    if (!isLogged()) {
        header("Location: index.php");
    }

    //Download errors:
    $errors = $_SESSION['errors'] ?? array();
    $refill = $_SESSION['refill'] ?? array();

    unset($_SESSION['errors']);
    unset($_SESSION['refill']);
}


//Codi per generar la pàgina:
include_once 'includes/header.php';
?>

<div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">

    <?php
    //Formulari d'eliminar categoria:
    if ($action == 'delete') :
        //Si l'usuari no està loguejat, no pot eliminar l'entrada.
        if (!isLogged()) {
            header("Location: index.php");
        }
    ?>
        <div class="row">
            <div class="col-sm content-box">
                <h4>Segur que vols eliminar <?= $categoryData['nombre'] ?>?</h4>
                <form action="functional/actionForm.php" method="post" class="content">
                    <div class="mb-2">
                        <input type="hidden" name="categoryId" value="<?= $categoryData['id'] ?>">
                        <input type="submit" value="Eliminar Categoria" name="deleteCategory" class="btn btn-danger">
                        <a href="categories.php?id=<?= $categoryData['id'] ?>&action=edit" class="btn btn-outline-success">Cancel·lar</a>
                    </div>

                    <?php
                    //Mostrar els botons de submit del formulari:
                    if ($action == 'edit') {
                        echo '<input type="hidden" name="entryId" value="' . $entryData['id'] . '">';
                        echo '<input type="submit" name="editEntry" value="Modificar entrada" class="btn btn-primary mt-2">';
                        echo '<a href="entrades.php?id=' . $entryData['id'] . '" class="btn btn-outline-info ms-2 mt-2">Veure entrada</a>';
                    } else {
                        '<input type="submit" name="newEntry" value="Crear entrada" class="btn btn-primary mt-2">';
                    } ?>
                </form>
            </div>
        </div>
    <?php endif;

    //Formulari d'afegir nova categoria:
    if ($action == 'create' || $action == 'edit') : ?>
        <div class="row create-categories">
            <div class="row text-center mb-4">
                <h2>Gestionar les categories:</h2>
                <h2 class="h6"><?= $action == 'create' ? 'Afegir noves categories al blog.' : 'Editant ' . $categoryData['nombre'] ?></h2>
                <hr class="mt-2">
            </div>

            <?php //Mostrar missatges d'error: ?>
            <?= isset($errors['newCategory']) ? getErrorsAlert($errors['newCategory']) : null ?>
            <?= isset($errors['editCategory']) ? getErrorsAlert($errors['editCategory']) : null ?>
            <?= isset($errors['deleteCategory']) ? getErrorsAlert($errors['deleteCategory']) : null ?>

            <div class="row">
                <div class="col-sm-8 content-box">
                    <h4><?= $action == 'create' ? 'Crear nova categoria:' : 'Editant ' . $categoryData['nombre'] ?></h4>
                    <form action="functional/actionForm.php" method="post" class="content">
                        <div class="mb-2">
                            <label for="categoryName" class="form-label">Nom de la categoria:</label>
                            <input type="text" name="categoryName" id="categoryName" class="form-control" value="<?= isset($categoryData) ? $categoryData['nombre'] : null; ?>">
                            <?= isset($errors['categoryName']) ? '<p class="alert alert-danger mt-2">El nom de la categoria ha de cumplir els requisits!</p>' : null ?>
                            <div class="form-text">Intordueix un nom amb només lletres i espais.</div>
                        </div>
                        <?php

                        //Mostrar els botons de submit del formulari:
                        if ($action == 'edit') {
                            echo '<input type="hidden" name="catId" value="' . $categoryData['id'] . '">';
                            echo '<input type="submit" name="editCategory" value="Modificar categoria" class="btn btn-primary mt-2">';
                            echo '<a href="categories.php?id=' . $categoryData['id'] . '&action=delete" class="btn btn-outline-danger mt-2 ms-2">Eliminar Categoria</a>';
                        } else {
                            echo '<input type="submit" name="newCategory" value="Crear categoria" class="btn btn-primary mt-2">';
                        } ?>

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
                                echo '<li class=""><a href="categories.php?action=edit&id=' . $categoria['id'] . '" class="nav-link active">' . $categoria['nombre'] . '</a></li>';
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
    if ($action == 'view') : ?>
        <div class="container-fluid mt-4">
            <h2>Posts de <?= $categoryData['nombre'] ?>:</h2>
            <?php
            $entrades = $currentPage ? getEntries(category: $categoryData['id'], page: $currentPage - 1) : getEntries(category: $categoryData['id']);

            if ($entrades) {
                echo '<div class="row"><div class="col mt-2 mx-3">';
                foreach ($entrades as $entry) {
                    echo showEntry($entry);
                }
                echo $currentPage ? getPaginationButtons('categories.php?id=' . $categoryData['id'], $currentPage, $categoryData['id']) : null;
            } else { ?>
                <div class="row justify-content-center">
                    <div class="col-6 d-flex flex-column justify-content-center mt-4">
                        <h2 class="text-center text-warning">No hi han entrades creades!</h2>
                        <div class="text-center"><img src="assets/img/no-entries.png" alt="Pàgina no trobada" width="300rem" class="img-fluid mx-4 my-3"></div>
                    <?php if (isLogged()) {
                        echo '<a href="entrades.php?action=create" class="btn btn-primary btn-block">Prova a crear una nova entrada</a>';
                    }
                } ?>
                    </div>
                </div>
        </div>

    <?php endif; ?>
</div>
<?php include 'includes/sidebar.php';
include_once 'includes/footer.php';
