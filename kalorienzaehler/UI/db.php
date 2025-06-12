<?php
//Für PHP relevante Infos, um auf Mariadb zugreifen zu können.
$host = 'localhost';
$dbname = 'Kalorientracker';
$user = '';
$password = '';

//Try-catch Methode überprüft, ob die Verbindung erfolgreich ist. Wenn nicht, gebe catch-Klausel zurück.
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbank-Verbindungsfehler: " . $e->getMessage());
}
?>
