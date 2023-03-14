<?php
/**
 * Methods for helping with GenerateBlocks compatibility.
 *
 * @package InstaAdmin
 */

namespace DLXPlugins\InstaAdmin\Compatibility;

use DLXPlugins\InstaAdmin\Admin_Landing as Admin_Landing;

/**
 * GenerateBlocks class.
 */
class Generate_Blocks {
	/**
	 * Class runner/init.
	 */
	public function run() {
		add_filter( 'generateblocks_do_content', array( $this, 'generateblocks_do_content' ) );

		add_action( 'ialp_pre_landing_page_html', array( $this, 'print_generateblocks_styles' ) );
	}

	/**
	 * Add landing page content to GenerateBlocks content.
	 *
	 * @param string $content The content.
	 * @return string
	 */
	public function generateblocks_do_content( $content ) {
		$landing_page_id = Admin_Landing::get_landing_page_id();
		if ( $landing_page_id ) {
			$landing_page = get_post( $landing_page_id );
			if ( $landing_page && has_blocks( $landing_page ) ) {
				$content .= $landing_page->post_content;
			}
		}
		return $content;
	}

	/**
	 * Output GenerateBlocks styles in the landing page.
	 */
	public function print_generateblocks_styles() {

		// Change mode from file to inline for this request.
		add_filter(
			'generateblocks_style_mode',
			function( $mode ) {
				return 'inline';
			}
		);

		// Build the CSS. Code is from GenerateBlocks plugin.
		if ( function_exists( 'generateblocks_get_dynamic_css' ) ) {
			generateblocks_get_dynamic_css();
			$css = generateblocks_get_frontend_block_css();
			if ( empty( $css ) ) {
				return;
			}

			// Add a "dummy" handle we can add inline styles to. Note: Custom handle to avoid conflicts.
			wp_register_style( 'generateblockscss', false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion

			wp_add_inline_style(
				'generateblockscss',
				wp_strip_all_tags( $css ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			// Manually print styles.
			wp_print_styles( array( 'generateblockscss' ) );
		}
	}
}
