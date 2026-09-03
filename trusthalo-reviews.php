<?php
/**
 * Plugin Name:       TrustHalo Reviews
 * Plugin URI:        https://github.com/delta-chip-holdings/trusthalo-reviews
 * Description:       Display Google reviews in a fast, responsive review slider.
 * Version:           0.3.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Delta Chip Holdings
 * Author URI:        https://github.com/delta-chip-holdings
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       trusthalo-reviews
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'TRUSTHALO_VERSION', '0.3.0' );
define( 'TRUSTHALO_FILE', __FILE__ );
define( 'TRUSTHALO_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRUSTHALO_URL', plugin_dir_url( __FILE__ ) );

require_once TRUSTHALO_PATH . 'includes/class-trusthalo-google-places.php';
require_once TRUSTHALO_PATH . 'includes/class-trusthalo-admin.php';
require_once TRUSTHALO_PATH . 'includes/class-trusthalo-shortcode.php';
require_once TRUSTHALO_PATH . 'includes/class-trusthalo-plugin.php';

register_activation_hook( TRUSTHALO_FILE, array( 'TrustHalo_Plugin', 'activate' ) );

/**
 * Boot TrustHalo after WordPress has loaded all active plugins.
 *
 * @return TrustHalo_Plugin
 */
function trusthalo_reviews() {
	return TrustHalo_Plugin::instance();
}

add_action( 'plugins_loaded', 'trusthalo_reviews' );
