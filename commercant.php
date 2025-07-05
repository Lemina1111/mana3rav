<?php
include 'conn.php';

// Traitement des actions (modifier, supprimer, suspendre/activer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editSubmit'])) {
        // Logique de modification
        $id = $_POST['editId'];
        $nom = $_POST['editOwner']; // Suppose que le nom du propriétaire est le nom du commerçant
        $email = $_POST['editEmail'];
        $tel = $_POST['editPhone'];
        $statut = $_POST['editStatus']; // Le statut n'est pas dans la DB, donc on ne peut pas le mettre à jour

        // Séparez le nom complet en prénom et nom
        $nom_parts = explode(' ', $nom, 2);
        $prenom = $nom_parts[0];
        $nom_de_famille = isset($nom_parts[1]) ? $nom_parts[1] : '';

        $stmt = $conn->prepare("UPDATE commercant SET prénom = ?, nom = ?, email = ?, tel = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $prenom, $nom_de_famille, $email, $tel, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: commercant.php"); // Rediriger pour éviter le resoumission
        exit;
    } elseif (isset($_POST['deleteId'])) {
        // Logique de suppression
        $id = $_POST['deleteId'];
        $stmt = $conn->prepare("DELETE FROM commercant WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo "Commerçant supprimé";
        exit;
    }
}


$result = $conn->query("SELECT * FROM commercant");
$commercants = [];
while ($row = $result->fetch_assoc()) {
    $commercants[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Commerçants</title>
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
                <a href="client.php">
                    <span class="material-symbols-outlined">group</span>
                    <h3>Clients</h3>
                </a>
                <a href="commercant.php" class="active">
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
            <h1>Gestion des Commerçants</h1>
            
            <div class="recent-orders">
                <h2>Liste des Commerçants</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Propriétaire</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commercants as $commercant): ?>
                        <tr>
                            <td><?= htmlspecialchars($commercant['id']) ?></td>
                            <td><?= htmlspecialchars($commercant['prénom'] . ' ' . $commercant['nom']) ?></td>
                            <td><?= htmlspecialchars($commercant['email']) ?></td>
                            <td><?= htmlspecialchars($commercant['tel']) ?></td>
                            <td><span class="status active">Actif</span></td>
                            <td>
                                <button class="btn-edit" data-id="<?= $commercant['id'] ?>"><span class="material-symbols-outlined">edit</span></button>
                                <button class="btn-delete" data-id="<?= $commercant['id'] ?>"><span class="material-symbols-outlined">delete</span></button>
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
                <h2>Modifier les informations du commerçant</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="commercant.php">
                    <input type="hidden" id="editId" name="editId">
                    <div class="form-group">
                        <label for="editOwner">Propriétaire</label>
                        <input type="text" id="editOwner" name="editOwner" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="editEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhone">Téléphone</label>
                        <input type="tel" id="editPhone" name="editPhone" required>
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
            document.getElementById('editOwner').value = data.owner;
            document.getElementById('editEmail').value = data.email;
            document.getElementById('editPhone').value = data.phone;
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
                    owner: cells[1].textContent,
                    email: cells[2].textContent,
                    phone: cells[3].textContent
                };
                openModal(data);
            };
        });

        deleteButtons.forEach(button => {
            button.onclick = function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce commerçant ?')) {
                    const id = this.dataset.id;
                    const formData = new FormData();
                    formData.append('deleteId', id);

                    fetch('commercant.php', {
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
