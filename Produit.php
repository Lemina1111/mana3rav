<?php
session_start();
include 'conn.php';

// Vérifier si le commerçant est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'commercant' || !isset($_SESSION['id_commercant'])) {
    header("Location: login.php");
    exit;
}

$id_commercant = $_SESSION['id_commercant'];

$conn->set_charset("utf8mb4");

// Sélectionner uniquement les produits du commerçant connecté
$stmt = $conn->prepare("SELECT * FROM Produit WHERE id_commercant = ?");
$stmt->bind_param("i", $id_commercant);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits - Commerçant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f5;
            margin: 0;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
        }

        .produits {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            padding: 20px;
        }

        .produit {
            text-align: center;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 8px #ccc;
            width: 180px;
        }

        .produit img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .produit .nom {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .produit .prix {
            color: #2a9d8f;
        }

        .actions {
            margin-top: 10px;
        }

        .actions a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            margin-right: 5px;
            font-size: 0.9em;
            display: inline-block;
        }

        .actions a.modifier {
            background-color: #007bff;
            color: white;
        }

        .actions a.supprimer {
            background-color: #dc3545;
            color: white;
        }

        .aucun-produit {
            text-align: center;
            font-style: italic;
            color: #888;
        }

        .navbar {
            background-color: rgb(150, 221, 239);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            color: white;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-links li a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header class="navbar">
    <h1><a href="#">Bienvenue</a></h1>
    <ul class="nav-links">
        <li><a href="Produit.php">Produits</a></li>
        <li><a href="Ajouter_un_produit.php">Ajouter un produit</a></li>
        <li>
            <a href="logout.php" title="Déconnexion" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828427.png" alt="Déconnexion" style="width: 25px; vertical-align: middle;">
            </a>
        </li>
    </ul>
</header>

<h2>Vos produits</h2>

<div class="produits">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="produit">
                <?php if (!empty($row['imagee'])): ?>
                    <img src="<?= htmlspecialchars($row['imagee']) ?>" alt="<?= htmlspecialchars($row['nom']) ?>">
                <?php else: ?>
                    <div style="height: 150px; line-height: 150px; text-align: center; background-color: #f0f0f0; border-radius: 6px;">Pas d'image</div>
                <?php endif; ?>
                <div class="nom"><?= htmlspecialchars($row['nom']) ?></div>
                <div class="prix"><?= number_format($row['prix'], 2, ',', ' ') ?> MRU</div>

                <div class="actions">
                    <a href="modifierProduit.php?id=<?= $row['id'] ?>" class="modifier">Modifier</a>
                    <a href="supprimerProduit.php?id=<?= $row['id'] ?>" class="supprimer" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="aucun-produit">Vous n'avez aucun produit pour le moment.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>
