<?php
/**
 * Main plugin coordinator.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

final class TrustHalo_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var TrustHalo_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Google connection service.
	 *
	 * @var TrustHalo_Google_Places
	 */
	private $google_places;

	/**
	 * Get the plugin instance.
	 *
	 * @return TrustHalo_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set safe defaults without overwriting existing configuration.
	 *
	 * @return void
	 */
	public static function activate() {
		if ( false === get_option( TrustHalo_Google_Places::OPTION_NAME, false ) ) {
			add_option(
				TrustHalo_Google_Places::OPTION_NAME,
				array(
					'connection_status' => 'not_connected',
				),
				'',
				false
			);
		}
	}

	/**
	 * Initialize public and admin features.
	 */
	private function __construct() {
		$this->google_places = new TrustHalo_Google_Places();

		new TrustHalo_Shortcode( $this->google_places );

		if ( is_admin() ) {
			new TrustHalo_Admin( $this->google_places );
		}

		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'trusthalo-reviews',
			false,
			dirname( plugin_basename( TRUSTHALO_FILE ) ) . '/languages'
		);
	}
}
