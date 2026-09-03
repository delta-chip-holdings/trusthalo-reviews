<?php
/**
 * TrustHalo settings screen.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

class TrustHalo_Admin {
	/**
	 * Google Places service.
	 *
	 * @var TrustHalo_Google_Places
	 */
	private $google_places;

	/**
	 * Constructor.
	 *
	 * @param TrustHalo_Google_Places $google_places Google Places service.
	 */
	public function __construct( TrustHalo_Google_Places $google_places ) {
		$this->google_places = $google_places;

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_trusthalo_refresh_reviews', array( $this, 'refresh_reviews' ) );
	}

	/**
	 * Register the settings page.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'TrustHalo Reviews', 'trusthalo-reviews' ),
			__( 'TrustHalo Reviews', 'trusthalo-reviews' ),
			'manage_options',
			'trusthalo-reviews',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register one compact settings option.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'trusthalo_reviews',
			TrustHalo_Google_Places::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'api_key'  => '',
					'place_id' => '',
				),
			)
		);
	}

	/**
	 * Load settings-only styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( 'settings_page_trusthalo-reviews' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'trusthalo-admin',
			TRUSTHALO_URL . 'assets/css/admin.css',
			array(),
			TRUSTHALO_VERSION
		);
	}

	/**
	 * Sanitize saved credentials.
	 *
	 * @param mixed $input Submitted settings.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$old   = $this->google_places->get_settings();
		$input = is_array( $input ) ? $input : array();
		$key   = isset( $input['api_key'] ) ? sanitize_text_field( wp_unslash( $input['api_key'] ) ) : '';
		$place = isset( $input['place_id'] ) ? sanitize_text_field( wp_unslash( $input['place_id'] ) ) : '';

		$new = array(
			'api_key'  => '' !== $key ? $key : $old['api_key'],
			'place_id' => trim( $place ),
		);

		return $new;
	}

	/**
	 * Test the server-side API connection.
	 *
	 * @return void
	 */
	public function refresh_reviews() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage TrustHalo.', 'trusthalo-reviews' ) );
		}

		check_admin_referer( 'trusthalo_refresh_reviews' );

		$result = $this->google_places->get_reviews( true );
		$args   = array( 'page' => 'trusthalo-reviews' );

		if ( is_wp_error( $result ) ) {
			$args['trusthalo_status'] = 'error';
			$args['trusthalo_message'] = $result->get_error_message();
		} else {
			$args['trusthalo_status'] = 'success';
			$args['trusthalo_message'] = sprintf(
				/* translators: 1: business name, 2: number of reviews returned by Google. */
				__( 'Connected to %1$s. Google returned %2$d review(s).', 'trusthalo-reviews' ),
				$result['business_name'],
				count( $result['reviews'] )
			);
		}

		wp_safe_redirect( add_query_arg( $args, admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * Render the complete Free settings experience and visible Pro locks.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = $this->google_places->get_settings();
		$status   = isset( $_GET['trusthalo_status'] ) ? sanitize_key( wp_unslash( $_GET['trusthalo_status'] ) ) : '';
		$message  = isset( $_GET['trusthalo_message'] ) ? sanitize_text_field( wp_unslash( $_GET['trusthalo_message'] ) ) : '';
		?>
		<div class="wrap trusthalo-admin-wrap">
			<div class="trusthalo-admin-heading">
				<div>
					<h1><?php esc_html_e( 'TrustHalo Reviews', 'trusthalo-reviews' ); ?></h1>
					<p><?php esc_html_e( 'A focused Google reviews slider for WordPress.', 'trusthalo-reviews' ); ?></p>
				</div>
				<span class="trusthalo-version"><?php echo esc_html( 'Free ' . TRUSTHALO_VERSION ); ?></span>
			</div>

			<?php if ( $message ) : ?>
				<div class="notice notice-<?php echo 'success' === $status ? 'success' : 'error'; ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
			<?php endif; ?>

			<div class="trusthalo-admin-grid">
				<div class="trusthalo-card">
					<h2><?php esc_html_e( 'Connect one Google business', 'trusthalo-reviews' ); ?></h2>
					<p class="description"><?php esc_html_e( 'The API key is used only by your WordPress server and is never printed in the slider markup.', 'trusthalo-reviews' ); ?></p>

					<form action="options.php" method="post">
						<?php settings_fields( 'trusthalo_reviews' ); ?>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><label for="trusthalo-api-key"><?php esc_html_e( 'Places API key', 'trusthalo-reviews' ); ?></label></th>
								<td>
									<input id="trusthalo-api-key" class="regular-text" type="password" name="trusthalo_settings[api_key]" value="" autocomplete="new-password" placeholder="<?php echo $settings['api_key'] ? esc_attr__( 'Configured — leave blank to keep it', 'trusthalo-reviews' ) : ''; ?>">
									<p class="description"><?php esc_html_e( 'Enable Places API (New), enable billing, and restrict the key where practical.', 'trusthalo-reviews' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="trusthalo-place-id"><?php esc_html_e( 'Google Place ID', 'trusthalo-reviews' ); ?></label></th>
								<td><input id="trusthalo-place-id" class="regular-text" type="text" name="trusthalo_settings[place_id]" value="<?php echo esc_attr( $settings['place_id'] ); ?>" required></td>
							</tr>
						</table>
						<?php submit_button( __( 'Save connection', 'trusthalo-reviews' ) ); ?>
					</form>

					<?php if ( $settings['api_key'] && $settings['place_id'] ) : ?>
						<form class="trusthalo-refresh-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
							<input type="hidden" name="action" value="trusthalo_refresh_reviews">
							<?php wp_nonce_field( 'trusthalo_refresh_reviews' ); ?>
							<?php submit_button( __( 'Test Google connection', 'trusthalo-reviews' ), 'secondary', 'submit', false ); ?>
						</form>
					<?php endif; ?>

					<p class="description trusthalo-policy-note"><?php esc_html_e( 'Google Maps content is fetched live and is not stored between page requests. Your website must also provide public Terms of Use and a Privacy Policy that meet Google Maps Platform requirements.', 'trusthalo-reviews' ); ?></p>
				</div>

				<div class="trusthalo-card">
					<h2><?php esc_html_e( 'Display', 'trusthalo-reviews' ); ?></h2>
					<p><?php esc_html_e( 'Paste this shortcode into any page, post, or compatible builder:', 'trusthalo-reviews' ); ?></p>
					<code class="trusthalo-shortcode">[trusthalo_reviews]</code>
					<p class="description"><?php esc_html_e( 'The connection runs server-side when the shortcode is displayed. Review content is not stored by TrustHalo.', 'trusthalo-reviews' ); ?></p>
				</div>

				<div class="trusthalo-card trusthalo-layout-card">
					<div class="trusthalo-card-title-row">
						<h2><?php esc_html_e( 'Layout & style', 'trusthalo-reviews' ); ?></h2>
						<span class="trusthalo-plan-badge"><?php esc_html_e( 'FREE', 'trusthalo-reviews' ); ?></span>
					</div>
					<?php $this->render_feature_row( __( 'Layout', 'trusthalo-reviews' ), __( 'Slider', 'trusthalo-reviews' ), false ); ?>
					<?php $this->render_feature_row( __( 'Responsive columns', 'trusthalo-reviews' ), __( '3 desktop / 2 tablet / 1 mobile', 'trusthalo-reviews' ), true ); ?>
					<?php $this->render_feature_row( __( 'Autoplay', 'trusthalo-reviews' ), __( 'On', 'trusthalo-reviews' ), true ); ?>
					<?php $this->render_feature_row( __( 'Navigation arrows', 'trusthalo-reviews' ), __( 'Shown', 'trusthalo-reviews' ), true ); ?>
					<?php $this->render_feature_row( __( 'Pagination dots', 'trusthalo-reviews' ), __( 'Shown', 'trusthalo-reviews' ), true ); ?>
					<?php $this->render_feature_row( __( 'Style preset', 'trusthalo-reviews' ), __( 'Clean light', 'trusthalo-reviews' ), true ); ?>
				</div>

				<div class="trusthalo-card trusthalo-pro-card">
					<span class="trusthalo-plan-badge trusthalo-plan-badge-pro"><?php esc_html_e( 'PRO — PLANNED', 'trusthalo-reviews' ); ?></span>
					<h2><?php esc_html_e( 'More control when you need it', 'trusthalo-reviews' ); ?></h2>
					<ul>
						<li><?php esc_html_e( 'Grid, masonry, badge, and carousel layouts', 'trusthalo-reviews' ); ?></li>
						<li><?php esc_html_e( 'Full styling and responsive column controls', 'trusthalo-reviews' ); ?></li>
						<li><?php esc_html_e( 'Autoplay, arrows, dots, and timing controls', 'trusthalo-reviews' ); ?></li>
						<li><?php esc_html_e( 'Multiple Google business locations', 'trusthalo-reviews' ); ?></li>
						<li><?php esc_html_e( 'Elementor widget and owner-connected reviews', 'trusthalo-reviews' ); ?></li>
					</ul>
					<p class="description"><?php esc_html_e( 'Pro will use domain-bound licensing with signed server verification, an offline grace period, and access to protected updates and support.', 'trusthalo-reviews' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render one fixed or Pro-locked feature row.
	 *
	 * @param string $label Feature label.
	 * @param string $value Current Free value.
	 * @param bool   $locked Whether the control is Pro-locked.
	 * @return void
	 */
	private function render_feature_row( $label, $value, $locked ) {
		?>
		<div class="trusthalo-feature-row <?php echo $locked ? 'is-locked' : ''; ?>">
			<span class="trusthalo-feature-label"><?php echo esc_html( $label ); ?></span>
			<span class="trusthalo-feature-value"><?php echo esc_html( $value ); ?></span>
			<?php if ( $locked ) : ?>
				<span class="trusthalo-lock" aria-label="<?php esc_attr_e( 'Available in TrustHalo Pro', 'trusthalo-reviews' ); ?>">PRO</span>
			<?php endif; ?>
		</div>
		<?php
	}
}
