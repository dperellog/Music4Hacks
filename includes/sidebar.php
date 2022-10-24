<?php
// Document que conté el codi que genera la sidebar.
declare(strict_types=1);

if (!isset($_SESSION)) {
session_start();
}

require_once 'functions.php';

//Download errors:
$errors = $_SESSION['errors'] ?? array();
$refill = $_SESSION['refill'] ?? array();

unset($_SESSION['errors']);
unset($_SESSION['refill']);

?>
<aside class="col-md me-4  aside">

    <?php if (isLogged()): ?>
    <div class="bg-white p-3 mt-4">
        <h4>Benvingut, <?= $_SESSION['userData']['nom'] ?></h4>
        <a class="btn btn-secondary mt-3" href="logout.php" role="button">Tancar sessió</a>
    </div>


    <?php endif; ?>

    <!-- SEARCH FORM -->
    <div class="bg-white p-3 mt-4">
        <form action="search.php" method="get">
            <h4>Cerca:</h4>
            <input class="form-control mb-2" type="text" name="s" id="searchinput">
            <input class="btn btn-primary mb-2" type="submit" value="Cercar">
        </form>
    </div>

    <?php if (!isLogged()): ?>

    <!-- LOGIN FORM -->
    <div class="bg-white mt-4 p-3">
        <form action="functional/login.php" method="post">
            <h4>Identifica't:</h4>
            <?= isset($errors['loginSuccess']) ? '<p class="alert alert-success">Usuari loguejat correctament!</p>' : null ?>
            <?= isset($errors['loginIncorrect']) ? '<p class="alert alert-danger">Credencials incorrectes!</p>' : null ?>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control mb-2" type="email" name="loginEmail" id="email" <?= isset($refill['loginEmail']) ? 'value="'.$refill['loginEmail'].'"' : null ?>>
                <?= isset($errors['loginEmail']) ? '<p class="alert alert-danger">El format de correu no és vàlid.</p>' : null ?>
            </div>
            <div class="mb-3 mt-3">
                <label for="passwd" class="form-label">Password:</label>
                <input class="form-control mb-2" type="password" name="loginPasswd" id="passwd">
                <?= isset($errors['loginPasswd']) ? '<p class="alert alert-danger">Has d\'especificar una contrasenya!</p>' : null ?>
            </div>
            <input class="btn btn-primary mb-2" type="submit" name="loginUser" value="Iniciar sessió">
        </form>
    </div>

    <!-- REGISTER FORM -->
    <div class="bg-white mt-4 p-3">
        <form action="functional/register.php" method="post">
            <h4>Regista't:</h4>
            <?= isset($errors['registerSuccess']) ? '<p class="alert alert-success">'.$errors['registerSuccess'].'</p>' : null ?>
            <?= isset($errors['registerFailed']) ? '<p class="alert alert-danger">'.$errors['registerFailed'].'</p>' : null ?>            
             
            <div class="mb-3 mt-3">
                <label for="name" class="form-label">Nom:</label>
                <input class="form-control mb-2" type="text" name="name" id="name" <?= isset($refill['name']) ? 'value="'.$refill['name'].'"' : null ?>>
                <?= isset($errors['name']) ? '<p class="alert alert-danger">El nom conté caràcters invàlids.</p>' : null ?>
            </div>
            <div class="mb-3 mt-3">
                <label for="surname" class="form-label">Cognoms:</label>
                <input class="form-control mb-2" type="text" name="surname" id="surname" <?= isset($refill['surname']) ? 'value="'.$refill['surname'].'"' : null ?>>
                <?= isset($errors['surname']) ? '<p class="alert alert-danger">Els cognoms contenen caràcters invàlids.</p>' : null ?>
            </div>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label" value="<?= isset($refill['email']) ? $refill['email'] : null ?>">Email:</label>
                <input class="form-control mb-2" type="email" name="email" id="email" <?= isset($refill['email']) ? 'value="'.$refill['email'].'"' : null ?>>
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
    <?php endif; ?>
</aside>