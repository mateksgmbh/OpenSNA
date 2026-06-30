Markdown
# OpenSNA — Open Source Notrufabfrage 🚨🚑🚒

OpenSNA ist eine moderne, webbasierte Plattform zur **strukturierten und standardisierten Notrufabfrage (SNA)** im Blaulichtbereich. Die Anwendung unterstützt Disponenten in integrierten Leitstellen (ILS) von Feuerwehr, Rettungsdienst und Polizei dabei, medizinische und feuerwehrspezifische Notrufe in Sekundenschnelle, präzise und rechtssicher abzufragen.

---

## ⚡ Kernfeatures auf einen Blick

* **Dynamische Abfragepfade:** Die Algorithmen passen sich den Antworten des Anrufers in Echtzeit an. Keine Zeitverschwendung durch irrelevante Fragen.
* **Strikte Mandantentrennung:** Entwickelt für den Einsatz in großen Zweckverbänden oder landesweiten Strukturen. Daten und Protokolle bleiben pro Kreis oder Stadt autark.
* **Nahtlose ELS-Schnittstellen (API):** Übergabe der ermittelten Alarmstufe und aller Notruf-Details in Echtzeit direkt an bestehende Einsatzleitsysteme.
* **High-Speed-Modus:** Bei kritischen Stichworten (z. B. Herz-Kreislauf-Stillstand) überspringt das System Heuristiken, um sofort lebensrettende Erste-Hilfe-Hinweise bereitzustellen.
* **Individuelle Auswertungsmodule:** Integrierte Analytics für das Qualitätsmanagement (QM) zur Analyse von Abfragezeiten und Meldebildern.
* **100% BOS-Datenschutz (On-Premise):** Volle Datensouveränität. Speicherung direkt in Ihrer SQL-Datenbank – ohne externe Tracker oder Cloud-Abhängigkeiten.

---

## 🚀 Systemarchitektur & Ablauf

Das System trennt strikt zwischen der hochlesbaren Benutzeroberfläche des Disponenten und der revisionssicheren Speicherung im Backend:

1. **Eingang/Landingpage:** Zentraler Einstiegspunkt für den Disponenten (`index.html`).
2. **Abfragemaske:** Strukturierte Führung durch die Meldebilder (`sna.html`) via Tailwind CSS.
3. **Backend-Protokollierung:** Asynchrone Speicherung der JSON-Strukturen via PHP in der SQL-Datenbank (`opensnadb`).
4. **Analytik:** Behörden-Auswertungsportal zur Nachbereitung und Qualitätssicherung (`auswertung.php`).

---

## 🛠️ Installation & Setup

Da OpenSNA als leichtgewichtiges, On-Premise-fähiges Webtool konzipiert ist, lässt es sich innerhalb weniger Minuten auf jedem behördlichen Webserver (z. B. Apache/Nginx mit PHP) aufsetzen.

### 1. Voraussetzungen
* Webserver (Apache, Nginx oder IIS)
* PHP 8.x oder höher (mit `PDO_MYSQL` Erweiterung)
* MySQL / MariaDB Datenbank

### 2. Datenbank vorbereiten
Erstelle eine Datenbank namens `opensnadb` und lege die Tabelle `notrufabfragen` an:

```sql
CREATE DATABASE opensnadb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE opensnadb;

CREATE TABLE notrufabfragen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alarmstufe VARCHAR(100) NOT NULL,
    detail TEXT NOT NULL,
    standort VARCHAR(255) DEFAULT NULL,
    erfassungszeit TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

3. Repository klonen & Konfigurieren
    1. Klone dieses Repository in dein Web-Verzeichnis (htdocs / www).

    2. Passe die Datenbank-Zugangsdaten in der speichere_notruf.php sowie der auswertung.php an:

    $host = 'localhost';
    $dbname = 'opensnadb';
    $user = 'DEIN_USER';
    $password = 'DEIN_PASSWORT';

4.  🔒 Rechtssicherheit & Compliance
Im BOS-Bereich (Behörden und Organisationen mit Sicherheitsaufgaben) sind Datenschutz und Nachweisbarkeit essenziell. OpenSNA speichert jeden Abfrageschritt mit exaktem Zeitstempel. Jede Eingabe wird serverseitig vor der SQL-Injektion geschützt (Prepared Statements) und im Auswertungsportal gegen Cross-Site-Scripting (XSS) maskiert.

    - Entspricht den Anforderungen der DSGVO (keine externen CDNs im Offline-Betrieb notwendig).

    - Entwickelt nach den Kriterien der ISO 27001 für kritische Infrastrukturen (KRITIS).

🤝 Mitwirken / Contributing
Beiträge von Entwicklern aus dem BOS-Umfeld, Rettungsdienst-Mitarbeitern und Leitstellen-Experten sind herzlich willkommen! Wenn du neue Abfragebäume (z.B. für MANV, Gefahrgut oder technische Hilfeleistung) einpflegen oder Schnittstellen erweitern möchtest:

    1.  Forke das Projekt

    2.  Erstelle einen Feature Branch (git checkout -b feature/NeuesMeldebild)

    3.  Committe deine Änderungen

    4.  Öffne einen Pull Request

📄 Lizenz
Dieses Projekt ist unter der Apache-2.0 license lizenziert — siehe die LICENSE Datei für Details.

🔍 Suchbegriffe / Keywords (für die Online-Auffindbarkeit)
Standardisierte Notrufabfrage Open Source | Strukturierte Notrufabfrage Software | Leitstelle Software GitHub | Rettungsdienst Abfrageschema | Feuerwehr Einsatzleitsystem Schnittstelle | BOS Webanwendung PHP SQL | OpenSNA Notruf | Meldebildermittlung ILS