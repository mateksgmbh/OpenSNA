// Globale Variable zur Speicherung der Auswahl
const notrufDaten = {};
let aktuellerSchritt = 1;

/**
 * Funktion zum Wechseln der Abfrageschritte.
 * @param {number} naechsterSchritt - Die ID des nächsten anzuzeigenden Schritts.
 * @param {string} schluessel - Der Schlüssel für die Daten (z.B. 'Alarmstufe').
 * @param {string} wert - Der Wert der Button-Auswahl (z.B. 'Herz-Kreislauf').
 */
function weiterZuSchritt(naechsterSchritt, schluessel, wert) {
    if (schluessel && wert) {
        // Speichere die Auswahl des aktuellen Schritts
        notrufDaten[schluessel] = wert;
    }

    // Blende den aktuellen Schritt aus
    document.getElementById(`schritt${aktuellerSchritt}`).classList.add('hidden');

    // Wenn der nächste Schritt der "Ergebnis-Schritt" (4) ist
    if (naechsterSchritt === 4) {
        // Bereite die Anzeige der Ergebnisse vor
        document.getElementById('ergebnis-daten').textContent = JSON.stringify(notrufDaten, null, 2);
        document.getElementById('schritt4').classList.remove('hidden');

        // Optional: Hier könntest du direkt die DB-Speicherung starten (s.u.)
        // datenbankSpeichern();

    } else if (naechsterSchritt > aktuellerSchritt) {
        // Update die Anzeige für den nächsten Schritt (z.B. 'Auswahl von Schritt 1: Herz-Kreislauf')
        if (naechsterSchritt === 3) {
            document.getElementById('auswahl2').textContent = notrufDaten['Detail'];
        } else if (naechsterSchritt === 2) {
            document.getElementById('auswahl1').textContent = notrufDaten['Alarmstufe'];
        }

        // Blende den nächsten Schritt ein
        document.getElementById(`schritt${naechsterSchritt}`).classList.remove('hidden');
        aktuellerSchritt = naechsterSchritt;
    }
}

/**
 * Funktion zur Speicherung der Daten in die SQL-Datenbank (Backend-Anbindung erforderlich!).
 */
function datenbankSpeichern() {
    console.log('Sende Daten an das Backend zur SQL-Speicherung:', notrufDaten);

    // *************************************************************************
    // ** WICHTIG: Dieser Teil erfordert ein BACKEND (PHP, Python etc.) **
    // *************************************************************************

    // Führe eine HTTP-Anfrage (Fetch API) an dein Backend-Skript durch
    fetch('speichere_notruf.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(notrufDaten), // Die gesammelten Daten werden als JSON gesendet
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Daten erfolgreich in der SQL-Datenbank gespeichert!');
        } else {
            alert('FEHLER beim Speichern: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Netzwerkfehler:', error);
        alert('Ein Fehler ist aufgetreten. Konnte das Backend nicht erreichen.');
    });
}