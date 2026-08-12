# flz_shortcode_redirect

Kleines WordPress-Plugin für kontrollierte Weiterleitungen aus dem bestehenden
Shortcode `[redirect]`. Ziele werden über `wp_safe_redirect()` begrenzt.

Das Plugin besitzt keine harte Laufzeitabhängigkeit. Wenn
`flz_wpdb_objects` verfügbar ist, wird dessen öffentlicher Fehlervertrag für
technische Logs verwendet; andernfalls greift ein lokaler Fallback.

Prüfung: `./scripts/check-fast`.
