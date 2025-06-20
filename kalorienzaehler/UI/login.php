<?php
session_start();
//Vererbung der Attribute von "db.php" zur Verbindung mit der Datenbank
require 'db.php';

//Überprüfe, HTML-Anfrage eine POST-Anfrage ist. Wenn es eine GET-Anfrage wäre, würde das Passwort und der Username in der Adresszeile landen, GET wollen wir also komplett vermeiden, also prüfen wir.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Username und Passwort aus entsprechenden Zeilen auslesen.
    $usernameAusZeile = trim($_POST['username']);
    $passwordAusZeile = $_POST['password'];

    //User aus Datenbank holen
    $stmt = $pdo->prepare("SELECT Passwort FROM User WHERE UserID = ?");
    $stmt->execute([$usernameAusZeile]);
    //Hole Reaktionszeile vom Prepared Statement
    $passwortAusDB = $stmt->fetch(PDO::FETCH_ASSOC);

    //Überprüfe, ob Passwörter übereinstimmen 
    if ($passwortAusDB && password_verify($passwordAusZeile, $passwortAusDB['Passwort'])) {
        //Wenn Passwort stimmt: Session starten mit UserID als Identifier.
        $_SESSION['username'] = $usernameAusZeile;
        header('Location: start.php');
        exit;
    } else {
        echo "Falscher Benutzername oder Passwort.";
    }
} else {
    echo "Ungültige Anfrage.";
}
?>