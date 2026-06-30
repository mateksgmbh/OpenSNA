<?php
// 1. Datenbankverbindung herstellen
$host = 'localhost';
$dbname = 'opensnadb';
$user = 'DEIN_USER'; // Anpassen an dein Setup
$password = 'DEIN_PASSWORT'; // Anpassen an dein Setup

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Fehler bei der Datenbankverbindung: " . htmlspecialchars($e->getMessage()));
}

// 2. Daten abfragen (Neueste Notrufe zuerst)
try {
    $stmt = $pdo->query("SELECT id, alarmstufe, detail, standort, erfassungszeit FROM notrufabfragen ORDER BY erfassungszeit DESC");
    $eintraege = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Fehler bei der Datenabfrage: " . htmlspecialchars($e->getMessage()));
}

// 3. Einfache Statistik für das Dashboard berechnen
$gesamt = count($eintraege);
$statistiken = [];
foreach ($eintraege as $eintrag) {
    $stufe = $eintrag['alarmstufe'] ?? 'Unbekannt';
    $statistiken[$stufe] = ($statistiken[$stufe] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenSNA - Protokoll-Auswertung</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans min-h-screen flex flex-col">

    <header class="bg-[#1C2D42] text-white px-6 py-4 flex justify-between items-center shadow-md">
        <div class="flex items-center space-x-3">
            <span class="text-2xl font-bold tracking-wider text-red-500">Open<span class="text-white">SNA</span></span>
            <span class="text-xs bg-slate-700 px-2 py-1 rounded text-gray-300">Auswertungsportal</span>
        </div>
        <a href="index.html" class="text-sm bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg text-gray-200 border border-slate-600 transition">
            ← Hauptmenü
        </a>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-[#1C2D42]">Einsatzprotokolle & Notrufabfragen</h1>
            <p class="text-gray-600 mt-2">Revisionssichere Übersicht aller standardisierten Abfragen der Datenbank <code class="bg-gray-100 px-1.5 py-0.5 rounded text-sm text-red-600 font-mono">publicSNA</code>.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Abfragen Gesamt</span>
                <div class="text-3xl font-black text-[#1C2D42] mt-1"><?= $gesamt ?></div>
            </div>
            <?php foreach ($statistiken as $stufe => $anzahl): ?>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Stufe: <?= htmlspecialchars($stufe) ?></span>
                    <div class="text-3xl font-black text-gray-700 mt-1"><?= $anzahl ?></div>
                </div>
            <?php endforeach ?>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-600">
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Zeitstempel</th>
                            <th class="py-4 px-6">Alarmstufe</th>
                            <th class="py-4 px-6">Detail / Indikation</th>
                            <th class="py-4 px-6">Standort</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                        <?php if (empty($eintraege)): ?>
                            <tr>
                                <td colspan="5" class="py-10 text-center text-gray-400 italic">
                                    Bisher keine protokollierten Notrufabfragen vorhanden.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($eintraege as $zeile): ?>
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-4 px-6 font-mono text-xs text-gray-400">#<?= htmlspecialchars($zeile['id']) ?></td>
                                    <td class="py-4 px-6 whitespace-nowrap font-medium">
                                        <?= date('d.m.Y - H:i:s', strtotime($zeile['erfassungszeit'])) ?> Uhr
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-[#D32F2F] border border-red-100">
                                            <?= htmlspecialchars($zeile['alarmstufe']) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 max-w-xs truncate" title="<?= htmlspecialchars($zeile['detail']) ?>">
                                        <?= htmlspecialchars($zeile['detail']) ?>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 italic">
                                        <?= htmlspecialchars($zeile['standort'] ?: 'Nicht angegeben') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="bg-gray-100 border-t border-gray-200 text-xs text-gray-500 px-6 py-4 text-center">
        OpenSNA Behörden-Auswertung &bull; Vertrauliche Einsatzdaten
    </footer>

</body>
</html>