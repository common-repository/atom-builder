<?php 
/**
 * Adds Atom Builder Page Widget.
 * This widget can be used to feature a page content in your sidebar, 
 * or on your page content using the Atom Builder.
 */ 
class Atom_Builder_Page_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'atom_builder_page_widget', 					          
			esc_html__( 'Atom Builder Page', 'atom-builder' ),
			array(
				'classname'	=> 'atom-builder-page-widget', 
				'description' => esc_html__( 'Features a page content with simple display options', 'atom-builder' ),
				'customize_selective_refresh' => true,
			) );

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
	 * @param array $args       Widget arguments.
	 * @param array $instance   Saved values from database.
	 */
	public function widget( $args, $instance ) {

		// Merge defaults arguments with user-submitted settings of the instance
		$defaults = $this->atom_builder_get_page_widget_default_settings();
		$instance = wp_parse_args( $instance, $defaults );

		// Echo the standard widget wrapper and title
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Prepare the query
		if ( empty( $instance['selected_page']) ){
			$instance['selected_page'] = get_the_ID();
		}

		$query = new WP_Query(  
			array( 'page_id' => $instance['selected_page'], 'post_type' => 'page')
		);

		// Load the template
		if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
		
			atom_builder_get_widget_template( $instance, 'widget-page' );

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
		$defaults = $this->atom_builder_get_page_widget_default_settings();
		$instance = wp_parse_args( $instance, $defaults );
		?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title :', 'atom-builder' ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'selected_page' ) ); ?>"><?php esc_html_e( 'Choose a page :', 'atom-builder' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'selected_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'selected_page' ) ); ?>" style="display: block; width: 100%;">
					
					<?php
						if ( empty( $instance['selected_page'] ) ) {
							echo '<option>' . esc_html( 'Choose a page', 'atom-builder' ) . '</option>';
						}

						// Get the list of pages and display an option list.
						$post_ids = get_posts( array( 'post_type' => 'page', 'fields' => 'ids', 'posts_per_page' => -1 ) );
						
						foreach ( $post_ids as $post_id ) {
							echo '<option value="' . esc_attr( (int) $post_id ) . '" ' . selected( $instance['selected_page'], $post_id, false ). '">' . esc_html( get_the_title( $post_id ) ) . '</option>';
						}
					?>

				</select>
			</p>

			<?php do_action( 'atom_builder_before_page_widget_settings', $instance, $this ); ?>
			
			<p>
				<?php esc_html_e( 'Thumbnail : ', 'atom-builder' ); ?><br>
				<?php $this->atom_builder_widget_radio_setting_html( $instance, 'thumbnail_option' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Content size : ', 'atom-builder' ); ?><br>
				<?php $this->atom_builder_widget_radio_setting_html( $instance, 'content_size' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Text style : ', 'atom-builder' ); ?><br>
				<?php $this->atom_builder_widget_radio_setting_html( $instance, 'text_style' ); ?>
			</p>
			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'display_full_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_full_page' ) ); ?>" value="1" <?php checked( $instance['display_full_page'], '1' ); ?> />
					<?php esc_html_e( 'Display full page ? ', 'atom-builder' );?>
				</label>
			</p>

			<?php do_action( 'atom_builder_after_page_widget_settings', $instance, $this );

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
		$instance['selected_page'] = absint( $new_instance['selected_page'] );
		$instance['thumbnail_option'] = $this->atom_builder_sanitize_page_widget_option( $new_instance['thumbnail_option'], 'thumbnail_option' );
		$instance['content_size'] = $this->atom_builder_sanitize_page_widget_option( $new_instance['content_size'], 'content_size' );
		$instance['text_style'] = $this->atom_builder_sanitize_page_widget_option( $new_instance['text_style'], 'text_style');
		$instance['display_full_page'] = atom_builder_sanitize_checkbox( $new_instance['display_full_page'] );

		return apply_filters( 'atom_builder_page_widget_updated_instance', $instance, $new_instance );
	}



	/**
	 * Get widget default settings array
	 *
	 * @return  array  $defaults  Defaults settings for the widget
	 **/
	public function atom_builder_get_page_widget_default_settings() {
		
		$defaults = array(
			'title'             => '',
			'selected_page'     => '',
			'thumbnail_option'  => 'left-thumbnail',
			'content_size'      => 'one-half',
			'text_style'        => 'text-left',
			'display_full_page' => '',
		);

		return apply_filters( 'atom_builder_builder_page_widget_default_settings', $defaults );
	}

	

	/**
	 * Gets an array of registered options for a given widget's setting.
	 *
	 * @param    string   $setting              The widget setting to retrieve registered values for.
	 * @return   array    $registered_options   An array of registered option for the given setting
	 **/
	public function atom_builder_get_page_widget_registered_options( $setting = '' ){

		switch ( $setting ) {

			case 'thumbnail_option':
				$registered_thumbnail_options = array(
					'no-thumbnail'    => __( 'No thumbnail', 'atom-builder' ),
					'left-thumbnail'  => __( 'Left thumbnail', 'atom-builder' ),
					'right-thumbnail' => __( 'Right thumbnail', 'atom-builder' ),
				);
				return apply_filters( 'atom_builder_page_widget_thumbnail_options', $registered_thumbnail_options );
				break;
			
			case 'content_size':
				$registered_content_size_options = array(
					'one-third'    => __( 'One third', 'atom-builder' ),
					'one-half'     => __( 'One half', 'atom-builder' ),
					'two-thirds'   => __( 'Two thirds', 'atom-builder' ),
				);
				return apply_filters( 'atom_builder_page_widget_content_size_options', $registered_content_size_options );
				break;
			
			case 'text_style':
				$registered_text_style_options = array(
					'text-center'    => __( 'Centered text', 'atom-builder' ),
					'text-left'      => __( 'Left-aligned', 'atom-builder' ),
					'text-right'     => __( 'Right-aligned', 'atom-builder' ),
				);
				return apply_filters( 'atom_builder_page_widget_text_style_options', $registered_text_style_options );
				break;

			default:
                return array();
				break;
		}

	}



	/**
	 * Sanitize the page widget options.
	 *
	 * @param  string    $value     The value of the option to sanitize.
	 * @param  object    $setting   The name of the widget setting.
	 * @return string    $value     The sanitized value.
	 */
	public function atom_builder_sanitize_page_widget_option( $value, $setting ) {

		$valid = $this->atom_builder_get_page_widget_registered_options( $setting );
		
		return atom_builder_sanitize_radio( $value, $valid );	

	}



	/**
	 * Prints out a set of radio inputs for the given setting.
	 *
	 * @param    string   $setting   The registered setting you want to output radio buttons for.
	 * @param    array    $instance  The current instance of the widget.
	 **/
	public function atom_builder_widget_radio_setting_html( $instance, $setting ){
		
		// Get an array of registered options
		$options = $this->atom_builder_get_page_widget_registered_options( $setting );

		// Loop throught the options and create the radio buttons.
		foreach ( $options as $value => $label ) {
			echo '<label><input type="radio" name="' . esc_attr( $this->get_field_name( $setting ) ) . '" value="' . esc_attr( $value ) . '" ' . checked( $instance[$setting], $value, false ) . ' />' . esc_html( $label ) . '</label><br>';
		}
		
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

        wp_enqueue_style( 'atom-builder-post-page-widget-styles', plugins_url( 'css/atom-builder-post-page-widget' . $suffix . '.css', __FILE__ ), array(), null );
    
	}

}
