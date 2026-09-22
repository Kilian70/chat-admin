# Chat-Teilnehmerverwaltung 0.1.1 – Testversion

Separates HumHub-Zusatzmodul für Messenger **3.3.13** (HumHub 1.18) und **3.4.5** (HumHub 1.19). Quellcode-Referenzen des offiziellen Repositorys https://github.com/humhub/mail:

- 3.3.13: `74c5718405e9fa4a1212d1f8fc1bf47661834231`
- 3.4.5: `33e02e8cf2c0552aa6926b93ca23991b83c24940`

Menü-Widget, Teilnehmerrelation, Löschereignisse und Nachrichten-Zugriffsprüfung wurden in beiden Versionen verglichen. Das ist eine Schnittstellenprüfung, kein Nachweis eines vollständigen HumHub-Integrationstests.

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

Auf einer getrennten lokalen Installation mit HumHub 1.19.0-beta.3 und Messenger 3.4.5 wurden Anmeldung als Systemadministrator, Messenger-Aufruf, Menüeintrag „Teilnehmer verwalten“ und die Teilnehmerseite eines bestehenden Testchats erfolgreich geprüft. Die Seite schützte den eigenen Zugang und zeigte das bestehende Audit an. Eine vollständige Sicherheitsprüfung der Entfernungsfunktion auf 1.19 wurde noch nicht durchgeführt.

Weiterhin offen sind manipulierte POST-Parameter über HTTP, die vollständigen Entfernungstests auf HumHub 1.19 sowie die Prüfung auf einem iPhone. Die lokale Prüfung ist keine Produktionsfreigabe.

Zusätzlich prüft `php tests/run.php` neun reine Berechtigungsfälle; `php tests/access.php` prüft zwölf Versions- und Zugriffsfälle mit Test-Doubles. Alle PHP-Dateien bestanden die Syntaxprüfung.
