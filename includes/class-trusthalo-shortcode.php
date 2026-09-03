<?php
/**
 * Front-end shortcode renderer.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

class TrustHalo_Shortcode {
	/**
	 * Google Places service.
	 *
	 * @var TrustHalo_Google_Places
	 */
	private $google_places;

	/**
	 * Number of sliders rendered in this request.
	 *
	 * @var int
	 */
	private static $instance_count = 0;

	/**
	 * Constructor.
	 *
	 * @param TrustHalo_Google_Places $google_places Google Places service.
	 */
	public function __construct( TrustHalo_Google_Places $google_places ) {
		$this->google_places = $google_places;
		add_shortcode( 'trusthalo_reviews', array( $this, 'render' ) );
	}

	/**
	 * Render the fixed Free slider.
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

		if ( empty( $data['reviews'] ) ) {
			return current_user_can( 'manage_options' )
				? '<div class="trusthalo-setup-notice">' . esc_html__( 'Google returned no public reviews for this place.', 'trusthalo-reviews' ) . '</div>'
				: '';
		}

		self::$instance_count++;
		$slider_id = 'trusthalo-slider-' . self::$instance_count;

		$this->enqueue_assets();

		ob_start();
		?>
		<section id="<?php echo esc_attr( $slider_id ); ?>" class="trusthalo-reviews" aria-label="<?php echo esc_attr( sprintf( __( 'Google reviews for %s', 'trusthalo-reviews' ), $data['business_name'] ) ); ?>">
			<div class="trusthalo-summary">
				<div>
					<p class="trusthalo-eyebrow"><span class="trusthalo-google-maps" translate="no">Google Maps</span> <?php esc_html_e( 'Reviews', 'trusthalo-reviews' ); ?></p>
					<h2 class="trusthalo-business-name"><?php echo esc_html( $data['business_name'] ); ?></h2>
				</div>
				<div class="trusthalo-rating-summary">
					<strong><?php echo esc_html( number_format_i18n( $data['rating'], 1 ) ); ?></strong>
					<span class="trusthalo-stars" aria-label="<?php echo esc_attr( sprintf( __( '%s out of 5 stars', 'trusthalo-reviews' ), number_format_i18n( $data['rating'], 1 ) ) ); ?>"><?php echo esc_html( $this->get_star_characters( $data['rating'] ) ); ?></span>
					<span><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', $data['review_count'], 'trusthalo-reviews' ), number_format_i18n( $data['review_count'] ) ) ); ?></span>
				</div>
			</div>

			<div class="trusthalo-slider" data-autoplay="5000">
				<div class="trusthalo-viewport">
					<div class="trusthalo-track">
						<?php foreach ( $data['reviews'] as $review ) : ?>
							<article class="trusthalo-review-card">
								<div class="trusthalo-review-author">
									<?php if ( $review['author_photo'] ) : ?>
										<img src="<?php echo esc_url( $review['author_photo'] ); ?>" alt="" width="48" height="48" loading="lazy" referrerpolicy="no-referrer">
									<?php else : ?>
										<span class="trusthalo-avatar" aria-hidden="true"><?php echo esc_html( $this->get_initial( $review['author_name'] ) ); ?></span>
									<?php endif; ?>
									<div>
										<?php if ( $review['author_uri'] ) : ?>
											<a class="trusthalo-author-link" href="<?php echo esc_url( $review['author_uri'] ); ?>" target="_blank" rel="noopener nofollow"><strong><?php echo esc_html( $review['author_name'] ); ?></strong></a>
										<?php else : ?>
											<strong><?php echo esc_html( $review['author_name'] ); ?></strong>
										<?php endif; ?>
										<?php if ( $review['relative_time'] ) : ?><span><?php echo esc_html( $review['relative_time'] ); ?></span><?php endif; ?>
										<?php if ( $review['visit_date'] ) : ?><span><?php echo esc_html( sprintf( __( 'Visited %s', 'trusthalo-reviews' ), $review['visit_date'] ) ); ?></span><?php endif; ?>
									</div>
								</div>
								<div class="trusthalo-review-stars" aria-label="<?php echo esc_attr( sprintf( __( '%s out of 5 stars', 'trusthalo-reviews' ), number_format_i18n( $review['rating'], 1 ) ) ); ?>"><?php echo esc_html( $this->get_star_characters( $review['rating'] ) ); ?></div>
								<div class="trusthalo-review-text"><?php echo wp_kses_post( wpautop( esc_html( $review['text'] ) ) ); ?></div>
								<?php if ( $review['text_language'] && $review['original_language'] && $review['text_language'] !== $review['original_language'] ) : ?>
									<p class="trusthalo-translation-note"><?php esc_html_e( 'Translated review. The original is available on Google Maps.', 'trusthalo-reviews' ); ?></p>
								<?php endif; ?>
								<?php if ( $review['review_uri'] ) : ?>
									<a class="trusthalo-review-link" href="<?php echo esc_url( $review['review_uri'] ); ?>" target="_blank" rel="noopener nofollow"><?php esc_html_e( 'View review on Google Maps', 'trusthalo-reviews' ); ?></a>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="trusthalo-controls">
					<button class="trusthalo-arrow trusthalo-prev" type="button" aria-label="<?php esc_attr_e( 'Previous reviews', 'trusthalo-reviews' ); ?>">‹</button>
					<div class="trusthalo-dots" aria-label="<?php esc_attr_e( 'Review pages', 'trusthalo-reviews' ); ?>"></div>
					<button class="trusthalo-arrow trusthalo-next" type="button" aria-label="<?php esc_attr_e( 'Next reviews', 'trusthalo-reviews' ); ?>">›</button>
				</div>
			</div>
			<p class="trusthalo-order-note"><?php esc_html_e( 'Reviews are selected and ordered by relevance by Google Maps.', 'trusthalo-reviews' ); ?></p>

			<?php if ( $data['maps_uri'] ) : ?>
				<a class="trusthalo-google-link" href="<?php echo esc_url( $data['maps_uri'] ); ?>" target="_blank" rel="noopener nofollow"><?php esc_html_e( 'See this business on Google Maps', 'trusthalo-reviews' ); ?></a>
			<?php endif; ?>
			<?php if ( ! empty( $data['attributions'] ) ) : ?>
				<p class="trusthalo-provider-attributions">
					<?php esc_html_e( 'Additional data:', 'trusthalo-reviews' ); ?>
					<?php foreach ( $data['attributions'] as $index => $attribution ) : ?>
						<?php if ( $index > 0 ) : ?>, <?php endif; ?>
						<?php if ( $attribution['uri'] ) : ?>
							<a href="<?php echo esc_url( $attribution['uri'] ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( $attribution['provider'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $attribution['provider'] ); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
		</section>
		<?php

		return ob_get_clean();
	}

	/**
	 * Load front-end assets only when the shortcode renders.
	 *
	 * @return void
	 */
	private function enqueue_assets() {
		wp_enqueue_style(
			'trusthalo-reviews',
			TRUSTHALO_URL . 'assets/css/frontend.css',
			array(),
			TRUSTHALO_VERSION
		);
		wp_enqueue_script(
			'trusthalo-reviews',
			TRUSTHALO_URL . 'assets/js/frontend.js',
			array(),
			TRUSTHALO_VERSION,
			true
		);
	}

	/**
	 * Get a safe one-character avatar fallback.
	 *
	 * @param string $name Reviewer name.
	 * @return string
	 */
	private function get_initial( $name ) {
		if ( function_exists( 'mb_substr' ) ) {
			return mb_strtoupper( mb_substr( $name, 0, 1 ) );
		}

		return strtoupper( substr( $name, 0, 1 ) );
	}

	/**
	 * Build a five-character visual star rating.
	 *
	 * The adjacent accessible label and numeric summary retain the exact rating.
	 *
	 * @param float $rating Rating from zero to five.
	 * @return string
	 */
	private function get_star_characters( $rating ) {
		$filled = min( 5, max( 0, (int) round( (float) $rating ) ) );

		return str_repeat( '★', $filled ) . str_repeat( '☆', 5 - $filled );
	}
}
