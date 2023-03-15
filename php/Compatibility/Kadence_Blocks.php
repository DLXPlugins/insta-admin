<?php
/**
 * Methods for helping with KadenceBlocks compatibility.
 *
 * @package InstaAdmin
 */

namespace DLXPlugins\InstaAdmin\Compatibility;

use DLXPlugins\InstaAdmin\Admin_Landing as Admin_Landing;
use DLXPlugins\InstaAdmin\Functions as Functions;

/**
 * Kadence Blocks class.
 */
class Kadence_Blocks {

	/**
	 * Placeholder to hold the current screen object.
	 *
	 * @var null|WP_Screen $current_screen The current screen object.
	 */
	private static $current_screen = null;

	/**
	 * Class runner/init.
	 */
	public function run() {
		// Trick WP into thinking it's not an admin page (if we're on the landing page in the admin that is).
		add_action( 'current_screen', array( $this, 'maybe_disable_is_admin' ), 100 );

		add_action( 'ialp_after_landing_page_html', array( $this, 'restore_admin_screen' ) );

		// Trick kadence into thinking wp_head has been run.
		add_action( 'admin_init', array( $this, 'set_wp_head_did_action' ) );

		// Trick kadence into loading its scripts.
		add_filter( 'ialp_use_wp_content_filter', '__return_false' );

		// Prevent Kadence from registering admin scripts. Run only on the landing page.
		$page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );
		if ( Functions::get_landing_page_slug() === $page ) {
			remove_action( 'init', 'kadence_gutenberg_editor_assets', 10 );
		}
	}

	/**
	 * Add the current_screen object back in. This is for tools like Query monitor that need to run in admin.
	 */
	public function restore_admin_screen() {
		if ( self::$current_screen ) {
			$GLOBALS['current_screen'] = self::$current_screen;
		}
	}

	/**
	 * Set wp_head as run in actions.
	 */
	public function set_wp_head_did_action() {
		// Get the current page.
		$page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );

		if ( Functions::get_landing_page_slug() === $page ) {
			global $wp_actions;
			$wp_actions['wp_head'] = 1; // this works with kadence.
		}
	}

	/**
	 * Disable the WP Admin flag if on the landing page.
	 *
	 * This is a bit hacky, but it's the only way to get Kadence to load its scripts.
	 */
	public function maybe_disable_is_admin() {
		// Get the current page.
		$page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );

		if ( Functions::get_landing_page_slug() === $page ) {
			// Create new current screen object.
			self::$current_screen      = $GLOBALS['current_screen'];
			$GLOBALS['current_screen'] = \WP_Screen::get( 'front' );
		}
	}
}
