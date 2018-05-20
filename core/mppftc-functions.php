<?php
/**
 * Plugin functions file
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if media is featured or not
 *
 * @param int $item_id Id of gallery or media.
 *
 * @return bool
 */
function mppftc_is_item_featured( $item_id ) {

	if ( get_post_meta( $item_id, '_mppftc_featured', true ) ) {
		return true;
	}

	return false;
}

/**
 * Checks if item can be featured
 *
 * @param int $item_id Item id.
 *
 * @return bool
 */
function mppftc_is_item_featurable( $item_id ) {

	$item = mppftc_get_item( $item_id );

	$is_featurable = true;

	if ( ! $item ) {
		$is_featurable = false;
	} elseif ( ! mppftc_is_enabled_for_component( $item->component ) ) {
		$is_featurable = false;
	} elseif ( ! mppftc_is_enabled_for_type( $item->type ) ) {
		$is_featurable = false;
	} elseif ( $item instanceof MPP_Gallery ) {
		$is_featurable = mppftc_is_enabled_for_gallery();
	} elseif ( $item instanceof MPP_Media ) {
		$is_featurable = mppftc_is_enabled_for_media();
	}

	return $is_featurable;
}

/**
 * Is featured Content module enabled for the given component.
 *
 * @param string $component component.
 *
 * @return bool
 */
function mppftc_is_enabled_for_component( $component ) {
	$enabled_components = mpp_get_option( 'mppftc_enabled_components', array() );
	return apply_filters( 'mppftc_enabled_for_component', in_array( $component, $enabled_components,true ), $component );
}

/**
 * Is featured Content module enabled for the given component.
 *
 * @param string $type media type.
 *
 * @return bool
 */
function mppftc_is_enabled_for_type( $type ) {
	$enabled_types = mpp_get_option( 'mppftc_enabled_types', array() );
	return apply_filters( 'mppftc_enabled_for_type', in_array( $type, $enabled_types ), $type );
}

/**
 * Is enabled for gallery?
 *
 * @return bool
 */
function mppftc_is_enabled_for_gallery() {

	$enabled_for = mpp_get_option( 'mppftc_enabled_for', array() );

	// Complex will fix in future.
	return in_array( 'gallery', $enabled_for, true );
}

/**
 * Is enabled of media.
 *
 * @return bool
 */
function mppftc_is_enabled_for_media() {
	$enabled_for = mpp_get_option( 'mppftc_enabled_for', array() );
	// Complex will fix in future.
	return in_array( 'media', $enabled_for, true );
}

/**
 * Check weather user mark item featured or not
 *
 * @param int $item_id Id of media or gallery.
 *
 * @return bool
 */
function mppftc_user_can_mark_item_featured( $item_id ) {

	$can     = false;
	$user_id = get_current_user_id();
	$item    = mppftc_get_item( $item_id );

	if ( empty( $user_id ) || empty( $item ) || ! mppftc_is_item_featurable( $item_id ) ) {
		return false;
	}

	// do not use === here, the values can be str/int.
	if ( $item->user_id == $user_id ) {
		$can = true;
	} elseif ( 'groups' === $item->component && function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) && groups_is_user_admin( $user_id, $item->component_id ) ) {
		$can = true;
	}

	return apply_filters( 'mppftc_user_can_mark_item_featured', $can, $item );
}

/**
 * Get components
 *
 * @return array
 */
function mppftc_get_components() {

	$components = array();

	$active_components = mpp_get_active_components();

	if ( empty( $active_components ) ) {
		return $components;
	}

	foreach ( $active_components as $key => $component ) {
		$label = '';

		/**
		 * @todo support sitewide gallery in future.
		 */
		if ( 'sitewide' === $key ) {
			continue;
		} elseif ( 'members' === $key ) {
			$label = __( 'Users', 'mpp-featured-content' );
		} elseif ( 'groups' === $key ) {
			$label = __( 'Groups', 'mpp-featured-content' );
		}

		if ( $label ) {
			$components[ $key ] = $label;
		}
	}

	return $components;
}

/**
 * Get active types
 *
 * @return array
 */
