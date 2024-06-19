<?php
// index.php

// Definiere den Pfad zur config.json Datei
$configFilePath = __DIR__ . '/config/config.json';

// Überprüfen, ob die config.json Datei existiert
if (!file_exists($configFilePath)) {
    // Wenn die Datei nicht existiert, sende einen HTTP 500 Fehlercode und eine Fehlermeldung
    http_response_code(500);
    echo 'Konfigurationsdatei nicht gefunden';
    exit; // Beende das Skript
}

// Laden und dekodieren der JSON-Datei in ein PHP-Array
$config = json_decode(file_get_contents($configFilePath), true);

// Überprüfen, ob die JSON-Datei korrekt dekodiert wurde
if (json_last_error() !== JSON_ERROR_NONE) {
    // Wenn es einen Fehler beim Dekodieren gibt, sende einen HTTP 500 Fehlercode und eine Fehlermeldung
    http_response_code(500);
    echo 'Fehler beim Dekodieren der Konfigurationsdatei';
    exit; // Beende das Skript
}

// Funktion, um benötigte Dateien serverseitig bereitzustellen
function serveFile($file)
{
    // Erstelle den vollständigen Pfad zur Datei
    $filePath = __DIR__ . 'index.php/' . $file;
    if (file_exists($filePath)) {
        // Wenn die Datei existiert, bestimme den MIME-Typ der Datei
        $mimeType = mime_content_type($filePath);
        // Setze den Content-Type Header auf den MIME-Typ der Datei
        header('Content-Type: ' . $mimeType);
        // Lese die Datei und sende ihren Inhalt an den Client
        readfile($filePath);
        exit; // Beende das Skript
    } else {
        // Wenn die Datei nicht existiert, sende einen HTTP 404 Fehlercode und eine Fehlermeldung
        http_response_code(404);
        echo 'Datei nicht gefunden';
        exit; // Beende das Skript
    }
}

// Prüfen, ob eine bestimmte Datei angefordert wird (über URL-Parameter)
if (isset($_GET['file'])) {
    // Liste der erlaubten Dateien
    $allowedFiles = [
        'style/styles.css',
        'helpers/sendMail.php',
        'helpers/SimpleMail.php',
    ];
    // Angeforderte Datei aus dem URL-Parameter
    $requestedFilePath = $_GET['file'];

    // Überprüfen, ob die angeforderte Datei in der Liste der erlaubten Dateien ist
    if (in_array($requestedFilePath, $allowedFiles)) {
        // Datei bereitstellen
        serveFile($requestedFilePath);
    } else {
        // Wenn die Datei nicht erlaubt ist, sende einen HTTP 403 Fehlercode und eine Fehlermeldung
        http_response_code(403);
        echo 'Zugriff verboten';
        exit; // Beende das Skript
    }
}

// Debug-Modus und Error-Reporting aus der Konfigurationsdatei lesen
$debug = isset($config['debug']) ? filter_var($config['debug'], FILTER_VALIDATE_BOOLEAN) : false;
$errors_enabled = isset($config['errors_enabled']) ? filter_var($config['errors_enabled'], FILTER_VALIDATE_BOOLEAN) : false;

