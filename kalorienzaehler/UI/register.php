<?php
//Vererbung der Attribute von "db.php" zur Verbindung mit der Datenbank
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        die("Die Passwörter stimmen nicht überein.");
    }

    //Check, ob Benutzername schon existiert
    $stmt = $pdo->prepare("SELECT UserID FROM User WHERE UserID = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        die("Benutzername ist bereits vergeben.");
    }

    //Passwort hashen
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    //User in DB anlegen
    $stmt = $pdo->prepare("INSERT INTO User (UserID, Passwort) VALUES (?, ?)");
    if ($stmt->execute([$username, $passwordHash])) {
        echo "Benutzer erfolgreich registriert. <a href='index.html'>Zum Login</a>";
    } else {
        echo "Fehler bei der Registrierung.";
    }
} else {
    echo "Ungültige Anfrage.";
}
?>