// Funktion zur Token-Überprüfung
function check_token_validity() {
    // IDs der Seiten, auf denen der Token-Check ausgeführt werden soll
    $protected_page_ids = array(583, 530, 528); // Beispiel-IDs (ersetze diese mit den echten Seiten-IDs)

    // Überprüfe, ob wir auf einer der geschützten Seiten sind
    if (is_page($protected_page_ids)) {
        // Token aus der URL holen, falls nicht in der Session vorhanden
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            // Validierung des Tokens in der Datenbank
            if (check_token_in_db($token)) {
                // Token in der Session speichern, um es später zu verwenden
                $_SESSION['valid_token'] = $token;
            } else {
                wp_die('Fehler: Ungültiges oder abgelaufenes Token.');
            }
        } elseif (!isset($_SESSION['valid_token'])) {
            // Wenn das Token weder in der URL noch in der Session vorhanden ist
            wp_die('Fehler: Kein Token vorhanden. Bitte geben Sie ein gültiges Token ein.');
        }
    }
}

// Diese Funktion prüft, ob das Token in der Datenbank gültig ist
function check_token_in_db($token) {
    global $wpdb;
    $result = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM login_tokens WHERE token = %s AND expiry > NOW()", $token));
    return $result > 0;
}

// Registriere das Snippet mit dem 'template_redirect' Hook
add_action('template_redirect', 'check_token_validity');
