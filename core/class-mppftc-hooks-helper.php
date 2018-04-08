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
class MPPFTC_Hooks_Helper {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Call back to various hooks
	 */
	public function setup_hooks() {
		add_action( 'mpp_media_meta', array( $this, 'add_media_ui' ) );
		add_action( 'mpp_lightbox_media_meta', array( $this, 'add_lightbox_ui' ) );

		add_action( 'mpp_gallery_meta', array( $this, 'add_gallery_ui' ) );
		add_action( 'bp_profile_header_meta', array( $this, 'render_user_header_featured_items' ) );
		add_action( 'bp_group_header_meta', array( $this, 'render_group_header_featured_items' ) );

		// Category attribute support to [mpp-list-gallery] and filter galleries based on categories ids.
		add_filter( 'mpp_shortcode_list_gallery_defaults', array( $this, 'modify_mpp_list_gallery_default_args' ) );
		add_filter( 'mpp_shortcode_list_gallery_query_args', array( $this, 'modify_mpp_list_gallery_query_args' ) );

		// Category attribute support to [mpp-list-media] and filter media based on categories ids.
		add_filter( 'mpp_shortcode_list_media_defaults', array( $this, 'modify_mpp_list_media_default_args' ) );
		add_filter( 'mpp_shortcode_list_media_query_args', array( $this, 'modify_mpp_list_media_query_args' ) );
	}

	/**
	 * Attach button to media to mark as featured
	 *
	 * @return string
	 */
	public function add_media_ui() {

		$media = mpp_get_media();

		if ( ! mppftc_is_valid_screen() || ! mppftc_is_item_featurable( $media->id ) || ! mppftc_user_can_mark_item_featured( $media->id ) ) {
			return '';
		}

		mppftc_featured_button( $media->id );
	}

	/**
	 * Add button in lightbox
	 *
	 * @return string
	 */
	public function add_lightbox_ui() {

		$media = mpp_get_media();
		$screens = mpp_get_option( 'mppftc_button_ui_places', array() );

		if ( ! array_key_exists( 'light_box', $screens ) || ! mppftc_is_item_featurable( $media->id ) || ! mppftc_user_can_mark_item_featured( $media->id ) ) {
			return '';
		}

		mppftc_featured_button( $media->id );
	}

	/**
	 * Attach button to gallery to mark as featured
	 *
	 * @return string
	 */
	public function add_gallery_ui() {

		$gallery = mpp_get_gallery();

		if ( ! mppftc_is_valid_screen() || ! mppftc_is_item_featurable( $gallery->id ) || ! mppftc_user_can_mark_item_featured( $gallery->id ) ) {
			return '';
		}

		mppftc_featured_button( $gallery->id );
	}

	/**
	 * Render user featured media in header
	 *
	 * @return string
	 */
	public function render_user_header_featured_items() {

		$show_in_header = mpp_get_option( 'mppftc_show_in_user_header', 'none' );

		if ( 'none' == $show_in_header ) {
			return '';
		}

		if ( 'media_list' === $show_in_header ) {
			mppftc_featured_media( array(
				'component'    => 'members',
				'component_id' => bp_displayed_user_id(),
				'per_page'     => mppftc_get_header_item_limit(),
			) );
		} elseif ( 'gallery_list' === $show_in_header ) {
			mppftc_featured_galleries( array(
				'component'    => 'members',
				'component_id' => bp_displayed_user_id(),
				'per_page'     => mppftc_get_header_item_limit(),
			) );
		}
	}

	/**
	 * Render group featured media in header
	 *
	 * @return string
	 */
	public function render_group_header_featured_items() {

		$show_in_header = mpp_get_option( 'mppftc_show_in_group_header', 'none' );

		if ( 'none' == $show_in_header ) {
			return '';
		}

		if ( 'media_list' === $show_in_header ) {
			mppftc_featured_media( array(
				'component'    => 'members',
				'component_id' => bp_displayed_user_id(),
				'per_page'     => mppftc_get_header_item_limit(),
			) );
		} elseif ( 'gallery_list' === $show_in_header ) {
			mppftc_featured_galleries( array(
				'component'    => 'members',
				'component_id' => bp_displayed_user_id(),
				'per_page'     => mppftc_get_header_item_limit(),
			) );
		}
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

		if ( ! empty( $atts['featured'] ) ) {
			$atts['meta_key'] = '_mppftc_featured';
		}

		return $atts;
	}
}

new MPPFTC_Hooks_Helper();
