# Roadmap: flz_shortcode_redirect

## Prüfstatus

**Funktionsstand 2.0.0; Release-Gate in Übernahmephase 2 blockiert.** Ziele werden über WordPress' Host-/Protokollvertrag
und `wp_safe_redirect()` begrenzt. Der anonyme Zugriff nutzt eine an die
Seiten-ID gebundene HMAC-Signatur mit höchstens 60 Minuten Lebensdauer; alte
Klartext-Secrets werden sicher abgewiesen. Der fokussierte CLI-Smoke deckt
Allow-/Deny-, Ziel-, Header- und Logging-Fallbacks ab. PR-/Main-CI und die
PHP-Ratsche von 65,09 Prozent sind remote enforced. Der reproduzierbare
ZIP-Builder ist konfiguriert. Vor einem Tag fehlen die Annäherung an das
85-Prozent-Ziel, das ausgefüllte DDEV-Abnahmeprotokoll sowie Installation und
Rückbau aus dem exakten Artefakt. Das Protokoll liegt unter
`docs/manual-acceptance.md`.

## P1

1. **Erledigt:** Fachvertrag entscheiden und dokumentieren: exakte erlaubte Hosts/Protokolle,
   Verhalten bei leerem Ziel oder leerem Secret, angemeldete Rollen und
   gewünschte Lebensdauer des Zugangs. Keine stillschweigende Autorisierung
   allein durch UI-Sichtbarkeit.
2. **Erledigt:** Secret nicht dauerhaft im Klartext in Shortcode-Inhalt und Query-Logs als
   langfristigen Zugriffsnachweis verwenden. Einen WordPress-nativen,
   zeitbegrenzten/signierten oder capability-basierten Vertrag entwerfen;
   Umstellung als öffentlichen Kompatibilitätswechsel behandeln.
3. **Erledigt:** Fokussierte Tests ergänzen: angemeldet, korrekt/falsch/fehlendes Secret,
   interner erlaubter Redirect, externer/gefälschter Host, bereits gesendete
   Header und fehlende Shared-Logging-Abhängigkeit.

## P2

1. **Erledigt:** Redaktionellen Gutenberg-Block über
   `flz_ui_register_shortcode_block()` anbieten, sofern der Shortcode weiterhin
   redaktionell verwendet wird; Abhängigkeit defensiv behandeln.
2. **Erledigt:** Plugin-Header um Textdomain und unterstützte Versionen ergänzen; alle Texte
   konsistent über die komponenteneigene Domain übersetzen.
3. Redirect möglichst vor der Ausgabe in einer geeigneten WordPress-
   Requestphase ausführen; Shortcode-Fallback nur für unvermeidbare
   Kompatibilität beibehalten und testen.
