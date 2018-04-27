<?php
/**
 * Class handler ajax request made by plugin
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPPFTC_Ajax_Handler
 */
class MPPFTC_Ajax_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Handler function provide callback to process ajax request
	 */
	public function setup() {
		add_action( 'wp_ajax_mppftc_featured_action', array( $this, 'process' ) );
	}

	/**
	 * Mark item featured or un featured
	 */
	public function process() {

		check_ajax_referer( 'mppftc-featured-action', 'nonce' );

		$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;

		if ( empty( $item_id ) || ! mppftc_user_can_mark_item_featured( $item_id ) ) {
			wp_send_json_error( array(
				'message' => __( 'Could not process.', 'mpp-featured-content' ),
			) );
		}

		// Removing media as featured media.
		if ( mppftc_is_item_featured( $item_id ) ) {
			delete_post_meta( $item_id, '_mppftc_featured' );
		} else {
			add_post_meta( $item_id, '_mppftc_featured', 1, 1 );
		}

		$button = mppftc_get_featured_button( $item_id, false );

		wp_send_json_success( array(
			'button'    => $button,
			'media_id' => $item_id,
		) );

		exit;
	}
}

new MPPFTC_Ajax_Handler();
