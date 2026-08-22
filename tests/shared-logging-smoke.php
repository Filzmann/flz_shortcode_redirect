<?php

namespace flz_wpdb_objects {
	final class FlzWpdbObjectsException {
		public static function log_error( \Throwable $error, string $component, string $operation ): void {
			$GLOBALS['flz_redirect_shared_log'] = array( $error, $component, $operation );
		}
	}
}

namespace {
	if ( PHP_SAPI !== 'cli' ) {
		http_response_code( 404 );
		exit;
	}

	define( 'ABSPATH', __DIR__ . '/' );
	$GLOBALS['flz_redirect_shared_log'] = array();

	function add_shortcode(): void {}
	function add_action(): void {}

	require dirname( __DIR__ ) . '/flz_shortcode_redirect.php';

	flz_shortcode_redirect_log( 'synthetischer Testfehler' );
	$log = $GLOBALS['flz_redirect_shared_log'];
	if ( ! ( $log[0] ?? null ) instanceof RuntimeException ) {
		throw new RuntimeException( 'Shared-Logging erhielt keine gekapselte Exception.' );
	}
	if ( 'synthetischer Testfehler' !== $log[0]->getMessage() ) {
		throw new RuntimeException( 'Shared-Logging hat die technische Testdiagnose verändert.' );
	}
	if ( 'flz_shortcode_redirect' !== ( $log[1] ?? '' ) || 'Ausführen des Redirect-Shortcodes' !== ( $log[2] ?? '' ) ) {
		throw new RuntimeException( 'Shared-Logging erhielt keinen stabilen Komponentenkontext.' );
	}

	echo "OK: flz_shortcode_redirect shared logging smoke test\n";
}
