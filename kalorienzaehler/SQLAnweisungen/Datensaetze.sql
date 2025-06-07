USE Kalorientracker;
INSERT INTO Makros (Kohlenhydrate, Fett, Eiweiss) VALUES (13.8, 0.2, 0.3);
INSERT INTO Produkt (EAN, MakroID, Name, Kategorie) VALUES (1234567890123, 1, 'Apfel', 'Obst');

INSERT INTO Makros (Kohlenhydrate, Fett, Eiweiss) VALUES (65, 0.5, 1.8);
INSERT INTO Produkt (EAN, MakroID, Name, Kategorie) VALUES (4306205816627, 2, 'Bio Datteln Getrocknet', 'Obst');

INSERT INTO Makros (Kohlenhydrate, Fett, Eiweiss) VALUES (0.4, 13, 6.4);
INSERT INTO Produkt (EAN, MakroID, Name, Kategorie) VALUES (7066307373165, LAST_INSERT_ID(), 'Cannellini Bohnen – Dm – 400g', 'Nuesse');

INSERT INTO Makros (Kohlenhydrate, Fett, Eiweiss) VALUES (30.9, 57.5, 6.3);
INSERT INTO Produkt (EAN, MakroID, Name, Kategorie) VALUES (3017620425035, LAST_INSERT_ID(), 'Nutella – Ferrero – 1 kg', 'Aufstrich');


