<?php
session_start();
//Vererbung der Attribute von "db.php" zur Verbindung mit der Datenbank
require 'db.php';

//Überprüfe, ob User angemeldet ist, wenn nicht: Schick ihn auf die Login seite
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit;
}

//Speichere die User ID in der Variable "user". Entnehme den Namen aus der Session.
$user = $_SESSION['username'];

//Prüfe ob Server POST verwendet (Ob die folgenden Daten im HTML-Body, oder in der URL angegeben werden)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ean = $_POST['ean'];
    $datum = $_POST['datum'] ?? date('Y-m-d');
    $time = $_POST['time'] ?? date('Y-m-d H:i:s');
    $menge = $_POST['menge'];

    //Füge eingegebene Werte aus den Eingabezeilen in Verlauf ein, beim Drücken auf den Button.
    $stmt = $pdo->prepare("INSERT INTO Verlauf (UserID, EAN, Datum, Menge) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user, $ean, $datum, $menge]);
    //Selbiges wie oben, nur dass eben die genaue Zeit mit Uhrzeit in die Tabelle "Sucht" für den Eingabeverlauf eingegeben wird.
    $stmt = $pdo->prepare("INSERT INTO Sucht (UserID, EAN, Suchzeitpunkt, EingbMenge) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user, $ean, $time, $menge]);

    echo "Eintrag wurde gespeichert.";
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Produkt auswählen & eintragen</title>
    <script src="autocomplete.js"></script>
    <!--CSS legt den Style für das Feld mit der Autosuggestion fest, soll ja schön aussehen.-->
    <style>
        .autocomplete-suggestions {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            background-color: white;
            z-index: 999;
        }

        .autocomplete-suggestion {
            padding: 5px;
            cursor: pointer;
        }

        .autocomplete-suggestion:hover {
            background-color: #ddd;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Produkt eintragen</h2>
    <form method="POST" autocomplete="off">
        <label>Datum:
            <input type="date" name="datum" value="<?= date('Y-m-d') ?>" required>
        </label><br><br>
        <!--Die Produktnamen werden Eingegeben und deren EAN wird im entsprechenden Feld gespeichert und versteckt. Somit wird die EAN des Produktes gleichzeitig in Verlauf gespeichert, ohne dass ein User es sieht.-->
        <label>Produktname/Kategorie:
            <input type="text" id="produktname" name="produktname" required>
            <input type="hidden" id="ean" name="ean">
        </label><br><br>

        <label for="menge">Menge (g):</label>
        <input type="number" id="menge" name="menge" value="100" min="1" required><br><br>

        <!--Feld für die Autosuggestion, wird gefüllt über das Skript "autocomplete.js" in Zeile 12,13 und 19ff.-->
        <div id="autocomplete-list" class="autocomplete-suggestions"></div>
        <!--Wenn User auf den Button "Eintragen" clickt, wird das Formular dem PHP Server übergeben und der PHP Code oben, wird vom Server ausgeführt, welcher entsprechende Informationen zurückgibt.-->
        <div class="button-container">
            <button class="logging" type="submit">Eintragen</button>
        </div>
    </form>


    <br>
    <a href="start.php" class="button-link">Zurück zur Startseite</a>

    <script>
        //Einbindung des Autocomplete Skriptes
        autocomplete(document.getElementById("produktname"), document.getElementById("ean"), "suche.php");
    </script>
</body>

</html>