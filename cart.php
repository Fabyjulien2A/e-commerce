<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$produits = [];
$total = 0; // Initialisation du total

// Vérification du panier et récupération des produits
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    $ids = implode(',', array_keys($_SESSION['panier']));
    $requete = $bdd->query("SELECT * FROM produits WHERE id IN ($ids)");
    $produits = $requete->fetchAll(PDO::FETCH_ASSOC);

    // Calcul du total
    foreach ($produits as $produit) {
        $id_produit = $produit['id'];
        $quantite = $_SESSION['panier'][$id_produit];
        $total += $produit['prix'] * $quantite; // Ajoute le prix total du produit au total global
    }
}

// Suppression d'un produit
if (isset($_POST['supprimer'])) {
    $id_supprimer = $_POST['id_produit'];
    unset($_SESSION['panier'][$id_supprimer]);
    header("Location: cart.php");
    exit();
}

// Vider le panier
if (isset($_POST['vider_panier'])) {
    unset($_SESSION['panier']);
    header("Location: cart.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
<body>
<?php require 'header.php'; ?>

<h1>Mon Panier</h1>

<?php if (empty($produits)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
            <tr>
                <td><img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" width="100">
                </td>
                <td><?= htmlspecialchars($produit['nom']) ?></td>
                <td><?= number_format($produit['prix'], 2) ?> €</td>
                <td><?= $_SESSION['panier'][$produit['id']] ?></td>
                <td><?= number_format($produit['prix'] * $_SESSION['panier'][$produit['id']], 2) ?> €</td>
                <td>
                    <form method="post">
                        <input type="hidden" name="id_produit" value="<?= $produit['id'] ?>">
                        <button type="submit" name="supprimer" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total : <?= number_format($total, 2) ?> €</h3>

    <form method="post">
        <button type="submit" name="vider_panier" class="btn btn-danger">Vider le panier</button>
    </form>

    <a href="checkout.php" class="btn btn-success">Passer la commande</a>

<?php endif; ?>

<?php require 'footer.php'; ?>
</body>
</html>