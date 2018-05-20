<?php
/**
 * Add Views.
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
class MPPFTC_Views_Helper {

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
		// add buttons.
		add_action( 'mpp_media_meta', array( $this, 'add_media_ui' ) );
		add_action( 'mpp_lightbox_media_action_before_link', array( $this, 'add_lightbox_ui' ) );
		add_action( 'mpp_gallery_meta', array( $this, 'add_gallery_ui' ) );

		// show the feature list.
		add_action( 'bp_profile_header_meta', array( $this, 'render_user_header_featured_items' ) );
		add_action( 'bp_group_header_meta', array( $this, 'render_group_header_featured_items' ) );
	}

	/**
	 * Attach button to media to mark as featured
	 *
	 * @return string
	 */
	public function add_media_ui() {

		$media = mpp_get_media();

		if ( ! mppftc_is_valid_screen() || ! mppftc_user_can_mark_item_featured( $media->id ) ) {
			return '';
		}

		mppftc_featured_button( $media->id );
	}

	/**
	 * Add button in lightbox.
	 *
	 * @return string
	 */
	public function add_lightbox_ui() {

		$media = mpp_get_media();
		$screens = mpp_get_option( 'mppftc_button_ui_places', array() );

		if ( ! array_key_exists( 'lightbox', $screens ) || ! mppftc_user_can_mark_item_featured( $media->id ) ) {
			return '';
		}

		mppftc_featured_button( $media->id, false );
	}

	/**
	 * Attach button to gallery to mark as featured
	 *
	 * @return string
	 */
	public function add_gallery_ui() {

		$gallery = mpp_get_gallery();

		if ( ! mppftc_is_valid_screen() || ! mppftc_user_can_mark_item_featured( $gallery->id ) ) {
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

		$enabled        = mppftc_is_enabled_for_component( 'members' );
		$show_in_header = mpp_get_option( 'mppftc_show_in_user_header', 'none' );

		if ( ! $enabled || 'none' === $show_in_header ) {
			return;
		}

		if ( 'media_list' === $show_in_header && mppftc_is_enabled_for_media() ) {
			mppftc_featured_media( array(
				'component'    => 'members',
				'component_id' => bp_displayed_user_id(),
				'per_page'     => mppftc_get_header_item_limit(),
				'lightbox' => mpp_get_option( 'mppftc_show_header_media_in_lightbox', true ),
			) );
		} elseif ( 'gallery_list' === $show_in_header && mppftc_is_enabled_for_gallery() ) {
			mppftc_featured_galleries( array(
				'component'    => 'members',
				'component_id' => bp_displayed_user_id(),
				'per_page'     => mppftc_get_header_item_limit(),
				'lightbox'     => mpp_get_option( 'mppftc_show_header_media_in_lightbox', true ),
			) );
		}
	}

	/**
	 * Render group featured media in header
	 *
	 * @return string
	 */
	public function render_group_header_featured_items() {

		$group_id       = groups_get_current_group()->id;
		$enabled        = mppftc_is_enabled_for_component( 'groups' );
		$show_in_header = mpp_get_option( 'mppftc_show_in_group_header', 'none' );

		if ( ! $enabled || 'none' === $show_in_header ) {
			return;
		}

		if ( 'media_list' === $show_in_header ) {
			mppftc_featured_media( array(
				'component'    => 'groups',
				'component_id' => $group_id,
				'per_page'     => mppftc_get_header_item_limit(),
			) );
		} elseif ( 'gallery_list' === $show_in_header ) {
			mppftc_featured_galleries( array(
				'component'    => 'groups',
				'component_id' => $group_id,
				'per_page'     => mppftc_get_header_item_limit(),
			) );
		}
	}
}

new MPPFTC_Views_Helper();
