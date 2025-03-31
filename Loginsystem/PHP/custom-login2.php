<?php
// WordPress-Funktionen und Datenbankverbindung einbinden
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php'; // Lädt WordPress-Funktionen
require_once 'db_connection.php'; // Lädt die Datenbankverbindung

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // E-Mail-Adresse aus dem Formular
    $email = $_POST['email'];

    // Überprüfen, ob die E-Mail-Adresse in der Tabelle wp_users vorhanden ist
    $sql = "SELECT ID FROM wp_users WHERE user_email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Fehler beim Vorbereiten der Datenbankabfrage: " . $conn->error . "<br>");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Die E-Mail existiert in der Datenbank
        $row = $result->fetch_assoc();
        $user_id = $row['ID'];

        // Token generieren
        $token = bin2hex(random_bytes(16)); // Token: 32 Zeichen
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes')); // Ablaufzeit: 30 Minuten

        // Token in der Tabelle login_tokens speichern
        $insert_sql = "INSERT INTO login_tokens (user_id, token, expiry) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);

        if (!$insert_stmt) {
            die("Fehler beim Einfügen des Tokens in die Datenbank: " . $conn->error . "<br>");
        }

        $insert_stmt->bind_param("iss", $user_id, $token, $expiry);
        $insert_stmt->execute();

        // E-Mail mit Token senden
        $to = $email;
        $subject = "Dein Einlog-Token";
        $message = "
        <html>
        <head>
            <title>Dein Einlog-Token</title>
        </head>
        <body>
            <p>Hallo,</p>
            <p>Hier ist dein Einlog-Token:</p>
            <p><b>$token</b></p>
            <p>Dieser Token ist gültig bis: $expiry</p>
        </body>
        </html>
        ";
        $headers[] = "Content-Type: text/html; charset=UTF-8";

        wp_mail($to, $subject, $message, $headers);

        // Token-Eingabeformular anzeigen
        echo "<br>Bitte gebe den erhaltenen Token ein:<br>";
        echo '<form method="POST" action="verify-token.php">
                <input type="text" name="token" required>
                <button type="submit">Token überprüfen</button>
              </form>';
    } else {
        // E-Mail-Adresse nicht gefunden, Newsletter-Anmeldung anzeigen
        echo "Sie haben sich erfolgreich zum Newsletter angemeldet!<br>";

        // Zurück zur Homepage Button
        echo '<form action="https://116.web.ide3.de/" method="get">
                <button type="submit">Zurück zur Homepage</button>
              </form>';
    }

    // Ressourcen schließen
    $stmt->close();
} else {
    echo "Kein Formular abgesendet.<br>";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
