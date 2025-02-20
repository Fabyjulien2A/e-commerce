<?php
session_start();
require 'vendor/autoload.php'; // Stripe SDK

\Stripe\Stripe::setApiKey('ta_cle_secrete_stripe'); // Remplace par ta vraie clé

$YOUR_DOMAIN = 'http://localhost/e-commerce'; // Mets ton domaine local

// Vérifier si le panier est rempli
if (empty($_SESSION['panier'])) {
    header('Location: index.php');
    exit();
}

// Création d'une session Stripe Checkout
$line_items = [];
foreach ($_SESSION['panier'] as $id_produit => $quantite) {
    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => 'Produit ' . $id_produit // Mets le vrai nom du produit
            ],
            'unit_amount' => 1000, // Prix en centimes (10,00€)
        ],
        'quantity' => $quantite,
    ];
}

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => $YOUR_DOMAIN . '/confirmation.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $YOUR_DOMAIN . '/index.php',
]);

header("Location: " . $checkout_session->url);
exit();
