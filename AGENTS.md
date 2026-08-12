# Regeln für flz_shortcode_redirect

Dieses Repository enthält ausschließlich das Plugin
`flz_shortcode_redirect`. Quellcode wird hier bearbeitet und in WordPress nur
per Symlink bereitgestellt. Core, fremde Erweiterungen, Uploads, Secrets und
produktive Konfiguration bleiben außerhalb des Scopes.

Das Plugin kapselt nur den kontrollierten Redirect-Vertrag. Ziele,
Requestwerte und erlaubte Protokolle/Hosts werden serverseitig validiert.
Secrets dürfen nicht in Logs erscheinen. Optionales Logging über
`flz_wpdb_objects` wird ausschließlich per öffentlichem Klassenvertrag und mit
datensparsamem lokalen Fallback verwendet; es ist keine harte Abhängigkeit.

Jede Verhaltensänderung braucht zuerst einen fokussierten Test für angemeldete
Nutzer, korrektes/falsches/fehlendes Secret, erlaubte und abgelehnte Ziele sowie
bereits gesendete Header. Ausgaben kontextbezogen escapen. Mindestens
`./scripts/check-fast` ausführen und fehlende WordPress-Integration benennen.
Keine Commits, Pushes oder Deployments ohne ausdrückliche Freigabe; nie
`git add .` verwenden.
