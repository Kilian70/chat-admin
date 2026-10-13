# Skill-Pilot: Chat Admin

Aktueller Stand vom 13.10.2026 nach funktionaler Prüfung:
Messenger 3.4.6 ist jetzt in der Freigabeliste enthalten. Eine vollständige
HumHub-1.19.0-Testinstallation bestand 63 positive HTTP-/Funktionsprüfungen sowie
16 zusätzliche HTTP-Sperr-/Gate-Prüfungen. Die bedingte Freigabe wurde erst nach
bestandener Kandidatenprüfung umgesetzt und am endgültigen Modulstand bestätigt.
Die bisherigen 1.18.1/1.18.5-Prüfstände bleiben grün; Mindestversion 1.18.1 bleibt.
Der letzte Abschnitt „Funktionale Freigabe“ enthält den aktuellen Stand. Die
vorherigen Abschnitte dokumentieren frühere Prüfstände und Fehlerkorrekturen.

## Historie der Skill-Einrichtung

Stand: 13.10.2026. Lokaler Branch: `feature/humhub-119-skill-pilot`.
Keine Commits, Pushes, Merges oder Deployments vorgenommen.

## Geprüfte Quellen

- Modul: https://github.com/Kilian70/chat-admin/tree/461f63746daff5e23263a2dca0b586b8103be4e1
- Skill und Framework: https://github.com/Kilian70/humhub-ai-dev/tree/3e9ca012767d0e249aab62f7ba5c48224e4e6b04
  aus `feature/humhub-119-skill`.
- Gelesen: Modul-README, Manifest, alle PHP-Dateien und drei Tests, beide Workflows;
  Framework-AGENTS/CLAUDE, README, START, Regeln, Quellen, Versionsrichtlinie,
  Skill und Installer. Keine eigenen Projektanweisungen oder Skills im Modul gefunden.

## Projekt und Versionsanforderungen

Kleines HumHub-Zusatzmodul ohne eigene Composer-Konfiguration. `Module.php` und
`config.php` registrieren Modul und Messenger-Menüevent. `Events.php` ergänzt das
Menü; `ParticipantsController` behandelt Lesen/Entfernen. Die Dienste trennen
Zugriff, reine Entfernungsregeln und transaktionale Entfernung mit Audit.
Eine Migration erstellt die eigene Audit-Tabelle; eine View zeigt Teilnehmer/Audit.

`module.json`: chat-admin 0.1.1, HumHub mindestens 1.18.1, keine Obergrenze.
Messenger-Allowlist im Code: genau 3.3.13 und 3.4.5. PHP-Anforderung nicht im
Manifest definiert; CI verwendet PHP 8.2/8.3. Die Syntax braucht mindestens PHP 8.0
(z.B. str_starts_with), woraus keine unterstützte Produktionsmatrix folgt.
Der Runtime-Workflow konfiguriert HumHub 1.18.5/PHP 8.2 und
1.19.0-beta.3/PHP 8.3; er läuft nur geplant oder manuell. Der Quality-Workflow
prüft Metadaten/Syntax, führt aber tests/run.php und tests/access.php nicht aus.
Der sogenannte Runtime-Test lädt nur Modulklasse und Konfiguration gegen den
Kern; Messenger, HTTP, CSRF, Datenbank und tatsächliche Entfernung fehlen dort.

Das README dokumentiert frühere lokale Funktionstests. Diese wurden in diesem
Pilot nicht wiederholt. Die tatsächliche Installation, PHP-Version und verfügbare
Testinstallation sind unbekannt. Beta.3 belegt keine stabile 1.19-Kompatibilität.
HumHub 1.19 bleibt Standard für neue Module; bestehender Versionsbereich bleibt erhalten.

## Minimale Integration

Vier ausschließlich neue Dateien:
- AGENTS.md: gemeinsame Projektanweisungen und Skill-Einstieg.
- CLAUDE.md: expliziter Verweis auf denselben Skill für Claude.
- .agents/skills/humhub-development/SKILL.md: lokaler Skill für Codex,
  aus dem Quell-Skill abgeleitet; Quellenpfade auf den festen Framework-Commit
  angepasst und konkrete Modulgrenzen/Testbefehle ergänzt.
- docs/ai/SKILL-PILOT.md: dieser Prüfbericht.

Keine Änderung an vorhandenen Dateien, Modulversion, Migrationen oder CI.
Keine vollständige Vorlageninstallation: zusätzliche Konfiguration, Prüfer und
Workflow sind für den schmalen Pilot nicht notwendig. Framework-Regeln werden
per festen Links gelesen; offline muss der festgehaltene Quellstand bereitstehen.
AGENTS/CLAUDE verweisen ausdrücklich auf die Skill-Datei, sodass die Anwendung
nicht allein von automatischer Skill-Erkennung abhängt. Eine tatsächliche
Codex-/Claude-Sitzung mit neu geladenem Projekt wurde nicht getestet.

## Prüfung und Grenzen

