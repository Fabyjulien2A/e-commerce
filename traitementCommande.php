<?php
session_start();
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Vérification de la connexion utilisateur
if (!isset($_SESSION['id'])) {
    die("Erreur : Vous devez être connecté pour passer une commande.");
}

$user_id = $_SESSION['id'];
$total = 0;

// Enregistrement de la commande avec prix_total
$insertCommande = $bdd->prepare('INSERT INTO commandes (user_id, date_commande, prix_total) VALUES (?, NOW(), ?)');
$insertCommande->execute([$user_id, $total]);
$commande_id = $bdd->lastInsertId();

// Enregistrement des produits de la commande
foreach ($_SESSION['panier'] as $id_produit => $quantite) {
    $requeteProduit = $bdd->prepare('SELECT prix FROM produits WHERE id = ?');
    $requeteProduit->execute([$id_produit]);
    $produit = $requeteProduit->fetch();
    $prix_total = $produit['prix'] * $quantite;
    $total += $prix_total;

    $insertDetail = $bdd->prepare('INSERT INTO details_commandes (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)');
    $insertDetail->execute([$commande_id, $id_produit, $quantite, $produit['prix']]);
    
}

// Mise à jour du prix_total dans la commande
$updateTotal = $bdd->prepare('UPDATE commandes SET prix_total = ? WHERE id = ?');
$updateTotal->execute([$total, $commande_id]);

// Vider le panier
unset($_SESSION['panier']);

// Enregistrer l'ID de la commande dans la session
$_SESSION['commande_id'] = $commande_id;

// Rediriger vers la page de confirmation
header('Location: confirmation.php');
exit();