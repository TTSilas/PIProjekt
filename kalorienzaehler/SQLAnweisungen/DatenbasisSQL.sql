CREATE DATABASE Kalorientracker;
USE Kalorientracker;

CREATE TABLE User
(
    UserID   VARCHAR(30),
    Passwort VARCHAR(255),
    Darkemode bit,
    PRIMARY KEY (UserID)
);

CREATE TABLE Makros
(
    MakroID       int NOT NULL AUTO_INCREMENT,
    Kohlenhydrate double(10, 2),
    Fett          double(10, 2),
    Eiweiss       double(10, 2),
    PRIMARY KEY (MakroID)
);

CREATE TABLE Produkt
(
    EAN       double,
    MakroID   int NOT NULL,
    Name      varchar(255),
    Kategorie varchar(255),
    PRIMARY KEY (EAN),
    FOREIGN KEY (MakroID) REFERENCES Makros (MakroID)
);

CREATE TABLE Verlauf
(
    VerlaufID int NOT NULL AUTO_INCREMENT,
    UserID    varchar(30),
    EAN       double,
    Datum     date,
    Menge     double(10,2),
    PRIMARY KEY (VerlaufID),
    FOREIGN KEY (UserID) REFERENCES User (UserID),
    FOREIGN KEY (EAN) REFERENCES Produkt (EAN)
);

CREATE TABLE Gewichtseintrag
(
    Datum   date,
    UserID  varchar(30),
    Gewicht int,
    PRIMARY KEY (Datum, UserID),
    FOREIGN KEY (UserID) REFERENCES User (UserID)
);

CREATE TABLE Sucht
(
    UserID        varchar(30),
    EAN           double,
    Suchzeitpunkt datetime,
    FOREIGN KEY (UserID) REFERENCES User (UserID),
    FOREIGN KEY (EAN) REFERENCES Produkt (EAN)
);
