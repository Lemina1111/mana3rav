<?php
session_start();
include 'conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client' || !isset($_SESSION['id_client'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$id_client = $_SESSION['id_client'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if ($action === 'get') {
    $stmt = $conn->prepare("SELECT p.id, p.nom, p.prix, pa.quantite FROM Panier pa JOIN Produit p ON pa.id_produit = p.id WHERE pa.id_client = ?");
    $stmt->bind_param("i", $id_client);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }
    echo json_encode(['success' => true, 'cart' => $cart]);
} elseif ($action === 'add') {
    $id_produit = intval($data['id_produit']);
    
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT quantite FROM Panier WHERE id_client = ? AND id_produit = ?");
    $stmt->bind_param("ii", $id_client, $id_produit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $new_quantite = $result->fetch_assoc()['quantite'] + 1;
        $update_stmt = $conn->prepare("UPDATE Panier SET quantite = ? WHERE id_client = ? AND id_produit = ?");
        $update_stmt->bind_param("iii", $new_quantite, $id_client, $id_produit);
        $update_stmt->execute();
    } else {
        // Insert new item
        $insert_stmt = $conn->prepare("INSERT INTO Panier (id_client, id_produit, quantite) VALUES (?, ?, 1)");
        $insert_stmt->bind_param("ii", $id_client, $id_produit);
        $insert_stmt->execute();
    }
    echo json_encode(['success' => true]);

} elseif ($action === 'remove') {
    $id_produit = intval($data['id_produit']);

    $stmt = $conn->prepare("SELECT quantite FROM Panier WHERE id_client = ? AND id_produit = ?");
    $stmt->bind_param("ii", $id_client, $id_produit);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $current_quantite = $result->fetch_assoc()['quantite'];
        if ($current_quantite > 1) {
            // Decrease quantity
            $new_quantite = $current_quantite - 1;
            $update_stmt = $conn->prepare("UPDATE Panier SET quantite = ? WHERE id_client = ? AND id_produit = ?");
            $update_stmt->bind_param("iii", $new_quantite, $id_client, $id_produit);
            $update_stmt->execute();
        } else {
            // Remove item
            $delete_stmt = $conn->prepare("DELETE FROM Panier WHERE id_client = ? AND id_produit = ?");
            $delete_stmt->bind_param("ii", $id_client, $id_produit);
            $delete_stmt->execute();
        }
    }
    echo json_encode(['success' => true]);
}

$conn->close();
?>
