<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

$user = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ean = $_POST['ean'];
    $datum = $_POST['datum'] ?? date('Y-m-d');
    $menge = $_POST['menge'];

    $stmt = $pdo->prepare("INSERT INTO Verlauf (UserID, EAN, Datum, Menge) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user, $ean, $datum, $menge]);

    echo "Eintrag wurde gespeichert.";
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Produkt auswählen & eintragen</title>
    <script src="autocomplete.js"></script>
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

        <label>Produktname:
            <input type="text" id="produktname" name="produktname" required>
            <input type="hidden" id="ean" name="ean">
        </label><br><br>

        <label for="menge">Menge (g):</label>
        <input type="number" id="menge" name="menge" value="100" min="1" required><br><br>

        <div id="autocomplete-list" class="autocomplete-suggestions"></div>

        <div class="button-container">
            <button class="logging" type="submit">Eintragen</button>
        </div>
    </form>


    <br>
    <a href="start.php" class="button-link">Zurück zur Startseite</a>

    <script>
        autocomplete(document.getElementById("produktname"), document.getElementById("ean"), "suche.php");
    </script>
</body>

</html>