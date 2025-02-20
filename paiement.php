<?php
session_start();
require 'vendor/autoload.php'; // Charge Stripe

\Stripe\Stripe::setApiKey('sk_test_xxxxxxxxxxxxxxxxxxxxxxxxx'); // Remplace par ta clé secrète Stripe

// Vérifie que l'utilisateur est connecté et qu'une commande existe
if (!isset($_SESSION['id']) || !isset($_SESSION['commande_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['id'];
$commande_id = $_SESSION['commande_id'];
$total = $_SESSION['total'] ?? 0.00; // Total de la commande

// Créer une session Stripe Checkout
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => 'Commande #' . $commande_id,
            ],
            'unit_amount' => intval($total * 100), // Converti en centimes
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/e-commerce/confirmation.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/e-commerce/paiement.php?status=cancelled',
]);

// Redirige vers la page Stripe
header("Location: " . $checkout_session->url);
exit();
?>
