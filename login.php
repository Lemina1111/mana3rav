 
 <?php
session_start();
include 'conn.php'; // connexion à la base

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, mot_de_passe FROM administrateur WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($admin_id, $admin_pwd);
if ($stmt->fetch() && password_verify($password, $admin_pwd)) {
    $_SESSION['role'] = 'admin';
    $_SESSION['id_admin'] = $admin_id;
    $_SESSION['email'] = $email;
    header("Location: prj.php");
    exit;
}
$stmt->close();



    $stmt = $conn->prepare("SELECT id, mot_de_passe FROM client WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($client_id, $client_pwd);
if ($stmt->fetch() && password_verify($password, $client_pwd)) {
    $_SESSION['role'] = 'client';
    $_SESSION['id_client'] = $client_id;
    $_SESSION['email'] = $email;
    header("Location: client.php");
    exit;
}
$stmt->close();




    $stmt = $conn->prepare("SELECT id, mot_de_passe FROM commercant WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($com_id, $com_pwd);
if ($stmt->fetch() && password_verify($password, $com_pwd)) {
    $_SESSION['role'] = 'commercant';
    $_SESSION['id_commercant'] = $com_id;
    $_SESSION['email'] = $email;
    header("Location: produit.php");
    exit;
}
$stmt->close();

}
?>

 
 
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="login.css">

</head>

<body>
    <div class="container">
        <div class="box" id="login">
            <form action="" method="post">
                <h2>Login</h2>
                <input type="email" placeholder="Email" id="email" name="email">
                <input type="password" placeholder="Password" id="password" name="password">
                <button type="submit" name="login">Login</button>
                <p>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
            </form>
        </div>
    </div>
</body>

       
</html>