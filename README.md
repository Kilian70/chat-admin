# Chat-Teilnehmerverwaltung 0.1.3 – Testversion

Separates HumHub-Zusatzmodul für Messenger **3.3.13** und **3.3.15** (HumHub 1.18) sowie **3.4.5** und **3.4.6** (HumHub 1.19). Quellcode-Referenzen des offiziellen Repositorys https://github.com/humhub/mail:

- 3.3.15: `b2b1ead3356d60d6b30805fbfc1ac74c72dfdb62`
- 3.3.13: `74c5718405e9fa4a1212d1f8fc1bf47661834231`
- 3.4.5: `33e02e8cf2c0552aa6926b93ca23991b83c24940`
- 3.4.6: `f1f6af8e2fa19d40d21eace55091122269670552`

Menü-Widget, Teilnehmerrelation, Löschereignisse und Nachrichten-Zugriffsprüfung wurden in diesen Versionsständen verglichen. Funktionale Ergebnisse zur stabilen Version 1.19.0 stehen unten; ein Quellvergleich allein ist kein Integrationstest.

## Installation zuerst auf einer Testseite

1. Datenbank und HumHub-Dateien sichern.
2. Ordner `chat-admin` nach `protected/modules/` kopieren.
3. Modul unter Administration → Module aktivieren. Die Datenbankmigration legt ausschließlich eine eigene Protokolltabelle an.
4. Als Systemadministrator einen eigenen Gruppenchat öffnen. Im Menü oben rechts erscheint „Teilnehmer verwalten“.
5. Eine Testperson entfernen und alle Prüfungen unten durchführen. Erst danach produktiv einsetzen.

## Schutz und Grenzen

- Nur angemeldete HumHub-Systemadministratoren dürfen Teilnehmer verwalten; zusätzlich müssen sie selbst Teilnehmer der jeweiligen Unterhaltung sein. Keine Einsicht in fremde private Chats.
- Verwaltung während einer Benutzer-Impersonation wird abgewiesen, auch wenn privater Zugriff im Hosting erlaubt ist. HumHubs normale Prüfung für deaktivierte/nicht freigegebene Konten bleibt aktiv.
- Entfernen nur per POST mit HumHubs normalem CSRF-Schutz und Sicherheitsabfrage. Der Server prüft Rechte erneut, auch bei manipulierten Formularen.
- Eigenes Konto und letzter Teilnehmer sind geschützt. Die Unterhaltung wird niemals durch dieses Modul gelöscht.
- Die Messenger-Teilnehmerzuordnung wird über dessen ActiveRecord gelöscht. Der normale Teilnehmer-verlassen-Hinweis und die Cache-Aktualisierung bleiben erhalten.
- Entfernung und eigenes Audit werden gemeinsam in einer Datenbanktransaktion gespeichert. Parallelentfernungen aus dem Zusatzmodul werden durch eine Zeilensperre serialisiert (MySQL/MariaDB).
- Das Protokoll speichert nur Chat-ID, ausführende/entfernte Benutzer-ID und UTC-Zeit, keine Nachrichtentexte. Es ist nur für noch beteiligte Systemadministratoren sichtbar.
- Es gibt keine dauerhafte Sperre: Teilnehmer mit Messenger-Einladerecht können eine entfernte Person wieder hinzufügen. Bereits angezeigte oder heruntergeladene Daten können nicht zurückgerufen werden.
- Messenger prüft den Nachrichten-/Dateizugriff über die Teilnehmerzuordnung (`MessageEntry::canView`). Direkte Dateiaufrufe müssen auf der konkreten HumHub-Installation zusätzlich getestet werden.
- Nach einem Messenger-Update schaltet sich die Funktion sicherheitshalber ab, bis die neue Version geprüft und freigegeben wird. Messenger-Dateien werden nicht verändert.
- Uninstall nicht automatisch unterstützt: Protokolldaten werden nicht stillschweigend entfernt. Vor manueller Datenbereinigung Sicherung erstellen.

## Prüfliste auf HumHub

