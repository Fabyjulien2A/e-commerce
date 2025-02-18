<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupération des produits
$requete = $bdd->query("SELECT * FROM produits ORDER BY date_ajout DESC");
$produits = $requete->fetchAll(PDO::FETCH_ASSOC);



// Gestion de l'ajout au panier
if (isset($_POST['ajouter_panier'])) {
    $id_produit = $_POST['id_produit'];

    // Vérifier si le panier existe déjà
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Ajouter le produit au panier
    if (!isset($_SESSION['panier'][$id_produit])) {
        $_SESSION['panier'][$id_produit] = 1; // 1ère fois qu'on ajoute ce produit
    } else {
        $_SESSION['panier'][$id_produit]++; // Incrémente la quantité
    }

    header("Location: cart.php"); // Redirige vers le panier
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
<?php require 'header.php'; ?>

<h1>Nos Produits</h1>
    
<div class="products-container">
    <?php foreach ($produits as $produit) : ?>
        <div class="product">
    <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="product-img">
    <h2><?= htmlspecialchars($produit['nom']) ?></h2>
    <p><?= htmlspecialchars($produit['description']) ?></p>
    <p class="prix"><?= number_format($produit['prix'], 2) ?> €</p>
    <a href="product.php?id=<?= $produit['id'] ?>" class="btn">Voir Détails</a>

    <form method="post">
        <input type="hidden" name="id_produit" value="<?= $produit['id'] ?>">
        <button type="submit" name="ajouter_panier" class="btn-panier">Ajouter au panier</button>
    </form>
</div>

    <?php endforeach; ?>
</div>




    <?php require 'footer.php'; ?>




</body>
</html>