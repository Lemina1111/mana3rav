
<?php
$serveur = "127.0.0.1"; // Utilisez "host.docker.internal" si vous êtes dans un conteneur Docker
$utilisateur = "root";
$mot_de_passe = "yourpassword";
$base_de_donnees = "commerce";

$conn = new mysqli($serveur, $utilisateur, $mot_de_passe, $base_de_donnees);
if ($conn->connect_error) {
    die("❌ Connexion échouée : " . $conn->connect_error);
}     

?>
