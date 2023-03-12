<?php
/**
 * Helper functions for setting up the plugin's admin settings.
 *
 * @package InstaAdmin
 */

namespace DLXPlugins\InstaAdmin;

/**
 * Class Admin
 */
class Admin {

	/**
	 * Class runner.
	 */
	public function run() {
		add_filter( 'plugin_action_links_' . plugin_basename( Functions::get_plugin_file() ), array( $this, 'add_settings_links' ) );
	}



	/**
	 * Add a settings link to the plugin's options.
	 *
	 * Add a settings link on the WordPress plugin's page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see init
	 *
	 * @param array $links Array of plugin options.
	 * @return array $links Array of plugin options
	 */
	public function add_settings_links( $links ) {
		// Get landing page ID from options.
		$landing_page_id = get_option( 'insta_admin_landing_page_id', 0 );

		// Build out setting links.
		$settings_link          = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=insta-admin-options' ) ), _x( 'Settings', 'Plugin settings link on the plugins page', 'insta-admin-landing-page' ) );
		$edit_landing_page_link = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'post.php?post=' . absint( $landing_page_id ) . '&action=edit' ) ), _x( 'Edit Landing Page', 'Plugin settings link on the plugins page', 'insta-admin-landing-page' ) );
		$view_landing_page_link = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=' . Functions::get_landing_page_slug() ) ), _x( 'View Landing Page', 'Plugin settings link on the plugins page', 'insta-admin-landing-page' ) );

		// Add setting links.
		array_unshift( $links, $view_landing_page_link );
		array_unshift( $links, $edit_landing_page_link );
		array_unshift( $links, $settings_link );
		return $links;
	}
}
