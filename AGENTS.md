# Chat Admin – Projektanweisungen

Lies vor HumHub-Entwicklungsaufgaben `.agents/skills/humhub-development/SKILL.md`,
`README.md`, `module.json` und `docs/ai/SKILL-PILOT.md`.
Der Skill bindet die Regeln aus `Kilian70/humhub-ai-dev` an den dort dokumentierten Commit.
Dieselbe Datei ist der gemeinsame Einstieg für Codex und Claude.

Codex muss die zentralen Dateien tatsächlich lesen: `AGENTS.md`, `README.md`,
`START.md`, `docs/REGELN.md`, `docs/QUELLEN.md` und `docs/VERSIONEN.md` aus
https://github.com/Kilian70/humhub-ai-dev am im lokalen Skill festgelegten Commit.
Relative Verweise dort gegen dieses zentrale Repository am selben Commit auflösen.
Ein GitHub-Link oder der lokale Skill allein ersetzt das Lesen nicht. Gelesenen
Commit und Dateien im Review nennen. Bei fehlendem Zugriff die betroffenen Quellen
und die Zugriffslücke melden; davon abhängige Änderungen erst nach Bereitstellung
der Quellen fortsetzen, keine Regeln, APIs oder Testergebnisse erfinden.

HumHub 1.19 ist Standard für neue Module. Dieses bestehende Modul behält seine
Mindestversion 1.18.1 und bisherigen Prüfstände. Ein Beta-Test ist kein Nachweis
für die stabile 1.19-Version. Messenger-Versionen nur nach gesonderter Prüfung erweitern.

Projektregeln und bestehende Dateien vor Änderungen vergleichen; nichts blind ersetzen.
Bestehende Migrationen und Sicherheitsbedingungen erhalten. Den Skill nicht als
Kompatibilitäts- oder Sicherheitszertifikat behandeln.
Commit, Push, Merge, Release oder Deployment nur nach ausdrücklicher Freigabe
für die jeweilige Aktion; eine lokale Branch-Vorbereitung ist keine Veröffentlichung.
