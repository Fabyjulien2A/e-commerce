<?php
session_start();

// Connexion à la base de données
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);


// Traitement de la suppression
if (isset($_POST['supprimer']) && isset($_POST['product'])) {
    $productId = $_POST['product'];

    // Préparer et exécuter la requête de suppression
    $supprimerProduit = $bdd->prepare("DELETE FROM produits WHERE id = ?");
    $supprimerProduit->execute([$productId]);

    // Message de confirmation
    $_SESSION['message_confirmation_delete'] = "Le produit a été supprimé avec succès.";
}

// Redirection vers la page des employés
header('location: ../admin/admin.php');
exit;
