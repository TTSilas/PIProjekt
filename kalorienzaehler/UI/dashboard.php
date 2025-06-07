<?php
session_start();
require 'db.php';

// Fehler anzeigen (nur für Entwicklung)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Benutzer eingeloggt?
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

$user = $_SESSION['username'];

// Kalorien aus Verlauf berechnen (Makros × Kalorienfaktor)
$stmt = $pdo->prepare("
    SELECT 
        v.Datum,
        SUM(m.Kohlenhydrate * 4 + m.Eiweiss * 4 + m.Fett * 9) AS Tageskalorien
    FROM Verlauf v
    JOIN Produkt p ON v.EAN = p.EAN
    JOIN Makros m ON p.MakroID = m.MakroID
    WHERE v.UserID = ?
    GROUP BY v.Datum
    ORDER BY v.Datum
");
$stmt->execute([$user]);
$kalorien = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gewichtseinträge
$stmt = $pdo->prepare("SELECT Datum, Gewicht FROM Gewichtseintrag WHERE UserID = ? ORDER BY Datum");
$stmt->execute([$user]);
$gewicht = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Daten kombinieren
$daten = [];
foreach ($kalorien as $k) {
    $datum = $k['Datum'];
    $daten[$datum]['kalorien'] = round($k['Tageskalorien']);
}
foreach ($gewicht as $g) {
    $datum = $g['Datum'];
    $daten[$datum]['gewicht'] = $g['Gewicht'];
}
ksort($daten); // Nach Datum sortieren

// Für JavaScript vorbereiten
$dates = array_keys($daten);
$caloriesData = array_map(fn($v) => $v['kalorien'] ?? 0, $daten);
$weightData = array_map(fn($v) => $v['gewicht'] ?? null, $daten);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h2>Kalorientracker – Verlauf</h2>

    <label><input type="checkbox" id="showCalories" checked> Kalorien anzeigen</label>
    <label><input type="checkbox" id="showWeight" checked> Gewicht anzeigen</label>

    <canvas id="chart" style="max-width: 800px; height: 400px;"></canvas>

    <script>
        const dates = <?= json_encode($dates) ?>;
        const calories = <?= json_encode($caloriesData) ?>;
        const weight = <?= json_encode($weightData) ?>;

        const ctx = document.getElementById('chart').getContext('2d');

        const chartConfig = {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Kalorien',
                        data: calories,
                        borderColor: 'orange',
                        backgroundColor: 'rgba(255,165,0,0.2)',
                        yAxisID: 'yCalories'
                    },
                    {
                        label: 'Gewicht (kg)',
                        data: weight,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0,0,255,0.2)',
                        yAxisID: 'yWeight'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                stacked: false,
                scales: {
                    yCalories: {
                        type: 'linear',
                        position: 'left',
                        title: { display: true, text: 'Kalorien' },
                        beginAtZero: true
                    },
                    yWeight: {
                        type: 'linear',
                        position: 'right',
                        title: { display: true, text: 'Gewicht (kg)' },
                        beginAtZero: false,
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        };

        const myChart = new Chart(ctx, chartConfig);

        // Checkbox-Steuerung
        document.getElementById('showCalories').addEventListener('change', function () {
            myChart.data.datasets[0].hidden = !this.checked;
            myChart.update();
        });

        document.getElementById('showWeight').addEventListener('change', function () {
            myChart.data.datasets[1].hidden = !this.checked;
            myChart.update();
        });
    </script>

    <br>
<a href="start.php" class="button-link">Zurück zur Startseite</a>

</body>
</html>
