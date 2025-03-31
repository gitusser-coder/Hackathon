<?php
// WordPress-Funktionen und Datenbankverbindung einbinden
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php'; // Lädt WordPress-Funktionen
require_once 'db_connection.php'; // Lädt die Datenbankverbindung

session_start(); // Session starten, um den Benutzer nach der Verifizierung zu speichern

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    echo "Token empfangen: $token<br>";

    // SQL-Abfrage, um das Token in der Datenbank zu überprüfen
    $sql = "SELECT user_id FROM login_tokens WHERE token = ? AND expiry > NOW()";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Fehler beim Vorbereiten der Datenbankabfrage: " . $conn->error . "<br>");
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Überprüfen, ob das Token in der Datenbank gefunden wurde
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        echo "Token gefunden. Benutzer-ID: $user_id<br>";

        // Session setzen, um den Benutzer zu authentifizieren
        $_SESSION['valid_token'] = true;
        $_SESSION['user_id'] = $user_id;

        // Benutzer zur geschützten Seite weiterleiten
        header("Location: protected-page.php"); // Weiterleitung zur geschützten Seite
        exit;
    } else {
        echo "Fehler: Ungültiges oder abgelaufenes Token.<br>";
    }

    // Ressourcen schließen
    $stmt->close();
} else {
    echo "Kein Token übermittelt.<br>";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
