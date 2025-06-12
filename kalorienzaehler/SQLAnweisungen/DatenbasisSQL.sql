CREATE DATABASE Kalorientracker;
USE Kalorientracker;

CREATE TABLE User
(
    UserID   VARCHAR(30), /*Man kann lediglich eine Benutzer ID anlegen und keinen Spitznamen hinzufügen*/
    Passwort VARCHAR(255), /*Passwort hat 255 maximale Zeichenbytes, so kann man lange sichere Passwörter erstellen*/
    PRIMARY KEY (UserID)
);

CREATE TABLE Makros
(
    MakroID       int NOT NULL AUTO_INCREMENT,
    Kohlenhydrate double(10, 2), 
    Fett          double(10, 2),
    Eiweiss       double(10, 2),
    PRIMARY KEY (MakroID)
    /*Sog. "Makronährstoffe" werden in einer separaten Tabelle gespeichert, um Flexibel zu sein und 
    mögliche Redundanzen zu vermeiden. Die Einheit wird in Gramm/100g angegeben.*/
);

CREATE TABLE Produkt
(
    EAN       double, /*Double weil EAN groß sein kann*/
    MakroID   int NOT NULL,
    Name      varchar(255), /*255 Chars, weil die Namen der Produktdatenbank teilweise sehr lang sind*/
    Kategorie varchar(255), /*Teilweise ist in Kategorie eine ganze Beschreibung angelegt.*/
    PRIMARY KEY (EAN),
    FOREIGN KEY (MakroID) REFERENCES Makros (MakroID)
    /*Es werden Produkte gespeichert, die man im Tracker eingeben kann. Die Nährwerte werden durch die Tabelle "Makros" referenziert.*/
);

CREATE TABLE Verlauf
(
    VerlaufID int NOT NULL AUTO_INCREMENT,
    UserID    varchar(30), 
    EAN       double, 
    Menge     double(10, 2),
    Datum     date,
    PRIMARY KEY (VerlaufID),
    FOREIGN KEY (UserID) REFERENCES User (UserID), 
    FOREIGN KEY (EAN) REFERENCES Produkt (EAN) 
    /*User hat eine Verlaufstabelle, die die EAN von jedem Produkt speichern kann, woraufhin beim Eintragen eines Produktes automatisch die Makros einem Benutzer zugewiesen werden.
    Die Kalorien werden dabei aus den Nährstoffen berechnet, wobei der Benutzer eine Menge des Produktes in Gramm eingeben muss, also wieviel er davon konsumiert hat. 
    Diese haben ebenfalls VerlaufIDs, um die Löschung von möglicherweise falsch eingetragener Werte zu vereinfachen. 
    Jeder Benutzer kann dabei beliebig viele Einträge pro Tag machen.*/
);

CREATE TABLE Gewichtseintrag
(
    Datum   date,
    UserID  varchar(30),
    Gewicht int,
    PRIMARY KEY (Datum, UserID),
    FOREIGN KEY (UserID) REFERENCES User (UserID)
    /*Jeder User soll die Möglichkeit haben, sein Gewicht zu protokollieren. 
    Dies kann jeder User nur einmal pro Tag machen, weshalb ein zusammengesetzter Schlüssel aus Datum und UserID völlig ausreichend ist, um einen Gewichtseintrag eindeutig zu identifizieren.*/
);

CREATE TABLE Sucht
(
    UserID        varchar(30),
    EAN           double,
    Suchzeitpunkt datetime,
    EingbMenge double(10, 2),
    FOREIGN KEY (UserID) REFERENCES User (UserID),
    FOREIGN KEY (EAN) REFERENCES Produkt (EAN)
    /*Die Zwischentabelle "Sucht" verbindet User mit Produkt, damit dieser Produkte suchen kann. Dies geschieht mittelst einer n:m Assoziation, da jedes Produkt von jedem User gesucht werden können soll und umgekehrt.
    Wäre es nur eine 1:n Assoziation, könnten nur spezifische User auf diverse Produkte zugreifen.*/
);