- Framework: `python3 -m unittest discover -s tests -v`: 15 Fälle,
  14 bestanden, 1 wegen fehlendem PHP CLI übersprungen.
- Skill: bundled skill-creator/scripts/quick_validate.py: bestanden
  (PyYAML nur im Arbeitsordner installiert).
- Manifest als JSON gelesen; Mindestversion und Modul-ID unverändert.
- Vorhandene getrackte Dateien byteweise gegen Basis-Commit geprüft: unverändert.
- Lokale Skill-Verweise und gepinnte Framework-Pfade geprüft: vorhanden.
- Patch auf frische Kopie des Basisstands angewendet und mit Vorbereitung verglichen.
- PHP-Syntax, 9 Entfernungsfälle und 12 Zugriffsfälle nicht ausgeführt: PHP CLI fehlt.
- HumHub-Smoke-Tests und vollständige Integrationstests nicht ausgeführt:
  vorbereiteter Kern, Abhängigkeiten, Datenbank und Testinstallation fehlen.
- Kein neuer Nachweis für Sicherheit, 1.18.1 oder stabile 1.19-Kompatibilität;
  kein vollständiges Sicherheitsreview und keine Produktionsfreigabe.

## Nächste funktionale Prüfung

Mit PHP 8.2/8.3: `php tests/run.php`, `php tests/access.php`, alle PHP-Dateien linten.
Den vorhandenen Smoke-Test auf beiden konfigurierten Kernständen ausführen.
Vor funktionalen Codeänderungen versionsgebundenen Kern, dessen Projektanweisungen,
Migration Guide und relevantes offizielles Messenger-Modul erneut lesen.
Für stabile HumHub 1.19 einen offiziellen Tag/Commit und passende PHP-Anforderungen
ermitteln und getrennt testen; den Beta-Prüfstand nicht still ersetzen.

Die vollständige README-Prüfliste auf Testinstallationen ausführen: berechtigter
Admin im Chat, Gast/normaler Teilnehmer/Admin außerhalb des Chats, fremde IDs,
GET und POST ohne CSRF, Selbstentfernung, letzter Teilnehmer, entfernte Person
mit direktem Nachrichten-/Anhangzugriff, Impersonation, parallele Entfernung,
Wiedereinladung, deaktivierter/unfreigegebener Messenger und iPhone-Bedienung.
Neuinstallation und Update prüfen; Ergebnisse je Kern/PHP/Messenger-Version
festhalten. Bis dahin bestehende Sicherheitsbedingungen und Versionsfreigaben erhalten.

## Ergänzung: offizielles HumHub 1.19 Release

Am 13.10.2026 gelesen: https://docs.humhub.org/docs/about/releasenotes/release_notes_1_19
Die Seite beschreibt das Release im Oktober 2026. Zusätzlich per git ls-remote
verifiziert: offizieller Tag v1.19.0, Ref-SHA
8f22a72361841404b3b442306126650fc0f8153b. Der Ref wurde nicht lokal geladen;
Kernquellen, PHP-Anforderungen und Messenger-Kombination am stabilen Stand
wurden damit noch nicht geprüft. Der bestehende Beta-Smoke-Prüfstand bleibt erhalten.

Aus den Release Notes abgeleitete Schwerpunkte für den nächsten Modultest:
- UserSource und geänderter Login: Admin-/Kontostatus und Sitzungen prüfen.
- User Gates (z.B. 2FA/Nutzungsbedingungen): direkter Verwaltungszugriff darf
  erforderliche Zugangsschritte nicht umgehen.
- Remote Storage/S3 und angepasste Downloads: Zugriff einer entfernten Person
  auf vorher kopierte Anhang-URLs mit der konkreten Speicherkonfiguration prüfen.

Die Release Notes warnen vor einer irreversiblen Migration der Aktivitätstabelle.
Das betrifft ein späteres Core-Upgrade, nicht diese reine Skill-Integration.
Kein Upgrade durchgeführt. Release-Dokumentation und Tag sind kein Beleg für
Kompatibilität von chat-admin oder Messenger mit dem stabilen Release.

## Prüfung vor der Fehlerkorrektur: stabile HumHub 1.19.0 (13.10.2026)

### Ergebnis und Umfang

Der unveränderte Modulcode funktioniert in den geprüften Komponentenpfaden mit
HumHub 1.19.0 / Messenger 3.4.5. Die passende aktuelle getaggte Messenger-Version
für die 1.19-Linie ist 3.4.6; sie verlangt laut module.json mindestens HumHub 1.19.
Der Default-Branch nennt bereits 3.4.7, dessen Changelog sie als unveröffentlicht
kennzeichnet; dieser bewegliche Stand wurde nicht als Testziel verwendet.

Messenger 3.4.6 wird vom Modul absichtlich nicht zugelassen. In beiden geprüften
Sperrpfaden entsteht zusätzlich ein PHP-Fehler statt der vorgesehenen 503-Ausnahme.
Damit ist chat-admin für 1.19.0 + Messenger 3.4.6 derzeit NICHT freigabefähig.
Die Freigabeliste wurde nicht erweitert und der Fehler noch nicht korrigiert.

