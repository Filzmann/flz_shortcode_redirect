<?php
/*
Plugin Name: FLZ Shortcode Redirect
Description: Kontrollierte Weiterleitung mit kurzlebigen, signierten Zugangslinks.
Version: 2.0.0
Author: Tagore-Gymnasium
License: GPLv2 or later
Requires at least: 6.5
Requires PHP: 8.1
Text Domain: flz-shortcode-redirect
*/

defined( 'ABSPATH' ) || exit;

const FLZ_SHORTCODE_REDIRECT_VERSION = '2.0.0';
const FLZ_SHORTCODE_REDIRECT_DEFAULT_TTL = 900;
const FLZ_SHORTCODE_REDIRECT_MAX_TTL = 3600;

/**
 * Erzeugt die Signatur für eine konkrete WordPress-Seite und Ablaufzeit.
 */
function flz_shortcode_redirect_signature( int $post_id, int $expires ): string {
	if ( $post_id <= 0 || $expires <= 0 ) {
		throw new InvalidArgumentException( 'Seiten-ID und Ablaufzeit müssen größer als null sein.' );
	}

	return hash_hmac(
		'sha256',
		'v1|' . $post_id . '|' . $expires,
		wp_salt( 'auth' )
	);
}

/**
 * Erzeugt serverseitig einen kurzlebigen Zugangslink.
 */
function flz_shortcode_redirect_create_signed_url(
	string $page_url,
	int $post_id,
	int $ttl = FLZ_SHORTCODE_REDIRECT_DEFAULT_TTL,
	?int $now = null
): string {
	$page_url = esc_url_raw( $page_url );
	if ( '' === $page_url || $post_id <= 0 ) {
		throw new InvalidArgumentException( 'Für einen Zugangslink werden eine gültige Seiten-URL und Seiten-ID benötigt.' );
	}
	if ( $ttl <= 0 || $ttl > FLZ_SHORTCODE_REDIRECT_MAX_TTL ) {
		throw new InvalidArgumentException( 'Die Gültigkeit eines Zugangslinks muss zwischen 1 und 3600 Sekunden liegen.' );
	}

	$expires = ( $now ?? time() ) + $ttl;

	return add_query_arg(
		array(
			'flz_redirect_expires' => $expires,
			'flz_redirect_signature' => flz_shortcode_redirect_signature( $post_id, $expires ),
		),
		$page_url
	);
}

/**
 * Prüft eine an die aktuelle Seite gebundene, kurzlebige Signatur.
 */
function flz_shortcode_redirect_request_is_authorized( int $post_id, ?int $now = null ): bool {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Kurzlebige HMAC-Signatur ist der öffentliche Lesezugriffsvertrag.
	$expires_raw = isset( $_GET['flz_redirect_expires'] )
		? sanitize_text_field( wp_unslash( $_GET['flz_redirect_expires'] ) )
		: '';
	$provided_signature = isset( $_GET['flz_redirect_signature'] )
		? sanitize_text_field( wp_unslash( $_GET['flz_redirect_signature'] ) )
		: '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	if ( $post_id <= 0 || ! ctype_digit( $expires_raw ) || ! preg_match( '/^[a-f0-9]{64}$/', $provided_signature ) ) {
		return false;
	}

	$now = $now ?? time();
	$expires = (int) $expires_raw;
	if ( $expires <= $now || $expires > $now + FLZ_SHORTCODE_REDIRECT_MAX_TTL ) {
		return false;
	}

	return hash_equals(
		flz_shortcode_redirect_signature( $post_id, $expires ),
		$provided_signature
	);
}

/**
 * Prüft Ziele über WordPress' Host-/Protokollvertrag.
 */
function flz_shortcode_redirect_target_is_allowed( string $redirect_url ): bool {
	return '' !== $redirect_url && '' !== wp_validate_redirect( $redirect_url, '' );
}

/**
 * Protokolliert Redirect-Probleme mit Shared-Logging, falls verfügbar.
 */
