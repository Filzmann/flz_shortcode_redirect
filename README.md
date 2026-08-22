# flz_shortcode_redirect

Kleines WordPress-Plugin für kontrollierte Weiterleitungen aus dem bestehenden
Shortcode `[redirect redirect="/anmeldung/"]`. Ziele werden über WordPress'
Host- und Protokollvertrag sowie `wp_safe_redirect()` begrenzt.

Angemeldete WordPress-Nutzer dürfen die Seite wie bisher sehen. Gäste benötigen
seit 2.0.0 einen maximal eine Stunde gültigen, an die Seiten-ID gebundenen
HMAC-Zugangslink. Dauerhafte Klartext-Secrets werden absichtlich ignoriert.
Ein Link wird ausschließlich serverseitig erzeugt:

```php
$url = flz_shortcode_redirect_create_signed_url(
    get_permalink( 123 ),
    123,
    900
);
```

Die Standardgültigkeit beträgt 15 Minuten, die harte Obergrenze 60 Minuten.
Die Query-Parameter heißen `flz_redirect_expires` und
`flz_redirect_signature`; die Signatur verwendet den WordPress-Auth-Salt und
wird nicht gespeichert oder protokolliert. Ein optionaler dynamischer
Gutenberg-Block wird registriert, wenn `flz_ui_components` aktiv ist.

Das Plugin besitzt keine harte Laufzeitabhängigkeit. Wenn
`flz_wpdb_objects` verfügbar ist, wird dessen öffentlicher Fehlervertrag für
technische Logs verwendet; andernfalls greift ein lokaler Fallback.

Prüfung: `./scripts/check-fast`.
