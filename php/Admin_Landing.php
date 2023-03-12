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

		// Add the settings menu item.
		$args = array(
			'parent' => 'insta_admin_landing_page',
			'id'     => 'insta_admin_landing_page_settings',
			'title'  => __( 'InstaAdmin Settings', 'insta-admin-landing-page' ),
			'href'   => esc_url( admin_url( 'options-general.php?page=' . Functions::get_landing_page_slug() ) ),
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
			'href'   => esc_url( admin_url( 'options-general.php?page=' . Functions::get_landing_page_slug() ) ),
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
			$classes .= ' is-insta-admin-landing-page';
		}
		return $classes;
	}

	/**
	 * Add the admin landing page menu.
	 */
	public function add_admin_landing_page_menu() {

		add_options_page(
			Functions::get_landing_page_title(),
			Functions::get_landing_page_menu_title(),
			'manage_options',
			Functions::get_landing_page_slug(),
			array( $this, 'admin_landing_page_menu_callback' )
		);
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

		self::$landing_page_id = $this->get_landing_page_id();
	}

	/**
	 * Callback for the admin landing page menu.
	 */
	public function admin_landing_page_menu_callback() {
		// Get the landing page.
		$landing_page_id = $this->get_landing_page_id();

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

		// Output the landing page.
		$landing_page_content = apply_filters( 'the_content', $landing_page->post_content );
		?>
		<div id="insta-admin-landing-page" class="insta-admin-landing-page-wrap">
			<div class="insta-admin-landing-page-content">
				<?php
				// todo - needs kses update.
				echo wp_kses_post( $landing_page_content );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Modify the path of the stylesheet directory for theme.json.
	 */
	public function conditional_theme_json_init() {
		add_filter( 'stylesheet_directory', array( static::class, 'load_plugin_theme_json' ), 10, 1 );

		// Only if you need a parent > child theme.json.
		add_filter( 'template_directory', array( static::class, 'load_plugin_theme_json' ), 10, 1 );

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
			\WP_Theme_JSON_Resolver::clean_cached_data();
		}
	}

	/**
	 * Enqueue the admin scripts and theme.json styles.
	 *
	 * @param string $hook Page hook in the admin.
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Only enqueue on the landing page.
		if ( 'settings_page_' . Functions::get_landing_page_slug() !== $hook ) {
			return;
		}

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

		/**
		 * Fires when the admin landing page enqueue scripts.
		 *
		 * @param string $hook Page hook in the admin.
		 */
		do_action( 'insta_admin_landing_page_enqueue_scripts', $hook );
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
	}

	/**
	 * Get the landing page ID.
	 *
	 * @return int
	 */
	public function get_landing_page_id() {
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
	 * Set the stylesheet directory to our theme.json file path.
	 *
	 * @param string $stylesheet_dir The stylesheet directory.
	 *
	 * @return string The plugin's theme.json directory.
	 */
	public static function load_plugin_theme_json( $stylesheet_dir ) {
		global $current_screen;
		if ( null !== $current_screen ) {

			if ( 'insta_admin_landing' === $current_screen->post_type || 'settings_page_insta-admin' === $current_screen->id ) {
				$stylesheet_dir = Functions::get_plugin_dir( '/assets/json/' );
			}
		}
		return $stylesheet_dir;
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
			$landing_page_id = $this->get_landing_page_id();
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
		 * Meta for enabling full screen view..
		 */
		// register_post_meta(
		// 	'page',
		// 	'_bl_nav_type', /* can be: main, alt */
		// 	array(
		// 		'sanitize_callback' => 'sanitize_text_field',
		// 		'show_in_rest'      => true,
		// 		'type'              => 'string',
		// 		'auth_callback'     => function () {
		// 			return current_user_can( 'edit_posts' );
		// 		},
		// 		'single'            => true,
		// 		'default'           => 'main',
		// 	)
		// );
	}
}
