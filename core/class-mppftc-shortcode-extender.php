<?php
/**
 * Plugin hooks file
 *
 * @package mediapress-featured-content
 */

// Exit if access directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPPFTC_Hooks_Helper
 */
class MPPFTC_Shortcode_Extender {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Call back to various hooks
	 */
	public function setup() {

		// extend shortcodes to list featured items.
		// featured attribute support to [mpp-list-gallery] and filter galleries based on categories ids.
		add_filter( 'mpp_shortcode_list_gallery_defaults', array( $this, 'modify_mpp_list_gallery_default_args' ) );
		add_filter( 'mpp_shortcode_list_gallery_query_args', array( $this, 'modify_mpp_list_gallery_query_args' ) );

		// featured attribute support to [mpp-list-media] and filter media based on categories ids.
		add_filter( 'mpp_shortcode_list_media_defaults', array( $this, 'modify_mpp_list_media_default_args' ) );
		add_filter( 'mpp_shortcode_list_media_query_args', array( $this, 'modify_mpp_list_media_query_args' ) );
	}

	/**
	 * Override default args applied on [mpp-list-gallery]
	 *
	 * @param array $args List of default args for the shortcode to list galleries.
	 *
	 * @return array
	 */
	public function modify_mpp_list_gallery_default_args( $args ) {
		$args['featured'] = 0;
		return $args;
	}

	/**
	 * Query args passed to shortcode to list galleries
	 *
	 * @param array $atts List of parameter passed to the loop.
	 *
	 * @return array
	 */
	public function modify_mpp_list_gallery_query_args( $atts ) {

		/**
		 * Note: it will conflict with other meta queries.
		 *
		 * @todo use better handling for featured atts.
		 */
		if ( ! empty( $atts['featured'] ) ) {
			$atts['meta_key'] = '_mppftc_featured';
		}

		return $atts;
	}

	/**
	 * Override default args applied on [mpp-list-media]
	 *
	 * @param array $args List of default args for the shortcode to list media.
	 *
	 * @return array
	 */
	public function modify_mpp_list_media_default_args( $args ) {
		$args['featured'] = 0;
		return $args;
	}

	/**
	 * Query args passed to shortcode to list galleries
	 *
	 * @param array $atts List of parameter passed to the loop.
	 *
	 * @return array
	 */
	public function modify_mpp_list_media_query_args( $atts ) {

		/**
		 * Note: it will conflict with other meta queries.
		 *
		 * @todo use better handling for featured atts.
		 */
		if ( ! empty( $atts['featured'] ) ) {
			$atts['meta_key'] = '_mppftc_featured';
		}

		return $atts;
	}
}

new MPPFTC_Shortcode_Extender();
