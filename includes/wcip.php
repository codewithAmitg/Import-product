<?php

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * This class defines wcip logic code for the plugin.
 */

if ( ! class_exists( 'WCIP' ) ) {

	class WCIP {

		private $default_value      = null;


		public function __construct() {

			// add frontend style and scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'wcip_front_enqueue_assets' ) );

		}


		public function wcip_front_enqueue_assets() {
				wp_enqueue_style( 'wcip_menu_style', WCIP_PLUGIN_PATH . 'assets/frontend/css/menu_style.css', '', '1.0' );
				wp_enqueue_script( 'wcip_menu_script', WCIP_PLUGIN_PATH . 'assets/frontend/js/menu_script.js', array( 'jquery' ), '1.0' );
		}

	}

	$wcip            = new WCIP();
	$GLOBALS['WCIP'] = $wcip;
}