function flz_shortcode_redirect_log( string $message ): void {
	$error = new RuntimeException( $message );

	if ( class_exists( 'flz_wpdb_objects\\FlzWpdbObjectsException' ) ) {
		flz_wpdb_objects\FlzWpdbObjectsException::log_error(
			$error,
			'flz_shortcode_redirect',
			'Ausführen des Redirect-Shortcodes'
		);
		return;
	}

	error_log( '[flz_shortcode_redirect] ' . $message );
}

/**
 * Leitet nicht angemeldete Besucher ohne gültige Signatur sicher weiter.
 *
 * Der historische Shortcode-Name [redirect] bleibt aus Kompatibilitätsgründen
 * bestehen; nur die PHP-Funktion folgt jetzt der flz_-Namenskonvention.
 */
function flz_shortcode_redirect( $atts ): string {
	$atts = shortcode_atts(
		array(
			'secret' => '',
			'redirect' => '',
		),
		(array) $atts,
		'redirect'
	);

	if ( is_user_logged_in() ) {
		return '';
	}

	if ( flz_shortcode_redirect_request_is_authorized( get_queried_object_id() ) ) {
		return '';
	}
	if ( '' !== (string) $atts['secret'] ) {
		flz_shortcode_redirect_log( 'Ein veraltetes Klartext-Secret wurde ignoriert; erforderlich ist ein signierter Zugangslink.' );
	}

	$redirect_url = esc_url_raw( (string) $atts['redirect'] );
	if ( $redirect_url === '' ) {
		flz_shortcode_redirect_log( 'Weiterleitung abgebrochen: Im Shortcode fehlt eine gültige Ziel-URL.' );
		return '<p>' . esc_html__( 'Die Weiterleitung ist nicht konfiguriert.', 'flz-shortcode-redirect' ) . '</p>';
	}
	if ( ! flz_shortcode_redirect_target_is_allowed( $redirect_url ) ) {
		flz_shortcode_redirect_log( 'Weiterleitung abgebrochen: WordPress hat Zielprotokoll oder Zielhost nicht erlaubt.' );
		return '<p>' . esc_html__( 'Die Weiterleitung konnte nicht ausgeführt werden.', 'flz-shortcode-redirect' ) . '</p>';
	}

	if ( headers_sent( $source_file, $source_line ) ) {
		flz_shortcode_redirect_log(
			'Weiterleitung nicht mehr möglich: HTTP-Header wurden bereits in '
			. (string) $source_file . ':' . (string) $source_line . ' gesendet.'
		);
		return '<p><a href="' . esc_url( $redirect_url ) . '">'
			. esc_html__( 'Zur Zielseite', 'flz-shortcode-redirect' )
			. '</a></p>';
	}

	if ( ! wp_safe_redirect( $redirect_url ) ) {
		flz_shortcode_redirect_log( 'wp_safe_redirect() hat die Ziel-URL abgelehnt.' );
		return '<p>' . esc_html__( 'Die Weiterleitung konnte nicht ausgeführt werden.', 'flz-shortcode-redirect' ) . '</p>';
	}
	exit;
}

/**
 * Bietet den vorhandenen Shortcode optional als redaktionellen Block an.
 */
function flz_shortcode_redirect_register_block(): void {
	if ( ! function_exists( 'flz_ui_register_shortcode_block' ) ) {
		return;
	}

	flz_ui_register_shortcode_block(
		array(
			'name' => 'flz/shortcode-redirect',
			'shortcode' => 'redirect',
			'title' => __( 'FLZ geschützte Seite', 'flz-shortcode-redirect' ),
			'description' => __( 'Leitet Gäste ohne gültigen, kurzlebigen Zugangslink weiter.', 'flz-shortcode-redirect' ),
			'attributes' => array(
				'redirect' => array(
					'type' => 'string',
					'default' => '',
				),
			),
			'fields' => array(
				'redirect' => array(
					'label' => __( 'Weiterleitungsziel', 'flz-shortcode-redirect' ),
					'description' => __( 'Interne oder von WordPress erlaubte Ziel-URL.', 'flz-shortcode-redirect' ),
				),
			),
		)
	);
}

add_shortcode( 'redirect', 'flz_shortcode_redirect' );
add_action( 'init', 'flz_shortcode_redirect_register_block', 20 );