function mppftc_get_types() {

	$types = array();

	$active_types = mpp_get_active_types();

	if ( empty( $active_types ) ) {
		return $types;
	}

	foreach ( $active_types as $key => $type ) {
		$types[ $key ] = $type->label;
	}

	return $types;
}

/**
 * Function checks if current screen is valid from where user can mark item as featured
 *
 * @return bool
 */
function mppftc_is_valid_screen() {

	$screens = mpp_get_option( 'mppftc_button_ui_places', array() );

	if ( mpp_is_group_gallery_component() && bp_is_action_variable( 'featured-media', 0 ) ) {
		return true;
	}

	if ( bp_is_current_action( 'featured-media' ) || bp_is_current_action( 'featured-gallery' ) ) {
		return true;
	}

	if ( mpp_is_single_media() ) {
		return array_key_exists( 'single_media', $screens );
	} elseif ( mpp_is_single_gallery() ) {
		return array_key_exists( 'single_gallery', $screens );
	} elseif ( array_key_exists( 'gallery_home', $screens ) && mpp_is_gallery_home() ) {
		return true;
	}

	return false;
}

/**
 * Get item
 *
 * @param int $item_id Item id.
 *
 * @return MPP_Gallery|MPP_Media
 */
function mppftc_get_item( $item_id ) {

	$item = '';

	if ( mpp_is_valid_media( $item_id ) ) {
		$item = mpp_get_media( $item_id );
	} elseif ( mpp_is_valid_gallery( $item_id ) ) {
		$item = mpp_get_gallery( $item_id );
	}

	return $item;
}

/**
 * Get featured media
 *
 * @param array $args args.
 *
 * @return array
 */
function mppftc_get_featured_media( $args = array() ) {

	$default = array(
		'component'    => 'members',
		'component_id' => get_current_user_id(),
		'per_page'     => 5,
	);

	/**
	 * Note: it will conflict with other meta queries.
	 *
	 * @todo use better handling for featured atts.
	 */
	$args = wp_parse_args( $args, $default );
	$args['meta_key'] = '_mppftc_featured';

	$args['status'] = mpp_get_accessible_statuses( $args['component'], $args['component_id'] );

	$query = new MPP_Media_Query( $args );

	return $query->posts;
}

/**
 * Get featured galleries
 *
 * @param array $args args.
 *
 * @return array
 */
function mppftc_get_featured_galleries( $args = array() ) {

	$default = array(
		'component'    => 'members',
		'component_id' => get_current_user_id(),
		'per_page'     => 5,
	);

	/**
	 * Note: it will conflict with other meta queries.
	 *
	 * @todo use better handling for featured atts.
	 */
	$args = wp_parse_args( $args, $default );
	$args['meta_key'] = '_mppftc_featured';

	$args['status'] = mpp_get_accessible_statuses( $args['component'], $args['component_id'] );

	$query = new MPP_Gallery_Query( $args );

	return $query->posts;
}

/**
 * Get header media limit
 *
 * @return mixed
 */
function mppftc_get_header_item_limit() {
	return mpp_get_option( 'mppftc_header_item_limit', 5 );
}

/**
 * Show media of user types
 *
 * @return array
 */
function mppftc_show_media_of() {

	return array(
		'loggedin'  => __( 'Logged In', 'mpp-featured-content' ),
		'displayed' => __( 'Displayed', 'mpp-featured-content' ),
	);
}

/**
 * Get group featured gallery url
 *
 * @return string
 */
function mppftc_groups_get_featured_gallery_url() {

	if ( ! bp_is_group() ) {
		return '';
	}

	$component    = 'groups';
	$component_id = groups_get_current_group()->id;

	return trailingslashit( mpp_get_gallery_base_url( $component, $component_id ) ) . 'featured-gallery/';
}

/**
 * Get group featured media url
 *
 * @return string
 */
function mppftc_groups_get_featured_media_url() {

	if ( ! bp_is_group() ) {
		return '';
	}

	$component    = 'groups';
	$component_id = groups_get_current_group()->id;

	return trailingslashit( mpp_get_gallery_base_url( $component, $component_id ) ) . 'featured-media/';
}
