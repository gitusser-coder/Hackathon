<?php
// Sitzung starten, um die Token-Daten zu überprüfen
session_start();

// Überprüfen, ob die Sitzung eine Benutzer-ID enthält (gültiges Token)
if (isset($_SESSION['user_id'])) {
    // Erfolgreiche Token-Validierung: Willkommensmenü anzeigen
    echo "<!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Willkommen</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                margin-top: 50px;
            }
            h1 {
                color: #333;
            }
            .menu {
                margin-top: 30px;
            }
            .menu button {
                display: block;
                margin: 10px auto;
                padding: 10px 20px;
                font-size: 16px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            .menu button:hover {
                background-color: #45a049;
            }
            .menu button a {
                text-decoration: none;
                color: white;
            }
        </style>
    </head>
    <body>
        <h1>Willkommen auf der geschützten Seite!</h1>
        <p>Bitte wählen Sie eine der folgenden Optionen:</p>
        <div class='menu'>
            <button><a href='https://116.web.ide3.de/kalender'>Kalender</a></button>
            <button><a href='https://116.web.ide3.de/forum'>Forum</a></button>
            <button><a href='https://116.web.ide3.de/index.php/privatebin-forum/'>Dateiaustauschsystem</a></button>
        </div>
    </body>
    </html>";
} else {
    // Kein gültiges Token oder keine Sitzung: Fehlermeldung anzeigen
    echo "<!DOCTYPE html>
    <html lang='de'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Zugriff verweigert</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                margin-top: 50px;
            }
            h1 {
                color: red;
            }
            .error-button {
                margin-top: 30px;
            }
            .error-button a {
                text-decoration: none;
                background-color: #f44336;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px;
            }
            .error-button a:hover {
                background-color: #d32f2f;
            }
        </style>
    </head>
    <body>
        <h1>Fehler: Kein Zugriff</h1>
        <p>Ihr Token ist entweder ungültig oder abgelaufen.</p>
        <div class='error-button'>
            <a href='https://116.web.ide3.de/'>Zurück zur Homepage</a>
        </div>
    </body>
    </html>";
    exit; // Beendet das Skript, um unautorisierten Zugriff zu verhindern
}
?>
