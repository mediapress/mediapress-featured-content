<?php
/**
 * Plugin admin settings helper class
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly
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

		$fields = array(
			array(
				'name'    => 'mppftc_enabled_for',
				'label'   => __( 'Enable for', 'mediapress-featured-content' ),
				'type'    => 'multicheck',
				'options' => array(
					'media'   => __( 'Media', 'mediapress-featured-content' ),
					'gallery' => __( 'Gallery', 'mediapress-featured-content' ),
				),
			),
			array(
				'name'    => 'mppftc_enabled_components',
				'label'   => __( 'Select components', 'mediapress-featured-content' ),
				'type'    => 'multicheck',
				'options' => mppftc_get_components(),
			),
			array(
				'name'    => 'mppftc_enabled_types',
				'label'   => __( 'Select types', 'mediapress-featured-content' ),
				'type'    => 'multicheck',
				'options' => mppftc_get_types(),
			),
			array(
				'name'    => 'mppftc_button_ui_places',
				'label'   => __( 'Where to show mark featured button', 'mediapress-featured-content' ),
				'type'    => 'multicheck',
				'options' => array(
					'single_media'   => __( 'Single Media Page', 'mediapress-featured-content' ),
					'light_box'      => __( 'LightBox', 'mediapress-featured-content' ),
					'single_gallery' => __( 'Single Gallery', 'mediapress-featured-content' ),
					'gallery_home'   => __( 'Gallery home', 'mediapress-featured-content' ),
				),
			),
			array(
				'name'    => 'mppftc_show_in_user_header',
				'label'   => __( 'Show in user header', 'mediapress-featured-content' ),
				'type'    => 'select',
				'options' => $this->get_header_options(),
			),
			array(
				'name'    => 'mppftc_show_in_group_header',
				'label'   => __( 'Show in group header', 'mediapress-featured-content' ),
				'type'    => 'select',
				'options' => $this->get_header_options(),
			),
			array(
				'name'  => 'mppftc_header_item_limit',
				'label' => __( 'Header item limit', 'mediapress-featured-content' ),
				'type'  => 'text',
			),
		);

		$panel->add_section( 'mppftc-settings', __( 'MediaPress Featured Settings', 'mediapress-featured-content' ) )->add_fields( $fields );
	}

	/**
	 * Get header options
	 *
	 * @return array
	 */
	private function get_header_options() {
		return array(
			'none'         => __( 'None', 'mediapress-featured-content' ),
			'media_list'   => __( 'Media List', 'mediapress-featured-content' ),
			'gallery_list' => __( 'Gallery List', 'mediapress-featured-content' ),
		);
	}
}

new MPPFTC_Admin_Settings_Helper();

