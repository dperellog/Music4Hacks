<?php
// Landing page del formulari de contacte del blog.
declare(strict_types=1);

if (!isset($_SESSION)) {
    session_start();
}

//Codi de capçalera:
$pageName = "Contacte amb nosaltres";

//Tractament dels resultats de la validació:
$errors = $_SESSION['errors'] ?? array();
$refill = $_SESSION['refill'] ?? array();

unset($_SESSION['errors']);
unset($_SESSION['refill']);

//Codi per generar la pàgina.
include_once 'includes/header.php';

?>
    <div class="main-content col-md-8 col-lg-9 col-xl-10 p-4">
        <div class="container-fluid mt-4">
            <h2>Contacta amb nosaltres:</h2>
            <div class="row">
                <div class="col mt-2 mx-3">
                <?= isset($errors['contactMsg']) ? getErrorsAlert($errors['contactMsg']) : null ?>
                <form action="functional/actionForm.php" method="post">
                <div class="mb-3 mt-3">
                    <label for="contactName" class="form-label">Nom:</label>
                    <input class="form-control mb-2" type="text" name="contactName" id="contactName" <?= isset($refill['contactName']) ? 'value="'.$refill['contactName'].'"' : null ?>>
                    <?= isset($errors['contactName']) ? '<p class="alert alert-danger">Has d\'introduir un nom vàlid!.</p>' : null ?>
                </div>
                <div class="mb-3 mt-3">
                    <label for="contactEmail" class="form-label">Email:</label>
                    <input class="form-control mb-2" type="email" name="contactEmail" id="contactEmail" <?= isset($refill['contactEmail']) ? 'value="'.$refill['contactEmail'].'"' : null ?>>
                    <?= isset($errors['contactEmail']) ? '<p class="alert alert-danger">El format de correu no és vàlid.</p>' : null ?>
                </div>
                <div class="mb-3 mt-3">
                        <label for="contactMessage" class="form-label">Missatge:</label>
                        <textarea class="form-control" name="contactMessage" id="contactMessage" style="min-height: 10em;"><?= isset($refill['contactMessage']) ? $refill['contactMessage'] : null ?></textarea>
                        <?= isset($errors['contactMessage']) ? '<p class="alert alert-danger mt-2">El missage no pot estar buit!</p>' : null ?>
                    </div>
                <input class="btn btn-primary mb-2" type="submit" name="contactMsg" value="Enviar missatge">
            </form>
            
                </div>
            </div>
        </div>

    </div>
    <?php include 'includes/sidebar.php';

include_once 'includes/footer.php';
