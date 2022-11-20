<?php
// Fitxer per mostrar error de pàgina no trobada.
declare(strict_types=1);

$pageName = 'Pàgina no trobada';

include_once 'includes/header.php';


?>
    <div class="main-content col-md-8 col-lg-9 col-xl-10 p-2">
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-6 d-flex flex-column justify-content-center">
                    <h2 class="text-center">Oh no! No s'ha trobat la pàgina!</h2>
                    <div class="text-center"><img src="assets/img/404.png" alt="Pàgina no trobada" width="800rem" class="img-fluid"></div>
                    <a href="index.php" class="btn btn-primary btn-block">Tornar a l'inici</a>
                </div>
            </div>
        </div>

    </div>
    <?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
