<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // User aus DB holen
    $stmt = $pdo->prepare("SELECT Passwort FROM User WHERE UserID = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Passwort'])) {
        // Passwort stimmt: Session setzen
        $_SESSION['username'] = $username;
        header('Location: start.php');
        exit;
    } else {
        echo "Falscher Benutzername oder Passwort.";
    }
} else {
    echo "Ungültige Anfrage.";
}
?>