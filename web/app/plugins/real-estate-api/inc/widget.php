<?php
/**
 * Real Estate Filter Widget
 */

class RealEstate_Filter_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'realestate_filter_widget',
			__( 'Фільтр обʼєктів нерухомості', 'realestate-api-domain' ),
			[
				'description' => __( 'Відображає форму фільтрації для обʼєктів нерухомості', 'realestate-api-domain' ),
			]
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		echo '<div class="realestate-widget">';
		include plugin_dir_path( __FILE__ ) . 'views/filter-form-widget.php';
		echo '<div id="realestate-results" class="mt-4"></div>';
		echo '</div>';

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Фільтр обʼєктів', 'realestate-api-domain' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Заголовок:', 'realestate-api-domain' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
				   value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = [];
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}
}

function register_realestate_filter_widget() {
	register_widget( 'RealEstate_Filter_Widget' );
}

add_action( 'widgets_init', 'register_realestate_filter_widget' );
