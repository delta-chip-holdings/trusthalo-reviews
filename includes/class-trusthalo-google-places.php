<?php
/**
 * TrustHalo Connect service placeholder.
 *
 * This public plugin deliberately does not accept customer Google API keys.
 * Review retrieval will be provided by the separately operated TrustHalo
 * Connect service after Google Business Profile API approval.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

class TrustHalo_Google_Places {
	const OPTION_NAME = 'trusthalo_settings';

	/**
	 * Retrieve non-sensitive connection state.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = get_option( self::OPTION_NAME, array() );

		return wp_parse_args(
			is_array( $settings ) ? $settings : array(),
			array(
				'connection_status' => 'not_connected',
			)
		);
	}

	/**
	 * No direct Google Places calls are made from customer WordPress sites.
	 *
	 * @return WP_Error
	 */
	public function get_reviews() {
		return new WP_Error(
			'trusthalo_connect_unavailable',
			__( 'TrustHalo Connect is not available yet. The review slider will appear after you connect your Google Business Profile.', 'trusthalo-reviews' )
		);
	}
}
