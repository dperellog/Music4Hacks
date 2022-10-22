<?php
// Document que conté el codi que genera la sidebar.
declare(strict_types=1);

if (!isset($_SESSION)) {
session_start();
}

//Download errors:
if (isset($_SESSION['errors'])) {
    $allCorrect = (sizeof($_SESSION['errors']) == 0);
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}else{
    $allCorrect = false;
    $errors = array();
}

$refill = $_SESSION['refill'] ?? array();

?>
<aside class="col-sm-3 aside py-3 pe-5">
    <div class="container-sm bg-white p-3">
        <form action="search.php" method="get">
            <h4>Cerca:</h4>
            <input class="form-control mb-2" type="text" name="s" id="searchinput">
            <input class="btn btn-primary mb-2" type="submit" value="Cercar">
        </form>
    </div>
    <div class="container-sm bg-white mt-4 p-3">
        <form action="login.php" method="post">
            <h4>Identifica't:</h4>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control mb-2" type="text" name="email" id="email">
            </div>
            <div class="mb-3 mt-3">
                <label for="passwd" class="form-label">Email:</label>
                <input class="form-control mb-2" type="password" name="passwd" id="passwd">
            </div>
            <input class="btn btn-primary mb-2" type="submit" value="Iniciar sessió">
        </form>
    </div>
    <div class="container-sm bg-white mt-4 p-3">
        <form action="functional/register.php" method="post">
            <h4>Regista't:</h4>
            <?php if(isset($errors['registerStatus'])){
                echo $errors['registerStatus'] ? '<p class="alert alert-success">Usuari registrat correctament!.</p>' : '<p class="alert alert-warning">ERROR: L\'usuari ja existeix.</p>';
            } ?>
             
            <div class="mb-3 mt-3">
                <label for="name" class="form-label">Nom:</label>
                <input class="form-control mb-2" type="text" name="name" id="name">
                <?= isset($errors['name']) ? '<p class="alert alert-danger">El nom conté caràcters invàlids.</p>' : null ?>
            </div>
            <div class="mb-3 mt-3">
                <label for="surname" class="form-label">Cognoms:</label>
                <input class="form-control mb-2" type="text" name="surname" id="surname">
                <?= isset($errors['surname']) ? '<p class="alert alert-danger">Els cognoms contenen caràcters invàlids.</p>' : null ?>
            </div>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control mb-2" type="email" name="email" id="email">
                <?= isset($errors['email']) ? '<p class="alert alert-danger">El correu no té el format vàlid.</p>' : null ?>
            </div>
            <div class="mb-3 mt-3">
                <label for="passwd" class="form-label">Password:</label>
                <input class="form-control mb-2" type="password" name="passwd" id="passwd">
                <?= isset($errors['passwd']) ? '<p class="alert alert-danger">La contrasenya no cumpleix amb els requisits mínims.</p>' : null ?>
            </div>
            <input class="btn btn-primary mb-2" type="submit" name="registerUser" value="Registrar-se">
        </form>
    </div>
</aside>