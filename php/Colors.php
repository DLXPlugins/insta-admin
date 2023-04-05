<?php
/**
 * Helper functions for setting up the plugin's color pallette.
 *
 * @package InstaAdmin
 */

namespace DLXPlugins\InstaAdmin;

/**
 * Class Colors
 */
class Colors {

	/**
	 * Class runner.
	 */
	public function run() {

		// Remove default palette.
		// add_action( 'after_setup_theme', array( $this, 'remove_default_palette' ), 101 );

		// Add our own palette.
		add_action( 'admin_init', array( $this, 'add_color_palette' ), 100 );

	}

	/**
	 * Remove existing colors.
	 */
	public function remove_default_palette() {
		if ( Functions::is_landing_page() || Functions::is_landing_page_editor() ) {
			remove_theme_support( 'disable-custom-colors' );
		}
	}

	/**
	 * Retrieve a color palette for the admin landing page and block editor.
	 *
	 * @param bool $skip_filters true to return unfiltered results, false to apply filters.
	 *
	 * @return array {
	 *  The color palette.
	 *  @type string $name The name of the color.
	 *  @type string $slug The slug of the color.
	 *  @type string $color The color.
	 * }
	 */
	public static function get_color_palette( $skip_filters = false ) {
		// Retrieve user's admin color scheme.
		$admin_color_scheme = get_user_option( 'admin_color' );

		$color_palette = array();
		if ( $admin_color_scheme ) {
			$admin_colors = $GLOBALS['_wp_admin_css_colors'][ $admin_color_scheme ]->colors ?? array();

			// Expecting 4 colors.
			if ( 4 === count( $admin_colors ) ) {
				$color_palette[] = array(
					'name'  => 'Primary Admin',
					'slug'  => 'admin-primary',
					'color' => $admin_colors[0],
				);
				$color_palette[] = array(
					'name'  => 'Secondary Admin',
					'slug'  => 'admin-secondary',
					'color' => $admin_colors[1],
				);
				$color_palette[] = array(
					'name'  => 'Tertiary Admin',
					'slug'  => 'admin-tertiary',
					'color' => $admin_colors[2],
				);
				$color_palette[] = array(
					'name'  => 'Quaternary Admin',
					'slug'  => 'admin-quaternary',
					'color' => $admin_colors[3],
				);
			}
		}

		// Add in background and foreground colors.
		$color_palette[] = array(
			'name'  => 'Background',
			'slug'  => 'background',
			'color' => '#ebebeb',
		);
		$color_palette[] = array(
			'name'  => 'Foreground',
			'slug'  => 'foreground',
			'color' => '#333',
		);

		// Add in the remainer of colors.
		$color_palette[] = array(
			'name'  => 'Honolulu Blue',
			'slug'  => 'honolulu-blue',
			'color' => '#0073AA',
		);
		$color_palette[] = array(
			'name'  => 'Steel Blue',
			'slug'  => 'steel-blue',
			'color' => '#2C82C9',
		);
		$color_palette[] = array(
			'name'  => 'Alice Blue',
			'slug'  => 'alice-blue',
			'color' => '#E5F6FF',
		);
		$color_palette[] = array(
			'name'  => 'Jet Black',
			'slug'  => 'jet-black',
			'color' => '#333333',
		);
		$color_palette[] = array(
			'name'  => 'White Smoke',
			'slug'  => 'white-smoke',
			'color' => '#F3F3F3',
		);
		$color_palette[] = array(
			'name'  => 'Pantone Orange',
			'slug'  => 'pantone-orange',
			'color' => '#F26622',
		);
		$color_palette[] = array(
			'name'  => 'Forest Green',
			'slug'  => 'forest-green',
			'color' => '#009933',
		);
		$color_palette[] = array(
			'name'  => 'Red Orange',
			'slug'  => 'red-orange',
			'color' => '#D54E21',
		);
		$color_palette[] = array(
			'name'  => 'Black',
			'slug'  => 'black',
			'color' => '#000000',
		);
		$color_palette[] = array(
			'name'  => 'White',
			'slug'  => 'white',
			'color' => '#FFFFFF',
		);

		// Return filtered or unfiltered.
		if ( ! $skip_filters ) {
			/**
			 * Filter the color palette array.
			 *
			 * @param array $color_palette {
			 *      The color palette.
			 *      @type string $name The name of the color.
			 *      @type string $slug The slug of the color.
			 *      @type string $color The color.
			 * }
			 */
			$color_palette = apply_filters( 'ialp_block_color_palette', $color_palette );
		}

		return $color_palette;
	}

	/**
	 * Add the color scheme palette for the block editor.
	 */
	public function add_color_palette() {
		if ( ! Functions::is_landing_page() && ! Functions::is_landing_page_editor() ) {
			return;
		}

		// Retrieve color palette.
		$color_palette = self::get_color_palette();

		// Add theme support for the colors.
		add_theme_support( 'editor-color-palette', $color_palette );

		// Now let's enqueue it.
		wp_register_style( 'ialp-block-color-palette', false );
		wp_enqueue_style( 'ialp-block-color-palette' );
		$css = array();
		foreach ( $color_palette as $color ) {
			$css[] = '.has-' . $color['slug'] . '-color { color: ' . $color['color'] . '; }';
			$css[] = '.has-' . $color['slug'] . '-background-color { background-color: ' . $color['color'] . '; }';
		}

		/**
		 * Filter the color palette CSS.
		 *
		 * @param array $css The CSS.
		 * @param array $color_palette {
		 *      The color palette.
		 *      @type string $name The name of the color.
		 *      @type string $slug The slug of the color.
		 *      @type string $color The color.
		 * }
		 */
		$css = apply_filters( 'ialp_block_color_palette_css', $css, $color_palette );

		// Add inline style.
		wp_add_inline_style( 'ialp-block-color-palette', sanitize_text_field( implode( ' ', $css ) ) );
	}
}
