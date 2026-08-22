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

## Commit-, Coverage- und Release-Gates

- Der aktuelle Übernahmestand ist Phase 2: PR-/Main-CI und die PHP-Coverage-
  Ratsche sind remote belegt. Normale Produktcommits brauchen das enforced
  Commit-Gate; Releasecommits bleiben bis zur Abnahme und zum reproduzierbaren
  Artefakt-Gate blockiert.
- Vor jedem normalen Commit sind Status, Diff-Statistik und vollständige
  Dateiliste zu zeigen; fokussierte Tests, `./scripts/check-fast`, CI und
  Coverage-Gates müssen grün sein. Dateien werden einzeln gestaged;
  `git add .` bleibt verboten.
- PHP-Line-Coverage wird gegen eine gemessene No-Regression-Baseline geprüft.
  Neuer oder wesentlich geänderter Code erreicht mindestens 85 Prozent;
  Sicherheitsinvarianten sind unabhängig davon vollständig abgedeckt.
- Der PHPCOV-/Xdebug-Messjob erzwingt eine PHP-Baseline von 65,09 Prozent; das
  separate Ziel für neuen oder wesentlich geänderten Code bleibt 85 Prozent.
- `scripts/build-release` erzeugt über den kanonischen Workspace-Builder ein
  reproduzierbares Ein-Wurzel-ZIP mit Manifest und SHA-256. Das Artefakt-Gate
  bleibt bis zur Prüfung des exakten ZIP in WordPress `configured`.
- Ein Fast- oder Diagnosecheck ist kein Releaseurteil. Ein Release braucht ein
  sauberes Repository, konsistente Version/Changelog/Lizenz, vollständig
  ausgefülltes `docs/manual-acceptance.md`, ein reproduzierbares Ein-Wurzel-
  Archiv, Manifest und SHA-256 sowie geprüfte Installation, Ziele, Ablauf,
  sichtbares Verhalten und Rückbau aus dem exakten Artefakt.
- Bauen, Signieren, Taggen, Pushen, Publizieren und Deployen bleiben getrennte,
  ausdrücklich zu autorisierende Aktionen.
