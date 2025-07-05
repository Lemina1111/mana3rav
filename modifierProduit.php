<?php
session_start();
include 'conn.php';

// This page is no longer restricted
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'commercant' || !isset($_SESSION['id_commercant'])) {
//     header("Location: login.php");
//     exit;
// }

$ID = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$data = null;

if ($ID > 0) {
    $stmt = $conn->prepare("SELECT * FROM Produit WHERE id = ?");
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        echo "Produit non trouvé.";
        exit;
    }
    $stmt->close();
}

if (isset($_POST["update"])) {
    $ID = intval($_POST["id"]);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);

    $stmt_check = $conn->prepare("SELECT imagee FROM Produit WHERE id = ?");
    $stmt_check->bind_param("i", $ID);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows === 0) {
        echo "Action non autorisée.";
        exit;
    }
    $current_data = $result_check->fetch_assoc();
    $image_base64 = $current_data['imagee'];
    $stmt_check->close();


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

    $stmt = $conn->prepare("UPDATE Produit SET nom=?, prix=?, stock=?, imagee=? WHERE id=?");
    $stmt->bind_param("sdsis", $nom, $prix, $stock, $image_base64, $ID);

    if ($stmt->execute()) {
        header("Location: Produit.php");
        exit();
    } else {
        echo "❌ Erreur lors de la mise à jour : " . $stmt->error;
    }

    $stmt->close();
}
?>    

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MODIFIER</title>
<style>
    body { font-family: Arial, sans-serif; margin: 0; background-color: white; }
    .navbar { background-color: rgb(150, 221, 239); padding: 15px 30px; display: flex; justify-content: space-between; color: white; }
    .navbar h1 { margin: 0; font-size: 24px; }
    .navbar a { color: white; text-decoration: none; margin-left: 20px; font-weight: bold; }
    .nav-links { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; }
    .nav-links li a:hover { text-decoration: underline; }
    .container { width: 500px; margin: 40px auto; background: white; padding: 20px; box-shadow: 0px 0px 10px #ccc; border-radius: 8px; }
    .form-group { display: flex; align-items: center; margin-bottom: 15px; }
    .form-group label { width: 150px; }
    .form-group input { flex: 1; padding: 10px; }
    button { background-color: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; margin-top: 10px; }
    button:hover { background-color: #218838; }
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

<center>
<div class="container">
    <h2>Mise à jour de produit</h2>
    <?php if ($data): ?>
    <form action="modifierProduit.php?id=<?= $ID ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value='<?php echo $data["id"]; ?>'>
        
        <div class="form-group">
            <label for="nom">Nom du produit</label>
            <input type="text" name="nom" id="nom" value='<?php echo htmlspecialchars($data["nom"]); ?>'>
        </div>
        <div class="form-group">
            <label for="prix">Prix</label>
            <input type="number" name="prix" id="prix" value='<?php echo $data["prix"]; ?>' >
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" name="stock" id="stock" value='<?php echo $data["stock"]; ?>'>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" name="imagee">
            <?php if (!empty($data['imagee'])): ?>
                <img src="<?= htmlspecialchars($data['imagee']) ?>" width="100" alt="Image actuelle">
            <?php endif; ?>
        </div>
       
        <button type="submit" name="update">Mise à jour du produit</button>
    </form>
    <?php else: ?>
        <p>Produit non trouvé.</p>
    <?php endif; ?>
</div>
</center>

</body>
</html>
<?php
$conn->close();
?>
