<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Vérifie si une commande a été passée
if (!isset($_SESSION['commande_id'])) {
    header('Location: index.php');
    exit();
}

$commande_id = $_SESSION['commande_id'];
$requeteCommande = $bdd->prepare('SELECT * FROM commandes WHERE id = ?');
$requeteCommande->execute([$commande_id]);
$commande = $requeteCommande->fetch();

$requeteDetails = $bdd->prepare('SELECT d.produit_id, d.quantite, d.prix_unitaire, p.nom 
                                 FROM details_commandes d 
                                 JOIN produits p ON d.produit_id = p.id 
                                 WHERE d.commande_id = ?');
$requeteDetails->execute([$commande_id]);
$details = $requeteDetails->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de commande</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Merci pour votre commande, <?= htmlspecialchars($_SESSION['email'] ?? 'Client') ?> !</h2>
        <p>Numéro de commande : <?= htmlspecialchars($commande['id']) ?></p>
        <p>Date : <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p>Total : <?= number_format($commande['prix_total'], 2, ',', ' ') ?> €</p>

        <h4>Détails de la commande :</h4>
        <ul>
            <?php foreach ($details as $detail): ?>
                <li><?= htmlspecialchars($detail['nom']) ?> - Quantité : <?= htmlspecialchars($detail['quantite']) ?> - Prix : <?= number_format($detail['prix_unitaire'], 2, ',', ' ') ?> €</li>
            <?php endforeach; ?>
        </ul>

        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</body>
</html>
