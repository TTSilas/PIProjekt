<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gewicht = $_POST['gewicht'];
    $datum = $_POST['datum'] ?? date('Y-m-d');
    $user = $_SESSION['username'];

    $stmt = $pdo->prepare("REPLACE INTO Gewichtseintrag (Datum, UserID, Gewicht) VALUES (?, ?, ?)");
    $stmt->execute([$datum, $user, $gewicht]);

    echo "Gewicht gespeichert für $datum.";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Gewicht eintragen</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h2>Gewicht eintragen</h2>
    <form method="POST">
        <label>Datum:
            <input type="date" name="datum" value="<?= date('Y-m-d') ?>" required>
        </label><br><br>
        <label>Gewicht (kg):
            <input type="number" name="gewicht" step="0.1" required>
        </label><br><br>
        <button type="submit">Speichern</button>
    </form>
    <br>
    <a href="start.php" class="button-link">Zurück zur Startseite</a>
</body>
</html>
