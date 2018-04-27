<?php
/**
 * Template related.
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

	$label = __( 'Set Featured', 'mpp-featured-content' );

	$css_class = 'mppftc-btn-mark-featured';

	if ( mppftc_is_item_featured( $item_id ) ) {
		$label = __( 'Remove Featured', 'mpp-featured-content' );
		$css_class = 'mppftc-btn-remove-featured';
	}

	return sprintf( '<div class="generic-button %s"><a href="#" class="mppftc-featured-btn" data-item-id="%s" data-nonce="%s">%s</a></div>', $css_class, $item_id, wp_create_nonce( 'mppftc-featured-action' ), $label );
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
