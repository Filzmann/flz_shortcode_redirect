# Manuelles Abnahmeprotokoll – FLZ Shortcode Redirect

Dieses Formular dokumentiert die Abnahme des exakten Release-Artefakts. Keine
Signaturen, Salts, Secrets oder vollständigen geschützten URLs eintragen. Pro
Fall genau ein Ergebnis markieren und Abweichungen begründen.

## Kopfdaten

| Feld | Eintrag |
|---|---|
| Datum / Prüfer*in | |
| Umgebung / WordPress / PHP | |
| Plugin-Version / vollständiger Git-Commit | |
| Artefaktname / SHA-256 | |
| Neutrale Testseite | |

## Automatisierte Nachweise

| Nachweis | Kommando / Lauf | Ergebnis / Beleg |
|---|---|---|
| PR-/`main`-CI / PHP 8.1 und 8.5 | Workflow-Lauf / vollständiger Commit | |
| Signatur-, Ziel-, Header- und Logging-Smoke | `./scripts/check-fast` | |
| PHP-Line-Coverage / Baseline / Ziel 85 % | | |
| Reproduzierbarkeit und Archivinhalt | | |

## Manuelle Prüffälle

| ID | Prüfschritte | Erwartetes Ergebnis | Ergebnis | Warum / Beleg / Abweichung |
|---|---|---|---|---|
| RED-01 | Als angemeldetes Konto die neutrale Testseite öffnen. | Der dokumentierte Zielpfad wird sicher erreicht. | [ ] erfolgreich [ ] nicht erfolgreich [ ] nicht geprüft | |
| RED-02 | Anonym einen gültigen, kurzlebig signierten Link verwenden. | Weiterleitung erfolgt exakt zum erlaubten lokalen Ziel. | [ ] erfolgreich [ ] nicht erfolgreich [ ] nicht geprüft | |
| RED-03 | Signatur verändern, entfernen und nach Ablauf erneut verwenden. | Alle Varianten werden ohne Redirect und ohne interne Detailausgabe abgewiesen. | [ ] erfolgreich [ ] nicht erfolgreich [ ] nicht geprüft | |
| RED-04 | Signatur einer anderen Seiten-ID wiederverwenden. | Seitenbindung verhindert die Weiterleitung. | [ ] erfolgreich [ ] nicht erfolgreich [ ] nicht geprüft | |
| RED-05 | Externes, schemaloses und manipuliertes Ziel prüfen. | Nur durch WordPress erlaubte sichere Ziele werden akzeptiert. | [ ] erfolgreich [ ] nicht erfolgreich [ ] nicht geprüft | |
| RED-06 | Logs und Seiteninhalt kontrollieren. | Keine Signatur, kein Salt und kein Legacy-Klartextsecret werden gespeichert oder protokolliert. | [ ] erfolgreich [ ] nicht erfolgreich [ ] nicht geprüft | |

## Abschlussentscheidung

| Feld | Eintrag |
|---|---|
| Erfolgreich / nicht erfolgreich / nicht geprüft | |
| Kritische Abweichungen / Tickets | |
| Legacy-Inhalte geprüft | [ ] ja [ ] nein |
| Gesamtentscheidung | [ ] abgenommen [ ] mit Auflagen abgenommen [ ] nicht abgenommen |
| Name / Datum | |
