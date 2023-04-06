<?php
/**
 * Helper functions for setting up the landing page in the admin.
 *
 * @package InstaAdmin
 */

namespace DLXPlugins\InstaAdmin;

/**
 * Class Admin_Landing
 */
class Admin_Landing {

	/**
	 * Stores the landing page ID so we don't keep trying to retrieve it.
	 *
	 * @var int $landing_page_id Landing page ID.
	 */
	public static $landing_page_id = null;

	/**
	 * Class runner.
	 */
	public function run() {
		// Initialize post types.
		add_action( 'init', array( $this, 'add_post_types' ) );

		// Register landing page sidebar meta options.
		add_action( 'init', array( $this, 'register_meta_boxes' ) );

		// Redirect to landing page if on the post type list table.
		add_action( 'wp', array( $this, 'redirect_to_landing_page_edit_screen' ) );

		// Add top menu for shortcut to admin settings and the landing page.
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );

		// Add the admin page.
		add_action( 'admin_menu', array( $this, 'add_admin_landing_page_menu' ) );

		// Redirect to correct settings page.
		add_action( 'current_screen', array( $this, 'maybe_redirect_to_settings_page' ), 9 );

		// Add the admin page for full-screen.
		add_action( 'current_screen', array( $this, 'maybe_output_dashboard_landing_page' ), 10 );

		// Add landing page admin enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Conditionally load in theme.json for the landing page.
		add_filter( 'after_setup_theme', array( $this, 'conditional_theme_json_init' ), 10, 2 );

		// Add body class to landing page admin.
		add_action( 'admin_body_class', array( $this, 'add_admin_body_class' ) );

		// Add sidebar JS to landing page post type edit screen.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Add the admin bar menu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar object.
	 */
	public function add_admin_bar_menu( $wp_admin_bar ) {
		// Add the main menu item.
		$args = array(
			'id'    => 'insta_admin_landing_page',
			'title' => '<div class="ab-icon"><img style="width: 20px; height: 20px;" src="' . esc_url( Functions::get_plugin_url( 'assets/img/bolt.png' ) ) . '" alt="InstaAdmin Icon" /></div>' . __( 'InstaAdmin', 'insta-admin-landing-page' ),
		);
		$wp_admin_bar->add_node( $args );

		// Get admin panel URL.
		$admin_url = add_query_arg(
			array(
				'action'   => 'ialp_landing_redirect',
				'redirect' => true,
				'nonce'    => wp_create_nonce( 'ialp_admin_landing_redirect_nonce' ),
			),
			admin_url( 'index.php' )
		);

		// Add the settings menu item.
		$args = array(
			'parent' => 'insta_admin_landing_page',
			'id'     => 'insta_admin_landing_page_settings',
			'title'  => __( 'InstaAdmin Settings', 'insta-admin-landing-page' ),
			'href'   => esc_url( admin_url( 'options-general.php?page=insta-admin-settings' ) ),
		);
		$wp_admin_bar->add_node( $args );

		$args = array(
			'parent' => 'insta_admin_landing_page',
			'id'     => 'insta_admin_landing_page_settings_page',
			'title'  => __( 'Edit Admin Page', 'insta-admin-landing-page' ),
			'href'   => esc_url( admin_url( 'post.php?post=' . self::$landing_page_id . '&action=edit' ) ),
		);
		$wp_admin_bar->add_node( $args );

		$args = array(
			'parent' => 'insta_admin_landing_page',
			'id'     => 'insta_admin_landing_page_view_admin_page',
			'title'  => __( 'View Landing Page', 'insta-admin-landing-page' ),
			'href'   => esc_url( $admin_url ),
		);
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Add class to body class in the admin if on the appearances sub tab.
	 *
	 * @param string $classes Space seperated string of body classes.
	 *
	 * @return string $classes.
	 */
	public function add_admin_body_class( $classes ) {
		$current_page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );
		if ( Functions::get_landing_page_slug() === $current_page ) {
			$classes .= ' ialp-admin-landing-page';
		}
		return $classes;
	}

	/**
	 * Add the admin landing page menu.
	 */
	public function add_admin_landing_page_menu() {

		// Get landing page ID.
		$landing_page_id = self::get_landing_page_id();

		// Get fullscreen|regular post meta.
		$fullscreen = (bool) get_post_meta( $landing_page_id, '_ialp_full_screen', true );

		// If full screen, add dashboard page.
		if ( $fullscreen ) {
			add_dashboard_page(
				'',
				'',
				'manage_options',
				Functions::get_landing_page_slug(),
				''
			);
		} else {
			add_options_page(
				Functions::get_landing_page_title(),
				Functions::get_landing_page_menu_title(),
				'manage_options',
				Functions::get_landing_page_slug(),
				array( $this, 'admin_landing_page_menu_callback' )
			);
		}
	}

