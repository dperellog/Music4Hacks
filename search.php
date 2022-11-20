<?php
// Fitxer per realitzar cerques dins del blog.
declare(strict_types=1);

//Codi de capçalera:

//Si no ens passen per get una cadena a cercar, redirigeix al inici.
$searchStr = $_GET['s'] ?? '';

if (empty($searchStr)) {
    header("Location: index.php");
}

include_once 'includes/functions.php';

$pageName = "Cerca: $searchStr";
$searchStr = sanitStr($searchStr);

//Cercar les entrades que siguin similars a la cadena que hem de cercar.
$entrades = selectDB(array('table' => 'entrades', 'fields' => array('titol' => "%$searchStr%", '...'), 'operator' => 'LIKE'), true);


//Codi per generar la pàgina:
include_once 'includes/header.php';

?>
<div class="main-content col-md-8 col-lg-9 col-xl-10 p-2">
    <div class="container-fluid mt-4">
        <h2>Resultats de "<?= $searchStr ?>":</h2>
        <?php
        if ($entrades) {
            echo '<div class="row"><div class="col mt-2 mx-3">';
            foreach ($entrades as $entry) {
                echo showEntry($entry);
            }
        } else { ?>
            <div class="row justify-content-center">
                <div class="col-6 d-flex flex-column justify-content-center mt-4">
                    <h4 class="text-center text-warning">No s'han trobat resultats!</h4>
                    <div class="text-center"><img src="assets/img/no-entries.png" alt="Pàgina no trobada" width="200px" class="img-fluid mx-4 my-3"></div>
                <?php } ?>
                </div>
            </div>
    </div>

</div>
<?php include 'includes/sidebar.php';
include_once 'includes/footer.php';
