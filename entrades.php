<?php
// Fitxer per carregar la pagina de categories.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

include_once 'includes/functions.php';

$action = $_GET['action'] ?? 'view';

if ($action != 'create' && !isset($_GET['id'])) {
    header("Location: index.php");
}

switch ($action) {
    case 'create':
        $pageName = 'Crear nova entrada';
        break;

    case 'edit':
        !isset($_GET['id']) ? header("Location: 404.php") : null;
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = 'Modificant ' . $entryData['titol'];
        break;

    case 'delete':
        !isset($_GET['id']) ? header("Location: 404.php") : null;
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], 'usuari_id' => '', 'titol' => '')));
        $pageName = 'Eliminar ' . $entryData['titol'];
        break;

    default:
        !isset($_GET['id']) ? header("Location: index.php") : null;
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = $entryData['titol'];
        break;
}

if (isset($entryData) && !$entryData) {
    header("Location: 404.php");
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
    //Formualri de confirmació d'eliminar:
    if ($action == 'delete') :
        if (!isLogged() || $entryData['usuari_id'] != getUserID()) {
            header("Location: index.php");
        }
    ?>
        <div class="row">
            <div class="col-sm content-box">
                <h4>Segur que vols eliminar <?= $entryData['titol'] ?>?</h4>
                <form action="functional/actionForm.php" method="post" class="content">
                    <div class="mb-2">
                        <input type="hidden" name="entryId" value="<?= $entryData['id'] ?>">
                        <input type="submit" value="Eliminar Entrada" name="deleteEntry" class="btn btn-danger">
                        <a href="entrades.php?id=<?= $entryData['id'] ?>" class="btn btn-outline-success">Cancel·lar</a>
                    </div>
                </form>
            </div>

        </div>

    <?php endif;
    //Formulari d'afegir nova categoria:
    if ($action == 'create' || $action == 'edit') :
        if (!isLogged()) {
            header("Location: index.php");
        }

        if ($action == 'edit') {
            $refill = translateKeys($entryData, array('titol' => 'entryName', 'descripcio' => 'entryDescription', 'categoria_id' => 'entryCat'));
        }
    ?>
        <div class="row text-center mb-4">
            <h2>Gestionar les entrades:</h2>
            <h2 class="h6"><?= $action == 'create' ? 'Afegir noves entrades al blog.' : 'Editant ' . $entryData['titol'] ?></h2>
            <hr class="mt-2">
        </div>

        <?= isset($errors['newEntry']) ? getErrorsAlert($errors['newEntry']) : null ?>
        <?= isset($errors['editEntry']) ? getErrorsAlert($errors['editEntry']) : null ?>
        <?= isset($errors['deleteEntry']) ? getErrorsAlert($errors['deleteEntry']) : null ?>

        <div class="row">
            <div class="col-sm content-box">
                <h4><?= $action == 'create' ? 'Crear nova entrada:' : 'Editant ' . $entryData['titol'] ?></h4>
                <form action="functional/actionForm.php" method="post" class="content">
                    <div class="mb-2">
                        <label for="entryName" class="form-label">Nom de l'entrada:</label>
                        <input type="text" name="entryName" id="entryName" class="form-control" <?= isset($refill['entryName']) ? 'value="' . $refill['entryName'] . '"' : null ?>>
                        <?= isset($errors['entryName']) ? '<p class="alert alert-danger mt-2">El nom de l\'entrada ha de cumplir els requisits!</p>' : null ?>
                        <div class="form-text">Intordueix un nom amb només lletres, números i espais.</div>
                    </div>
                    <div class="mb-2">
                        <label for="entryDescription" class="form-label">Descripció:</label>
                        <textarea class="form-control" name="entryDescription" id="entryDescription" style="min-height: 10em;"><?= isset($refill['entryDescription']) ? $refill['entryDescription'] : null ?></textarea>
                        <?= isset($errors['entryDescription']) ? '<p class="alert alert-danger mt-2">La descripció no pot estar buida!</p>' : null ?>
                    </div>
                    <div class="mb-2">
                        <label for="entryCategory" class="form-label">Categoria:</label>
                        <select class="form-select" aria-label="Categories existents." id="entryCategory" name="entryCat">
                            <?php

                            $refillCat = $refill['entryCat'] ?? 0;
                            foreach (getCategories() as $cat) {
                                if ($cat['id'] == $refillCat) {
                                    echo '<option value="' . $cat['id'] . '" selected>' . $cat['nombre'] . '</option>';
                                } else {
                                    echo '<option value="' . $cat['id'] . '">' . $cat['nombre'] . '</option>';
                                }
                            } ?>
                        </select>
                        <?= isset($errors['entryCat']) ? '<p class="alert alert-danger mt-2">Has de seleccionar una categoria existent!</p>' : null ?>
                    </div>
                    <?php
                    if ($action == 'edit') {
                        echo '<input type="hidden" name="entryId" value="' . $entryData['id'] . '">';
                        echo '<input type="submit" name="editEntry" value="Modificar entrada" class="btn btn-primary mt-2">';
                        echo '<a href="entrades.php?id=' . $entryData['id'] . '" class="btn btn-outline-info ms-2 mt-2">Veure entrada</a>';
                    } else {
                        echo '<input type="submit" name="newEntry" value="Crear entrada" class="btn btn-primary mt-2">';
                    } ?>
                </form>
            </div>

        </div>
    <?php endif;

    //Carregar els posts de la categoria:
    if ($action == 'view') :
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $entryData['categoria_id'], '...')));
        $userData = selectDB(array('table' => 'usuaris', 'fields' => array('id' => $entryData['usuari_id'], '...')))
    ?>

        <div class="container mt-4">
            <div class="row mx-3">
                <div class="header entry-header col mt-2">
                    <h2><?= $entryData['titol'] ?>:</h2>
                    <p><a href="categories.php?id=<?= $entryData['categoria_id'] ?>"><?= $categoryData['nombre'] ?></a> - Escrit per <?= $userData['nom'] ?> || <?= $entryData['data'] ?></p>
                </div>
                <?php if ($entryData['usuari_id'] == getUserID()) : ?>
                    <div class="col-2 d-grid gap-2 me-3">
                        <a href="entrades.php?id=<?= $entryData['id'] ?>&action=edit" class="btn btn-outline-success">Editar entrada</a>
                        <a href="entrades.php?id=<?= $entryData['id'] ?>&action=delete" class="btn btn-outline-danger">Eliminar entrada</a>
                    </div>
                <?php endif; ?>
                <div class="col-12">
                    <hr class="mt-2">
                </div>

            </div>
            <div class="row mx-3">
                <div class="col mt-2 ">
                    <?= $entryData['descripcio'] ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