Die Mindestversion 1.18.1, der bisherige Prüfstand 1.18.5, die Modulversion 0.1.1,
Migrationen, Messenger-Freigaben und beide CI-Dateien sind unverändert.
Alle ursprünglichen getrackten Moduldateien wurden byteweise gegen den Basis-Commit
geprüft. Der Branch bleibt lokal und ohne Commit/Push/Merge/Deployment.

### Reproduzierbare Quellenstände

| Repository | Release-Tag | vollständiger Commit |
|---|---|---|
| humhub/humhub | v1.19.0 | 8f22a72361841404b3b442306126650fc0f8153b |
| humhub/humhub | v1.18.5 | 13dc14723e7bd227dd6bf78c12771842fb7acb28 |
| humhub/humhub | v1.18.1 | c9901f89cfb79a83b77bdf729193a594da73975d |
| humhub/mail | v3.4.6 | f1f6af8e2fa19d40d21eace55091122269670552 |
| humhub/mail | v3.4.5 | 33e02e8cf2c0552aa6926b93ca23991b83c24940 |
| humhub/mail | v3.3.13 | 74c5718405e9fa4a1212d1f8fc1bf47661834231 |

Gelesen: CLAUDE.md am 1.19.0- und 1.18.5-Tag; am Tag 1.18.1 ist keine
CLAUDE.md vorhanden. Je Ziel wurden passende Migrationshinweise geprüft:
1.19.0 docs/develop/module-migrate-1.19.md und 1.18 MIGRATE-DEV.md.
Am stabilen Tag außerdem module-development.md, advanced-security.md,
user-gates.md, Controller, GateFilter/GateManager, ControllerAccess/AccessControl,
User/Impersonation, Module/Widget/Migration sowie File/DownloadAction gelesen.
Messenger: Manifest, Changelog, Modul-/Eventregistrierung, Menüwidget,
Message, UserMessage, MessageEntry, AbstractMessageEntry und Teilnehmerstatuspfad.

