<?php
/**
 * Front-end shortcode renderer.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

class TrustHalo_Shortcode {
	/**
	 * Connection service.
	 *
	 * @var TrustHalo_Google_Places
	 */
	private $google_places;

	/**
	 * Constructor.
	 *
	 * @param TrustHalo_Google_Places $google_places Connection service.
	 */
	public function __construct( TrustHalo_Google_Places $google_places ) {
		$this->google_places = $google_places;
		add_shortcode( 'trusthalo_reviews', array( $this, 'render' ) );
	}

	/**
	 * Render the fixed Free slider when TrustHalo Connect has supplied reviews.
	 *
	 * @return string
	 */
	public function render() {
		$data = $this->google_places->get_reviews();

		if ( is_wp_error( $data ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				return sprintf(
					'<div class="trusthalo-setup-notice">%1$s <a href="%2$s">%3$s</a></div>',
					esc_html( $data->get_error_message() ),
					esc_url( admin_url( 'options-general.php?page=trusthalo-reviews' ) ),
					esc_html__( 'Open TrustHalo settings', 'trusthalo-reviews' )
				);
			}

			return '';
		}

		return '';
	}
}
