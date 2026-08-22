<?php

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

define( 'ABSPATH', __DIR__ . '/' );

// Testausgaben und -Exceptions werden ausschließlich von der CLI ausgewertet.
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.ExceptionNotEscaped

$GLOBALS['flz_redirect_logged_in'] = false;
$GLOBALS['flz_redirect_post_id'] = 42;
$GLOBALS['flz_redirect_allowed_targets'] = array( '/intern' );

function add_shortcode(): void {}
function add_action(): void {}
function shortcode_atts( array $defaults, array $atts ): array {
	return array_merge( $defaults, $atts );
}
function is_user_logged_in(): bool {
	return (bool) $GLOBALS['flz_redirect_logged_in'];
}
function get_queried_object_id(): int {
	return (int) $GLOBALS['flz_redirect_post_id'];
}
function wp_salt(): string {
	return 'test-only-auth-salt';
}
function wp_unslash( $value ) {
	return $value;
}
function sanitize_text_field( $value ): string {
	return trim( (string) $value );
}
function absint( $value ): int {
	return abs( (int) $value );
}
function esc_url_raw( string $url ): string {
	return trim( $url );
}
function wp_validate_redirect( string $url, string $fallback = '' ): string {
	return in_array( $url, $GLOBALS['flz_redirect_allowed_targets'], true ) ? $url : $fallback;
}
function wp_safe_redirect( string $url ): bool {
	return false;
}
function esc_html__( string $text ): string {
	return $text;
}
function esc_url( string $url ): string {
	return htmlspecialchars( $url, ENT_QUOTES );
}
function add_query_arg( array $args, string $url ): string {
	return $url . '?' . http_build_query( $args );
}
require dirname( __DIR__ ) . '/flz_shortcode_redirect.php';

$GLOBALS['flz_redirect_logged_in'] = true;
if ( '' !== flz_shortcode_redirect( array( 'redirect' => '/intern' ) ) ) {
	throw new RuntimeException( 'Angemeldete Personen wurden nicht freigeschaltet.' );
}

$GLOBALS['flz_redirect_logged_in'] = false;
$now = time();
$expires = $now + 300;
$_GET = array(
	'flz_redirect_expires' => (string) $expires,
	'flz_redirect_signature' => flz_shortcode_redirect_signature( 42, $expires ),
);
if ( '' !== flz_shortcode_redirect( array( 'redirect' => '/intern' ) ) ) {
	throw new RuntimeException( 'Eine gültige kurzlebige Signatur wurde abgelehnt.' );
}

foreach (
	array(
		array(),
		array(
			'flz_redirect_expires' => (string) $expires,
			'flz_redirect_signature' => str_repeat( '0', 64 ),
		),
		array(
			'flz_redirect_expires' => (string) ( $now - 1 ),
			'flz_redirect_signature' => flz_shortcode_redirect_signature( 42, $now - 1 ),
		),
	) as $invalid_query
) {
	$_GET = $invalid_query;
	if ( flz_shortcode_redirect_request_is_authorized( 42, $now ) ) {
		throw new RuntimeException( 'Eine fehlende, falsche oder abgelaufene Signatur wurde akzeptiert.' );
	}
}

if ( ! flz_shortcode_redirect_target_is_allowed( '/intern' ) ) {
	throw new RuntimeException( 'Ein internes Redirectziel wurde abgelehnt.' );
}
if ( flz_shortcode_redirect_target_is_allowed( 'https://attacker.example/phishing' ) ) {
	throw new RuntimeException( 'Ein nicht erlaubter externer Host wurde akzeptiert.' );
}

$url = flz_shortcode_redirect_create_signed_url( 'https://example.test/geschuetzt', 42, 300, $now );
if ( ! str_contains( $url, 'flz_redirect_expires=' ) || ! str_contains( $url, 'flz_redirect_signature=' ) ) {
	throw new RuntimeException( 'Der Signatur-Helper erzeugt keinen vollständigen Zugangslink.' );
}

$_GET = array();
$redirect_denied = flz_shortcode_redirect( array( 'redirect' => '/intern' ) );
if ( ! str_contains( $redirect_denied, 'konnte nicht ausgeführt werden' ) ) {
	throw new RuntimeException( 'Eine Ablehnung durch wp_safe_redirect() wird nicht sicher behandelt.' );
}

$_GET = array();
while ( ob_get_level() > 0 ) {
	ob_end_flush();
}
echo "headers-sent-test\n";
$fallback = flz_shortcode_redirect( array( 'redirect' => '/intern' ) );
if ( ! str_contains( $fallback, 'href="/intern"' ) ) {
	throw new RuntimeException( 'Bei bereits gesendeten Headern fehlt der sichere Fallback-Link.' );
}
echo "OK: flz_shortcode_redirect signed redirect smoke test\n";
