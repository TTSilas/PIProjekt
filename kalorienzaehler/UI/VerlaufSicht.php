<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

$user = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT Suchzeitpunkt, EAN Ziffer, (SELECT Name FROM Produkt WHERE Ziffer = Produkt.EAN LIMIT 1) AS 'PRName', EingbMenge AS 'Menge',
(SELECT Kategorie FROM Produkt WHERE Ziffer = Produkt.EAN LIMIT 1) AS 'KategorieName'
FROM Sucht s, User u WHERE u.UserID = s.UserID AND s.UserID = ? ORDER BY Suchzeitpunkt");
$stmt->execute([$user]);
$verlauf = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verlauf</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Verlauf ihrer eingetragenen Produkte</h1>
    <?php if (count($verlauf) === 0): ?>
        <h2>Hier ist Twix!</h2>
        <div class="TwixDiv">

            <img id="thefunny" src="./twix.jpg">
        </div>
        <h2>Du hast hier noch nichts eingetragen!</h2>
        <div class="auswahlStart">
            <a href="eintrag_hinzufuegen.php" class="button-link">Jetzt ein Produkt hinzufügen!</a>
        </div>
    <?php else: ?>
        <div class="scroll-container">
            <table>
                <thead>
                    <tr>
                        <th>Produktname</th>
                        <th>Kategorie</th>
                        <th>EAN</th>
                        <th>Zeitpunkt</th>
                        <th>Menge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verlauf as $einheit): ?>
                        <tr>


                            <td><?= htmlspecialchars($einheit['PRName']) ?></td>
                            <td><?= htmlspecialchars($einheit['KategorieName']) ?></td>
                            <td><?= htmlspecialchars($einheit['Ziffer']) ?></td>
                            <td><?= htmlspecialchars($einheit['Suchzeitpunkt']) ?></td>
                            <td><?= htmlspecialchars($einheit['Menge']) ?>g</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div class="auswahlStart">
        <a href="start.php" class="button-link">Zurück zur Startseite</a>
    </div>
</body>

</html>