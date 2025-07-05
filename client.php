<?php
session_start();
include 'conn.php';

// Vérifier si le client est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client' || !isset($_SESSION['id_client'])) {
    header("Location: login.php");
    exit;
}

$id_client = $_SESSION['id_client'];

// Traitement de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'envoyer_commande') {
    // Récupérer le panier de la base de données
    $stmt = $conn->prepare("SELECT p.nom, pa.quantite, p.prix FROM Panier pa JOIN Produit p ON pa.id_produit = p.id WHERE pa.id_client = ?");
    $stmt->bind_param("i", $id_client);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
    $stmt->close();

    if (empty($cart_items)) {
        echo "Le panier est vide.";
        exit;
    }

    // Insérer la commande
    foreach ($cart_items as $item) {
        $nom_produit = htmlspecialchars($item['nom']);
        $stmt = $conn->prepare("INSERT INTO Commande (nom_produit, quantite, prix, id_client) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sidi", $nom_produit, $item['quantite'], $item['prix'], $id_client);
        $stmt->execute();
        $stmt->close();
    }

    // Vider le panier
    $delete_stmt = $conn->prepare("DELETE FROM Panier WHERE id_client = ?");
    $delete_stmt->bind_param("i", $id_client);
    $delete_stmt->execute();
    $delete_stmt->close();

    echo "La commande a été enregistrée avec succès";
    exit;
}


// Chargement des produits
$result = $conn->query("SELECT p.*, c.id as id_commercant FROM Produit p JOIN commercant c ON p.id_commercant = c.id");
$produits = [];
while ($row = $result->fetch_assoc()) {
    $produits[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Clients - Boutique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .product {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            background-color: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product img {
            max-height: 150px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .dropdown-cart {
            min-width: 300px;
            max-height: 400px;
            overflow-y: auto;
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 5px 10px;
        }
        #search-bar {
            display: none;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Boutique</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="client.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="panierDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cart"></i> Panier (<span id="cart-count-nav">0</span>) 
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-cart p-2" aria-labelledby="panierDropdown">
                        <div id="cart-items-nav">Panier vide</div>
                        <div class="mt-2 text-center">
                            <p>Total : <strong id="total-price-nav">0 MRU</strong></p>
                            <button class="btn btn-success btn-sm w-100" onclick="envoyerCommande()">Confirmer la commande</button>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu principal -->
<div class="container-fluid py-4">
    <div class="row g-3" id="products">
        <!-- Produits dynamiques -->
    </div>
</div>

<script>
const allProduits = <?= json_encode($produits, JSON_UNESCAPED_UNICODE) ?>;
let cart = [];

function afficherProduits() {
    const conteneur = document.getElementById("products");
    conteneur.innerHTML = '';
    allProduits.forEach(produit => {
        const div = document.createElement("div");
        div.className = "col-md-6 col-lg-4 col-xl-3 produit-item";
        div.innerHTML = `
            <div class="product">
                <img src="${produit.imagee || 'placeholder.png'}" alt="${produit.nom}" class="img-fluid">
                <div>
                    <h5 class="nom-produit">${produit.nom}</h5>
                    <p><strong>${produit.prix} MRU</strong></p>
                    <button class="btn btn-outline-primary" onclick="ajouterAuPanier(${produit.id})">Ajouter au panier</button>
                    <a href="chat.php?id_commercant=${produit.id_commercant}" class="btn btn-outline-secondary btn-sm mt-2">Contacter le vendeur</a>
                </div>
            </div>
        `;
        conteneur.appendChild(div);
    });
}

async function updateCartView() {
    const conteneur = document.getElementById("cart-items-nav");
    conteneur.innerHTML = "Panier vide";
    let total = 0;
    let totalItems = 0;

    if (cart.length > 0) {
        conteneur.innerHTML = "";
        cart.forEach(item => {
            total += item.prix * item.quantite;
            totalItems += item.quantite;
            const div = document.createElement("div");
            div.className = "cart-item d-flex justify-content-between align-items-center";
            div.innerHTML = `
                <div>
                    <strong>${item.nom}</strong><br>
                    <small>${item.prix} × ${item.quantite} = ${item.prix * item.quantite} MRU</small>
                </div>
                <button class="btn btn-sm btn-danger" onclick="retirerDuPanier(${item.id})"><i class="bi bi-trash"></i></button>
            `;
            conteneur.appendChild(div);
        });
    }
    
    document.getElementById("cart-count-nav").innerText = totalItems;
    document.getElementById("total-price-nav").innerText = total.toFixed(2) + " MRU";
}

async function fetchCart() {
    const response = await fetch('cart_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'get' })
    });
    const data = await response.json();
    if (data.success) {
        cart = data.cart;
        updateCartView();
    }
}

async function ajouterAuPanier(id_produit) {
    await fetch('cart_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'add', id_produit: id_produit })
    });
    await fetchCart();
}

async function retirerDuPanier(id_produit) {
    await fetch('cart_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ action: 'remove', id_produit: id_produit })
    });
    await fetchCart();
}

async function envoyerCommande() {
    if (cart.length === 0) {
        alert("Le panier est vide.");
        return;
    }
    const formData = new FormData();
    formData.append("action", "envoyer_commande");

    const response = await fetch("client.php", {
        method: "POST",
        body: formData
    });
    const msg = await response.text();
    alert(msg);
    await fetchCart();
}

window.onload = () => {
    afficherProduits();
    fetchCart();
};
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
