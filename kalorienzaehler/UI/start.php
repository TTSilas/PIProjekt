<?php
session_start();
//Vererbung der Attribute von "db.php" zur Verbindung mit der Datenbank
require 'db.php';

//Überprüfe, ob User angemeldet ist, wenn nicht: Schick ihn auf die Login seite
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit;
}

$user = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    //echo "DELETE VerlaufID: $delete_id, UserID: $user<br>";  // Debug
    $stmt = $pdo->prepare("DELETE FROM Verlauf WHERE VerlaufID = ? AND UserID = ?");
    $stmt->execute([$delete_id, $user]);
}


//Berechnung der Kalorien und Abrufen der Makros
$stmt = $pdo->prepare("
    SELECT v.VerlaufID, v.Datum, p.Name, p.Kategorie, v.Menge,
           (m.Kohlenhydrate * 4 + m.Eiweiss * 4 + m.Fett * 9) * (v.Menge / 100) AS KalorienProEinheit
    FROM Verlauf v
    JOIN Produkt p ON v.EAN = p.EAN
    JOIN Makros m ON p.MakroID = m.MakroID
    WHERE v.UserID = ?
    ORDER BY v.Datum DESC, p.Name
");
$stmt->execute([$user]);
$produkte = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Makro-Summen berechnen (gesamt oder heute)
$heute = date('Y-m-d');

//Für gesamten Pie-Chart
$stmt = $pdo->prepare("
    SELECT 
        SUM(m.Kohlenhydrate * v.Menge / 100) AS Kohlenhydrate,
        SUM(m.Eiweiss * v.Menge / 100) AS Eiweiss,
        SUM(m.Fett * v.Menge / 100) AS Fett
    FROM Verlauf v
    JOIN Produkt p ON v.EAN = p.EAN
    JOIN Makros m ON p.MakroID = m.MakroID
    WHERE v.UserID = ?
");
$stmt->execute([$user]);
$gesamtMakros = $stmt->fetch(PDO::FETCH_ASSOC);

//Für heutigen Pie-Chart
$stmt = $pdo->prepare("
    SELECT 
        SUM(m.Kohlenhydrate * v.Menge / 100) AS Kohlenhydrate,
        SUM(m.Eiweiss * v.Menge / 100) AS Eiweiss,
        SUM(m.Fett * v.Menge / 100) AS Fett
    FROM Verlauf v
    JOIN Produkt p ON v.EAN = p.EAN
    JOIN Makros m ON p.MakroID = m.MakroID
    WHERE v.UserID = ? AND v.Datum = ?
");
$stmt->execute([$user, $heute]);
$heuteMakros = $stmt->fetch(PDO::FETCH_ASSOC);
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

    <div class="auswahlStart">
        <ul>
            <li><a href="dashboard.php" class="button-link">Verlauf und Diagramm</a></li>
            <li><a href="gewicht_eintragen.php" class="button-link">Gewicht eintragen</a></li>
            <li><a href="VerlaufSicht.php" class="button-link">Produkt Verlauf</a></li>
            <li><a href="eintrag_hinzufuegen.php" class="button-link">Produkt eintragen</a></li>
            <li><a href="logout.php" class="button-link">Ausloggen</a></li>
        </ul>
    </div>
    <h3>Makronährstoffverteilung</h3>
    <div class="button-container">
        <!--Auswahl, ob der Pie-Chart für heute, oder insgesamt angegeben werden soll.-->
        <button class="chart-button" onclick="zeigeDiagramm('heute')" id="btleft">Heute</button>
        <button class="chart-button" onclick="zeigeDiagramm('gesamt')" id="btright">Gesamt</button>
    </div>
    <!--Pie-Chart Canvas-->
    <canvas id="makroChart" width="300" height="300"></canvas>

    <!--Externe Quelle für Pie-Charts-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        //Speichere Makros von heute und gesamte Makros in jeweilige Variablen, als einzelnes Objekt.
        const makrosHeute = <?= json_encode($heuteMakros) ?>;
        const makrosGesamt = <?= json_encode($gesamtMakros) ?>;

        const ctx = document.getElementById('makroChart').getContext('2d');
        let chart;

        function zeigeDiagramm(typ) {
            const daten = typ === 'heute' ? makrosHeute : makrosGesamt;

            const config = {
                type: 'pie',
                data: {
                    labels: ['Kohlenhydrate', 'Eiweiß', 'Fett'],
                    datasets: [{
                        data: [daten.Kohlenhydrate, daten.Eiweiss, daten.Fett],
                        backgroundColor: ['#f39c12', '#27ae60', '#e74c3c']
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const data = context.dataset.data;
                                    const total = data.reduce((a, b) => a + b, 0);
                                    const value = context.raw;
                                    const percentage = ((value / total) * 100).toFixed(1) + '%';
                                    return `${context.label}: ${value}g (${percentage})`;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: typ === 'heute' ? 'Makros heute' : 'Makros gesamt'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            };

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, config);
        }

        zeigeDiagramm('heute');
    </script>

    <h3>Meine eingetragenen Produkte</h3>

    <?php if (count($produkte) === 0): ?>
        <p>Du hast noch keine Produkte eingetragen.</p>
    <?php else: ?>
        <div class="scroll-container">
            <table>
                <thead>
                    <tr>
                        <!--Lege eine Tabelle mit Spaltennamen in der Kopfzeile an-->
                        <th>Datum</th>
                        <th>Produktname</th>
                        <th>Kategorie</th>
                        <th>Menge (g)</th>
                        <th>Kalorien (kcal)</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <!--Für jede Spalte aus dem Array "produkte", speichere einzelne Objekt (also ganze Spalten) in "produkt"-->
                    <?php foreach ($produkte as $produkt): ?>
                        <tr>
                            <!--Füge der Tabelle Spalten hinzu, wobei jedes benötigte Attribut vom jeweiligen Objekt "produkt" aus dessen derzeitigen Loop, entnommen wird-->
                            <td><?= htmlspecialchars($produkt['Datum']) ?></td>
                            <td><?= htmlspecialchars($produkt['Name']) ?></td>
                            <td><?= htmlspecialchars($produkt['Kategorie']) ?></td>
                            <td><?= htmlspecialchars($produkt['Menge']) ?></td>
                            <td><?= round($produkt['KalorienProEinheit']) ?></td>
                            <td>
                                <!--Erstelle Formular für einen Lösch-Knopf der einzelnen Elemente-->
                                <form method="post" style="display:inline;" id="deleteDis">
                                    <!--Wenn der Knopf gedrückt wird, wird das Formular abgesendet und die Zeile wird gelöscht-->
                                    <input type="hidden" name="delete_id"
                                        value="<?= htmlspecialchars($produkt['VerlaufID']) ?>">
                                    <button type="submit" class="delete-button">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>



</body>

</html>