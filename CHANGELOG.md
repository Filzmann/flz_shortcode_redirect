# Changelog

Alle wesentlichen Änderungen an `flz_shortcode_redirect` werden in dieser
Datei dokumentiert. Ein Datum wird erst bei einer tatsächlichen
Veröffentlichung ergänzt.

## Unreleased

- Reproduzierbare PR-/Main-CI für PHP 8.1 und 8.5 ergänzt.
- Formale Lizenz- und Abnahmenachweise in den Delivery-Vertrag aufgenommen.

## 2.0.0

- Zeitlich begrenzte, seitenspezifische HMAC-Zugangslinks für Gäste eingeführt.
- Redirect-Ziele auf den WordPress-Vertrag für sichere Hosts und Protokolle
  begrenzt.
- Dauerhafte Klartext-Secrets verworfen und technisches Logging datensparsam
  auf den optionalen Shared-Vertrag umgestellt.
