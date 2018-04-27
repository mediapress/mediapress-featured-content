<?php
/**
 * Plugin admin settings helper class
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPPFTC_Admin_Settings_Helper
 */
class MPPFTC_Admin_Settings_Helper {

	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'mpp_admin_register_settings', array( $this, 'register_settings' ) );
	}

	/**
	 * Register settings
	 *
	 * @param MPP_Admin_Settings_Page $page Page object.
	 */
	public function register_settings( $page ) {

		$panel = $page->get_panel( 'addons' );
		$section = $panel->add_section( 'mppftc-settings', __( 'MediaPress Featured Content', 'mpp-featured-content' ) );

		$fields = array(
			array(
				'name'    => 'mppftc_enabled_for',
				'label'   => __( 'Enable for', 'mpp-featured-content' ),
				'type'    => 'multicheck',
				'options' => array(
					'media'   => __( 'Media', 'mpp-featured-content' ),
					'gallery' => __( 'Gallery', 'mpp-featured-content' ),
				),
			),
			array(
				'name'    => 'mppftc_enabled_components',
				'label'   => __( 'Select components', 'mpp-featured-content' ),
				'type'    => 'multicheck',
				'options' => mppftc_get_components(),
			),
			array(
				'name'    => 'mppftc_enabled_types',
				'label'   => __( 'Select types', 'mpp-featured-content' ),
				'type'    => 'multicheck',
				'options' => mppftc_get_types(),
			),
			array(
				'name'    => 'mppftc_button_ui_places',
				'label'   => __( 'Where to show mark featured button?', 'mpp-featured-content' ),
				'type'    => 'multicheck',
				'options' => array(
					'single_media'   => __( 'Single Media Page', 'mpp-featured-content' ),
					'lightbox'      => __( 'Lightbox', 'mpp-featured-content' ),
					'single_gallery' => __( 'Single Gallery', 'mpp-featured-content' ),
					'gallery_home'   => __( 'Gallery home', 'mpp-featured-content' ),
				),
			),
			array(
				'name'    => 'mppftc_show_in_user_header',
				'label'   => __( 'Show in user header', 'mpp-featured-content' ),
				'type'    => 'select',
				'options' => $this->get_header_options(),
			),
			array(
				'name'    => 'mppftc_show_in_group_header',
				'label'   => __( 'Show in group header', 'mpp-featured-content' ),
				'type'    => 'select',
				'options' => $this->get_header_options(),
			),
			array(
				'name'    => 'mppftc_show_header_media_in_lightbox',
				'label'   => __( 'Open Media in lightbox when the featured item in header is clicked.', 'mpp-featured-content' ),
				'type'    => 'select',
				'options' => array(
					1 => __( 'Yes', 'mpp-featured-content' ),
					0 => __( 'No', 'mpp-featured-content' ),
				),
				'default' => 1,
			),
			array(
				'name'  => 'mppftc_header_item_limit',
				'label' => __( 'Header item limit', 'mpp-featured-content' ),
				'type'  => 'text',
			),
		);

		$section->add_fields( $fields );
	}

	/**
	 * Get header options
	 *
	 * @return array
	 */
	private function get_header_options() {
		return array(
			'none'         => __( 'None', 'mpp-featured-content' ),
			'media_list'   => __( 'Media List', 'mpp-featured-content' ),
			'gallery_list' => __( 'Gallery List', 'mpp-featured-content' ),
		);
	}
}

new MPPFTC_Admin_Settings_Helper();

