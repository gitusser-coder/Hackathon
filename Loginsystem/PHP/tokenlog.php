<?php
include('db_connection.php'); // Datenbankverbindung einbinden

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Die eingegebene E-Mail und das Token aus dem POST-Request
    $email = $_POST['email'];
    $token = $_POST['token'];

    // Überprüfen, ob E-Mail in der Datenbank vorhanden ist
    $query = "SELECT ID FROM wp_users WHERE user_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); // binden der E-Mail-Adresse
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Wenn die E-Mail gefunden wird, Benutzer-ID abrufen
        $row = $result->fetch_assoc();
        $user_id = $row['ID'];

        // Token in der Datenbank speichern und mit der Benutzer-ID verknüpfen
        $token_query = "INSERT INTO login_tokens (user_id, token) VALUES (?, ?)";
        $stmt = $conn->prepare($token_query);
        $stmt->bind_param("is", $user_id, $token); // binden der User-ID und des Tokens
        $stmt->execute();

        // Erfolgreiche Nachricht
        echo "Token wurde erfolgreich gespeichert!";
    } else {
        // Wenn die E-Mail nicht in der Datenbank gefunden wird
        echo "Ungültige E-Mail-Adresse. Bitte versuchen Sie es erneut.";
    }
}
?>

<!-- Formular zur Eingabe von E-Mail und Token -->
<form method="POST" action="tokenlog.php">
    E-Mail: <input type="email" name="email" required><br>
    Token: <input type="text" name="token" required><br>
    <input type="submit" value="Absenden">
</form>
