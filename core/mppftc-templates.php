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
 * @param int  $item_id item_id.
 * @param bool $wrapper use link wrapper.
 */
function mppftc_featured_button( $item_id, $wrapper = true ) {
	echo mppftc_get_featured_button( $item_id, $wrapper );
}

/**
 * Get mark as featured button
 *
 * @param int  $item_id Item id.
 * @param bool $wrapper use link wrapper.
 *
 * @return string
 */
function mppftc_get_featured_button( $item_id, $wrapper = true ) {

	$label = __( 'Set Featured', 'mpp-featured-content' );

	$css_class = 'mppftc-mark-featured-link';

	if ( mppftc_is_item_featured( $item_id ) ) {
		$label = __( 'Remove Featured', 'mpp-featured-content' );
		$css_class = 'mppftc-remove-featured-link';
	}

	$link = sprintf( '<a href="#" class="mppftc-featured-btn %s" data-item-id="%s" data-nonce="%s">%s</a>', $css_class, $item_id, wp_create_nonce( 'mppftc-featured-action' ), $label );

	if ( $wrapper ) {
		return sprintf( '<div class="generic-button %s">', $css_class . '-btn' ) . $link . '</div>';
	} else {
		return $link;
	}
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
	$lightbox = empty( $args['lightbox'] ) ? 0 : 1;
	unset( $args['lightbox'] );

	$media_items = mppftc_get_featured_media( $args );

	if ( empty( $media_items ) ) {
		return '';
	}

	$ids    = wp_list_pluck( $media_items, 'ID' );
	$id_str = join( ',', $ids );
	?>

	<div class="mppftc-featured-media" data-mpp-media-ids="<?php echo esc_attr( $id_str );?>" data-mpp-lightbox-enabled="<?php echo $lightbox;?>">
		<ul class="mppftc-featured-media-list">
			<?php foreach ( $media_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( mpp_get_media_permalink( $item->ID ) );?>" data-mpp-media-id="<?php echo $item->ID;?>">
						<img width="<?php echo $size;?>" src="<?php mpp_media_src( 'thumbnail', $item->ID ); ?>" title="<?php echo esc_attr( mpp_get_media_title( $item->ID ) ); ?>" />
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php
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

	$lightbox = empty( $args['lightbox'] ) ? 0 : 1;
	unset( $args['lightbox'] );

	$featured_galleries = mppftc_get_featured_galleries( $args );

	if ( empty( $featured_galleries ) ) {
		return '';
	}

	$lb_class = $lightbox ? 'mpp-lightbox-link' : '';

	?>

	<div class="mppftc-featured-gallery">
		<ul class="mppftc-featured-gallery-list">
			<?php foreach ( $featured_galleries as $featured_gallery ) : ?>
			<?php
				$atts = mpp_get_html_attributes( array(
					'class'           => $lb_class,
					'data-gallery-id' => $featured_gallery->ID,
				) ); ?>
			<li>
				<a href="<?php echo esc_url( mpp_get_gallery_permalink( $featured_gallery->ID ) ); ?>" <?php echo $atts;?>>
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
	bp_core_load_template( array( 'members/single/plugins' ) );
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
	bp_core_load_template( array( 'members/single/plugins' ) );
}

/**
 * Include Featured loop of users.
 */
function mppftc_render_user_featured_media() {
	mpp_locate_template( array( 'members/loop-featured-media.php' ), true, mppftc_featured_content()->get_path() . 'templates/' );
}