- Systemadministrator im Chat: Menüpunkt sichtbar, Name sicher angezeigt, Sicherheitsabfrage, Entfernung funktioniert.
- Normaler Teilnehmer und Gast: Menüpunkt fehlt; direkter GET/POST-Zugriff liefert Fehler.
- Systemadministrator außerhalb des Chats: kein Zugriff, auch bei bekannter Chat-ID.
- GET auf Entfernen und POST ohne gültigen CSRF-Token: abgewiesen.
- Manipulierte fremde Chat-ID, eigene Benutzer-ID oder bereits entfernte Person: abgewiesen.
- Entfernte Testperson in eigenem Browserkonto: Chat neu laden, Nachrichten-/REST-Aufruf und zuvor kopierte direkte Anhang-URL testen. Alles muss verweigert werden.
- Verbleibende Teilnehmer: Nachrichten und Anhänge unverändert lesbar; Teilnehmermeldung und Audit vorhanden.
- Gleichzeitig dieselbe Person entfernen: höchstens eine erfolgreiche Entfernung/ein Audit.
- Erneutes Einladen ausprobieren: Zugriff kehrt bewusst zurück.
- Messenger deaktiviert oder andere Messenger-Version: Funktion blockiert.
- HumHub 1.19: während Benutzer-Impersonation müssen Menü und direkter Zugriff gesperrt bleiben.
- iPhone: Menü und Teilnehmerformular prüfen.

## Funktionale Prüfung auf HumHub 1.19.0 / Messenger 3.4.6

Am 13.10.2026 bestand eine isolierte vollständige Testinstallation mit PHP 8.3.30
und MySQL 8.0.44 insgesamt 79 HTTP-/Funktionsprüfungen. Die 219 Kernmigrationen,
Messenger-Migrationen und eigene Audit-Migration wurden ausgeführt; beide Module
wurden regulär aktiviert. Verwendet wurden ausschließlich synthetische Testdaten.

Geprüft: echte Anmeldung und Modul-/Messenger-Seiten, Admin außerhalb des Chats,
Gäste und normale Teilnehmer, manipulierte IDs, GET-/CSRF-Sperren, gültige
Entfernung, Audit und Teilnehmermeldung, tatsächlicher privater Anhang-Download
vor/nach Entfernung, Wiedereinladung, parallele Entfernungen aus zwei getrennten
Sitzungen, Impersonation, letzter Teilnehmer, nicht freigegebene Messenger-Version,
fehlende Audit-Tabelle, deregistriertes Messenger-Modul und das echte
Passwort-Pflicht-Gate. Zwei parallele Anfragen erzeugten genau eine erfolgreiche
Entfernung und einen zusätzlichen Audit-/Teilnehmer-verlassen-Eintrag.

Die Freigabeliste wurde erst nach bestandener Prüfung einer Kandidatenkopie um
3.4.6 erweitert; danach bestand auch der endgültige Modulstand dieselben Prüfungen.
Die Zusatzprüfungen mit echten Klassen und Datenbank bestanden weiterhin auf
HumHub 1.18.1 und 1.18.5 mit Messenger 3.3.13 (je 35 Fälle), sowie auf 1.19.0 mit
Messenger 3.4.5 und 3.4.6 (je 42 Fälle). Die Mindestversion 1.18.1 bleibt erhalten.

Nicht geprüft: PHP 8.2, Upgrade einer vorhandenen Installation, echte
S3-/SSO-Konfiguration, REST-Modul, vollständige Browser-JavaScript-Bedienung oder
iPhone. Die lokale Funktionsfreigabe ersetzt keine Produktionsprüfung auf der
konkreten Installation. Details und Quellen: [Prüfbericht](docs/ai/SKILL-PILOT.md).

## Bisherige Testergebnisse

Am 22.09.2026 wurde das Modul lokal auf HumHub 1.18.5 mit Messenger 3.3.13 getestet:

