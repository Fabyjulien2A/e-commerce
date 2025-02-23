<?php
session_start();
require 'vendor/autoload.php'; // Stripe SDK

\Stripe\Stripe::setApiKey('sk_test_51QufiTInfJIJehteuFR35ZjMijc49Gi3jScrawH8j1VRvi4L6MMTokRSYTMWacVUgPyra4NIwn1SFOnbuOCdVjqd007BxuWudf'); // Ta clé secrète Stripe

// Vérifie que le formulaire a bien été soumis
if (!isset($_POST['nom'], $_POST['adresse'], $_POST['telephone'], $_POST['email'], $_POST['total'])) {
    die("Erreur : Informations de commande incomplètes.");
}

// Stocker les infos du client dans la session
$_SESSION['nom'] = $_POST['nom'];
$_SESSION['adresse'] = $_POST['adresse'];
$_SESSION['telephone'] = $_POST['telephone'];
$_SESSION['email_client'] = $_POST['email']; // Pour envoyer l'email plus tard
$_SESSION['total'] = floatval($_POST['total']);

// Vérifie que le panier n'est pas vide
if (empty($_SESSION['panier'])) {
    header('Location: cart.php');
    exit();
}

// Connexion à la base de données
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Construction des line_items pour Stripe
$line_items = [];
foreach ($_SESSION['panier'] as $id_produit => $quantite) {
    // Récupérer nom/prix en base
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
                'unit_amount' => intval($produit['prix'] * 100),
            ],
            'quantity' => $quantite,
        ];
    }
}

// Création de la session Stripe Checkout
$YOUR_DOMAIN = 'http://localhost/e-commerce';
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => $YOUR_DOMAIN . '/confirmation.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $YOUR_DOMAIN . '/cart.php',
]);

// Redirection vers la page de paiement Stripe
header("Location: " . $checkout_session->url);
exit();
