<?php 
/**
 * Adds the Atom Builder Posts Widget.
 * This widget can be used to display up to 4 posts in your sidebar, 
 * or on your page content using the Atom Builder.
 */
class Atom_Builder_Posts_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'atom_builder_posts_widget',
			esc_html__( 'Atom Builder Posts', 'atom-builder' ),
			array(
				'classname'	=> 'atom-builder-posts-widget', 
				'description' => esc_html__( 'Displays a set of posts in your sidebar or other widgetized area.', 'atom-builder' ),
				'customize_selective_refresh' => true,
			)
		);

		// Enqueue style if widget is active (appears in a sidebar) or if in Customizer preview.
        if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'atom_builder_widget_enqueue_scripts' ) );
		}
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		// Merge defaults arguments with user-submitted settings of the instance
		$defaults = $this->atom_builder_get_posts_widget_default_settings();
		$instance = wp_parse_args( $instance, $defaults );
		$category_ids = get_categories( array( 'fields' => 'ids' ) );
		
		// Echo the standard widget wrapper and title
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . $args['after_title'];
		}

		// Setup our posts query
		$query_args = array( 
			'posts_per_page' => absint( $instance['num_posts'] ), 
			'post_type' => 'post', 
			'ignore_sticky_posts' => 1,
		);

		if ( ! empty( $instance['categories'] )  && $instance['categories'] != $category_ids ){
			$query_args['category__in'] = $instance['categories'];
		}

        $query = new WP_Query( $query_args );
		
		// Load the template
        if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();

			atom_builder_get_widget_template( $instance, 'widget-posts' );

		endwhile; endif; 
		
		wp_reset_postdata();

		// Close the wrapper
		echo $args['after_widget'];
	}



	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		// Merge defaults arguments with user-submitted settings of the instance
		$defaults = $this->atom_builder_get_posts_widget_default_settings();
		$instance = wp_parse_args( $instance, $defaults );
		$categories = get_categories( array( 'fields' => 'id=>name' ) );
		?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title :', 'atom-builder' ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</p>
			<p>
				<?php esc_html_e( 'Number of posts to display : ', 'atom-builder' ); ?><br>
				<?php for ( $i = 2; $i <= apply_filters( 'atom_builder_posts_widgets_max_posts', 6 ); $i++ ) : ?>
					<label><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'num_posts' ) ); ?>" value="<?php echo esc_attr( $i ); ?>" <?php checked( $instance['num_posts'], $i ); ?> /><?php printf( esc_html__( '%d posts', 'atom-builder' ), (int) $i );?></label><br>
				<?php endfor; ?>
			</p>
			<p>
				<?php esc_html_e( 'Categories to display posts from : ', 'atom-builder' ); ?><br>
				<?php foreach ( $categories as $id => $name ) : ?>
					<label style="display:inline-block; min-width: 45%;">
						<input type='checkbox' name="<?php echo esc_attr( $this->get_field_name( 'categories[]' ) ); ?>" value="<?php echo esc_attr( $id ); ?>" <?php checked( in_array( $id , $instance['categories'] ) ); ?> />
						<?php echo esc_html( $name ); ?>
					</label>
				<?php endforeach; ?>
			</p>

		<?php do_action( 'atom_builder_before_posts_widget_settings', $instance, $this ); ?>

			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'display_meta' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_meta' ) ); ?>" value="1" <?php checked( $instance['display_meta'], 1 ); ?> />
					<?php esc_html_e( 'Display Meta Information ?', 'atom-builder' ); ?>
				</label> 
			</p>
			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'display_thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_thumbnail' ) ); ?>" value="1" <?php checked( $instance['display_thumbnail'], 1 ); ?> />
					<?php esc_html_e( 'Display Thumbnail ?', 'atom-builder' ); ?>
				</label> 
			</p>
			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'display_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_excerpt' ) ); ?>" value="1" <?php checked( $instance['display_excerpt'], 1 ); ?> />
					<?php esc_html_e( 'Display Excerpt ?', 'atom-builder' ); ?>
				</label> 
			</p>

		<?php do_action( 'atom_builder_after_posts_widget_settings', $instance, $this );
 
	}



	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['num_posts'] = absint( $new_instance['num_posts'] );
		$instance['categories'] = array_filter( $new_instance['categories'], 'absint' );
		$instance['display_meta'] = atom_builder_sanitize_checkbox( $new_instance['display_meta'] );
		$instance['display_thumbnail'] = atom_builder_sanitize_checkbox( $new_instance['display_thumbnail'] );
		$instance['display_excerpt'] = atom_builder_sanitize_checkbox( $new_instance['display_excerpt'] );
		
		return apply_filters( 'atom_builder_posts_updated_instance', $instance );
	}



	/**
	 * Get widget default settings array
	 *
	 * @return  array  $defaults  Defaults settings for the widget
	 **/
	public function atom_builder_get_posts_widget_default_settings() {
		
		$defaults = array( 
			'title'             => '',
			'num_posts'			=> 3,
			'categories'        => get_categories( array( 'fields' => 'ids' ) ),
			'display_meta'      => 1,
			'display_thumbnail' => 1,
			'display_excerpt'   => 1, 
		);

		return apply_filters( 'atom_builder_posts_widget_default_settings', $defaults );
	}


	/**
	 * Enqueues basic layout styles for this widget.
 	 **/
	public function atom_builder_widget_enqueue_scripts() {
    
	    // Enqueue minified styles by default. Enqueue unminified styles if WP_DEBUG is set to true
		$suffix = '.min';
		if ( defined( 'WP_DEBUG' ) && 1 == constant( 'WP_DEBUG' ) ) {
			$suffix = '';
		}

		wp_enqueue_style( 'atom-builder-posts-widget-styles', plugins_url( 'css/atom-builder-posts-widget' . $suffix . '.css', __FILE__ ), array(), null );
    }

}
