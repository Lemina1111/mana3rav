<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'commercant' || !isset($_SESSION['id_commercant'])) {
    header("Location: login.php");
    exit;
}

$id_commercant = $_SESSION['id_commercant'];

// Get all clients who have chatted with this merchant
$stmt = $conn->prepare("SELECT DISTINCT c.id, c.prénom, c.nom FROM client c JOIN Chat ch ON c.id = ch.id_client WHERE ch.id_commercant = ?");
$stmt->bind_param("i", $id_commercant);
$stmt->execute();
$result = $stmt->get_result();
$clients = [];
while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Conversations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Vos Conversations</h2>
    <div class="list-group">
        <?php if (count($clients) > 0): ?>
            <?php foreach ($clients as $client): ?>
                <a href="chat.php?id_client=<?= $client['id'] ?>" class="list-group-item list-group-item-action">
                    <?= htmlspecialchars($client['prénom'] . ' ' . $client['nom']) ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez aucune conversation pour le moment.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
