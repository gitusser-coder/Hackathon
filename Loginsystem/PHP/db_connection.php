<?php
$servername = "localhost";  // oder die IP-Adresse deines Datenbankservers
$username = "test";
$password = "admin1234";
$dbname = "wordpress";  // Der Name deiner Datenbank, z.B. "wordpress"

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}
?>