- Aktivierung beider Module und Aufruf der Teilnehmerverwaltung aus einem Testchat als Systemadministrator erfolgreich; eigener Zugang ist geschützt.
- Als normale Testperson fehlt der Menüpunkt. Ein direkter Aufruf der Teilnehmerverwaltung wird mit „Forbidden“ abgewiesen.
- Ein GET-Aufruf der Entfernen-Aktion wird als unzulässige Methode abgewiesen; eine nicht vorhandene Chat-ID liefert „Not Found“. Anonyme GET- und POST-Aufrufe wurden mit HTTP 403 abgewiesen.
- Ein angemeldeter POST ohne CSRF-Token wurde abgewiesen. Direkte Dienstaufrufe mit eigenem Konto, bereits entfernter oder unbekannter Person, unbekannter Chat-ID und normaler Benutzerrolle wurden abgewiesen; das Audit blieb dabei unverändert.
- Eine Testperson wurde zweimal entfernt, mit bewusster Wiedereinladung dazwischen. Pro Entfernung entstand genau ein Audit-Eintrag. Chat, Nachricht und Anhang blieben für den Administrator erhalten.
- Bei zwei gleichzeitig gestarteten Entfernungsversuchen war einer erfolgreich und einer wurde abgewiesen; es kam nur ein Audit-Eintrag hinzu.
- Nach der Entfernung erschien der Chat für die Testperson nicht mehr. Der zuvor kopierte direkte Anhangslink wurde mit „Unzureichende Rechte“ abgewiesen. Die Berechtigungsprüfung von Messenger und HumHub lieferte für Nachricht und Anhang vor der Entfernung Zugriff und danach keinen Zugriff mehr.
- In den lokalen HumHub-Protokollen wurden bei diesen Tests keine neuen Fehler gefunden.

Auf einer getrennten lokalen Installation mit HumHub 1.19.0-beta.3 und Messenger 3.4.5 wurden Anmeldung als Systemadministrator, Messenger-Aufruf, Menüeintrag „Teilnehmer verwalten“ und die Teilnehmerseite eines Testchats erfolgreich geprüft. Die Seite schützte den eigenen Zugang und zeigte das bestehende Audit an.

- Manipulierte HTTP-Aufrufe: GET auf die Entfernen-Aktion lieferte 405, POST ohne CSRF-Token 400; eigenes Konto, unbekannte Person, ungültige Benutzer-ID, unbekannte Chat-ID und bereits entfernte Person wurden mit 403 abgewiesen.
- Eine gültige Entfernung lieferte eine Weiterleitung und genau einen zusätzlichen Audit-Eintrag. Der Chat blieb bestehen; ein zweiter Entfernungsversuch wurde mit 403 abgewiesen.
- Eine erneute Einladung gab der Testperson wieder Zugriff auf den Anhang (HTTP 200). Nach erneuter Entfernung wurde der direkte Anhangslink für sie mit HTTP 401 abgewiesen; der verbleibende Administrator konnte ihn weiterhin öffnen (HTTP 200). Die Verwaltungsseite blieb für die normale Testperson gesperrt (HTTP 403).
- Während der Administrator einen Benutzer impersonierte, blieb die Verwaltungsseite gesperrt (HTTP 403); der Chat war ebenfalls gesperrt und der Verwaltungsmenüpunkt nicht sichtbar.
- Zwei gleichzeitige Entfernungsanfragen ergaben eine Weiterleitung und eine Ablehnung (HTTP 302/403) sowie genau einen weiteren Audit-Eintrag.
- In den lokalen HumHub-Protokollen wurden bei diesen Tests keine neuen Fehler gefunden.

Ein vom Betreiber bereitgestellter iPhone-Screenshot zeigt die Teilnehmerseite mit lesbarer Tabelle sowie sichtbaren Schaltflächen „Entfernen“ und „Zurück zum Chat“. Die Bedienung der Entfernung auf dem iPhone wurde damit nicht geprüft; der Screenshot weist die HumHub-Version der abgebildeten Installation nicht nach.

Beim damaligen Beta-Prüfstand blieben die Entfernung per iPhone, die Prüfung eines Administrators außerhalb des Chats und Tests mit deaktiviertem oder nicht unterstütztem Messenger offen. Die oben beschriebenen aktuellen Prüfungen betreffen den stabilen 1.19.0-Kern. Die lokale Prüfung ist keine Produktionsfreigabe.

Zusätzlich prüft `php tests/run.php` neun reine Berechtigungsfälle; `php tests/access.php` prüft sechzehn Versions- und Zugriffsfälle mit Test-Doubles. Alle PHP-Dateien bestanden die Syntaxprüfung.
