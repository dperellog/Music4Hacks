<?php
// Home Page del blog.
declare(strict_types=1);

$pageName = 'Home';

include_once 'includes/functions.php';
include_once 'includes/header.php';


?>
    <div class="main-content col-md-8 col-lg-9 col-xl-10 p-2">
        <?php 
       
        //var_dump(getEntries(category : 11))

        
    ?>

        <div class="container mt-4">
            <h2>Últimes entrades:</h2>
            <?php  ?>
            <div class="row">
                <div class="col mt-2 mx-3">
                <?php
                    $entrades = getEntries();

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

    </div>
    <?php include 'includes/sidebar.php' ?>

<?php
include_once 'includes/footer.php';
