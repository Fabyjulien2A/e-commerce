<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Traitement du formulaire d'ajout de produit
if (isset($_POST['ajouter_produit'])) {
    // Récupération des informations du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prix = $_POST['prix'];
    $description = $_POST['description'];

    // Vérification de l'upload du fichier image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        // Chemin de destination de l'image
        $dossier = 'images/'; // dossier où l'image sera enregistrée
        $imageTmp = $_FILES['image']['tmp_name']; // fichier temporaire téléchargé
        $imageName = $_FILES['image']['name']; // nom du fichier téléchargé
        $imagePath = $dossier . basename($imageName); // chemin complet de l'image

        // Déplacer l'image du dossier temporaire vers le dossier des images
        if (move_uploaded_file($imageTmp, $imagePath)) {
            // Si l'image a bien été téléchargée, on insère les données dans la base de données
            $req = $bdd->prepare("INSERT INTO produits (nom, prix, description, image) VALUES (?, ?, ?, ?)");
            $req->execute([$nom, $prix, $description, $imagePath]);

            // Redirection après l'ajout
            header("Location: products.php"); // Redirige vers la page des produits
            exit();
        } else {
            echo "Erreur lors du téléchargement de l'image.";
        }
    } else {
        echo "Veuillez sélectionner une image.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit</title>
</head>

<body>
    <h1>Ajouter un Nouveau Produit</h1>

    <form action="ajouter_produit.php" method="post" enctype="multipart/form-data">
        <label for="nom">Nom du produit :</label>
        <input type="text" name="nom" required><br>

        <label for="description">Description :</label>
        <textarea name="description" rows="4" required></textarea><br>

        <label for="prix">Prix du produit :</label>
        <input type="number" step="0.01" name="prix" required><br>

        <label for="image">Image du produit :</label>
        <input type="file" name="image" accept="image/*" required><br>

        <button type="submit" name="ajouter_produit">Ajouter le produit</button>
    </form>

</body>

</html>