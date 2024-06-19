# Einfaches Kontaktformular

Dieses Projekt stellt ein einfaches Kontaktformular bereit, das PHP, CSS, HTML und JavaScript verwendet, um Benutzern das Senden von Nachrichten zu ermöglichen.

## Funktionen

- **Formularfelder:** Das Formular enthält Felder für Name, E-Mail, Telefon, Betreff und Nachricht.
- **Sicherheitsfrage:** Eine einfache Rechenaufgabe schützt vor Spam.
- **Thema umschalten:** Der Benutzer kann zwischen hell und dunkel wechseln.
- **Debug-Modus:** Zeigt zusätzliche Informationen im Debug-Output an.

## Konfiguration

Das Formular und seine Funktionalitäten können durch die `config/config.json` Datei konfiguriert werden. Die Konfiguration umfasst:

- **E-Mails:** Absender- und Empfängeradresse für eingehende Nachrichten (`from` und `to` in `emails`).
- **Nachrichten:** Erfolg- und Fehlermeldungen für den Formularversand (`success` und `error` in `messages`).
- **Feldnamen:** Beschriftungen für die Formularfelder (`name`, `email`, `phone`, `subject`, `message`, `security`, `btn-send` in `fields`).
- **Footer-Text:** Ein zusätzlicher Text, der am Ende der Seite angezeigt wird und in der `config.json` angepasst werden kann.
- **Debug-Einstellungen:** Aktivieren/Deaktivieren des Debug-Modus (`debug` und `errors_enabled`).

## Installation

1. Klone das Repository auf deinen Server:
   ```sh
    git clone https://github.com/username/repo-name.git
2. Passe die Einstellungen in `config/config.json` an deine Bedürfnisse an.
3. Stelle sicher, dass ein Webserver mit PHP-Unterstützung (z.B. Apache) läuft und konfiguriert ist, um Dateien zu bedienen.

## Verwendung

- Öffne `index.php` in deinem Browser.
- Fülle das Kontaktformular aus und löse die Sicherheitsfrage.
- Klicke auf "Senden", um die Nachricht abzuschicken.

## Fehlerbehebung

- Bei Problemen wird eine Fehlermeldung angezeigt, falls der Debug-Modus aktiviert ist.
- Überprüfe die Server- und PHP-Logs für detailliertere Fehlerinformationen.

## Lizenz

Dieses Projekt ist unter der Apache License 2.0 lizenziert. Weitere Informationen finden Sie in der `LICENSE` Datei.
