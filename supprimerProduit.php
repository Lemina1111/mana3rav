<?php 
session_start();
include 'conn.php';

// This page is no longer restricted
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'commercant' || !isset($_SESSION['id_commercant'])) {
//     header("Location: login.php");
//     exit;
// }

$ID = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($ID > 0) {
    $stmt = $conn->prepare("DELETE FROM Produit WHERE id = ?");
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $stmt->close();
}

header("Location: Produit.php");
exit();

$conn->close();
?>
