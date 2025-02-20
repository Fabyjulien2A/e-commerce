<?php
session_start();
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51QufiTInfJIJehteuFR35ZjMijc49Gi3jScrawH8j1VRvi4L6MMTokRSYTMWacVUgPyra4NIwn1SFOnbuOCdVjqd007BxuWudf'); // Remplace avec ta clé secrète Stripe

// Vérification des données
if (!isset($_POST['nom'], $_POST['adresse'], $_POST['telephone'], $_POST['total'])) {
    die("Erreur : Informations de commande incomplètes.");
}

$total = floatval($_POST['total']); // Total en euros

// Création de la session de paiement
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => 'Commande E-commerce',
            ],
            'unit_amount' => intval($total * 100), // Stripe attend le montant en centimes
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/e-commerce/confirmation.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/e-commerce/cart.php',
]);

// Redirection vers Stripe
header("Location: " . $checkout_session->url);
exit();
