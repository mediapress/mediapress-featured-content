<?php
/**
 * Widget class to list featured gallery
 *
 * @package mediapress-featured-content
 */

// Exit if file access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MPPFTC_Gallery_widget
 */
class MPPFTC_Gallery_widget extends WP_Widget {

	/**
	 * The constructor.
	 *
	 * @param string $name Widget name.
	 * @param array  $widget_options Widget other options.
	 */
	public function __construct( $name = '', $widget_options = array() ) {

		if ( empty( $name ) ) {
			$name = __( '( MediaPress ) Featured Gallery', 'mpp-featured-content' );
		}

		parent::__construct( false, $name, $widget_options );
	}

	/**
	 * Render widget content
	 *
	 * @param array $args Widget array of other info.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {

		$component    = $instance['component'];
		$component_id = '';

		if ( 'groups' === $component ) {
			$component_id = bp_get_current_group_id();
		} elseif ( 'members' === $component ) {
			$component_id = ( 'displayed' == $instance['user_type'] ) ? bp_displayed_user_id() : bp_loggedin_user_id();
		}

		if ( empty( $component_id ) ) {
			return;
		}

		$query_args = array(
			'component'    => $instance['component'],
			'component_id' => $component_id,
			'status'       => $instance['status'],
			'type'         => $instance['type'],
			'order'        => 'DESC', // order.
			'orderby'      => 'date',
			'meta_key'     => '_mppftc_featured',
			'meta_value'   => 1,
			'post_status'  => 'inherit',
		);

		/**
		 * The query object is used in the included file.
		 */
		$query = new MPP_Gallery_Query( $query_args );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

		require mpp_locate_template( array( 'loop-featured-gallery.php' ), false, mppftc_featured_content()->get_path() . 'templates/widgets' );

		echo $args['after_widget']; ?>

		<?php
	}

	/**
	 * Update widget settings
	 *
	 * @param array $new_instance New settings values.
	 * @param array $old_instance Old settings values.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                = $old_instance;
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['component']   = $new_instance['component'];
		$instance['status']      = $new_instance['status'];
		$instance['type']        = $new_instance['type'];
		$instance['max_to_list'] = $new_instance['max_to_list'];
		$instance['order_by']    = $new_instance['order_by'];
		$instance['order']       = $new_instance['order'];
		$instance['user_type']   = $new_instance['user_type'];

		return $instance;
	}

	/**
	 * Render widget settings form
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		$defaults = array(
			'title'       => __( 'Featured Galleries', 'mpp-featured-content' ),
			'component'   => 'members',
			'status'      => 'public',
			'type'        => 'photo',
			'max_to_list' => 5,
			'user_type'   => 'displayed',
			'order_by'    => 'title',
			'order'       => 'ASC',
		);

		$instance    = wp_parse_args( (array) $instance, $defaults );
		$title       = strip_tags( $instance['title'] );
		$component   = $instance['component'];
		$status      = $instance['status'];
		$type        = $instance['type'];
		$max_to_list = strip_tags( $instance['max_to_list'] );
		$user_type   = $instance['user_type'];
		$order_by    = $instance['order_by'];
		$order       = $instance['order'];

		?>

        <p>
            <label>
				<?php _e( 'Title:', 'mpp-featured-content' ); ?>
                <input class="mppftc-input" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>"/>
            </label>
        </p>

        <p>
			<?php _e( 'List media of user:', 'mpp-featured-content' ); ?>

			<?php foreach ( mppftc_show_media_of() as $key => $label ) : ?>

                <label>
                    <input name="<?php echo $this->get_field_name( 'user_type' ); ?>" type="radio"
                           value="<?php echo $key; ?>" <?php checked( $key, $user_type ); ?>/>
					<?php echo $label; ?>
                </label>

			<?php endforeach; ?>
        </p>

        <p>
			<?php

			$components = mppftc_get_components();

			_e( 'Select Component:', 'mpp-featured-content' );

			?>

			<?php if ( ! empty( $components ) ) : ?>

                <select name="<?php echo $this->get_field_name( 'component' ); ?>">
					<?php foreach ( $components as $key => $label ) : ?>
                        <option value="<?php echo $key ?>" <?php selected( $component, $key ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
                </select>

			<?php else: ?>

				<?php _e( 'No active component', 'mpp-featured-content' ); ?>

			<?php endif; ?>
        </p>

        <p>
			<?php

			$types = mppftc_get_types();

			_e( 'Select Type:', 'mpp-featured-content' );

			?>

			<?php if ( ! empty( $types ) ) : ?>

                <select name="<?php echo $this->get_field_name( 'type' ); ?>">

					<?php foreach ( $types as $key => $label ) : ?>
                        <option value="<?php echo $key ?>" <?php selected( $type, $key ) ?>>
							<?php echo $label; ?>
                        </option>

					<?php endforeach; ?>

                </select>

			<?php else: ?>

				<?php _e( 'No Active Type!', 'mpp-featured-content' ); ?>

			<?php endif; ?>
        </p>

        <p>
			<?php

			$active_status = mpp_get_active_statuses();

			_e( 'Select Status:', 'mpp-featured-content' );

			?>

			<?php if ( ! empty( $active_status ) ) : ?>

                <select name="<?php echo $this->get_field_name( 'status' ); ?>">

					<?php foreach ( $active_status as $key => $label ) : ?>

                        <option value="<?php echo $key ?>" <?php selected( $status, $key ); ?>>
							<?php echo $label->label; ?>
                        </option>

					<?php endforeach; ?>

                </select>

			<?php endif; ?>
        </p>

        <p>
            <label>
				<?php _e( 'Max media to show:', 'mpp-featured-content' ); ?>
                <input type="number" name="<?php echo $this->get_field_name( 'max_to_list' ); ?>"
                       value="<?php echo $max_to_list; ?>"/>
            </label>
        </p>

        <p>
            <label>
				<?php _e( 'Sort Order:', 'mpp-featured-content' ); ?>
                <select id="<?php echo $this->get_field_id( 'order' ); ?>"
                        name="<?php echo $this->get_field_name( 'order' ); ?>">
                    <option
                            value="ASC" <?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'mpp-featured-content' ); ?></option>
                    <option
                            value="DESC" <?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'mpp-featured-content' ); ?></option>
                </select>
            </label>

        </p>

        <p>
            <label>
				<?php _e( 'Order By:', 'mpp-featured-content' ); ?>
                <select id="<?php echo $this->get_field_id( 'order_by' ); ?>"
                        name="<?php echo $this->get_field_name( 'order_by' ); ?>">
                    <option
                            value="title" <?php selected( 'title', $order_by ); ?>><?php _e( 'Alphabet', 'mpp-featured-content' ); ?></option>
                    <option
                            value="date" <?php selected( 'date', $order_by ); ?>><?php _e( 'Date', 'mpp-featured-content' ); ?></option>
                    <option
                            value="rand" <?php selected( 'rand', $order_by ); ?>><?php _e( 'Random', 'mpp-featured-content' ); ?></option>
                </select>
            </label>
        </p>

		<?php
	}
}

/**
 * Register gallery widget
 */
function mppftc_register_gallery_widget() {
	register_widget( 'MPPFTC_Gallery_widget' );
}

add_action( 'mpp_widgets_init', 'mppftc_register_gallery_widget' );

