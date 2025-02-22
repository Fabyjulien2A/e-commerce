<?php
session_start();
require 'vendor/autoload.php'; // Stripe SDK

\Stripe\Stripe::setApiKey('sk_test_51QufiTInfJIJehteuFR35ZjMijc49Gi3jScrawH8j1VRvi4L6MMTokRSYTMWacVUgPyra4NIwn1SFOnbuOCdVjqd007BxuWudf');

$YOUR_DOMAIN = 'http://localhost/e_commerce'; // Mets ton domaine local

// Vérifie si le panier est vide
if (empty($_SESSION['panier'])) {
    header('Location: cart.php');
    exit();
}

// Connexion à la base de données
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Création des items Stripe
$line_items = [];
foreach ($_SESSION['panier'] as $id_produit => $quantite) {
    // Récupérer le produit en base de données
    $requete = $bdd->prepare("SELECT nom, prix FROM produits WHERE id = ?");
    $requete->execute([$id_produit]);
    $produit = $requete->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $produit['nom']
                ],
                'unit_amount' => intval($produit['prix'] * 100), // Convertir en centimes
            ],
            'quantity' => $quantite,
        ];
    }
}

// Création de la session Stripe Checkout
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'http://localhost/e-commerce/confirmation.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $YOUR_DOMAIN . '/cart.php',
]);

// Redirection vers Stripe
header("Location: " . $checkout_session->url);
exit();
