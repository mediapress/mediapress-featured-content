<?php
/**
 * Plugin Name: MediaPress Featured Content
 * Description: Let user feature their media and galleries.
 * Plugin URI: https://buddydev.com/plugins/mpp-featured-content
 * Author: BuddyDev
 * Author URI: https://buddydev.com/
 * Version: 1.0.2
 *
 * Text Domain: mpp-featured-content
 * Domain Path: /languages
 */

/**
 * Contributor: @raviousprime
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPPFTC_Featured_Content
 */
class MPPFTC_Featured_Content {

	/**
	 * Class instance
	 *
	 * @var MPPFTC_Featured_Content
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
	 * @return MPPFTC_Featured_Content
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
			'core/mppftc-templates.php',
			'core/class-mppftc-ajax-handler.php',
			'core/class-mppftc-action-handler.php',
			'core/class-mppftc-view-helper.php',
			'core/class-mppftc-shortcode-extender.php',
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

		wp_register_style( 'mpp-featured-content', $this->url . 'assets/css/mpp-featured-content.css' );

		wp_register_script( 'mpp-featured-content', $this->url . 'assets/js/mpp-featured-content.js', array( 'jquery' ) );

		wp_localize_script( 'mpp-featured-content', 'MPPFeaturedContent', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_style( 'mpp-featured-content' );
		wp_enqueue_script( 'mpp-featured-content' );
	}

	/**
	 * Load plugin language file
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'mpp-featured-content', false, basename( dirname( __FILE__ ) ) . '/languages/' );
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
 * @return MPPFTC_Featured_Content
 */
function mppftc_featured_content() {
	return MPPFTC_Featured_Content::get_instance();
}

mppftc_featured_content();
