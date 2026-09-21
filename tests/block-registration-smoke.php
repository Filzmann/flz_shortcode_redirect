<?php

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['flz_redirect_blocks'] = array();

function add_shortcode(): void {}
function add_action(): void {}
function __( string $text ): string {
	return $text;
}
function flz_ui_register_shortcode_block( array $config ): void {
	$GLOBALS['flz_redirect_blocks'][] = $config;
}

require dirname( __DIR__ ) . '/flz_shortcode_redirect.php';

flz_shortcode_redirect_register_block();
if ( 1 !== count( $GLOBALS['flz_redirect_blocks'] ) ) {
	throw new RuntimeException( 'Der Redirect-Shortcode wurde nicht genau einmal als Block registriert.' );
}
$block = $GLOBALS['flz_redirect_blocks'][0];
if ( 'flz/shortcode-redirect' !== ( $block['name'] ?? '' ) || 'redirect' !== ( $block['shortcode'] ?? '' ) ) {
	throw new RuntimeException( 'Der Redirect-Block besitzt keinen stabilen Namen oder Shortcode.' );
}
if ( 'string' !== ( $block['attributes']['redirect']['type'] ?? '' ) ) {
	throw new RuntimeException( 'Das Redirectziel fehlt im Blockvertrag.' );
}
if ( 'Weiterleitungsziel' !== ( $block['fields']['redirect']['label'] ?? '' ) ) {
	throw new RuntimeException( 'Das Redirectfeld besitzt kein verständliches Label.' );
}

echo "OK: flz_shortcode_redirect block registration smoke test\n";
