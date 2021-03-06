<?php
/**
 * WooCommerce Navigation
 * NOTE: DO NOT edit this file in WooCommerce core, this is generated from woocommerce-admin.
 *
 * @package Woocommerce Admin
 */

namespace Automattic\WooCommerce\Admin\Features;

/**
 * Contains logic for the WooCommerce Navigation.
 */
class Navigation {
	/**
	 * Class instance.
	 *
	 * @var Navigation instance
	 */
	protected static $instance = null;

	/**
	 * Array index of menu capability.
	 *
	 * @var int
	 */
	const CAPABILITY = 1;

	/**
	 * Array index of menu callback.
	 *
	 * @var int
	 */
	const CALLBACK = 2;

	/**
	 * Array index of menu callback.
	 *
	 * @var int
	 */
	const SLUG = 3;

	/**
	 * Array index of menu CSS class string.
	 *
	 * @var int
	 */
	const CSS_CLASSES = 4;

	/**
	 * Store top level categories.
	 *
	 * @var array
	 */
	protected static $categories = array();

	/**
	 * Store related menu items.
	 *
	 * @var array
	 */
	protected static $menu_items = array();

	/**
	 * Screen IDs of registered pages.
	 *
	 * @var array
	 */
	protected static $screen_ids = array();

	/**
	 * Registered post types.
	 *
	 * @var array
	 */
	protected static $post_types = array();

	/**
	 * Registered callbacks or URLs with migration boolean as key value pairs.
	 *
	 * @var array
	 */
	protected static $callbacks = array();

	/**
	 * Check if we're on a WooCommerce page
	 *
	 * @return bool
	 */
	public function is_woocommerce_page() {
		global $pagenow, $plugin_page;

		// Get post type if on a post screen.
		$post_type = '';
		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) {
			if ( isset( $_GET['post'] ) ) { // phpcs:ignore CSRF ok.
				$post_type = get_post_type( (int) $_GET['post'] ); // phpcs:ignore CSRF ok.
			} elseif ( isset( $_GET['post_type'] ) ) { // phpcs:ignore CSRF ok.
				$post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore CSRF ok.
			}
		}
		$post_types = apply_filters( 'woocommerce_navigation_post_types', self::$post_types );

		// Get current screen ID.
		$current_screen = get_current_screen();
		$screen_ids     = apply_filters( 'woocommerce_navigation_screen_ids', self::$screen_ids );

