<?php
/**
 * Template for user's Featured media
 *
 * @package mediapress-featured-content
 */

/**
 * If file access directly if will exit
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'component'     => 'members',
	'component_id'  => bp_displayed_user_id(),
	'meta_key'      => '_mppftc_featured',
);

$query = new MPP_Media_Query( $args );

?>

<?php if ( $query->have_media() ) : ?>
    <?php while ( $query->have_media() ) : $query->the_media(); ?>
        <div class="mpp-u <?php mpp_media_class( mpp_get_media_grid_column_class() ); ?>">
            <?php do_action( 'mpp_before_media_item' ); ?>

            <div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
				<?php do_action( 'mpp_media_meta_top' ); ?>
            </div>

            <div class='mpp-item-entry mpp-media-entry mpp-photo-entry'>
                <a href="<?php mpp_media_permalink(); ?>" <?php mpp_media_html_attributes( array( 'class' => 'mpp-item-thumbnail mpp-media-thumbnail mpp-photo-thumbnail' ) ); ?>>
                    <img src="<?php mpp_media_src( 'thumbnail' ); ?>" alt="<?php echo esc_attr( mpp_get_media_title() ); ?> "/>
                </a>
            </div>

            <div class="mpp-item-actions mpp-media-actions">
				<?php mpp_media_action_links(); ?>
            </div>

            <div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
				<?php do_action( 'mpp_media_meta' ); ?>
            </div>

			<?php do_action( 'mpp_after_media_item' ); ?>
        </div>

    <?php endwhile; ?>
	<?php mpp_reset_media_data(); ?>
<?php else : ?>
    <?php _e( 'You do not have any featured media', 'mpp-featured-content' ) ?>
<?php endif; ?>