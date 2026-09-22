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

Lokal: `php tests/run.php` prüft neun reine Berechtigungsfälle; `php tests/access.php` prüft Versionsfreigabe, Administratorzugriff und Impersonation mit Test-Doubles. PHP-Syntax wurde kontrolliert. **Noch kein Live-/Integrationstest mit einer HumHub-Installation durchgeführt; keine Produktionsfreigabe.**
