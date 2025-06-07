<?php
$host = 'localhost';
$dbname = 'Kalorientracker';
$user = 'silas';
$password = '1806';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbank-Verbindungsfehler: " . $e->getMessage());
}
?>


