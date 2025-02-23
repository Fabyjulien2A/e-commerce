<?php
session_start();
require 'vendor/autoload.php'; // Stripe et PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$bdd = new PDO("mysql:host=localhost;dbname=e_commerce;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

if (!isset($_GET['session_id'])) {
    die("Erreur : session_id absent de l'URL !");
}

$session_id = $_GET['session_id'];

// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51QufiTInfJIJehteuFR35ZjMijc49Gi3jScrawH8j1VRvi4L6MMTokRSYTMWacVUgPyra4NIwn1SFOnbuOCdVjqd007BxuWudf'); // Ta clé secrète Stripe

try {
    // Récupérer la session Stripe
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

    // Vérifier le paiement
    if (!$checkout_session || $checkout_session->payment_status !== 'paid') {
        die("Erreur : paiement non validé !");
    }

    // Récupérer le montant total
    $total = $checkout_session->amount_total / 100; // Montant en euros

    // Récupérer infos du client depuis la session
    $nom = $_SESSION['nom'] ?? 'Client Inconnu';
    $adresse = $_SESSION['adresse'] ?? 'Adresse inconnue';
    $telephone = $_SESSION['telephone'] ?? 'Non renseigné';
    $clientEmail = $_SESSION['email_client'] ?? 'client@exemple.com';

    // Gérer l'utilisateur (si pas de système de connexion)
    $user_id = $_SESSION['user_id'] ?? 1; // Par défaut 1 ou un "guest user"

    // Insérer la commande en base
    $req = $bdd->prepare("INSERT INTO commandes (user_id, total, statut, prix_total) VALUES (?, ?, ?, ?)");
    $req->execute([$user_id, $total, 'Payée', $total]);

    $commande_id = $bdd->lastInsertId();

    // (Optionnel) Vider le panier
    unset($_SESSION['panier']);

    // Envoi de l'e-mail de confirmation via PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurer le serveur SMTP (exemple Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'votreEmail@gmail.com';
        $mail->Password   = 'votreMotDePasse';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Configurer l'e-mail
        $mail->setFrom('votreEmail@gmail.com', 'Mon E-commerce');
        $mail->addAddress($clientEmail, $nom); // E-mail du client
        $mail->isHTML(true);

        $mail->Subject = "Confirmation de commande #{$commande_id}";
        $mail->Body    = "
            <h2>Merci pour votre commande !</h2>
            <p>Numéro de commande : <strong>{$commande_id}</strong></p>
            <p>Montant payé : <strong>{$total} €</strong></p>
            <p>Statut : Payée</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Erreur d'envoi de l'email : " . $mail->ErrorInfo);
    }

    // Afficher la confirmation
    echo "<h2>Merci pour votre commande !</h2>";
    echo "<p>Commande #{$commande_id} enregistrée avec succès.</p>";
    echo "<p>Total payé : <strong>{$total} €</strong></p>";
    echo "<p>Un e-mail de confirmation a été envoyé à <strong>{$clientEmail}</strong>.</p>";
    echo "<a href='index.php'>Retour à l'accueil</a>";

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
