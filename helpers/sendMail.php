<?php

use helpers\SimpleMail;

// Verwendung der SimpleMail-Klasse

require_once 'SimpleMail.php'; // Einbinden der SimpleMail-Klasse

// Pfad zur Konfigurationsdatei
$configFilePath = __DIR__ . '/../config/config.json';

// Überprüfen, ob die Konfigurationsdatei existiert
if (!file_exists($configFilePath)) {
    http_response_code(500); // Antwort-Code für Internen Serverfehler
    echo 'Konfigurationsdatei nicht gefunden'; // Fehlermeldung, wenn die Datei nicht gefunden wurde
    exit; // Beenden des Skripts
}

// Laden und dekodieren der JSON-Konfigurationsdatei
$config = json_decode(file_get_contents($configFilePath), true);

// Überprüfen, ob die JSON-Konfigurationsdatei korrekt dekodiert wurde
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500); // Antwort-Code für Internen Serverfehler
    echo 'Fehler beim Dekodieren der Konfigurationsdatei'; // Fehlermeldung, wenn Dekodierung fehlschlägt
    exit; // Beenden des Skripts
}

// Array für Fehlermeldungen
$errors = [];

// Verarbeiten des POST-Requests (wenn das Formular abgesendet wurde)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daten aus dem Formular erhalten
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $subjectPrefix = $config['subject']['prefix'] ?? '[Contact Form Message]';
    $subjectUserInput = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $debug = filter_var($config['debug'], FILTER_VALIDATE_BOOLEAN); // Debug-Modus aus der Konfiguration lesen
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown'; // IP-Adresse des Absenders

    // Betreff für die E-Mail zusammenstellen
    $subject = $subjectPrefix . ' ' . $subjectUserInput;

    // E-Mail-Versand vorbereiten mit SimpleMail
    $mail = new SimpleMail(
        $config['emails']['to'], // Empfänger der E-Mail
        $config['emails']['from'], // Absender der E-Mail
        $name, // Name des Absenders
        $email, // E-Mail-Adresse des Absenders
        $telephone, // Telefonnummer des Absenders
        $message, // Nachricht
        $ip_address, // IP-Adresse des Absenders
        $debug // Debug-Modus
    );

    $mail->setSubject($subject); // Betreff der E-Mail setzen
    $mail->setSubjectMessage($subjectUserInput); // In der Nachricht ohne Prefix anzeigen

    // Wenn nicht im Debug-Modus, E-Mail senden
    if (!$debug) {
        if ($mail->send()) {
            echo "
                <h2>Success</h2>
                <p>{$config['messages']['success']}</p>
            ";
        } else {
            $errors[] = $config['messages']['error']; // Fehlermeldung bei Sendefehler speichern
        }
    } else {
        // Im Debug-Modus: Kein E-Mail-Versand, nur Debug-Ausgabe
        $debugOutput = $mail->send(); // Debug-Ausgabe holen
        echo "
            <h2>Debug Mode: Email Content</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>";
        if (!empty($telephone)) {
            echo "<p><strong>Telephone:</strong> $telephone</p>";
        }
        echo "
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong><br>$message</p>
            <p><strong>Sender's IP Address:</strong> $ip_address</p>
        ";

        // Zusätzliche Debug-Informationen aus SimpleMail holen und anzeigen
        echo "<div id='debugOutput'>";
        echo "<h2>Debug Mode: Active</h2>";
        echo "<p><strong>Recipient:</strong> {$debugOutput['to']}</p>";
        echo "<p><strong>Sender:</strong> {$debugOutput['from']}</p>";
        echo "<p><strong>Subject:</strong> {$debugOutput['subject']}</p>";
        echo "<h3>Header:</h3>";
        echo "<pre>{$debugOutput['header']}</pre>";
        echo "<h3>Message:</h3>";
        echo "<pre>{$debugOutput['message']}</pre>";
        echo "</div>";
    }

    // Fehlerausgabe für Debug-Modus und bei Fehlern
    if (!empty($errors)) {
        echo "<div id='debugOutput'>";
        echo "<h2>Error</h2>";
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
    }
}
?>
