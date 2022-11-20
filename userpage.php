<?php
// Fitxer per carregar la pagina dels usuaris.
declare(strict_types=1);

//Al treballar amb formularis, iniciem sessió.
if (!isset($_SESSION)) {
    session_start();
}

include_once 'includes/functions.php';

//Requerim tenir la sessió iniciada dins la web per carregar la pàgina.
if (!isLogged()) {
    header("Location: index.php");
}


$pageName = 'Editar dades personals';


//Tractament dels resultats de la validació:
$errors = $_SESSION['errors'] ?? array();
$refill = $_SESSION['refill'] ?? array();

unset($_SESSION['errors']);
unset($_SESSION['refill']);

//Obtenir les dades de l'usuari.
$userData = getUserData();

include_once 'includes/header.php';
?>

<div class="main-content col-md-8 col-lg-9 col-xl-10 p-2">
    <div class="container mt-4">
        <div class="row text-center mb-4">
            <h2>Les meves dades:</h2>
            <h2 class="h6">Modificar dades personals.</h2>
            <hr class="mt-2">
        </div>
        <?= isset($errors['updateUserData']) ? getErrorsAlert($errors['updateUserData']) : null ?>

        <div class="row">
            <div class="col-sm content-box mb-4">
                <form action="functional/actionForm.php" method="post" class="content">
                    <div class="mb-2">
                        <label for="name" class="form-label">Nom:</label>
                        <input type="text" name="name" id="name" value="<?= $userData['nom'] ?>" class="form-control">
                        <?= isset($errors['name']) ? '<p class="alert alert-danger mt-2">El nom ha de cumplir els requisits!</p>' : null ?>
                        <div class="form-text">Intordueix un nom amb només lletres i espais.</div>
                    </div>
                    <div class="mb-2">
                        <label for="surname" class="form-label">Cognoms:</label>
                        <input type="text" name="surname" id="surname" value="<?= $userData['cognom'] ?>" class="form-control">
                        <?= isset($errors['surname']) ? '<p class="alert alert-danger mt-2">El cognom ha de cumplir els requisits!</p>' : null ?>
                        <div class="form-text">Intordueix un nom amb només lletres i espais.</div>
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email:</label>
                        <input type="text" name="email" id="email" value="<?= $userData['email'] ?>" class="form-control">
                        <?= isset($errors['email']) ? '<p class="alert alert-danger mt-2">El correu electrònic ha de ser vàlid!</p>' : null ?>
                    </div>
                    <input type="submit" name="updateUserData" value="Actualitzar dades" class="btn btn-primary mt-2">
                </form>
            </div>
        </div>
    </div>

</div>
<?php include 'includes/sidebar.php';
include_once 'includes/footer.php';