// Array für Fehlermeldungen (wird später im Skript verwendet)
$errors = [];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactform</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
<div class="container">
    <div class="toggle-container">
        <button id="themeToggle" class="theme-toggle">🌙</button>
    </div>
    <h1>Contactform</h1>
    <!-- Formular für das Kontaktformular -->
    <form id="contactForm" method="POST">
        <!-- Eingabefeld für den Namen -->
        <label for="name"><?php echo htmlspecialchars($config['fields']['name']); ?>:</label>
        <input type="text" id="name" name="name" required>

        <!-- Eingabefeld für die E-Mail-Adresse -->
        <label for="email"><?php echo htmlspecialchars($config['fields']['email']); ?>:</label>
        <input type="email" id="email" name="email" required>

        <!-- Eingabefeld für die Telefonnummer -->
        <label for="telephone"><?php echo htmlspecialchars($config['fields']['phone']); ?>:</label>
        <input type="tel" id="telephone" name="telephone">

        <!-- Eingabefeld für das Betreff -->
        <label for="subject"><?php echo htmlspecialchars($config['fields']['subject']); ?>:</label>
        <input type="text" id="subject" name="subject" required>

        <!-- Textarea für die Nachricht -->
        <label for="message"><?php echo htmlspecialchars($config['fields']['message']); ?>:</label>
        <textarea id="message" name="message" required></textarea>

        <!-- Rechenaufgabe als Sicherheitsfrage -->
        <label id="mathQuestion"><?php echo htmlspecialchars($config['fields']['security']); ?>:</label>
        <input type="text" id="mathAnswer" required>

        <!-- Hidden-Field für Debug-Modus -->
        <input type="hidden" name="debug" value="<?php echo $debug ? 'true' : 'false'; ?>">

        <!-- Absenden-Button -->
        <button type="submit"><?php echo htmlspecialchars($config['fields']['btn-send']); ?></button>
    </form>
    <div id="debugOutput"></div>
    <footer>
        <?php echo $config['footer']['footertext']; ?>
    </footer>
</div>

<script>
    // Debug-Modus aus PHP-Konfiguration in JavaScript überführen
    const debug = <?php echo $debug ? 'true' : 'false'; ?>; // Setze auf true für Debugging

    // Generiere zufällige Zahlen für die Rechenaufgabe
    function generateMathQuestion() {
        const num1 = Math.floor(Math.random() * 10) + 1;
        const num2 = Math.floor(Math.random() * 10) + 1;
        const question = `${num1} + ${num2} = ?`;
        document.getElementById('mathQuestion').innerText = question;
        return num1 + num2;
    }

    // Speichere die korrekte Antwort auf die Rechenaufgabe
    const correctAnswer = generateMathQuestion();

    // Event-Listener für das Absenden des Formulars
    document.getElementById('contactForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Verhindere das standardmäßige Absenden des Formulars

        const userAnswer = parseInt(document.getElementById('mathAnswer').value, 10);
        const debugOutput = document.getElementById('debugOutput');

        // Überprüfen, ob die Antwort auf die Rechenaufgabe korrekt ist
        if (userAnswer !== correctAnswer) {
            // Wenn die Antwort falsch ist, zeige eine Fehlermeldung an
            debugOutput.innerHTML = `
                <h2>Error</h2>
                <p>Incorrect answer to the math question. Please try again.</p>
            `;
            debugOutput.style.display = 'block';
            return;
        }

        // Sammle die Formulardaten
        const formData = new FormData(this);

        // Sende die Formulardaten per Fetch API an den Server
        fetch('helpers/sendMail.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                // Zeige die Antwort vom Server im Debug-Output an
                debugOutput.innerHTML = data;
                debugOutput.style.display = 'block';
                if (!debug) {
                    // Wenn nicht im Debug-Modus, setze das Formular zurück
                    document.getElementById('contactForm').reset();
                    generateMathQuestion(); // Generiere eine neue Rechenaufgabe
                }
            })
            .catch(error => {
                // Bei einem Fehler, zeige eine Fehlermeldung an
                console.error('Error:', error);
                debugOutput.innerHTML = `
                <h2>Error</h2>
                <p>${error}</p>
            `;
                debugOutput.style.display = 'block';
            });
    });

    // Funktion, um das Standard-Theme basierend auf den Browser-Einstellungen zu setzen
    function setDefaultTheme() {
        const userPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (userPrefersDark) {
            document.body.classList.remove('light-mode');
            document.getElementById('themeToggle').textContent = '🌙';
        } else {
            document.body.classList.add('light-mode');
            document.getElementById('themeToggle').textContent = '☀️';
        }
    }

    // Theme Toggle Funktion
    document.getElementById('themeToggle').addEventListener('click', function() {
        document.body.classList.toggle('light-mode');
        this.textContent = document.body.classList.contains('light-mode') ? '☀️' : '🌙';
    });

    // Setze das Standard-Theme beim Laden der Seite
    setDefaultTheme();
</script>
</body>
</html>
