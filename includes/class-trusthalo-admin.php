<?php
/**
 * TrustHalo settings screen.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

class TrustHalo_Admin {
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

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register settings page.
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
	 * Load settings-only styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( 'settings_page_trusthalo-reviews' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'trusthalo-admin', TRUSTHALO_URL . 'assets/css/admin.css', array(), TRUSTHALO_VERSION );
	}

	/**
	 * Render the customer-friendly connection and Free/Pro settings experience.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap trusthalo-admin-wrap">
			<div class="trusthalo-admin-heading">
				<div>
					<h1><?php esc_html_e( 'TrustHalo Reviews', 'trusthalo-reviews' ); ?></h1>
					<p><?php esc_html_e( 'A focused Google reviews slider for WordPress.', 'trusthalo-reviews' ); ?></p>
				</div>
				<span class="trusthalo-version"><?php echo esc_html( 'Free ' . TRUSTHALO_VERSION ); ?></span>
			</div>

			<div class="trusthalo-admin-grid">
				<div class="trusthalo-card">
					<h2><?php esc_html_e( 'Connect Google', 'trusthalo-reviews' ); ?></h2>
					<p><?php esc_html_e( 'TrustHalo will connect to the Google account that manages your Business Profile. You will not need a Google API key, a Place ID, or your own Google Cloud billing account.', 'trusthalo-reviews' ); ?></p>
					<p class="description"><strong><?php esc_html_e( 'Coming soon:', 'trusthalo-reviews' ); ?></strong> <?php esc_html_e( 'TrustHalo Connect is being prepared while Google Business Profile API access is approved. The button will be enabled when the secure connection service is live.', 'trusthalo-reviews' ); ?></p>
					<button class="button button-primary" type="button" disabled aria-disabled="true"><?php esc_html_e( 'Connect Google Business Profile', 'trusthalo-reviews' ); ?></button>
					<p class="description trusthalo-policy-note"><?php esc_html_e( 'Do not add a Google API key or billing account for TrustHalo. The connection will be handled by TrustHalo Connect after launch.', 'trusthalo-reviews' ); ?></p>
				</div>

				<div class="trusthalo-card">
					<h2><?php esc_html_e( 'Display', 'trusthalo-reviews' ); ?></h2>
					<p><?php esc_html_e( 'Your shortcode is ready. It will show reviews after Google is connected:', 'trusthalo-reviews' ); ?></p>
					<code class="trusthalo-shortcode">[trusthalo_reviews]</code>
					<p class="description"><?php esc_html_e( 'No review content is shown until TrustHalo Connect is live and the business owner authorizes the connection.', 'trusthalo-reviews' ); ?></p>
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
