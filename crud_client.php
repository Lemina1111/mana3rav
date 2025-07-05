<?php
include 'conn.php';

// Traitement des actions (modifier, supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editSubmit'])) {
        // Logique de modification
        $id = $_POST['editId'];
        $prenom = $_POST['editPrenom'];
        $nom = $_POST['editNom'];
        $email = $_POST['editEmail'];

        $stmt = $conn->prepare("UPDATE client SET prénom = ?, nom = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $prenom, $nom, $email, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: crud_client.php"); // Rediriger pour éviter le resoumission
        exit;
    } elseif (isset($_POST['deleteId'])) {
        // Logique de suppression
        $id = $_POST['deleteId'];
        $stmt = $conn->prepare("DELETE FROM client WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo "Client supprimé";
        exit;
    }
}

$result = $conn->query("SELECT id, prénom, nom, email, tel FROM client");
$clients = [];
while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Clients</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="prj.css">
    <style>
        /* Styles pour la modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border-radius: 10px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 1rem;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-save,
        .btn-cancel {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-save {
            background-color: #1976d2;
            color: white;
        }

        .btn-save:hover {
            background-color: #1565c0;
        }

        .btn-cancel {
            background-color: #f5f5f5;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #e0e0e0;
        }
    </style>
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
                <a href="prj.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Table de bord</h3>
                </a>
                <a href="crud_client.php" class="active">
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
            <h1>Gestion des Clients</h1>
            
            <div class="recent-orders">
                <h2>Liste des Clients</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client['id']) ?></td>
                            <td><?= htmlspecialchars($client['prénom']) ?></td>
                            <td><?= htmlspecialchars($client['nom']) ?></td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td><?= htmlspecialchars($client['tel']) ?></td>
                            <td>
                                <button class="btn-edit" data-id="<?= $client['id'] ?>"><span class="material-symbols-outlined">edit</span></button>
                                <button class="btn-delete" data-id="<?= $client['id'] ?>"><span class="material-symbols-outlined">delete</span></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal de modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier les informations du client</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="crud_client.php">
                    <input type="hidden" id="editId" name="editId">
                    <div class="form-group">
                        <label for="editPrenom">Prénom</label>
                        <input type="text" id="editPrenom" name="editPrenom" required>
                    </div>
                    <div class="form-group">
                        <label for="editNom">Nom</label>
                        <input type="text" id="editNom" name="editNom" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="editEmail" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="editSubmit" class="btn-save">Enregistrer</button>
                        <button type="button" class="btn-cancel">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('editModal');
        const closeBtn = document.querySelector('.close');
        const cancelBtn = document.querySelector('.btn-cancel');
        const editButtons = document.querySelectorAll('.btn-edit');
        const deleteButtons = document.querySelectorAll('.btn-delete');

        function openModal(data) {
            document.getElementById('editId').value = data.id;
            document.getElementById('editPrenom').value = data.prenom;
            document.getElementById('editNom').value = data.nom;
            document.getElementById('editEmail').value = data.email;
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        closeBtn.onclick = closeModal;
        cancelBtn.onclick = closeModal;
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        editButtons.forEach(button => {
            button.onclick = function() {
                const row = this.closest('tr');
                const cells = row.cells;
                const data = {
                    id: cells[0].textContent,
                    prenom: cells[1].textContent,
                    nom: cells[2].textContent,
                    email: cells[3].textContent
                };
                openModal(data);
            };
        });

        deleteButtons.forEach(button => {
            button.onclick = function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
                    const id = this.dataset.id;
                    const formData = new FormData();
                    formData.append('deleteId', id);

                    fetch('crud_client.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
                }
            };
        });
    </script>
</body>
</html>
