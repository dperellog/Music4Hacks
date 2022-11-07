<?php
// Fitxer per carregar la pagina de categories.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

include_once 'includes/functions.php';

$action = $_GET['action'] ?? 'view';

$pageName = $action == 'create' ? 'Crear categories' : 'Categories del blog';

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

    <?php if ($action == 'create'): ?>
    <div class="row create-categories">
        <div class="row text-center mb-4">
            <h2>Gestionar les categories:</h2>
            <h2 class="h6">Afegir noves categories al blog.</h2>
            <hr class="mt-2">
        </div>
        <div class="row">
            <?= isset($errors['newCatSuccess']) ? '<div class="alert alert-success" role="alert">Categoria creada exitosament!</div>' : null ?>
            <?= isset($errors['newCatFailed']) ? '<div class="alert alert-danger" role="alert">' . $errors['newCatFailed'] . '</div>' : null ?>
        </div>

        <div class="row">
            <div class="col-sm-8 content-box">
                <h4>Crear nova categoria:</h4>
                <form action="functional/newcategory.php" method="post" class="content">
                    <div class="mb-2">
                        <label for="category-name" class="form-label">Nom de la categoria:</label>
                        <input type="text" name="categoryName" class="form-control">
                        <?= isset($errors['categoryName']) ? '<p class="alert alert-danger mt-2">El nom de la categoria ha de cumplir els requisits!</p>' : null ?>
                        <div class="form-text">Intordueix un nom amb només lletres i espais.</div>
                    </div>
                    <input type="submit" name="newCategory" value="Crear categoria" class="btn btn-primary mt-2">
                </form>
            </div>
            <div class="col-sm ms-2 content-box">
                <h4>Categories creades:</h4>
                <div class="content">
                    <?php echo listCategories(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
