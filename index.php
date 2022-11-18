<?php
// Home Page del blog.
declare(strict_types=1);

$pageName = 'Home';



include_once 'includes/functions.php';
$currentPage = countEntries() > POSTS_PER_PAGE ? $_GET['p'] ?? 1 : false;

include_once 'includes/header.php';


?>
    <div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">
        <?php 

        
    ?>

        <div class="container mt-4">
            <h2>Últimes entrades:</h2>
                <?php
                    $entrades = $currentPage ? getEntries(page : $currentPage-1) : getEntries();

                    if ($entrades){
                        echo '<div class="row"><div class="col mt-2 mx-3">';
                        foreach ($entrades as $entry) {
                            echo showEntry($entry);
                        }
                    }else{ ?>
                        <div class="row justify-content-center">
                        <div class="col-6 d-flex flex-column justify-content-center mt-4">
                            <h4 class="text-center text-warning">No s'ha creat cap entrada!</h4>
                            <div class="text-center"><img src="assets/img/no-entries.png" alt="Pàgina no trobada" width="200px" class="img-fluid mx-4 my-3"></div>
                            <?php if (isLogged()) {
                            echo '<a href="entrades.php?action=create" class="btn btn-primary btn-block">Prova a crear una nova entrada</a>';
                        }} ?>
                </div>
            </div>
                    <?= $currentPage ? getPaginationButtons('index.php', $currentPage) : null ?>
        </div>

    </div>
    <?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
