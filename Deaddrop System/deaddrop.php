<?php
/**
 * Plugin Name: Dead Drop System
 * Plugin URI: https://yourwebsite.com
 * Description: Ein hoffentlich endlich funktionierendes Deaddrop-System
 * Version: 10000000000.0
 * Author: Darius und Melina
 * Author URI: https://116.web.ide3.de
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Direktzugriff verhindern
}

// Shortcode für das Upload-Formular
function dead_drop_upload_form() {
    ob_start(); ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label for="file">Datei hochladen:</label>
        <input type="file" name="file" id="file" required>
        <br><br>
        <button type="submit" name="upload_file">Hochladen</button>
    </form>

    <?php
    if ( isset( $_POST['upload_file'] ) ) {
        dead_drop_handle_file_upload();
    }

    return ob_get_clean();
}
add_shortcode( 'dead_drop', 'dead_drop_upload_form' );

function dead_drop_enqueue_styles() {
    wp_enqueue_style( 'dead-drop-style', plugins_url( 'dead-drop.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'dead_drop_enqueue_styles' );  

function dead_drop_handle_file_upload() {
    if ( ! isset( $_FILES['file'] ) ) {
        echo "Keine Datei hochgeladen.";
        return;
    }

    $uploaded_file = $_FILES['file'];
    $allowed_types = array('image/jpeg', 'image/png', 'application/pdf', 'text/plain');
    $max_size = 5 * 1024 * 1024; // 5 MB
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/dead-drop/';

    // Dateitypen prüfen
    if ( ! in_array( $uploaded_file['type'], $allowed_types ) ) {
        echo "Dieser Dateityp ist nicht erlaubt.";
        return;
    }

    // Dateigröße prüfen
    if ( $uploaded_file['size'] > $max_size ) {
        echo "Die Datei ist zu groß. Maximale Größe: 5 MB.";
        return;
    }

    // Ordner erstellen, falls nicht vorhanden
    if ( ! file_exists( $upload_path ) ) {
        mkdir( $upload_path, 0755, true );
    }

    $file_name = sanitize_file_name( $uploaded_file['name'] );
    $file_path = $upload_path . $file_name;

    // Datei speichern
    if ( move_uploaded_file( $uploaded_file['tmp_name'], $file_path ) ) {
        echo "Datei erfolgreich hochgeladen.";
    } else {
        echo "Fehler beim Hochladen der Datei.";
    }
}


function dead_drop_admin_menu() {
    add_menu_page(
        'Dead Drop Dateien',
        'Dead Drop',
        'manage_options',
        'dead-drop',
        'dead_drop_admin_page'
    );
}
add_action( 'admin_menu', 'dead_drop_admin_menu' );

function dead_drop_admin_page() {
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/dead-drop/';

    echo '<h1>Hochgeladene Dateien</h1>';

    if ( ! file_exists( $upload_path ) ) {
        echo 'Keine Dateien vorhanden.';
        return;
    }

    $files = scandir( $upload_path );
    foreach ( $files as $file ) {
        if ( $file === '.' || $file === '..' ) {
            continue;
        }
        $file_url = $upload_dir['baseurl'] . '/dead-drop/' . $file;
        echo '<a href="' . esc_url( $file_url ) . '" download>' . esc_html( $file ) . '</a><br>';
    }
}

function dead_drop_delete_old_files() {
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/dead-drop/';

    if ( ! file_exists( $upload_path ) ) {
        return;
    }

    $files = scandir( $upload_path );
    $expiration_time = 1 * DAY_IN_SECONDS; // 1 Tag

    foreach ( $files as $file ) {
        if ( $file === '.' || $file === '..' ) {
            continue;
        }

        $file_path = $upload_path . $file;
        if ( filemtime( $file_path ) < ( time() - $expiration_time ) ) {
            unlink( $file_path ); // Datei löschen
        }
    }
}
add_action( 'wp_scheduled_delete', 'dead_drop_delete_old_files' );

// Shortcode, um hochgeladene Dateien anzuzeigen
function dead_drop_display_files() {
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/dead-drop/';  // Der Ordner, in dem die Dateien gespeichert werden

    if ( ! file_exists( $upload_path ) ) {
        return 'Keine Dateien vorhanden.';
    }

    $files = scandir( $upload_path );
    $file_list = '<h2>Hochgeladene Dateien</h2><ul>';

    // Dateien durchlaufen und anzeigen
    foreach ( $files as $file ) {
        if ( $file === '.' || $file === '..' ) {
            continue;  // Überspringen von Systemdateien
        }

        $file_url = $upload_dir['baseurl'] . '/dead-drop/' . $file;
        $file_list .= '<li><a href="' . esc_url( $file_url ) . '" download>' . esc_html( $file ) . '</a></li>';
    }

    $file_list .= '</ul>';
    return $file_list;
}
add_shortcode( 'dead_drop_files', 'dead_drop_display_files' );

?>