	/**
	 * Add a insta admin hidden post type.
	 */
	public function add_post_types() {
		register_post_type(
			'insta_admin_landing',
			array(
				'labels'       => array(
					'name'          => __( 'Admin Landing Page', 'insta-admin-landing-page' ),
					'singular_name' => __( 'Admin Landing Page', 'insta-admin-landing-page' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'has_archive'  => false,
				'show_in_menu' => false,
				'show_in_rest' => true,
				'rewrite'      => false,
				'supports'     => array( 'title', 'editor', 'revisions', 'custom-fields' ),
			)
		);

		// Exit if not in admin.
		if ( ! is_admin() ) {
			return;
		}

		// Exit if Ajax or REST.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Don't set the landing page on multiple iterations of this hook.
		if ( null !== self::$landing_page_id ) {
			return;
		}

		self::$landing_page_id = self::get_landing_page_id();
	}

	/**
	 * Callback for the admin landing page menu.
	 */
	public function admin_landing_page_menu_callback() {
		self::get_landing_page_body();
	}

	/**
	 * Modify the path of the stylesheet directory for theme.json.
	 */
	public function conditional_theme_json_init() {
		add_filter( 'stylesheet_directory', array( static::class, 'load_plugin_theme_json' ), 1000, 1 );

		// Only if you need a parent > child theme.json.
		add_filter( 'template_directory', array( static::class, 'load_plugin_theme_json' ), 1000, 1 );

		// Clean the theme.json cache if on the landing page settings screen or in the landing page editor.
		$can_clear_theme_json_cache = false;
		if ( is_admin() ) {
			// Check for get variable on landing screen.
			$current_page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );
			if ( Functions::get_landing_page_slug() === $current_page ) {
				$can_clear_theme_json_cache = true;
			} else {
				$maybe_current_post = absint( filter_input( INPUT_GET, 'post', FILTER_DEFAULT ) );
				$landing_page_id    = absint( get_option( 'insta_admin_landing_page_id', 0 ) );
				if ( $maybe_current_post === $landing_page_id ) {
					$can_clear_theme_json_cache = true;
				}
			}
		}
		if ( $can_clear_theme_json_cache ) {
			add_theme_support( 'wp-block-styles' );
			add_editor_style( 'style.css' );
			\WP_Theme_JSON_Resolver::clean_cached_data();
		}
	}

	/**
	 * Enqueue the admin scripts and theme.json styles.
	 *
	 * @param string $hook Page hook in the admin.
	 */
	public function enqueue_admin_scripts( $hook ) {
		$is_settings_page           = 'settings_page_' . Functions::get_landing_page_slug() === $hook;
		$is_settings_dashboard_page = false;
		if ( empty( $hook ) ) {
			// Get current page.
			$current_page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );
			if ( Functions::get_landing_page_slug() === $current_page ) {
				$is_settings_dashboard_page = true;
			}
		}
		// Only enqueue on the landing page.
		if ( ! $is_settings_page && ! $is_settings_dashboard_page ) {
			return;
		}

		// Enqueue WordPress block styles.
		wp_enqueue_style(
			'block-library',
			includes_url( 'css/dist/block-library/style.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);

		// Clear cached data to retrieve updated theme.json file.
		\WP_Theme_JSON_Resolver::clean_cached_data();

		// Enqueue theme.json assets.
		$tree       = \WP_Theme_JSON_Resolver::get_merged_data();
		$stylesheet = $tree->get_stylesheet( array( 'variables', 'presets', 'base-layout-styles', 'styles' ) );
		wp_register_style( 'global-styles-css-custom-properties', false, array(), true, true );
		wp_add_inline_style( 'global-styles-css-custom-properties', $stylesheet );
		wp_enqueue_style( 'global-styles-css-custom-properties' );

		// enqueue frontend block assets (or try to).
		do_action( 'enqueue_block_assets' );
		do_action( 'enqueue_block_editor_assets' );

		// Enqueue custom landing scripts.
		wp_enqueue_style(
			'insta-admin-landing-page',
			Functions::get_plugin_url( '/dist/insta-admin-landing-page.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);

		// Enqueue web fonts.
		wp_enqueue_style(
			'insta-admin-font-ubuntu',
			Functions::get_plugin_url( '/dist/insta-gfont-ubuntu.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);
		wp_enqueue_style(
			'insta-admin-font-lato',
			Functions::get_plugin_url( '/dist/insta-gfont-lato.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);

		/**
		 * Fires when the admin landing page enqueue scripts.
		 *
		 * @param string $hook Page hook in the admin.
		 */
		do_action( 'ialp_enqueue_scripts', $hook );
	}

	/**
	 * Enqueue the sidebar scripts needed on the landing page post edit screen.
	 */
	public function enqueue_block_editor_assets() {
		// Ensure we're in the correct post type.
		$screen = get_current_screen();
		if ( 'insta_admin_landing' !== $screen->post_type ) {
			return;
		}

		// Enqueue the sidebar JS.
		wp_enqueue_script(
			'insta-admin-landing-page-sidebar',
			Functions::get_plugin_url( '/build/landing-page-block-sidebar.js' ),
			array(),
			Functions::get_plugin_version(),
			true
		);

		// Add localized vars.
		wp_localize_script(
			'insta-admin-landing-page-sidebar',
			'instaAdminLandingPageSidebar',
			array(
				'colorPalette'   => Colors::get_color_palette(),
				'landingPageUrl' => esc_url( admin_url( 'options-general.php?page=' . Functions::get_landing_page_slug() ) ),
			)
		);

		// Enqueue editor styles.
		wp_enqueue_style(
			'insta-admin-landing-page-block-editor',
			Functions::get_plugin_url( '/dist/insta-admin-block-editor.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);
		// Enqueue web fonts.
		wp_enqueue_style(
			'insta-admin-font-ubuntu',
			Functions::get_plugin_url( '/dist/insta-gfont-ubuntu.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);
		wp_enqueue_style(
			'insta-admin-font-lato',
			Functions::get_plugin_url( '/dist/insta-gfont-lato.css' ),
			array(),
			Functions::get_plugin_version(),
			'all'
		);
		// Clear cached data to retrieve updated theme.json file.
		\WP_Theme_JSON_Resolver::clean_cached_data();
	}

	/**
	 * Get the landing page ID.
	 *
	 * @return int
	 */
	public static function get_landing_page_id() {
		if ( ! is_null( self::$landing_page_id ) ) {
			return self::$landing_page_id;
		}

		// Get the landing page.
		$landing_page = get_posts(
			array(
				'post_type'      => 'insta_admin_landing',
				'posts_per_page' => 1,
				'post_status'    => array( 'publish', 'private' ),
			)
		);

		if ( ! empty( $landing_page ) ) {
			self::$landing_page_id = $landing_page[0]->ID;
		} else {
			// Clear existing landing pages that might be in the trash.
			$landing_pages = get_posts(
				array(
					'post_type'      => 'insta_admin_landing',
					'posts_per_page' => 1,
					'post_status'    => 'trash',
				)
			);
			if ( ! empty( $landing_pages ) ) {
				foreach ( $landing_pages as $landing_page ) {
					wp_delete_post( $landing_page->ID, true );
				}
			}

			// Now create a page for the admin landing page.
			$post_args = array(
				'post_type'    => 'insta_admin_landing',
				'post_title'   => __( 'Plugin Information Page', 'insta-admin-landing-page' ),
				'post_status'  => 'private',
				'post_content' => '',
			);

			// If no landing page, create one.
			$landing_page_id = wp_insert_post( $post_args );
			update_option( 'insta_admin_landing_page_id', $landing_page_id );
			self::$landing_page_id = $landing_page_id;
		}

		return self::$landing_page_id;
	}

	/**
	 * Retrieve the landing page admin URL.
	 */
	public static function get_landing_page_admin_url() {
		// Get landing page ID.
		$landing_page_id = self::get_landing_page_id();

		// Get fullscreen|regular post meta.
		$fullscreen = (bool) get_post_meta( $landing_page_id, '_ialp_full_screen', true );

		// If full screen, get dashboard URL.
		if ( $fullscreen ) {
			$admin_url = admin_url( 'index.php?page=' . Functions::get_landing_page_slug() );
		} else {
			// Not full screen, get regular admin URL.
			$admin_url = admin_url( 'options-general.php?page=' . Functions::get_landing_page_slug() );
		}
		return $admin_url;
	}

	/**
	 * Output the landing page body. Works for both fullscreen and regular view.
	 */
	public static function get_landing_page_body() {
		// Get the landing page.
		$landing_page_id = self::get_landing_page_id();

		// Get the landing page and the page content.
		$landing_page = get_post( $landing_page_id );

		// Check landing page.
		if ( ! $landing_page ) {
			// Landing page not found.
			echo '<div class="wrap">';
			echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';
			echo '<p>' . esc_html__( 'The landing page for the admin area has not been created yet.', 'insta-admin-landing-page' ) . '</p>';
			echo '</div>';
			return;
		}

		/**
		 * Fires before the landing page HTML is output. Useful for enqueuing scripts/styles for both fullscreen and regular view.
		 */
		do_action( 'ialp_pre_landing_page_html' );

		$use_wp_content_filter = apply_filters( 'ialp_use_wp_content_filter', true );
		if ( ! $use_wp_content_filter ) {
			$landing_page_content = apply_filters( 'ialp_the_content', $landing_page->post_content );
		} else {
			$landing_page_content = apply_filters( 'the_content', $landing_page->post_content );
		}

		?>
		<div id="insta-admin-landing-page" class="insta-admin-landing-page-wrap">
			<div class="insta-admin-landing-page-content">
				<?php
				// todo - needs kses update.
				echo $landing_page_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
		<?php
		/**
		 * Fires after the landing page HTML is output. Useful for enqueuing scripts/styles for both fullscreen and regular view.
		 */
		do_action( 'ialp_after_landing_page_html' );
	}

	/**
	 * Retrieve the fullscreen landing page footer HTML.
	 */
	public static function get_landing_page_footer() {
		do_action( 'admin_footer' );
		do_action( 'admin_print_footer_scripts' );
		?>
		</body>
		</html>
		<?php
	}

	/**
	 * Retrieve the fullscreen landing page header HTML.
	 *
	 * Credit: SliceWP Setup Wizard.
	 */
	public static function get_landing_page_header() {
		// Get the current screen for script loading.
		$current_screen = get_current_screen();

		// Get locale.
		global $wp_locale;
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<?php
		// Get the admin landing page title.
		$landing_page_title = get_the_title( self::get_landing_page_id() );

		/**
		 * Filter the admin landing page title.
		 *
		 * @param string $landing_page_title The admin landing page title.
		 */
		$landing_page_title = apply_filters( 'ialp_landing_page_title', $landing_page_title );
		?>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php echo esc_html( $landing_page_title ); ?></title>
			<?php
			/**
			 * Admin script vars. From wp-admin/admin-header.php.
			 */
			?>
			<script type="text/javascript">
			addLoadEvent = function(func){if(typeof jQuery!=='undefined')jQuery(function(){func();});else if(typeof wpOnload!=='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
			var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', 'relative' ) ); ?>',
				pagenow = '<?php echo esc_js( $current_screen->id ); ?>',
				typenow = '<?php echo esc_js( $current_screen->post_type ); ?>',
				adminpage = '<?php echo esc_js( 'ialp-admin' ); ?>',
				thousandsSeparator = '<?php echo esc_js( $wp_locale->number_format['thousands_sep'] ); ?>',
				decimalPoint = '<?php echo esc_js( $wp_locale->number_format['decimal_point'] ); ?>',
				isRtl = <?php echo (int) is_rtl(); ?>;
			</script>
			<?php wp_enqueue_style( 'colors' ); ?>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
		</head>
		<?php
		/**
		 * Filter the admin body classes.
		 *
		 * @param array The admin body classes.
		 */
		$admin_body_classes = apply_filters(
			'ialp_admin_body_class',
			array(
				'ialp-admin',
				'ialp-admin-landing-page',
				'ialp-admin-landing-is-fullscreen',
			)
		);
		?>
		<body class="<?php echo esc_attr( implode( ' ', $admin_body_classes ) ); ?>">
		<?php
	}


	/**
	 * Set the stylesheet directory to our theme.json file path.
	 *
	 * @param string $stylesheet_dir The stylesheet directory.
	 *
	 * @return string The plugin's theme.json directory.
	 */
	public static function load_plugin_theme_json( $stylesheet_dir ) {
		global $current_screen;
		if ( null !== $current_screen ) {

			$insta_admin_slug = Functions::get_landing_page_slug();

			if ( 'insta_admin_landing' === $current_screen->post_type || 'settings_page_' . $insta_admin_slug === $current_screen->id || Functions::get_landing_page_slug() === $current_screen->id ) {
				$stylesheet_dir = Functions::get_plugin_dir( '/assets/json/' );
			}
		}
		return $stylesheet_dir;
	}

	/**
	 * Redirect to the correct page if the admin settings URL is invalid.
	 */
	public function maybe_redirect_to_settings_page() {
		// Get the current action, if any.
		$action = filter_input( INPUT_GET, 'action', FILTER_DEFAULT );
		if ( 'ialp_landing_redirect' !== $action ) {
			return;
		}

		// Get redirect flag.
		$redirect = filter_input( INPUT_GET, 'redirect', FILTER_VALIDATE_BOOLEAN );

		// Action matches, let's check nonce and permissions.
		$nonce = filter_input( INPUT_GET, 'nonce', FILTER_DEFAULT );
		if ( ! wp_verify_nonce( $nonce, 'ialp_admin_landing_redirect_nonce' ) || ! current_user_can( 'manage_options' ) || ! $redirect ) {
			return;
		}

		// All is well, redirect to admin landing page.
		wp_safe_redirect(
			esc_url(
				self::get_landing_page_admin_url()
			)
		);
		exit;
	}

	/**
	 * Output a dashboard full-screen admin if landing page is fullscreen.
	 */
	public function maybe_output_dashboard_landing_page() {
		// Exit if not in admin.
		if ( ! is_admin() ) {
			return;
		}

		// Get landing page slug.
		$landing_page_slug = Functions::get_landing_page_slug();

		// Get current screen base.
		$current_screen      = get_current_screen();
		$current_screen_base = $current_screen->base;

		// Get admin page.
		$admin_page = filter_input( INPUT_GET, 'page', FILTER_DEFAULT );
		if ( $landing_page_slug !== $admin_page || $current_screen_base !== $landing_page_slug || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get landing page ID.
		$landing_page_id = self::get_landing_page_id();

		// Get fullscreen|regular post meta.
		$fullscreen = (bool) get_post_meta( $landing_page_id, '_ialp_full_screen', true );

		// If not fullscreen, exit.
		if ( ! $fullscreen ) {
			return;
		}

		// Get landing page dashboard header.
		self::get_landing_page_header();

		// Get landing page body.
		self::get_landing_page_body();

		// Get landing page dashboard footer.
		self::get_landing_page_footer();

		// Exit silently.
		exit;
	}

	/**
	 * Redirect user from post type edit screen to landing page edit screen.
	 */
	public function redirect_to_landing_page_edit_screen() {
		// Exit early if not in admin screen.
		if ( ! is_admin() ) {
			return;
		}

		// If current screen is edit.php and post_type is insta_admin_landing.
		$current_screen = get_current_screen();
		if ( 'edit' === $current_screen->base && 'insta_admin_landing' === $current_screen->post_type ) {
			// Skip if WP_DEBUG is on and user is an admin.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) {
				return;
			}

			// Redirect to the landing page edit screen.
			$landing_page_id = self::get_landing_page_id();
			if ( $landing_page_id ) {
				$can_redirect = wp_safe_redirect(
					esc_url_raw( admin_url( 'post.php?post=' . $landing_page_id . '&action=edit' ) ),
					302,
					'InstaAdmin'
				);
				if ( $can_redirect ) {
					exit;
				}
			}
		}
	}

	/**
	 * Register Meta Boxes for the landing page sidebar.
	 */
	public function register_meta_boxes() {
		/**
		 * Meta for a fullscreen or traditional admin.
		 */
		register_post_meta(
			'insta_admin_landing',
			'_ialp_full_screen',
			array(
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'type'              => 'boolean',
				'auth_callback'     => function () {
					return current_user_can( 'manage_options' );
				},
				'single'            => true,
				'default'           => true,
			)
		);

		/**
		 * Meta for setting a admin landing page slug.
		 */
		register_post_meta(
			'insta_admin_landing',
			'_ialp_slug',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'type'              => 'string',
				'auth_callback'     => function () {
					return current_user_can( 'manage_options' );
				},
				'single'            => true,
				'default'           => 'insta-admin',
			)
		);

		/**
		 * Meta for setting the menu title.
		 */
		register_post_meta(
			'insta_admin_landing',
			'_ialp_menu_title',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'type'              => 'string',
				'auth_callback'     => function () {
					return current_user_can( 'manage_options' );
				},
				'single'            => true,
				'default'           => __( 'Site Features', 'insta-admin-landing-page' ),
			)
		);

		/**
		 * Meta for setting the background color of the admin landing page.
		 */
		register_post_meta(
			'insta_admin_landing',
			'_ialp_background_color',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'type'              => 'string',
				'auth_callback'     => function () {
					return current_user_can( 'manage_options' );
				},
				'single'            => true,
				'default'           => '#f0f0f1',
			)
		);
	}
}