		if (
			in_array( $post_type, $post_types, true ) ||
			in_array( $current_screen->id, self::$screen_ids, true )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( is_admin() && get_option( 'woocommerce_navigation_enabled', false ) ) {
			add_action( 'admin_menu', array( $this, 'add_core_menu_items' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_settings' ), 20 );
			add_filter( 'add_menu_classes', array( $this, 'migrate_menu_items' ) );
			add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		}
	}

	/**
	 * Add navigation classes to body.
	 *
	 * @param string $classes Classes.
	 * @return string
	 */
	public function add_body_class( $classes ) {
		if ( self::is_woocommerce_page() ) {
			$classes .= ' has-woocommerce-navigation';
		}

		return $classes;
	}

	/**
	 * Add registered admin settings.
	 */
	public function add_admin_settings() {
		$setting_pages = \WC_Admin_Settings::get_settings_pages();
		$settings      = array();
		foreach ( $setting_pages as $setting_page ) {
			$settings = $setting_page->add_settings_page( $settings );
		}
		foreach ( $settings as $key => $setting ) {
			self::add_menu_item(
				'settings',
				$setting,
				'manage_woocommerce',
				$key,
				'admin.php?page=wc-status&tab=' . $key
			);
		}
	}

	/**
	 * Add the core menu items to the new navigation
	 */
	public function add_core_menu_items() {
		// Orders category.
		self::add_menu_category(
			__( 'Orders', 'woocommerce' ),
			'edit_shop_orders',
			'orders',
			'edit.php?post_type=shop_order'
		);

		// Products category.
		self::add_menu_category(
			__( 'Products', 'woocommerce' ),
			'edit_products',
			'products',
			'edit.php?post_type=product'
		);

		// Extensions category.
		self::add_menu_category(
			__( 'Extensions', 'woocommerce' ),
			'activate_plugins',
			'extensions',
			'plugins.php',
			null,
			null,
			false
		);
		self::add_menu_item(
			'extensions',
			__( 'My extensions', 'woocommerce' ),
			'manage_woocommerce',
			'my-extensions',
			'plugins.php',
			null,
			null,
			false
		);
		self::add_menu_item(
			'extensions',
			__( 'Marketplace', 'woocommerce' ),
			'manage_woocommerce',
			'marketplace',
			'wc-addons'
		);

		// Settings category.
		self::add_menu_category(
			__( 'Settings', 'woocommerce' ),
			'manage_woocommerce',
			'settings',
			'wc-settings'
		);

		// Tools category.
		self::add_menu_category(
			__( 'Tools', 'woocommerce' ),
			'manage_woocommerce',
			'tools',
			'wc-status'
		);
		self::add_menu_item(
			'tools',
			__( 'System status', 'woocommerce' ),
			'manage_woocommerce',
			'system-status',
			'wc-status'
		);
		self::add_menu_item(
			'tools',
			__( 'Import / Export', 'woocommerce' ),
			'import',
			'import-export',
			'import.php',
			null,
			null,
			false
		);
		self::add_menu_item(
			'tools',
			__( 'Utilities', 'woocommerce' ),
			'manage_woocommerce',
			'utilities',
			'admin.php?page=wc-status&tab=tools'
		);

		// User profile.
		self::add_menu_category(
			wp_get_current_user()->user_login,
			'read',
			'profile',
			'profile.php',
			null,
			null,
			false
		);
	}

	/**
	 * Convert a WordPress menu callback to a URL.
	 *
	 * @param string $callback Menu callback.
	 * @return string
	 */
	public static function get_callback_url( $callback ) {
		$pos  = strpos( $callback, '?' );
		$file = $pos > 0 ? substr( $callback, 0, $pos ) : $callback;
		if ( file_exists( ABSPATH . "/wp-admin/$file" ) ) {
			return $callback;
		}
		return 'admin.php?page=' . $callback;
	}

	/**
	 * Adds a top level menu item to the navigation.
	 *
	 * @param string $title Menu title.
	 * @param string $capability WordPress capability.
	 * @param string $slug Menu slug.
	 * @param string $url URL or menu callback.
	 * @param string $icon Menu icon.
	 * @param int    $order Menu order.
	 * @param bool   $migrate Migrate the menu option and hide the old one.
	 */
	public static function add_menu_category( $title, $capability, $slug, $url = null, $icon = null, $order = null, $migrate = true ) {
		self::$categories[] = array(
			'title'      => $title,
			'capability' => $capability,
			'slug'       => $slug,
			'url'        => self::get_callback_url( $url ),
			'icon'       => $icon,
			'order'      => $order,
			'migrate'    => $migrate,
		);

		self::$callbacks[ $url ] = $migrate;
	}

	/**
	 * Adds a child menu item to the navigation.
	 *
	 * @param string $parent_slug Parent item slug.
	 * @param string $title Menu title.
	 * @param string $capability WordPress capability.
	 * @param string $slug Menu slug.
	 * @param string $url URL or menu callback.
	 * @param string $icon Menu icon.
	 * @param int    $order Menu order.
	 * @param bool   $migrate Migrate the menu option and hide the old one.
	 */
	public static function add_menu_item( $parent_slug, $title, $capability, $slug, $url = null, $icon = null, $order = null, $migrate = true ) {
		self::$menu_items[ $parent_slug ][] = array(
			'title'      => $title,
			'capability' => $capability,
			'slug'       => $slug,
			'url'        => self::get_callback_url( $url ),
			'icon'       => $icon,
			'order'      => $order,
			'migrate'    => $migrate,
		);

		self::$callbacks[ $url ] = $migrate;
	}

	/**
	 * Get the parent menu item if one exists.
	 *
	 * @param string $url URL or callback.
	 * @return string|null
	 */
	public static function get_parent_menu_item( $url ) {
		global $submenu;

		// This is already a parent item.
		if ( isset( $submenu[ $url ] ) ) {
			return null;
		}

		foreach ( $submenu as $key => $menu ) {
			foreach ( $menu as $item ) {
				if ( $item[ self::CALLBACK ] === $url ) {
					return $key;
				}
			}
		}

		return null;
	}

	/**
	 * Hides all WP admin menus items and adds screen IDs to check for new items.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public static function migrate_menu_items( $menu ) {
		global $submenu;

		foreach ( $menu as $key => $menu_item ) {
			if (
				isset( self::$callbacks[ $menu_item[ self::CALLBACK ] ] ) &&
				self::$callbacks[ $menu_item[ self::CALLBACK ] ]
			) {
				$menu[ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
			}
		}

		foreach ( $submenu as $parent_key => $parent ) {
			foreach ( $parent as $key => $menu_item ) {
				if (
					isset( self::$callbacks[ $menu_item[ self::CALLBACK ] ] ) &&
					self::$callbacks[ $menu_item[ self::CALLBACK ] ]
				) {
					// Disable phpcs since we need to override submenu classes.
					// Note that `phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited` does not work to disable this check.
					// phpcs:disable
					if ( ! isset( $menu_item[ self::SLUG ] ) ) {
						$submenu[ $parent_key ][ $key ][] = '';
					}
					if ( ! isset( $menu_item[ self::CSS_CLASSES ] ) ) {
						$submenu[ $parent_key ][ $key ][] .= ' hide-if-js';
					} else {
						$submenu[ $parent_key ][ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
					}
					// phps:enable
				}
			}
		}

		foreach ( array_keys( self::$callbacks ) as $callback ) {
			self::add_screen_id( $callback );
		}

		return $menu;
	}

	/**
	 * Adds a screen ID to the list and automatically finds the parent if none is given.
	 *
	 * @param string      $url URL or callback for page.
	 * @param string|null $parent Parent slug.
	 */
	public static function add_screen_id( $url, $parent = null ) {
		global $submenu;

		if ( ! $parent ) {
			$parent = self::get_parent_menu_item( $url );
		}
		self::$screen_ids[] = get_plugin_page_hookname( $url, $parent );
	}

	/**
	 * Add the menu to the page output.
	 */
	public function add_menu_settings() {
		global $submenu, $parent_file, $typenow, $self;

		$categories = self::$categories;
		foreach ( $categories as $index => $category ) {
			if ( $category[ 'capability' ] && ! current_user_can( $category[ 'capability' ] ) ) {
				unset( $categories[ $index ] );
				continue;
			}

			$categories[ $index ]['children'] = array();
			if( isset( self::$menu_items[ $category['slug'] ] ) ) {
				foreach ( self::$menu_items[ $category['slug'] ] as $item ) {
					if ( $item[ 'capability' ] && ! current_user_can( $item[ 'capability' ] ) ) {
						continue;
					}

					$categories[ $index ]['children'][] = $item;
				}
			}
		}

		$data_registry = \Automattic\WooCommerce\Blocks\Package::container()->get(
			\Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class
		);

		$data_registry->add( 'wcNavigation', $categories );
	}
}
