<?php 
session_start();
include 'conn.php';

// Vérifier si le commerçant est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'commercant' || !isset($_SESSION['id_commercant'])) {
    header("Location: login.php");
    exit;
}

$id_commercant = $_SESSION['id_commercant'];
$ID = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($ID > 0) {
    // D'abord, vérifier que le produit appartient bien au commerçant
    $stmt = $conn->prepare("SELECT id FROM Produit WHERE id = ? AND id_commercant = ?");
    $stmt->bind_param("ii", $ID, $id_commercant);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        // Le produit appartient au commerçant, on peut le supprimer
        $delete_stmt = $conn->prepare("DELETE FROM Produit WHERE id = ?");
        $delete_stmt->bind_param("i", $ID);
        $delete_stmt->execute();
        $delete_stmt->close();
    } else {
        // Le produit n'existe pas ou n'appartient pas au commerçant
        echo "Action non autorisée.";
        exit;
    }
    $stmt->close();
}

header("Location: Produit.php");
exit();

$conn->close();
?>
