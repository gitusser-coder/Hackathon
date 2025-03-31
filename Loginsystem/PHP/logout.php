<?php
// Starten der Session, um auf die Sitzung zuzugreifen
session_start();

// Alle Session-Daten löschen
session_unset();

// Die Session zerstören
session_destroy();

// Weiterleitung zur Login-Seite
header("Location: https://116.web.ide3.de/index.php/login-seite/");
exit;
?>
