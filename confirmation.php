<?php
session_start();
require 'vendor/autoload.php';

// Connexion à la base de données
$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Vérifier si session_id est présent dans l'URL
if (!isset($_GET['session_id'])) {
    die("Erreur : session_id absent de l'URL !");
}

$session_id = $_GET['session_id'];

// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51QufiTInfJIJehteuFR35ZjMijc49Gi3jScrawH8j1VRvi4L6MMTokRSYTMWacVUgPyra4NIwn1SFOnbuOCdVjqd007BxuWudf');

try {
    // Récupérer les détails de la session Stripe
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

    // Vérifier si la session de paiement est valide
    if (!$checkout_session || $checkout_session->payment_status !== 'paid') {
        die("Erreur : paiement non validé !");
    }

    // Récupérer le montant total (Stripe renvoie en centimes)
    $total = $checkout_session->amount_total / 100;

    // Vérifier si l'utilisateur est connecté (si non, définir un ID par défaut)
    $user_id = $_SESSION['user_id'] ?? 1; // Remplace 1 par un ID invité ou autre logique

    // Insérer la commande en base
    $requete = $bdd->prepare("INSERT INTO commandes (user_id, total, statut, prix_total) VALUES (?, ?, ?, ?)");
    $requete->execute([$user_id, $total, 'Payée', $total]);

    // Récupérer l'ID de la commande insérée
    $commande_id = $bdd->lastInsertId();

    // Optionnel : vider le panier après paiement réussi
    unset($_SESSION['panier']);

    echo "<h2>Merci pour votre commande !</h2>";
    echo "<p>Votre commande #{$commande_id} a été enregistrée avec succès.</p>";
    echo "<p>Total payé : <strong>{$total} €</strong></p>";
    echo "<a href='index.php'>Retour à l'accueil</a>";

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
