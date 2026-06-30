<?php
// Setze den Content-Type-Header, um eine JSON-Antwort zu senden
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json');

// ------------------------------------
// ⚠️ DATENBANK-KONFIGURATION ANPASSEN!
// ------------------------------------
$host = 'localhost';
$db   = 'opensnadb'; // Beispiel: 'notruf_db'
$user = 'DEIN_USER';    // Beispiel: 'notruf_user'
$pass = 'DEIN_PASSWORT';        // Beispiel: 'SicheresPasswort123'
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// **********************************************
// 1. Daten empfangen und dekodieren
// **********************************************
// Lese den JSON-Input vom Frontend
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Prüfe, ob die JSON-Dekodierung erfolgreich war und Daten vorhanden sind
if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Ungültige JSON-Daten empfangen.']);
    exit;
}

// Stelle sicher, dass alle benötigten Schlüssel vorhanden sind
$erforderliche_schluessel = ['Alarmstufe', 'Detail', 'Standort'];
foreach ($erforderliche_schluessel as $key) {
    if (!isset($data[$key])) {
        echo json_encode(['success' => false, 'message' => "Fehlender Schlüssel: {$key}."]);
        exit;
    }
}

// Extrahiere die Daten für die bessere Lesbarkeit
$alarmstufe = $data['Alarmstufe'];
$detail = $data['Detail'];
$standort = $data['Standort'];

// **********************************************
// 2. Datenbankverbindung herstellen und speichern
// **********************************************
try {
    // Verbindung zur MariaDB/MySQL-Datenbank herstellen
    $pdo = new PDO($dsn, $user, $pass, $options);

    // SQL-Befehl vorbereiten (Prepared Statement)
    $sql = "INSERT INTO notrufabfragen (alarmstufe, detail, standort) 
            VALUES (:alarmstufe, :detail, :standort)";
            
    $stmt = $pdo->prepare($sql);

    // Werte binden und sanitizen (Bereinigen)
    $stmt->bindParam(':alarmstufe', $alarmstufe);
    $stmt->bindParam(':detail', $detail);
    $stmt->bindParam(':standort', $standort);

    // SQL-Befehl ausführen
    $stmt->execute();

    // Erfolgs-Antwort an das Frontend senden
    echo json_encode(['success' => true, 'message' => 'Daten erfolgreich gespeichert.', 'id' => $pdo->lastInsertId()]);

} catch (\PDOException $e) {
    // Fehler bei der Datenbankverbindung oder Ausführung
    // Sende eine allgemeine Fehlermeldung, um DB-Details zu verbergen
    // Fehler können im Server-Log genauer betrachtet werden (Exception-Code und -Nachricht)
    http_response_code(500); // Setze den HTTP-Statuscode auf 500
    error_log("DB Fehler: " . $e->getMessage()); // Schreibe den Fehler ins PHP-Log
    echo json_encode(['success' => false, 'message' => 'Ein Serverfehler ist aufgetreten.']);
}

?>