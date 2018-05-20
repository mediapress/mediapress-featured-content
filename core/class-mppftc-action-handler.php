<?php
/**
 * Plugin action file
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPPFTC_Action_Handler
 */
class MPPFTC_Action_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Setup hooks
	 */
	public function setup() {
		add_action( 'mpp_setup_nav', array( $this, 'register_subnav_items' ), 0 );
		add_action( 'mpp_group_nav', array( $this, 'register_group_subnav_items' ), 1 );
		add_action( 'mpp_setup_globals', array( $this, 'reset_query' ) );

		add_filter( 'mpp_groups_gallery_located_template', array( $this, 'load_group_template' ) );
	}

	/**
	 * Register new subnav items for gallery tab
	 */
	public function register_subnav_items() {
		$user_id = bp_displayed_user_id();

		if ( ! mppftc_is_enabled_for_component( 'members' ) ) {
			return;
		}

		$user_domain = trailingslashit( bp_displayed_user_domain() );
		$parent_slug = MPP_GALLERY_SLUG;
		$parent_url  = trailingslashit( $user_domain . $parent_slug );

		$sub_nav_items = array();

		if ( mppftc_is_enabled_for_gallery() ) {
			$sub_nav_items[] = array(
				'name'            => __( 'Featured Gallery', 'mpp-featured-content' ),
				'slug'            => 'featured-gallery',
				'parent_url'      => $parent_url,
				'parent_slug'     => $parent_slug,
				'position'        => 91,
				'screen_function' => 'mppftc_render_user_featured_gallery_page',
			);
		}

		if ( mppftc_is_enabled_for_media() ) {
			$sub_nav_items[] = array(
				'name'            => __( 'Featured Media', 'mpp-featured-content' ),
				'slug'            => 'featured-media',
				'parent_url'      => $parent_url,
				'parent_slug'     => $parent_slug,
				'position'        => 92,
				'screen_function' => 'mppftc_render_user_featured_media_page',
			);
		}

		foreach ( $sub_nav_items as $sub_nav_item ) {
			bp_core_new_subnav_item( $sub_nav_item );
		}
	}

	/**
	 * Register new group nav item
	 */
	public function register_group_subnav_items() {

		$group_id = bp_is_group() ? groups_get_current_group()->id : 0;

		if ( ! mppftc_is_enabled_for_component( 'groups' ) ) {
			return;
		}

		if ( mppftc_is_enabled_for_gallery() ) {
			echo sprintf( "<li><a href='%s'>%s</a></li>", esc_url( mppftc_groups_get_featured_gallery_url() ), __( 'Featured Gallery', 'mpp-featured-content' ) );
		}

		if ( mppftc_is_enabled_for_media() ) {
			echo sprintf( "<li><a href='%s'>%s</a></li>", esc_url( mppftc_groups_get_featured_media_url() ), __( 'Featured Media', 'mpp-featured-content' ) );
		}
	}

	/**
	 * Reset query for featured media and featured gallery screen
	 */
	public function reset_query() {
		$component = mpp_get_current_component();

		if ( ! $component || ! mppftc_is_enabled_for_component( $component ) ) {
			return;
		}

		if ( mpp_is_gallery_component() && bp_is_current_action( 'featured-media' ) ) {
			mediapress()->is_gallery_home   = false;
			mediapress()->the_gallery_query = new MPP_Gallery_Query();
		} elseif ( mpp_is_gallery_component() && bp_is_current_action( 'featured-gallery' ) ) {
			mediapress()->is_gallery_home   = false;
			mediapress()->the_gallery_query = new MPP_Gallery_Query();
		} elseif ( mpp_is_group_gallery_component() && bp_is_action_variable( 'featured-media', 0 ) ) {
			mediapress()->is_gallery_home   = false;
			mediapress()->the_gallery_query = new MPP_Gallery_Query();
		}
	}

	/**
	 * Load group gallery templates
	 *
	 * @param string $template Template name.
	 *
	 * @return string found template
	 */
	public function load_group_template( $template ) {
		$group_id = bp_is_group() ? groups_get_current_group()->id : 0;

		if ( ! mppftc_is_enabled_for_component( 'groups' ) ||  ! mpp_is_group_gallery_component() ) {
			return $template;
		}

		if ( bp_is_action_variable( 'featured-media', 0 ) ) {
			$template = mpp_locate_template( array( 'loop-featured-media.php' ), false, mppftc_featured_content()->get_path() . 'templates/groups/' );
		} elseif ( bp_is_action_variable( 'featured-gallery', 0 ) ) {
			$template = mpp_locate_template( array( 'loop-featured-gallery.php' ), false, mppftc_featured_content()->get_path() . 'templates/groups/' );
		}

		return $template;
	}
}

new MPPFTC_Action_Handler();
