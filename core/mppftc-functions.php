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
 * Render featured media button
 *
 * @param int $item_id item_id.
 */
function mppftc_featured_button( $item_id ) {
	echo mppftc_get_featured_button( $item_id );
}

/**
 * Get mark as featured button
 *
 * @param int $item_id Item id.
 *
 * @return string
 */
function mppftc_get_featured_button( $item_id ) {

	$label = __( 'Mark Featured', 'mpp-featured-content' );

	$css_class = 'mppftc-btn-mark-featured';

	if ( mppftc_is_item_featured( $item_id ) ) {
		$label = __( 'Remove Featured', 'mpp-featured-content' );
		$css_class = 'mppftc-btn-remove-featured';
	}

	return sprintf( '<div class="generic-button %s"><a href="#" class="mppftc-featured-btn" data-item-id="%s" data-nonce="%s">%s</a></div>', $css_class, $item_id, wp_create_nonce( 'mppftc-featured-action' ), $label );
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

	if ( empty( $user_id ) || empty( $item ) ) {
		return false;
	}
    // do not use === here, the values can be str/int.
	if ( $item->user_id == $user_id ) {
		$can = true;
	} elseif ( 'groups' === $item->component && bp_is_active( 'groups' ) && groups_is_user_admin( $user_id, $item->component_id ) ) {
		$can = true;
	}

	return apply_filters( 'mppftc_user_can_mark_item_featured', $can, $item );
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

	$enabled_for       = mpp_get_option( 'mppftc_enabled_for', array() );
	$enabled_component = mpp_get_option( 'mppftc_enabled_components', array() );
	$enabled_type      = mpp_get_option( 'mppftc_enabled_types', array() );

	// Complex will fix in future.
	if ( mpp_is_valid_media( $item_id ) && ! in_array( 'media', $enabled_for, true ) ) {
		return false;
	} elseif ( mpp_is_valid_gallery( $item_id ) && ! in_array( 'gallery', $enabled_for, true ) ) {
		return false;
	}

	if ( ! in_array( $item->component, $enabled_component,true ) || ! in_array( $item->type, $enabled_type, true ) ) {
		return false;
	}

	return true;
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

	if ( mpp_is_single_media() && array_key_exists( 'single_media', $screens ) ) {
		return true;
	} elseif ( ! mpp_is_single_media() && mpp_is_single_gallery() && array_key_exists( 'single_gallery', $screens ) ) {
		return true;
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

	$query = new MPP_Media_Query( $args );

	return $query->posts;
}

/**
 * Render featured media
 *
 * @param array $args args.
 *
 * @return string
 */
function mppftc_featured_media( $args = array() ) {
	$size = isset( $args['size'] ) ? absint( $args['size'] ) : 50;
	unset( $args['size'] );
	$media_items = mppftc_get_featured_media( $args );

	if ( empty( $media_items ) ) {
		return '';
	}

	?>

	<div class="mppftc-featured-media">
		<ul class="mppftc-featured-media-list">
			<?php foreach ( $media_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( mpp_get_media_permalink( $item->ID ) );?>">
						<img width="<?php echo $size;?>" src="<?php mpp_media_src( 'thumbnail', $item->ID ); ?>" title="<?php echo esc_attr( mpp_get_media_title( $item->ID ) ); ?>" />
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php
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

	$query = new MPP_Gallery_Query( $args );

	return $query->posts;
}

/**
 * Render featured media
 *
 * @param array $args args.
 *
 * @return string
 */
function mppftc_featured_galleries( $args = array() ) {
	$size = isset( $args['size'] ) ? absint( $args['size'] ) : 50;
	unset( $args['size'] );

	$featured_galleries = mppftc_get_featured_galleries( $args );

	if ( empty( $featured_galleries ) ) {
		return '';
	}

	?>

	<div class="mppftc-featured-gallery">
		<ul class="mppftc-featured-gallery-list">
			<?php foreach ( $featured_galleries as $featured_gallery ) : ?>
				<li>
                    <a href="<?php echo esc_url( mpp_get_gallery_permalink( $featured_gallery->ID ) ); ?>">
                        <img width="<?php echo $size;?>" src="<?php echo esc_url( mpp_get_gallery_cover_src( 'thumbnail', $featured_gallery->ID ) ); ?>" alt="<?php echo esc_attr( mpp_get_gallery_title( $featured_gallery->ID ) ); ?>"/>
                    </a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php
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
 * Render featured gallery page
 */
function mppftc_render_user_featured_gallery_page() {
	add_action( 'bp_template_content', 'mppftc_render_user_featured_gallery' );
	bp_core_load_template( 'members/single/plugins' );
}

/**
 * Show featured gallery list.
 */
function mppftc_render_user_featured_gallery() {
	mpp_locate_template( array( 'members/loop-featured-gallery.php' ), true, mppftc_featured_content()->get_path() . 'templates/' );
}

/**
 * Render featured media page
 */
function mppftc_render_user_featured_media_page() {
	add_action( 'bp_template_content', 'mppftc_render_user_featured_media' );
	bp_core_load_template( 'members/single/plugins' );
}

/**
 * Include Featured loop of users.
 */
function mppftc_render_user_featured_media() {
	mpp_locate_template( array( 'members/loop-featured-media.php' ), true, mppftc_featured_content()->get_path() . 'templates/' );
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
