<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ean = $_POST['ean'];
    $datum = $_POST['datum'];
    $user = $_SESSION['username'];

    // Prüfen, ob Produkt mit EAN existiert
    $stmt = $pdo->prepare("SELECT * FROM Produkt WHERE EAN = ?");
    $stmt->execute([$ean]);
    if (!$stmt->fetch()) {
        die("Produkt mit dieser EAN existiert nicht.");
    }

    // Eintrag hinzufügen
    $stmt = $pdo->prepare("INSERT INTO Verlauf (UserID, EAN, Datum) VALUES (?, ?, ?)");
    if ($stmt->execute([$user, $ean, $datum])) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Fehler beim Hinzufügen des Eintrags.";
    }
} else {
    echo "Ungültige Anfrage.";
}
?>
