<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit;
}

$username = $_SESSION['username'];

echo "<h1>Willkommen, " . htmlspecialchars($username) . "!</h1>";
echo "<p><a href='logout.php'>Ausloggen</a></p>";

// Verlauf mit Produkt- und Makro-Daten abfragen
$stmt = $pdo->prepare("
    SELECT v.Datum, p.Name, p.Kategorie, m.Kohlenhydrate, m.Fett, m.Eiweiss
    FROM Verlauf v
    JOIN Produkt p ON v.EAN = p.EAN
    JOIN Makros m ON p.MakroID = m.MakroID
    WHERE v.UserID = ?
    ORDER BY v.Datum DESC
");
$stmt->execute([$username]);
$eintraege = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$eintraege) {
    echo "<p>Keine Einträge im Verlauf gefunden.</p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Datum</th><th>Produkt</th><th>Kategorie</th><th>Kohlenhydrate (g)</th><th>Fett (g)</th><th>Eiweiß (g)</th></tr>";

    foreach ($eintraege as $eintrag) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($eintrag['Datum']) . "</td>";
        echo "<td>" . htmlspecialchars($eintrag['Name']) . "</td>";
        echo "<td>" . htmlspecialchars($eintrag['Kategorie']) . "</td>";
        echo "<td>" . htmlspecialchars($eintrag['Kohlenhydrate']) . "</td>";
        echo "<td>" . htmlspecialchars($eintrag['Fett']) . "</td>";
        echo "<td>" . htmlspecialchars($eintrag['Eiweiss']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
<h2>Neuen Eintrag hinzufügen</h2>

<form action="add_entry.php" method="post">
    Datum:<br>
    <input type="date" name="datum" required><br><br>

    <!-- Auswahlfeld für vorhandene Produkte -->
    Produkt aus Liste auswählen:<br>
    <select name="ean">
        <option value="">-- Bitte wählen --</option>
        <?php
        // Produkte aus DB laden
        $produkte = $pdo->query("SELECT EAN, Name FROM Produkt ORDER BY Name")->fetchAll();
        foreach ($produkte as $produkt) {
            echo '<option value="' . htmlspecialchars($produkt['EAN']) . '">' . htmlspecialchars($produkt['Name']) . '</option>';
        }
        ?>
    </select><br><br>

    <strong>Oder neues Produkt hinzufügen:</strong><br>
    Name:<br>
    <input type="text" name="neu_name"><br><br>
    Kategorie:<br>
    <input type="text" name="neu_kategorie"><br><br>
    Kohlenhydrate (g):<br>
    <input type="number" step="0.01" name="neu_kohlenhydrate"><br><br>
    Fett (g):<br>
    <input type="number" step="0.01" name="neu_fett"><br><br>
    Eiweiß (g):<br>
    <input type="number" step="0.01" name="neu_eiweiss"><br><br>

    <input type="submit" value="Eintragen">
</form>

