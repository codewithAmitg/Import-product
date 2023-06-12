<?php
/**
 * Plugin Name: WC Import Product
 * Description: Import Product using JSON File
 * Version: 1.6.0
 * Tags: import product
 * Author: Amitgupta
 * Author URI: #
 * Developer: Amitgupta
 * Developer URI: #
 * Text Domain: wcip
 *
 * WC requires at least: 3.0
 * WC tested up to: 7.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// $debug_tags = array();
// add_action( 'all', function ( $tag ) {
// global $debug_tags;
// if ( in_array( $tag, $debug_tags ) ) {
// return;
// }
// echo "<pre>" . $tag . "</pre>";
// $debug_tags[] = $tag;
// } );

/**
 * Define plugin constants
 */
define( 'WCIP_VERSION', '1.0.0' );
define( 'WCIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WCIP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Activation and Deactivation hooks
 */
register_activation_hook( __FILE__, 'wcip_activation' );
register_deactivation_hook( __FILE__, 'wcip_deactivation' );

function wcip_activation() {
	require_once WCIP_PLUGIN_PATH . 'includes/wcip-activator.php';
	WCIP_Activator::activate();
}

function wcip_deactivation() {
	require_once WCIP_PLUGIN_PATH . 'includes/wcip-deactivator.php';
	WCIP_Deactivator::deactivate();
}

/**
 * The plugin core
 */
require WCIP_PLUGIN_PATH . 'includes/wcip-core.php';

add_action( 'plugins_loaded', 'wcip_load_textdomain' );
function wcip_load_textdomain() {
	//$locale = apply_filters( 'plugin_locale', get_locale(), 'my-plugin' );
	load_plugin_textdomain( 'wcip', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

// Instantiate plugin
$wcip_core = WCIP_Core::getInstance();
