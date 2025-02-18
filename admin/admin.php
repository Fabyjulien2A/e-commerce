<?php
session_start();
if (isset($_SESSION['message_confirmation_delete'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['message_confirmation_delete'] . "</div>";
    unset($_SESSION['message_confirmation_delete']); // Supprime le message après l'affichage
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>

<div class="container fluid">
    <h1>Espace administateur</h1>
    <a href="../admin\layout.php"><button>Déconnexion</button></a>
    <a href="../ajouter_produit.php"><button>Ajouter un article</button></a>
    <a href="../supprimer_produit.php"><button>Supprimer un article</button></a>

</div>

<a href="../index.php">Retour page accueil</a>


</body>
</html>