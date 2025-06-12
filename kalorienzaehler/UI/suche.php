<?php
//Vererbung der Attribute von "db.php" zur Verbindung mit der Datenbank
require 'db.php';
//Globale Variable "q", wird vom Skript "autocomplete.js" in Zeile 8 beschrieben
$q = $_GET['q'] ?? '';
//Bereite SQL Befehl vor für Autocomplete
$stmt = $pdo->prepare("
    SELECT EAN, Name, Kategorie 
    FROM Produkt 
    WHERE Name LIKE ? OR Kategorie LIKE ? 
    ORDER BY Name 
    LIMIT 10
");
//Suche ein Produkt anhand seines Namens, oder seiner Kategorie
$like = '%' . $q . '%';
$stmt->execute([$like, $like]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
?>