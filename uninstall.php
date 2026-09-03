<?php
/**
 * Remove TrustHalo Free data when WordPress uninstalls the plugin.
 *
 * @package TrustHaloReviews
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'trusthalo_settings' );
