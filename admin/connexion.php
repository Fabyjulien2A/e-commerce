<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

if (isset($_POST['connexion'])) {
    if (!empty($_POST['email']) && !empty($_POST['mdp'])) {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['mdp'];

        // Vérification si l'email existe
        $recupUser = $bdd->prepare('SELECT * FROM users WHERE email = ?');
        $recupUser->execute([$email]);

        if ($recupUser->rowCount() > 0) {
            $utilisateur = $recupUser->fetch();

            // Vérification du mot de passe haché
            if (hash('sha256', $password) === $utilisateur['mdp']) {
                $_SESSION['id'] = $utilisateur['id'];
                $_SESSION['email'] = $email;

                // Correction de la condition
                if ($_SESSION['email']) {
                    header('Location: admin.php');
                    exit();
                }
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Adresse email non trouvée.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Mon espace</title>
</head>
<body class="body-connexion">
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-md-4">
            <form method="post" action="" class="p-4 border rounded">
                <h2 class="mb-4">Connexion</h2>
                <div class="mb-3">
                    <input type="text" class="form-control" name="email" placeholder="Email" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" name="mdp" placeholder="Mot de passe" required>
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary btn-block" name="connexion" value="Se connecter">
                </div>
                <?php
                if (isset($error)) {
                    echo "<p class='text-danger'>$error</p>";
                }
                ?>
            </form>
            <a href="../index.php" class="btn btn-secondary mt-2 btn-block">Retour au site</a>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>


