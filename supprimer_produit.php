<?php
session_start();

$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);


// Suppression de produit
if (isset($_POST['supprimer'])) {
    $productId = $_POST['product'];
    $supprimerProduit = $bdd->prepare("DELETE FROM produits WHERE id = ?");
    $supprimerProduit->execute([$productId]);

    $_SESSION['message_confirmation_delete'] = "Le produit a été supprimé avec succès.";
    header('location: espaceAdmin.php');
    exit;
}

// Récupération des produits
$listeProduits = $bdd->query("SELECT id, nom FROM produits")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <title>Supprimer un produit</title>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4">Supprimer un produit</h1>
        <form method="POST" action="traitementSuppression.php" class="mt-4">
            <div class="form-group">
                <label for="product">Sélectionnez le produit :</label>
                <select id="product" name="product" class="form-control">
                    <?php foreach ($listeProduits as $produit): ?>
                        <option value="<?= $produit['id'] ?>"><?= htmlspecialchars($produit['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="supprimer" class="btn btn-danger btn-block mt-3">Supprimer</button>
        </form>
        <a href="../admin/admin.php" class="btn btn-primary btn-block mt-3">Retour espace admin</a>
    </div>
</body>
</html>
