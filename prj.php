<?php
include 'conn.php';

// Fetching data for the cards
$revenue_result = $conn->query("SELECT SUM(Montant) as total_revenue FROM Paiement");
$revenue = $revenue_result->fetch_assoc()['total_revenue'] ?? 0;

$customers_result = $conn->query("SELECT COUNT(*) as total_customers FROM client");
$customers = $customers_result->fetch_assoc()['total_customers'] ?? 0;

$sales_result = $conn->query("SELECT COUNT(*) as total_sales FROM Commande");
$sales = $sales_result->fetch_assoc()['total_sales'] ?? 0;

// Fetching recent orders
$recent_orders_result = $conn->query("SELECT c.id, cl.nom as client_nom, c.prix as montant FROM Commande c JOIN client cl ON c.id_client = cl.id ORDER BY c.date_commande DESC LIMIT 5");
$recent_orders = [];
while ($row = $recent_orders_result->fetch_assoc()) {
    $recent_orders[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="prj.css">
</head>
<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <h2><span class="danger">commande</span></h2>
                    <div class="close">
                        <span class="material-symbols-outlined">close</span>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <a href="prj.php" class="active">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Table de bord</h3>
                </a>
                <a href="crud_client.php">
                    <span class="material-symbols-outlined">group</span>
                    <h3>Clients</h3>
                </a>
                <a href="commercant.php">
                    <span class="material-symbols-outlined">storefront</span>
                    <h3>Commerçants</h3>
                </a>
                <a href="#">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <h3>Commandes</h3>
                </a>
                <a href="#">
                    <span class="material-symbols-outlined">payment</span>
                    <h3>Paiement</h3>
                </a>  
                <a href="#">
                  <span class="material-symbols-outlined">bar_chart</span>
                  <h3>Statistiques</h3>
              </a>
                      
            </div>
        </aside>
        <main>
            <h1>Bienvenue Youssef</h1>
            <div class="top-right-buttons">

                <div class="profile-menu">
                  <button class="profile-btn">
                    <span class="material-symbols-outlined">account_circle</span> Profil
                    <span class="material-symbols-outlined">arrow_drop_down</span>
                  </button>
                  <div class="dropdown-content">
                    <a href="#">Paramètres</a>
                    <a href="logout.php">Déconnexion</a>
                    <a href="#">Changer mot de passe</a>
                  </div>
                </div>
              
                <div class="language-menu">
                  <button class="language-btn">
                    <span class="material-symbols-outlined">language</span> Langue
                    <span class="material-symbols-outlined">arrow_drop_down</span>
                  </button>
                  <div class="dropdown-content">
                    <a href="#">Français</a>
                    <a href="#">Anglais</a>
                    <a href="#">Arabe</a>
                  </div>
                </div>
              
              </div>
              
            
            
            <div class="cards">
                <div class="card">
                    <span class="material-symbols-outlined">paid</span>
                    <div class="content">
                        <h3>Revenue</h3>
                        <p><?= number_format($revenue, 2) ?> €</p>
                    </div>
                </div>
                <div class="card">
                    <span class="material-symbols-outlined">groups</span>
                    <div class="content">
                        <h3>Customers</h3>
                        <p><?= $customers ?></p>
                    </div>
                </div>
                <div class="card">
                    <span class="material-symbols-outlined">sell</span>
                    <div class="content">
                        <h3>Sales</h3>
                        <p><?= $sales ?></p>
                    </div>
                </div>
                <div class="card">
                    <span class="material-symbols-outlined">subscriptions</span>
                    <div class="content">
                        <h3>Subscription</h3>
                        <p>800</p>
                    </div>
                </div>
            </div>
            <section class="dashboard-tables">

                
                <div class="recent-orders">
                  <h2>Commandes Récentes</h2>
                  <table>
                    <thead>
                      <tr>
                        <th>ID Commande</th>
                        <th>Client</th>
                        <th>Statut</th>
                        <th>Montant (€)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($recent_orders as $order): ?>
                      <tr>
                        <td>CMD<?= htmlspecialchars($order['id']) ?></td>
                        <td><?= htmlspecialchars($order['client_nom']) ?></td>
                        <td>Livré</td>
                        <td><?= number_format($order['montant'], 2) ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              
                
                <div class="recent-activity">
                  <h2>Activité récente</h2>
                  <table>
                    <thead>
                      <tr>
                        <th>Activité</th>
                        <th>Date / Heure</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>You logged in</td>
                        <td>2025-05-27 09:15</td>
                      </tr>
                      <tr>
                        <td>Profile update</td>
                        <td>2025-05-26 18:40</td>
                      </tr>
                      <tr>
                        <td>New product added</td>
                        <td>2025-05-25 14:30</td>
                      </tr>
                      <tr>
                        <td>Order ID CMD1003 completed</td>
                        <td>2025-05-24 12:20</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              
              </section>
              
              
        </main>
    </div>
</body>
</html>
