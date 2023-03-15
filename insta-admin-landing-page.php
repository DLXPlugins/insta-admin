<?php // phpcs:ignore

/*
 * Plugin Name: InstaAdmin Landing Page
 * Plugin URI: https://dlxplugins.com/plugins/instant-admin-landing-page/
 * Description: Create an admin landing page using blocks.
 * Author: DLX Plugins
 * Version: 1.0.0
 * Requires at least: 5.1
 * Requires PHP: 7.2
 * Author URI: https://dlxplugins.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: highlight-and-share
 * Contributors: ronalfy
 */

namespace DLXPlugins\InstaAdmin;

define( 'INSTA_ADMIN_DLXVERSION', '1.0.0' );
define( 'INSTA_ADMIN_DLXFILE', __FILE__ );

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/lib/autoload.php' ) ) {
	require_once __DIR__ . '/lib/autoload.php';
}

/**
 * InstaAdmin Landing Page Main Class
 */
class Insta_Admin_DLX {
	/**
	 * InstaAdmin Landing Page instance.
	 *
	 * @var Insta_Admin_DLX $instance Instance of InstaAdmin Landing Page class.
	 */
	private static $instance = null;

	/**
	 * Return an instance of the class
	 *
	 * Return an instance of the InstaAdmin Landing Page Class.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Insta_Admin_DLX class instance.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) { // phpcs:ignore
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * Initialize plugin and load text domain for internationalization
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {
		// i18n initialization.
		load_plugin_textdomain( 'insta-admin-landing-page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load the admin landing page functionality.
		$admin_landing = new Admin_Landing();
		$admin_landing->run();

		// For plugin admin settings.
		$admin = new Admin();
		$admin->run();

		// For color settings.
		$colors = new Colors();
		$colors->run();

		// Check to see if GenerateBlocks is enabled and include compatibility.
		if ( Functions::is_activated( 'generateblocks/plugin.php' ) ) {
			$generate_blocks = new Compatibility\Generate_Blocks();
			$generate_blocks->run();
		}

		// Check to see if Kadence Blocks is enabled and include compatibility.
		if ( Functions::is_activated( 'kadence-blocks/kadence-blocks.php' ) ) {
			$kadence_blocks = new Compatibility\Kadence_Blocks();
			$kadence_blocks->run();
		}

	}
}

add_action( 'plugins_loaded', 'DLXPlugins\InstaAdmin\instaadmindlx_instantiate', 11 );
/**
 * Instantiate the HAS class.
 */
function instaadmindlx_instantiate() {
	Insta_Admin_DLX::get_instance();
}

/**
 * Custom the_content filter to avoid running into conflicts.
 */
global $wp_embed;
add_filter( 'ialp_the_content', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'ialp_the_content', array( $wp_embed, 'autoembed' ), 8 );
add_filter( 'ialp_the_content', 'do_blocks', 9 );
add_filter( 'ialp_the_content', 'wptexturize' );
add_filter( 'ialp_the_content', 'convert_smilies', 20 );
add_filter( 'ialp_the_content', 'wpautop' );
add_filter( 'ialp_the_content', 'shortcode_unautop' );
add_filter( 'ialp_the_content', 'prepend_attachment' );
add_filter( 'ialp_the_content', 'wp_filter_content_tags' );
add_filter( 'ialp_the_content', 'wp_replace_insecure_home_url' );
add_filter( 'ialp_the_content', 'do_shortcode', 11 );
add_filter( 'ialp_the_content', 'capital_P_dangit', 11 );
