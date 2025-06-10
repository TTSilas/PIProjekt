<?php
require 'db.php';

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("
    SELECT EAN, Name, Kategorie 
    FROM Produkt 
    WHERE Name LIKE ? OR Kategorie LIKE ? 
    ORDER BY Name 
    LIMIT 10
");

$like = '%' . $q . '%';
$stmt->execute([$like, $like]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
?>
