// Globale Variable zur Speicherung der Auswahl
let notrufDaten = {}; // Geändert von const auf let, damit wir es komplett leeren können
let aktuellerSchritt = 1;

// Hilfsfunktion für eine nicht-blockierende Nachricht
function zeigeFeedback(nachricht) {
    // Erstelle ein temporäres Div-Element
    const toast = document.createElement('div');
    toast.textContent = nachricht;
    
    // Einfaches Styling direkt via JS (oder du nutzt eine CSS-Klasse)
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        backgroundColor: '#4CAF50',
        color: 'white',
        padding: '16px',
        borderRadius: '5px',
        zIndex: '1000',
        boxShadow: '0px 4px 6px rgba(0,0,0,0.1)'
    });

    document.body.appendChild(toast);

    // Nach 3 Sekunden wird die Meldung automatisch entfernt
    setTimeout(() => {
        toast.remove();
    }, 3000);
}


/**
 * Funktion zum Wechseln der Abfrageschritte.
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
        
        // Wichtig, damit wir im Ergebnis-Schritt wissen, wo wir sind
        aktuellerSchritt = 4; 

    } else if (naechsterSchritt > aktuellerSchritt) {
        // Update die Anzeige für den nächsten Schritt
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
 * NEU: Funktion zum Zurücksetzen des Skripts in den Anfangszustand
 */
function resetNotruf() {
    // 1. Blende den aktuell sichtbaren Schritt aus
    document.getElementById(`schritt${aktuellerSchritt}`).classList.add('hidden');
    
    // 2. Daten leeren und Schritt-Zähler zurücksetzen
    notrufDaten = {};
    aktuellerSchritt = 1;
    
    // 3. Optionale Textfelder in der UI leeren (damit beim nächsten Durchlauf alte Daten weg sind)
    if(document.getElementById('auswahl1')) document.getElementById('auswahl1').textContent = '';
    if(document.getElementById('auswahl2')) document.getElementById('auswahl2').textContent = '';
    if(document.getElementById('ergebnis-daten')) document.getElementById('ergebnis-daten').textContent = '';

    // 4. Schritt 1 wieder einblenden
    document.getElementById('schritt1').classList.remove('hidden');
    
    console.log('Skript erfolgreich in den Anfangszustand zurückgesetzt.');
}

/**
 * Funktion zur Speicherung der Daten in die SQL-Datenbank.
 */
function datenbankSpeichern() {
    console.log('Sende Daten an das Backend zur SQL-Speicherung:', notrufDaten);

    fetch('speichere_notruf.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(notrufDaten),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            zeigeFeedback('Daten erfolgreich gespeichert!');
            
            // AUTOMATISCHER RESET: Nach erfolgreichem Speichern zurück zum Start
            resetNotruf();
            
        } else {
            alert('FEHLER beim Speichern: ' + data.message);
        }
    })
    .catch((error) => {
        console.error('Netzwerkfehler:', error);
        alert('Ein Fehler ist aufgetreten. Konnte das Backend nicht erreichen.');
    });
}