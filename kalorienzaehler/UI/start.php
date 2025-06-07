<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

$user = $_SESSION['username'];

// Hier deine SQL-Abfrage (Produktliste mit Menge usw.)
$stmt = $pdo->prepare("
    SELECT v.Datum, p.Name, p.Kategorie, v.Menge,
           ((m.Kohlenhydrate * 4 + m.Eiweiss * 4 + m.Fett * 9) * (v.Menge / 100))/100 AS KalorienProEinheit
    FROM Verlauf v
    JOIN Produkt p ON v.EAN = p.EAN
    JOIN Makros m ON p.MakroID = m.MakroID
    WHERE v.UserID = ?
    ORDER BY v.Datum DESC, p.Name
");
$stmt->execute([$user]);
$produkte = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Startseite</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h1>Willkommen, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>

    <ul>
        <li><a href="dashboard.php" class="button-link">Verlauf und Diagramm</a></li>
        <li><a href="gewicht_eintragen.php" class="button-link">Gewicht eintragen</a></li>
        <li><a href="eintrag_hinzufuegen.php" class="button-link">Produkt eintragen</a></li>
        <li><a href="logout.php" class="button-link">Ausloggen</a></li>
    </ul>

<h3>Meine eingetragenen Produkte</h3>

<?php if (count($produkte) === 0): ?>
    <p>Du hast noch keine Produkte eingetragen.</p>
<?php else: ?>
    <div class="scroll-container">
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Produktname</th>
                    <th>Kategorie</th>
                    <th>Menge (g)</th>
                    <th>Kalorien (gesamt)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produkte as $produkt): ?>
                    <tr>
                        <td><?= htmlspecialchars($produkt['Datum']) ?></td>
                        <td><?= htmlspecialchars($produkt['Name']) ?></td>
                        <td><?= htmlspecialchars($produkt['Kategorie']) ?></td>
                        <td><?= htmlspecialchars($produkt['Menge']) ?></td>
                        <td><?= round($produkt['KalorienProEinheit'] * $produkt['Menge']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>



</body>
</html>
