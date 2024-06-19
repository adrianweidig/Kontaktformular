<?php

namespace helpers;

class SimpleMail
{
    protected $to; // Empfänger der E-Mail
    protected $from; // Absender der E-Mail
    protected $sender_email; // E-Mail-Adresse des Absenders
    protected $subject; // Betreff der E-Mail
    protected $name; // Name des Absenders
    protected $email; // E-Mail-Adresse des Absenders
    protected $telephone; // Telefonnummer des Absenders
    protected $message; // Nachricht
    protected $ip_address; // IP-Adresse des Absenders
    protected $debugMode; // Debug-Modus
    protected $subjectMessage; // Nutzereingabe des Subjects ohne Prefix

    public function __construct($to, $from, $name, $email, $telephone = '', $message = '', $ip_address = '', $debugMode = false)
    {
        $this->to = $to;
        $this->from = $from;
        $this->sender_email = $email; // Die Absender-E-Mail wird auf die E-Mail des Kontakts gesetzt
        $this->name = $name;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->message = $message;
        $this->ip_address = $ip_address;
        $this->debugMode = $debugMode;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setSubjectMessage($subjectMessage) {
        $this->subjectMessage = $subjectMessage;
    }

    public function composeHtml()
    {
        // HTML für die E-Mail-Zusammenstellung
        $html = "<h1>{$this->subjectMessage}</h1>";
        $html .= "<p><strong>Name:</strong> {$this->name}</p>";
        $html .= "<p><strong>Email:</strong> {$this->email}</p>";
        if (!empty($this->telephone)) {
            $html .= "<p><strong>Telephone:</strong> {$this->telephone}</p>";
        }
        $html .= "<p><strong>Message:</strong><br>{$this->message}</p>";
        $html .= "<p><strong>Sender's IP Address:</strong> {$this->ip_address}</p>";

        return $html;
    }

    public function send()
    {
        // Aufbau der Header für die E-Mail
        $header  = 'MIME-Version: 1.0' . PHP_EOL;
        $header .= 'Content-type: text/html; charset=utf-8' . PHP_EOL;
        $header .= 'From: ' . $this->name . ' <' . $this->from . '>' . PHP_EOL;
        $header .= 'Reply-To: <' . $this->sender_email . '>' . PHP_EOL;
        $header .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;

        $message = $this->composeHtml(); // HTML-Nachricht erstellen

        if (!$this->debugMode) {
            ini_set('sendmail_from', $this->from);
            return mail( $this->to ,$this->subject, $message, $header); // E-Mail senden
        } else {
            return [
                'to' => $this->to,
                'from' => $this->sender_email,
                'subject' => $this->subject,
                'header' => $header,
                'message' => $message,
            ]; // Im Debug-Modus: Informationen zurückgeben, aber keine E-Mail senden
        }
    }
}
?>
