<?php
session_start();
include 'conn.php';

// Vérifier si le commerçant est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'commercant' || !isset($_SESSION['id_commercant'])) {
    header("Location: login.php");
    exit;
}

$id_commercant = $_SESSION['id_commercant'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajouter'])) {
    if (isset($_POST['nom'], $_POST['prix'], $_POST['stock'])) {
        $nom = htmlspecialchars(trim($_POST['nom']));
        $prix = floatval($_POST['prix']);
        $stock = intval($_POST['stock']);

        $image_base64 = NULL;
        if (isset($_FILES['imagee']) && $_FILES['imagee']['error'] === 0 && !empty($_FILES['imagee']['tmp_name'])) {
            $image_tmp_name = $_FILES['imagee']['tmp_name'];
            $image_type = mime_content_type($image_tmp_name);
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($image_type, $allowed_types)) {
                $image_data = file_get_contents($image_tmp_name);
                $image_base64 = 'data:' . $image_type . ';base64,' . base64_encode($image_data);
            } else {
                echo "❌ Seuls les fichiers JPG, PNG et GIF sont autorisés.";
                exit;
            }
        }

        $stmt = $conn->prepare("INSERT INTO Produit (nom, prix, stock, imagee, id_commercant) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdisi", $nom, $prix, $stock, $image_base64, $id_commercant);

        if ($stmt->execute()) {
            header("Location: Produit.php");
            exit;
        } else {
            echo "❌ Erreur lors de l'ajout : " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "❌ Tous les champs sont requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
<style>
     body {
            font-family: Arial, sans-serif;
            background-color:white;
            
            margin: 0;
        }

        .navbar {
            background-color:rgb(150, 221, 239);
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

    .container {
            width: 500px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px #ccc;
            border-radius: 8px;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 150px;
        }

        .form-group input {
            flex: 1;
            padding: 10px;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #218838;
        }

        </style>
</head>
<body>

    <header class="main-header">
        <nav class="navbar">
            <h1><a href="#">Bienvenue</a></h1>
            <ul class="nav-links">
                <li><a href="Produit.php">Produits</a></li>
                <li><a href="Ajouter_un_produit.php">Ajouter un produit</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <br><br><br>

    <div class="container">
        <h2>Ajouter un nouveau produit</h2>
        <form action="Ajouter_un_produit.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="nom">Nom du produit</label>
                <input type="text" name="nom" id="nom" required>
            </div>

            <div class="form-group">
                <label for="prix">Prix</label>
                <input type="number" name="prix" id="prix" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" required>
            </div>

            <div class="form-group">
                <label for="imagee">Image du produit</label>
                <input type="file" name="imagee">
            </div>

            <button type="submit" name="ajouter">Ajouter le produit</button>

        </form>
    </div>
</body>
</html>