Direkte Referenzen:
- [Kernanweisungen 1.19.0](https://github.com/humhub/humhub/blob/8f22a72361841404b3b442306126650fc0f8153b/CLAUDE.md)
- [Migration Guide 1.19](https://github.com/humhub/humhub/blob/8f22a72361841404b3b442306126650fc0f8153b/docs/develop/module-migrate-1.19.md)
- [Messenger 3.4.6 Manifest](https://github.com/humhub/mail/blob/f1f6af8e2fa19d40d21eace55091122269670552/module.json)
- [Messenger 3.4.6 Änderungen](https://github.com/humhub/mail/blob/f1f6af8e2fa19d40d21eace55091122269670552/docs/CHANGELOG.md)
- [Aktuelle Systemanforderungen](https://docs.humhub.org/docs/admin/requirements)

Alle drei gelesenen Kern-composer.json verlangen PHP >=8.2. Für die tatsächlichen
Tests wurde das lokale MAMP-PHP 8.3.30 benutzt. Die lock-Dateien lieferten Yii
2.0.55 bei 1.19.0/1.18.5 und 2.0.54 bei 1.18.1. Composer install lief mit
--no-dev --no-scripts --no-plugins in den isolierten Quellkopien; keine lock-Datei
wurde verändert. SHA-256 der lock-Dateien sind in sources.json der Prüfnachweise.

### Schnittstellen- und Sicherheitsabgleich

- Der Messenger-Diff von 3.4.5 nach 3.4.6 ändert in Message/UserMessage/
  AbstractMessageEntry Validierungsregeln (Attribute mit ! sind nicht mehr sicher
  für Mass Assignment). Das Modul verwendet gezielte Abfragen und delete(),
  keine load()-Massenzuweisung. Die benötigten Signaturen sind weiterhin vorhanden.
- ConversationSettingsMenu.message und das vererbte afterRun-Ereignis existieren.
  Das Ereignis wurde mit dem echten Widget und dem echten Modulhandler ausgelöst;
  die vollständige Messenger-Menüvorlage wurde dabei nicht gerendert.
- Message::isParticipant/getUsers, UserMessage::find/delete/afterDelete,
  MessageUserLeft::inform und die Unread-Cache-Invalidierung sind weiter vorhanden.
- ParticipantsController erbt weiter vom passenden HumHub-Controller. POST-Filter
  und CSRF bleiben aktiv. Auf 1.19.0 hängt der Kern den GateFilter tatsächlich an;
  ein synthetisch geöffnetes Pflicht-Gate blockierte AJAX, Session-JSON und Browser.
  Es wurde kein echtes 2FA-Modul installiert und kein 2FA-Login getestet.
- Die Modulprüfung auf aktive Impersonation greift auch dann, wenn der Kern
  privaten Zugriff während Impersonation erlaubt. Die bedingte zusätzliche
  RULE_DENY_IMPERSONATED-Abfrage bleibt auf 1.18 verwendbar.
- Das Modul greift nicht auf die entfernten UserSource/AuthClient-APIs zu. Die
  echte User-/Gruppenabfrage wurde geprüft; LDAP/SAML/OAuth-Login, gesperrte und
  nicht freigegebene Konten wurden nicht funktional durchlaufen.
- Der private Datei-Zugriff delegiert weiter an MessageEntry::canView und damit
  Teilnehmerzugehörigkeit. Diese Modellprüfung wurde vor/nach Entfernung geprüft.
  Download von Dateibytes, JWT-Downloadtokens, Browser-Anhang-URLs und S3 wurden
  nicht durchlaufen. Daraus folgt keine Freigabe der konkreten Speicherinstallation.

### Tatsächlich ausgeführte Tests

Testumgebung: PHP 8.3.30, finale Datenbankläufe auf MySQL 8.0.44, utf8mb4/InnoDB.
Ein neuer Datenbankprozess mit eigenem Datenverzeichnis lief ausschließlich über
einen lokalen Socket (TCP und MySQL X deaktiviert). Jeder Lauf erzeugte eine neue
zufällig benannte Datenbank mit synthetischen Daten und löschte sie in finally.
Keine existierende Installation und keine Produktionsdatenbank wurde benutzt.

Der zusätzliche Testaufbau verwendet echte Kern-, Messenger- und Modulklassen,
echte SQL-Abfragen, AR-Löschung, Controller-/CSRF-Filter und die Originalmigration.
Er ist eine minimale Yii-Web-Anwendung mit handgeschriebenem Fixtureschema,
kein vollständig installierter HumHub. Nur der Installationszustand und das offene
Pflicht-Gate werden als Fixtures vorgegeben. Die ersten Vorläufe auf MySQL 5.7
werden nicht als abschließender unterstützter Datenbanknachweis gewertet; alle
Ergebnisse unten wurden auf MySQL 8.0.44 wiederholt.

| HumHub | Messenger | bestandene Zusatzprüfungen | fehlgeschlagen | Ergebnis |
|---|---|---:|---:|---|
| 1.18.1 | 3.3.13 | 35 | 0 | geprüfte bestehende Pfade erhalten |
| 1.18.5 | 3.3.13 | 35 | 0 | geprüfte bestehende Pfade erhalten |
| 1.19.0 | 3.4.5 | 42 | 0 | geprüfte Pfade am stabilen Kern erhalten |
| 1.19.0 | 3.4.6 | 13 | 2 | nicht freigegeben; fehlerhafter Sperrpfad |

Bei erlaubten Kombinationen geprüft: Originalmigration mit Tabellenpräfix,
Admin-/Gruppenabfrage, Teilnehmerzugehörigkeit, Menüevent, Selbstentfernung,
unbekannte Person/Chat, fremder Chat, normale Teilnehmer/Gäste, GET-Entfernung,
POST ohne CSRF, gültiger POST mit Weiterleitung, genau ein Audit und ein
Teilnehmer-verlassen-Eintrag, Unread-Cache-Invalidierung, Chat/Nachricht erhalten,
Nachrichten-/Dateimodell-Zugriff entzogen, verbleibender Admin behält Zugriff,
wiederholte Entfernung abgewiesen und vorheriges Audit erhalten.

Zusätzlich: Ein gezielt ausgelöster Datenbankfehler beim Audit führte auf allen
zulässigen Kombinationen zur Rücknahme sowohl der Teilnehmerlöschung als auch
des Teilnehmer-verlassen-Eintrags; es blieb kein halber Audit-Datensatz zurück.
Die Veröffentlichung oder erneute Anwendung einer bereits bestehenden Migration
wurde damit nicht getestet. Auf 1.19 zusätzlich Impersonation (auch bei erlaubtem
privatem Kernzugriff) und die beschriebenen Gate-Interceptionen geprüft.

Weitere Ergebnisse:
- php tests/run.php: 9 Berechtigungsfälle bestanden.
- php tests/access.php: 12 bestehende Versions-/Zugriffsfälle mit Test-Doubles bestanden.
- PHP-Syntaxprüfung: alle 12 Modul-PHP-Dateien bestanden.
- Bestehender tests/humhub-compatibility.php: bestanden auf 1.18.1, 1.18.5, 1.19.0;
  dessen positives Kompatibilitätswort prüft nur Klassen-/Konfigurationsladung.
- Framework-Unittests: alle 15 bestanden, nach Ergänzung des verfügbaren PHP-Pfads.
- Lokaler Skill-Validator: bestanden; finale Patch-Anwendung und Dateivergleich bestanden.

### Zwei Fehlerfälle und Voraussetzung für eine spätere Freigabe

1. services/ParticipantRemoval.php:18 versucht bei nicht freigegebenem Messenger
   yii\web\ServiceUnavailableHttpException zu instanziieren.
2. controllers/ParticipantsController.php:39 verwendet dieselbe fehlende Klasse
   bei nicht freigegebenem Messenger bzw. fehlender Audit-Tabelle.

Die installierten Yii-Versionen liefern diese Klasse nicht. Laufzeitbeobachtung
mit 1.19.0/3.4.6: `Class "yii\web\ServiceUnavailableHttpException" not found`;
keine kontrollierte HTTP-503-Ausnahme. Die Sperre liegt vor der Transaktion bzw.
Aktion; Teilnehmer und Audit blieben unverändert. Dies ist kein beobachteter
Berechtigungsdurchbruch. Der Fehler kann auch bei früheren freigegebenen Versionen
auftreten, wenn die Audit-Tabelle fehlt; dieser Auslöser wurde hier nicht separat
ausgeführt.

Eine minimale Korrektur wäre die Verwendung der vorhandenen yii\web\HttpException
mit explizitem Status 503 in beiden Dateien, samt Regressionstests für die
Sperrpfade. Dies wurde NICHT im Modul umgesetzt. Ein positiver Quellvergleich
allein rechtfertigt außerdem keine Erweiterung der Messenger-Allowlist.
Vor einer 3.4.6-Freigabe müssen die Fehlerantworten korrigiert und die tatsächliche
Entfernung/Audit-/Berechtigungskette für 3.4.6 auf einem isolierten Kandidatenstand
sowie danach auf einer vollständigen Testinstallation geprüft werden.

### Verbleibende Grenzen

Keine vollständige HumHub-Neuinstallation, kein Upgrade einer vorhandenen
Installation, keine komplette Core-Migrationskette und kein Test auf PHP 8.2.
Kein Browser-/iPhone-Test, kein tatsächlich übertragenes Attachment oder S3,
keine externen Benutzerquellen/SSO, keine echten 2FA-Gates, kein REST-Endpunkt,
keine parallelen Entfernungsprozesse, keine Wiedereinladung im UI und keine
vollständigen Core-/Messenger-Codeception-Suites ausgeführt. Die funktionalen
Entfernungsprüfungen gelten NICHT für 3.4.6, weil die Original-Allowlist sie blockiert.
Die README-Prüfliste bleibt für eine vollständige Testinstallation erforderlich.

Geändert wurden für diese Prüfung nur dieser Bericht und die Versionshinweise im
bereits vorbereiteten lokalen Skill. Kein funktionaler Modulcode geändert.
Die Prüfnachweise enthalten den zusätzlichen isolierten Testaufbau, die
Versions-/Quellenmetadaten und die finalen vier JSON-Ergebnisse. Für 3.4.6 beendet
sich der Prüflauf wegen der zwei Fehler mit Exit 1; die anderen drei mit Exit 0.

## Fehlerkorrektur nach ausdrücklichem Auftrag (13.10.2026)

### Änderungen

- services/ParticipantRemoval.php und controllers/ParticipantsController.php:
  Die nicht existierende ServiceUnavailableHttpException wurde jeweils durch
  die tatsächliche yii\web\HttpException mit explizitem Status 503 ersetzt.
  Nachrichten und Bedingungen der Sperren sind unverändert.
- Neu tests/availability.php: 16 Sperrprüfungen (8 Szenarien jeweils für
  Controller und Dienst). Echte Yii-Ausnahmen und HumHub-Controller werden aus
  einem vorbereiteten Kern geladen; Authentifizierung, Messenger-Version und
  Schema-Bereitschaft sind Test-Doubles. Es wird keine Datenbank verbunden.
- .github/workflows/runtime-tests.yml: stabilen Prüfstand v1.19.0/PHP 8.3 und
  Ausführung des neuen Regressionstests ergänzt. 1.18.5/PHP 8.2 und der bisherige
  Beta-Prüfstand bleiben erhalten. Keine neuen Trigger oder Schreibrechte.
- Neu docs/CHANGELOG.md: Korrektur unter Unreleased dokumentiert; Modulversion
  0.1.1 wurde nicht erhöht.
- Prüfbericht und Skill-Hinweise auf die Korrektur aktualisiert.

Access.php und seine Freigabeliste 3.3.13/3.4.5, module.json mit Mindestversion
1.18.1, vorhandene Migrationen und der Quality-Workflow wurden byteweise gegen
den Basisstand geprüft und sind unverändert. Die Änderung erweitert keine
Messenger-Freigabe und führt keine Datenmigration ein.

### Regression vor/nach Korrektur

Der neue Test wurde zunächst mit dem ursprünglichen Modulcode und dem stabilen
1.19.0-Kern ausgeführt: Exit 1, Class "yii\web\ServiceUnavailableHttpException"
not found. Mit der Korrektur bestehen dieselben 16 Prüfungen auf jedem der drei
Kerne 1.18.1, 1.18.5 und 1.19.0 (jeweils Exit 0).

Abgedeckt sind: Messenger 3.4.6 oder unbekannte Version, fehlendes Messenger-Modul,
fehlende Audit-Tabelle bei beiden freigegebenen Versionslinien, sowie Gäste,
nicht berechtigte Benutzer und Impersonation. Unverfügbarkeit liefert 503;
unberechtigte Benutzer erhalten weiterhin 403. Kein abgewiesener Dienstaufruf
startet eine Transaktion. Der zuvor nur als möglicher Auslöser dokumentierte
Fall „Audit-Tabelle fehlt“ ist damit jetzt tatsächlich als Regression geprüft.

### Erneute Laufzeitprüfungen nach Korrektur

Die vier isolierten Komponenten-/Datenbankläufe wurden auf PHP 8.3.30 und
MySQL 8.0.44 erneut mit dem korrigierten Modul durchgeführt:

| HumHub | Messenger | bestanden | Fehler | Ergebnis |
|---|---|---:|---:|---|
| 1.18.1 | 3.3.13 | 35 | 0 | geprüfte Funktionen erhalten |
| 1.18.5 | 3.3.13 | 35 | 0 | geprüfte Funktionen erhalten |
| 1.19.0 | 3.4.5 | 42 | 0 | geprüfte Funktionen erhalten |
| 1.19.0 | 3.4.6 | 15 | 0 | kontrollierte 503-Sperre; keine Freigabe |

Bei 3.4.6 sind jetzt zusätzlich die beiden tatsächlichen 503-Ausnahmen erfolgreich
geprüft; Menü bleibt ausgeblendet, Teilnehmer und Audit bleiben unverändert.
Die positive Testzahl bedeutet ausdrücklich NICHT, dass eine Entfernung mit
3.4.6 freigegeben oder erfolgreich durchgeführt wurde.

Zusätzlich erneut bestanden: 9 Regeln, 12 Zugriffsfälle, alle 13 PHP-Dateien,
Klassen-/Konfigurations-Smoke-Tests und 16 neue Sperrprüfungen auf jedem der drei
Kerne. Workflow als YAML geparst, bestehende Ziele und neuer Testschritt geprüft.
Der Workflow wurde nicht auf GitHub ausgeführt; die PHP-8.2-CI-Läufe stehen aus.
Skill-Validierung sowie finale Patch-Anwendung an einem frischen Basisstand
bestanden. Die 15 Framework-Tests aus der vorherigen Prüfung bleiben gültig;
das Framework wurde durch diesen Fix nicht geändert.

### Grenzen und Übergabe

Die Grenzen des minimalen Komponenten-/Fixtureschemas bleiben bestehen. Kein
vollständiger Site-Upgrade-, Browser-/S3-/SSO-/Parallelitäts- oder Produktionsnachweis.
Für eine spätere Messenger-3.4.6-Freigabe sind getrennte positive Funktionstests
und eine bewusste Erweiterung der Allowlist weiterhin erforderlich.

Das aktualisierte Paket enthält den lokalen Skill-Pilot plus diesen Bugfix,
Regressionstest, Changelog und CI-Ergänzung. Die Prüfnachweise enthalten die
finalen JSON-Ergebnisse, Regressionsergebnisse, Testaufbau und Dateihashes.
Temporäre Fixture-Datenbanken wurden gelöscht und die Testdatenbankprozesse
beendet. Kein Commit, Push, Merge, Release oder Deployment durchgeführt.

## Funktionale Freigabe von Messenger 3.4.6 (13.10.2026)

### Vorgehen und Autorisierung

Der Nutzer beauftragte ausdrücklich die funktionale Prüfung und eine Erweiterung
der Freigabeliste bei bestandenen Tests. Zunächst wurde in einer isolierten
Kandidatenkopie ausschließlich 3.4.6 zur bestehenden Allowlist ergänzt. Die
Arbeitskopie chat-admin blieb bis zum positiven Testnachweis unverändert.
Die Quellenstände sind dieselben vollständigen Release-Commits aus der Tabelle
oben: HumHub 1.19.0 / Messenger 3.4.6, keine beweglichen Entwicklungsbranches.

Die Kandidatenkopie bestand zunächst 42 isolierte Klassen-/Datenbankprüfungen.
Danach wurde eine vollständige neue HumHub-Testinstallation erzeugt:
- PHP 8.3.30, MySQL 8.0.44 mit utf8mb4/InnoDB.
- Neue eigens erzeugte Datenbank, keine vorhandene Site oder Produktionsdaten.
- Alle 219 Kernmigrationen über den regulären Installer erfolgreich ausgeführt.
- Grundkonfiguration und synthetischer Administrator über den Installer erzeugt.
- Messenger und chat-admin regulär aktiviert und ihre Migrationen angewendet.
- Synthetische Benutzer mit echten Passwörtern, Admin-Gruppen, Chats und einem
  tatsächlich gespeicherten privaten Textanhang über HumHub/Messenger-Modelle erstellt.
- Mailer ausschließlich im lokalen Dateitransport; keine E-Mails versendet.
- Webserver nur auf 127.0.0.1, vier PHP-Worker für echte parallele Anfragen.

Für den vollständigen Web-Bootstrap wurden die vorab ausgeschalteten
Yii-Composer-Plugin-Hooks regulär nachgeholt (festgeschriebenes Bootstrap5-Paket
neu registriert). Fehlende kopierte Asset-Quellverzeichnisse wurden vervollständigt.
Dies waren Korrekturen des Testaufbaus; kein HumHub- oder Messenger-Quellcode wurde
gepatcht und keine Composer-Lock-Datei verändert. Eigene Konfiguration liegt nur
in der isolierten Testkopie. Die originale chat-admin-Auditmigration blieb unverändert.

### Vollständige HTTP-Funktionsprüfung

Die Kandidatenkopie bestand 63 Prüfschritte auf der vollständigen Installation.
Nach Erweiterung der tatsächlichen Allowlist wurde der endgültige Modulstand in
der Testinstallation mit neuen Testchats erneut geprüft: dieselben 63 Schritte
bestanden. Keine vorgetäuschten Sessions oder Handtabellen in diesen HTTP-Tests:
Anmeldung, CSRF-Token und HTTP-Anfragen verwenden den wirklichen HumHub-Stack.

Geprüft wurden:
- Neue zweistufige Anmeldung (Identität/Passwort) für Administrator, normales
  Mitglied und Administrator außerhalb des Chats.
- Teilnehmerseite, geschützter eigener Zugang, kodierter Chat-Titel und
  Bestätigungsattribut; echtes Messenger-Menü zeigt Verwaltung nur berechtigt.
- Gäste und normale Mitglieder erhalten 403; Administrator außerhalb des Chats
  erhält für die Verwaltungsseite 404 und für eine Entfernung 403.
- GET auf Entfernen: 405; POST ohne CSRF: 400; fremde Chat-ID, unbekannter Chat,
  eigene/unbekannte/ungültige Teilnehmer-ID: abgewiesen, Audit unverändert.
- Gültiger POST: 302, Teilnehmer tatsächlich entfernt, genau ein Audit und ein
  Messenger-Teilnehmer-verlassen-Eintrag; Chat und Nachricht bleiben erhalten.
- Privat gespeicherter Anhang über den echten Download-Endpunkt: exakte Bytes
  vor Entfernung 200, danach dieselbe kopierte URL für die entfernte Person 401;
  verbleibender Administrator erhält weiter 200 und identische Bytes.
- Entfernte Person verliert tatsächlichen Messenger-HTTP-Zugriff; wiederholte
  Entfernung erhält 403 und erzeugt keinen zusätzlichen Audit-Eintrag.
- Wiedereinladung über den echten Messenger-HTTP-Endpunkt stellt Mitgliedschaft
  und den Download desselben Anhangs wieder her.
- Zwei unabhängige angemeldete Administrator-Sitzungen senden zeitgleich
  Entfernungs-POSTs an getrennte Worker: Statuscodes 302 und 403, genau eine neue
  Entfernung sowie ein neuer Audit-/Teilnehmer-verlassen-Eintrag, Chat erhalten.
- Impersonation über den echten Kern-Endpunkt zu einem anderen Systemadmin:
  danach Verwaltungs-GET und Entfernungs-POST 403; Audit unverändert.
- Nach weiterer Entfernung bleibt der letzte Administrator im Chat; dessen
  Selbstentfernung erhält 403 und löscht die Unterhaltung nicht.

Zusätzlich bestanden 16 HTTP-Prüfschritte auf dem endgültigen Modul:
- Simulierte zukünftige Messenger-Version 3.4.7: GET/POST 503, danach Manifest
  wieder auf den Originalstand 3.4.6 zurückgesetzt.
- Audit-Tabelle vorübergehend umbenannt: GET/POST 503; danach wiederhergestellt.
- Messenger-Registrierung über den Kern deregistriert (ohne Daten-Uninstall):
  GET/POST 503; anschließend wieder aktiviert. Dies prüft fehlende Registrierung,
  nicht den destruktiven Messenger-Uninstall.
- Echtes Kern-Passwort-Pflicht-Gate über den Benutzer aktiviert: AJAX-POST erhält
  401 mit gate=must-change-password; Browser-POST wird in den Pflichtablauf
  umgeleitet. Keine Mutation. Nach Rücknahme des Pflichtflags wieder Zugriff 200.
- Alle Zusatzsperren erhalten Audit und Teilnehmerzuordnungen.

Ergebnis: 79 erfolgreiche HTTP-Prüfschritte am endgültigen Modulstand, zusätzlich
63 erfolgreiche Schritte an der vorherigen Kandidatenkopie. JSON-Nachweise führen
die einzelnen Schritte auf; Zählungen beziehen sich auf Assertions, nicht auf
79 voneinander unabhängige Geschäftsfunktionen.

### Freigabeliste und Regressionen

Nach erfolgreicher Kandidatenprüfung wurde services/Access.php genau um 3.4.6
erweitert. 3.3.13 und 3.4.5 bleiben enthalten. Die Versionsfehlermeldung nennt
jetzt alle drei Versionen. Die strikte Prüfung künftiger Versionen bleibt erhalten;
3.4.7 wird nicht zugelassen. Ohne Impersonation-Schnittstelle wird Messenger 3.4.6
wie die bisherige 3.4.5-Version weiterhin abgewiesen.

Der endgültige Modulstand bestand erneut:

| HumHub | Messenger | Komponenten-/DB-Prüfungen | Fehler |
|---|---|---:|---:|
| 1.18.1 | 3.3.13 | 35 | 0 |
| 1.18.5 | 3.3.13 | 35 | 0 |
| 1.19.0 | 3.4.5 | 42 | 0 |
| 1.19.0 | 3.4.6 | 42 | 0 |

Diese zusätzlichen Läufe enthalten weiterhin Audit-Rollback, Impersonation auch
bei erlaubtem privatem Kernzugriff, Pflicht-Gates und die übrigen vorher
beschriebenen Pfade. Für 1.18 wurden die bestehenden Komponenten-/DB-Tests erneut
verwendet; keine neue vollständige 1.18-Installation aufgebaut.

Weitere Prüfungen:
- tests/run.php: 9 Regeln bestanden.
- tests/access.php: 16 Versions-/Zugriffsfälle bestanden (3.4.6 ergänzt, nächste
  unfreigegebene Version 3.4.7 gesperrt; Legacy-Sitzungs-Sperre geprüft).
- tests/availability.php: je 18 Controller-/Dienst-Sperrfälle auf 1.18.1, 1.18.5
  und 1.19.0 bestanden, einschließlich fehlender Audit-Tabelle mit 3.4.6.
- Bestehender Klassen-Smoke-Test auf allen drei Kernen bestanden.
- Alle 13 PHP-Dateien bestanden die Syntaxprüfung.
- Manifest und unveränderte Mindestversion 1.18.1/Modulversion 0.1.1 geprüft.
- Quality-Workflow führt jetzt die vorhandenen Policy-/Versionsregressionen aus;
  bestehende Trigger, PHP-Matrix und Rechte bleiben unverändert.
- Beide Workflow-Dateien als YAML geprüft; bestehende 1.18-/Beta-Ziele bleiben.
- Skill validiert; Patch an frischem Basisstand geprüft.

README, Changelog, Skill und dieser Bericht wurden aktualisiert. Die ursprünglich
getrackten Migrationen und module.json sind byteweise unverändert. Kein Einsatz
neuer ausschließlich in 1.19 existierender Modul-APIs, kein Wegfall von 1.18.

### Grenzen und Lieferstand

Dies ist die lokale funktionale Freigabe für die konkret geprüften Versionen,
keine Produktionsfreigabe und kein Nachweis eines Core-Upgrades vorhandener Daten.
Nicht ausgeführt: PHP 8.2, echte S3-/SSO-Installation, ein separates REST-Modul,
visuelle/interaktive Browser-JavaScript-Prüfung, iPhone und vollständige
Core-/Messenger-Codeception-Suites. Download und Wiedereinladung wurden dagegen
jetzt tatsächlich über HTTP und auf einer vollständig installierten Site geprüft.

Lokaler Branch feature/humhub-119-skill-pilot; keine Commits, Pushes, Merges,
Releases oder Deployments. Alle erzeugten Testdatenbanken werden entfernt;
Webserver und isolierter Datenbankprozess werden beendet. Das Paket enthält
Skill-Integration, 503-Bugfix, Regressionen und die getestete 3.4.6-Freigabe.

## Bereitstellung auf GitHub (13.10.2026)

Der Nutzer hat nach der bestandenen Funktionsprüfung ausdrücklich den Upload
beauftragt. Dieser geprüfte Stand wird als Commit auf dem separaten Branch
feature/humhub-119-skill-pilot in Kilian70/chat-admin bereitgestellt. Die oben
dokumentierten Aussagen zu fehlendem Commit/Push beziehen sich auf die vorherigen
lokalen Prüfschritte. Der Upload verändert nicht den Hauptbranch; Merge, Release
und Deployment bleiben ausgeschlossen. Der übertragene Commit wird anschließend
mit dem Remote-Branch verglichen.

Branch: https://github.com/Kilian70/chat-admin/tree/feature/humhub-119-skill-pilot

## Release 0.1.2 (13.10.2026)

Auf ausdrücklichen Nutzerauftrag wird der funktional geprüfte Stand als
veröffentlichte Testversion v0.1.2 bereitgestellt, entsprechend dem bisherigen
Pre-release v0.1.1. Grundlage ist Branch feature/humhub-119-skill-pilot; main
wird nicht zusammengeführt. Nur Modulversion, README-Titel, Changelog und dieser
Release-Vermerk wurden für die Veröffentlichung angepasst. PHP-Funktionscode,
Freigaben und Migrationen entsprechen dem zuvor getesteten Commit dfd5413843fb5b86053142cbabee737a4758a676.

Erneute Release-Prüfung: Manifest-Version/Mindestversion, PHP-Syntax sowie die
9 Policy-, 16 Versions-/Zugriffs- und je 18 Sperrprüfungen auf den drei vorbereiteten
Kernständen. Das ZIP enthält ausschließlich den getaggten Repository-Stand unter
chat-admin/, keine Git-Metadaten oder temporären Testdateien. Kein Deployment.
