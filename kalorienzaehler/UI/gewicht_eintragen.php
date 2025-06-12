<?php
session_start();
//Vererbung der Attribute von "db.php" zur Verbindung mit der Datenbank
require 'db.php';

//Überprüfe, ob User angemeldet ist, wenn nicht: Schick ihn auf die Login seite
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit;
}

//Prüfe ob Eingabedaten im HTML-Body versteckt sind.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Speichere die in den Elementen mit entsprechender ID enthaltenen Daten in PHP Variablen.
    $gewicht = $_POST['gewicht'];
    $datum = $_POST['datum'] ?? date('Y-m-d');
    $user = $_SESSION['username'];
    /*Bereite ein Statement vor, das die Attribute am entsprechenden Tag verändert. Gewichtseingabe soll nur einmal pro Tag möglich sein, also gleich den REPLACE Befehl verwenden, um Daten zu ersetzen,
    sollte der User zweimal am selben Tag ein Gewicht eintragen*/
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
    <!--Das POST Formular bezieht sich hier auf das oben beschriebene PHP Skript.-->
    <form method="POST">
        <label>Datum:
            <input type="date" name="datum" value="<?= date('Y-m-d') ?>" required>
        </label><br><br>
        <label>Gewicht (kg):
            <input type="number" name="gewicht" step="0.1" required>
        </label><br><br>
        <!--Drückt der User auf den Button, werden die eingetragenen Werte vom Server bearbeitet, bzw. das Gewicht wird einfach für den aktuellen Tag eingetragen.-->
        <div class="button-container">
            <button class="logging" type="submit">Speichern</button>
        </div>
    </form>
    <br>
    <a href="start.php" class="button-link">Zurück zur Startseite</a>
</body>

</html>