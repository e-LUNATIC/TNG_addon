# TNG_addon
1. Zeile 237 der Datei statistics_front.php und Zeile 31 von last_updates.php entsprechend anpassen.
2. Alle 3 Dateien in das TNG Basisverzeichnis kopieren.

Vorhandene Dateien werden nicht überschreiben und auch sonst finden keine Änderungen bei TNG statt.

Die Dateien statistics_front.php und last_updates.php können nun direkt aufgerufen werden, unabhängig von den eingestellten Zugriffsrechten bei TNG. 

Datenschutz bei lebenden Personen ist nach wie vor Gewährleistet.

TNG kann somit leicht ein Stück weit in andere CMS Systeme integriert werden da hier nur eine Ausgabe der Daten erfolgt.

 

Das Design der Tabelle in der statistics_front.php Datei ist für Typo3 mit Bootstrap angepasst. In Zeile 31/33 kann die Tabellen Klasse angepasst werden.

class=\"table table-hover\"
