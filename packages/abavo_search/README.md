# abavo Search #

abavo Search.

## Details ##
**Extension Key:** EXT:abavo_search
**Autor:** Mathias Bruckmoser
**E-Mail:** dev@abavo.de
**Version:** 1.5.3

**Abhängigkeiten**

| Name | Version |
| --- | --- |
| TYPO3 | 8.7.0 - 9.5.99 |
| PHP | 7.0.99 - 7.2.99 |

## Funktionen ##

 * Frontend-Suche für TYPO3-Inhalte und Office-Dokumente
 * Die Basisfunktionalität flexibel und einfach mit wenigen Schritten erweitert werden

## Key Features ##

 * Nutzung des [Extbase Frameworks](https://docs.typo3.org/typo3cms/ExtbaseGuide/Extbase/Index.html) und [FLUID-Template-Engine](https://wiki.typo3.org/De:Fluid)
 * Unterstützung von bestehenden und eigenen Stop-Wörtern über XML konfigurierbar
 * Indexierung nicht global sondern abhängig von Konfigurationen in verschiedenen TYPO3-Sys-Ordnern
 * Einfache Erweiterbarkeit über das Erstellen eigener Index-Hooks ohne Feldbeschränkung mit eigenen FlexForms
 * Suche nach Begriffen ab 3 Zeichen (per TS-Setup konfigurierbar) möglich mit MySQL-Fallback „LIKE“ für „MATCH AGAINST“ mit MySQL Variable '[ft_min_word_len](https://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_ft_min_word_len)', sollte eine Suchbegriffslänge unter dem nur serverseitig konfigurierbaren  
 * Limit sein (i.d.R. 4)
 * Rückgabe der Dauer von Indexerstellung und Suchanfragen
 * Bei Alternativ-Wörtern sind die Aufrufe auf 60 Anfragen/Minute auf den jeweiligen Besucher begrenzt
 * Einbindung des Suchformulars unabhängig von Plugin über [FLUID](https://wiki.typo3.org/De:Fluid) auch mehrmals auf einer Seite möglich
 * Intelligentes, sich selbst bereinigendes, von Zugriffs-Berechtigung und sprachabhängiges Autocomplete (Autocomplete nur auf vorhandenen Index)
 * Synonyme mit Reverse-Funktion und stilistischer Unterscheidung in den Suchergebnissen
 * Facettierung über SysCategories

## Weitere Informationen ##

[zur Dokumentation als DocX](./Documentation/Manual.docx)
[zur Dokumentation als PDF](./Documentation/Manual.pdf)
[Overview als XML](./Documentation/abavo_search-overview-drawio.xml)
[Overview als PDF](./Documentation/abavo_search-overview.pdf)