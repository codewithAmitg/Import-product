<?php

/**
 * This class defines all core code for the plugin.
 */

if (!class_exists('WCIP_Core')) {
	
	class WCIP_Core {
		
		public static $instance = false;
		
		public static function getInstance() {
			if (!self::$instance) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}

		private function __construct() {
			add_action('plugins_loaded', array($this, 'plugin_dependencies'));
		}
		
		public function plugin_dependencies() {
			/**
			 * Filter
			 * 
			 * @since 1.0.0
			 */
			if ( function_exists('WC') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				$this->includes();
			} else {
				// show notice if WooCommerce plugin is not active
				add_action('admin_notices', array($this, 'wcip_inactive_plugin_notice'));
			}
		}

		/**
		 * Add Plugin Include Files
		 */
		private function includes() {
			include_once(WCIP_PLUGIN_PATH . '/includes/wcip.php');
			include_once(WCIP_PLUGIN_PATH . '/includes/wcip-settings.php');
		}
		
		public function wcip_inactive_plugin_notice() {
			?>
			<div id="message" class="error"><p><?php printf(esc_html(__('WC Import Product requires WooCommerce to be installed and active!', 'wccs'))); ?></p></div>
			<?php
		}

	}
	
}
