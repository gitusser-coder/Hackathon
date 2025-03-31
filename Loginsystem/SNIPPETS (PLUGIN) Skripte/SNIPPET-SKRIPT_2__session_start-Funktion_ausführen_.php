// Start der PHP-Session in WordPress
function start_custom_session() {
    if (!session_id()) {
        session_start(); // Startet die Session, falls noch keine existiert
    }
}
add_action('init', 'start_custom_session', 1); // Führe das Snippet möglichst früh aus
