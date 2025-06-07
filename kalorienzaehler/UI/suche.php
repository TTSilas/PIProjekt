<?php
require 'db.php';

$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT EAN, Name FROM Produkt WHERE Name LIKE ? ORDER BY Name LIMIT 10");
$stmt->execute(["%$q%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
