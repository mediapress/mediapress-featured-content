<?php
/**
 * Plugin Name: MediaPress Featured Content
 * Description: An addon for MediaPress with BuddyPress to mark users or group media or gallery as featured and list them using shortcode or widget across the site
 * Plugin URI: https://buddydev.com/plugins/mediapress-featured-content
 * Author: BuddyDev
 * Author URI: https://buddydev.com/
 * Version: 1.0.0
 */

/**
 * Contributor: @raviousprime
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPP_Featured_Content
 */
class MPP_Featured_Content {

	/**
	 * Class instance
	 *
	 * @var MPP_Featured_Content
	 */
	private static $instance = null;

	/**
	 * Plugin directory path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugin directory url
	 *
	 * @var string
	 */
	private $url;

	/**
	 * The constructor.
	 */
	private function __construct() {

		$this->path = plugin_dir_path( __FILE__ );
		$this->url  = plugin_dir_url( __FILE__ );

		$this->setup();
	}

	/**
	 * Get class instance
	 *
	 * @return MPP_Featured_Content
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup callbacks to mediapress hooks
	 */
	public function setup() {
		add_action( 'mpp_loaded', array( $this, 'load' ) );
		add_action( 'mpp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'mpp_init', array( $this, 'load_text_domain' ) );
	}

	/**
	 * Load plugins other files
	 */
	public function load() {

		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		$files = array(
			'core/mppftc-functions.php',
			'core/class-mppftc-ajax-handler.php',
			'core/class-mppftc-action-handler.php',
			'core/class-mppftc-hooks-helper.php',
			'core/widgets/class-mppftc-gallery-widget.php',
			'core/widgets/class-mppftc-media-widget.php',
		);

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			$files[] = 'admin/class-mppftc-admin-helper.php';
		}

		foreach ( $files as $file ) {
			require_once $this->path . $file;
		}
	}

	/**
	 * Loads plugin assets
	 */
	public function load_assets() {

		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		wp_register_style( 'mppftc_css', $this->url . 'assets/css/mppftc.css' );

		wp_register_script( 'mppftc_js', $this->url . 'assets/js/mppftc.js' );

		wp_localize_script( 'mppftc_js', 'MPPFTC', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_style( 'mppftc_css' );
		wp_enqueue_script( 'mppftc_js' );
	}

	/**
	 * Load plugin language file
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'mediapress-featured-content', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Return plugin directory path
	 *
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Return plugin directory url
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}
}

/**
 * Class instance
 *
 * @return MPP_Featured_Content
 */
function mppftc() {
	return MPP_Featured_Content::get_instance();
}

mppftc();
