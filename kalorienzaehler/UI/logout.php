<?php
//Zerstöre die Session vom User und leite ihn auf die Anmeldeseite weiter.
session_start();
session_destroy();
header("Location: index.html");
?>