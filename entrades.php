<?php
// Fitxer per tractar les pàgines relacionades amb les entrades.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

//Codi de capçalera:

include_once 'includes/functions.php';

//Obtindre l'acció a realitzar:
$action = $_GET['action'] ?? 'view';

//Si hem de mostrar una entrada i no tenim la ID, redirigir a pàgina no trobada.
//(Aquesta situació només es pot causar manipulant l'enllaç).
if ($action != 'create' && !isset($_GET['id'])) {
    header("Location: 404.php");
}

//Depenent del que haguem de realitzar, inicialitzem variables.
switch ($action) {
    case 'create':
        $pageName = 'Crear nova entrada';
        break;

    case 'edit':
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = 'Modificant ' . $entryData['titol'];
        break;

    case 'delete':
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], 'usuari_id' => '', 'titol' => '')));
        $pageName = 'Eliminar ' . $entryData['titol'];
        break;

    default:
        $entryData = selectDB(array('table' => 'entrades', 'fields' => array('id' => $_GET['id'], '...')));
        $pageName = $entryData['titol'];
        break;
}

//Si ens han entrat una ID que no correspón a cap entrada, redirigir a pàgina no trobada.
//(Aquesta situació es pot donar quan tenim un enllaç a una entrada prèviament borrada).
if (isset($entryData) && !$entryData) {
    header("Location: 404.php");
}

if ($action == 'create' || $action == 'edit') {
    //Si no s'està visualitzant l'entrada, comprovar que estigui loguejat.
    if (!isLogged()) {
        header("Location: index.php");
    }

    //Tractament dels resultats de la validació:
    $errors = $_SESSION['errors'] ?? array();
    $refill = $_SESSION['refill'] ?? array();

    unset($_SESSION['errors']);
    unset($_SESSION['refill']);

    if ($action == 'edit') {
        //Si hem d'editar les dades, les obtenim per mostrar-les als seus respectius camps.
        $refill = translateKeys($entryData, array('titol' => 'entryName', 'descripcio' => 'entryDescription', 'categoria_id' => 'entryCat'));
    }
}

//Codi per generar la pàgina:
include_once 'includes/header.php';
?>

<div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">

    <?php
    //Formualri de confirmació d'eliminar entrada:
    if ($action == 'delete') :
        //Si l'usuari que vol eliminar l'entrada no és seva, redirigir al index.
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

    //Formulari d'afegir nova entrada i d'editar una existent:
    if ($action == 'create' || $action == 'edit') :
    ?>
        <div class="row text-center mb-4">
            <h2>Gestionar les entrades:</h2>
            <h2 class="h6"><?= $action == 'create' ? 'Afegir noves entrades al blog.' : 'Editant ' . $entryData['titol'] ?></h2>
            <hr class="mt-2">
        </div>

        <?php //Mostrar missatges d'error: ?>
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
                        <div class="form-text">Intordueix un nom sense accents.</div>
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

                            //Si estem editant, seleccionem la categoria de l'entrada actual.
                            $refillCat = $refill['entryCat'] ?? 0;

                            //Generem llista de categories disponibles.
                            foreach (getCategories() as $cat) {
                                if ($cat['id'] == $refillCat) {
                                    echo '<option value="' . $cat['id'] . '" selected>' . $cat['nombre'] . '</option>';
                                } else {
                                    echo '<option value="' . $cat['id'] . '">' . $cat['nombre'] . '</option>';
                                }
                            } ?>
                        </select>
                        <?= isset($errors['entryCat']) ? '<p class="alert alert-danger mt-2">Has de seleccionar una categoria existent!</p>' : null ?>
                        <?php
                        //Si estem creant una entrada abans de crear categories, mostrar un missatge d'advertència.
                        if (empty(getCategories())) : ?>
                            <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                                <strong>Alerta!</strong> No s'ha creat encara cap categoria.
                                <hr>
                                <p>No pots crear cap entrada si no està associada a una categoria. <a class="alert-link" href="categories.php?action=create">Crea primer una categoria</a>.</p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php

                    //Mostrar els botons de submit del formulari:
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

    //Visualitzar l'entrada (opció per defecte si no hi ha "action"):
    if ($action == 'view') :

        //Obtenir les dades de la categoria i l'usuari pertenyents a l'entrada.
        $categoryData = selectDB(array('table' => 'categories', 'fields' => array('id' => $entryData['categoria_id'], '...')));
        $userData = selectDB(array('table' => 'usuaris', 'fields' => array('id' => $entryData['usuari_id'], '...')))
    ?>

        <div class="container-fluid mt-4">
            <div class="row mx-3">
                <div class="header entry-header col mt-2">
                    <h2><?= $entryData['titol'] ?>:</h2>
                    <p><a href="categories.php?id=<?= $entryData['categoria_id'] ?>"><?= $categoryData['nombre'] ?></a> - Escrit per <?= $userData['nom'] ?> || <?= $entryData['data'] ?></p>
                </div>
                <?php
                //Si l'usuari que ha creat l'entrada l'està visualitzant, mostrar els botons per editar i eliminar.
                if ($entryData['usuari_id'] == getUserID()) : ?>
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
                    <?= nl2br($entryData['descripcio']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php include 'includes/sidebar.php';
include_once 'includes/footer.php';
