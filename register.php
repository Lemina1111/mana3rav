<?php
include 'conn.php';

if(isset($_POST['register'])){
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $table = '';
    $redirect = '';

    switch ($role) {
        case 'client':
            $table = 'client';
            $redirect = 'client.php';
            break;
        case 'commercant':
            $table = 'commercant';
            $redirect = 'Produit.php';
            break;
        case 'administrateur':
            $table = 'administrateur';
            $redirect = 'prj.php';
            break;
        default:
            die("Type de compte invalide.");
    }

    $check_sql = "SELECT email FROM $table WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('❌ Cet email est déjà utilisé. Veuillez en choisir un autre.'); window.history.back();</script>";
    } else {
        $sql = "INSERT INTO $table (prénom, nom, email, mot_de_passe, tel) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssss", $prenom, $nom, $email, $password, $telephone);
            if ($stmt->execute()) {
                echo "Inscription réussie. Redirection...";
                echo "<script>window.location.href='$redirect';</script>";
                exit;
            } else {
                echo "Erreur d'exécution : " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $check_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
        <div class="box" id="register">
            <form id="registerForm" method="POST">
                <h2>Register</h2>
                <input type="text" id="nom" name="nom" placeholder="Nom" required>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                <input type="tel" id="telephone" name="telephone" placeholder="Téléphone" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <select name="role" id="role" required>
                    <option value="">Sélectionnez un rôle</option>
                    <option value="client">Client</option>
                    <option value="commercant">Commerçant</option>
                    <option value="administrateur">Administrateur</option>
                </select>
                <button type="submit" name="register">S'inscrire</button>
                <p>J'ai déjà un compte <a href="login.php">login</a></p>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            const nom = document.getElementById('nom').value;
            const prenom = document.getElementById('prenom').value;
            const telephone = document.getElementById('telephone').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;

            if (!nom || !prenom || !telephone || !email || !password || !role) {
                event.preventDefault();
                alert('Veuillez remplir tous les champs');
                return;
            }

            const phoneRegex = /^[0-9]{8}$/;
            if (!phoneRegex.test(telephone)) {
                event.preventDefault();
                alert('Veuillez entrer un numéro de téléphone valide (8 chiffres)');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                event.preventDefault();
                alert('Veuillez entrer une adresse email valide');
                return;
            }

            if (password.length < 6) {
                event.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }
        });
    </script>
</body>
</html>
