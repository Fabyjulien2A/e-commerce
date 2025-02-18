<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Vérification du panier
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header('Location: cart.php');
    exit;
}

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $id => $quantite) {
    $requete = $bdd->prepare("SELECT prix FROM produits WHERE id = ?");
    $requete->execute([$id]);
    $produit = $requete->fetch(PDO::FETCH_ASSOC);
    if ($produit) {
        $total += $produit['prix'] * $quantite;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation de commande</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <div class="container mt-5">
        <h2>Validation de votre commande</h2>
        <p>Total &agrave; payer : <strong><?= number_format($total, 2) ?> &euro;</strong></p>
        <form method="post" action="traitementCommande.php">
            <div class="mb-3">
                <label for="nom">Nom complet :</label>
                <input type="text" name="nom" id="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="adresse">Adresse de livraison :</label>
                <textarea name="adresse" id="adresse" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="telephone">T&eacute;l&eacute;phone :</label>
                <input type="tel" name="telephone" id="telephone" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Confirmer la commande</button>
        </form>
    </div>
    <?php require 'footer.php'; ?>
</body>
</html>
