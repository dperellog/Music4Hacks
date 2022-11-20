<?php
// Fitxer Home Page del blog.
declare(strict_types=1);

//Codi de capçalera:

include_once 'includes/functions.php';

$pageName = 'Home';

//Obté si s'ha de fer paginació i en cas correcte, el número de pàgina.
$currentPage = countEntries() > POSTS_PER_PAGE ? $_GET['p'] ?? 1 : false;


//Codi de pàgina:
include_once 'includes/header.php';

?>
<div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">
    <div class="container-fluid mt-4">
        <h2>Últimes entrades:</h2>
        <?php
        //Mostra les últimes entrades:
        $entrades = $currentPage ? getEntries(page: $currentPage - 1) : getEntries();

        if ($entrades) {
            echo '<div class="row"><div class="col mt-2 mx-3">';
            foreach ($entrades as $entry) {
                echo showEntry($entry);
            }
        } else { //En cas que no hi hagin entrades: 
        ?>
            <div class="row justify-content-center">
                <div class="col-6 d-flex flex-column justify-content-center mt-4">
                    <h2 class="text-center text-warning">No s'ha trobat cap entrada!</h2>
                    <div class="text-center"><img src="assets/img/no-entries.png" alt="Pàgina no trobada" width="300rem" class="img-fluid mx-4 my-3"></div>
                <?php if (isLogged()) {
                    echo '<a href="entrades.php?action=create" class="btn btn-primary btn-block">Prova a crear una nova entrada</a>';
                }
            } ?>
                </div>
            </div>
            <?= $currentPage ? getPaginationButtons('index.php', $currentPage) : null ?>
    </div>

</div>
<?php include 'includes/sidebar.php';


include_once 'includes/footer.php';
