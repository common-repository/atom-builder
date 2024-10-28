<?php
/**
 * This set of functions implements the Atom Builder.
 * Basicallay registers a widgetized area for all supported post types,
 * allowing users to replace their content with easy to use widgets.
 *
 * @package atom-builder
 */
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );



add_action( 'widgets_init', 'atom_builder_init' );
/**
 * Registers the widget area for each entry of a post type the Atom Builder supports, only in the customizer.
 **/
function atom_builder_init(){

    // If we're not in the admin, register a widget area for all supported posts.
	if( ! is_admin() || is_customize_preview() ){
		
		$post_ids = atom_builder_get_supported_posts_ids();

		if ( $post_ids ){
			array_map( 'atom_builder_register_widget_area', $post_ids );
		}

	}

	// Register our custom widgets.
	atom_builder_register_widgets();

	do_action( 'atom_builder_after_init' );
}



/**
 * Registers widgetized area for all posts supported by the Atom Builder
 * 
 * @param int   $post_id   The ID of the post to register a widgetized area for.
 **/
function atom_builder_register_widget_area( $post_id ){

	$sidebar_args = apply_filters( 'atom_builder_sidebar_args', array(
		'name'          => sprintf( esc_html__( 'Widgets for %s', 'atom-builder' ), get_the_title( $post_id ) ),
		'id'            => 'sidebar-post-' . $post_id,
		'description'   => esc_html__( 'Add widgets you want in your content here.', 'atom-builder' ),
		'before_widget' => '<section id="%1$s" class="atom-builder-section"><div class="widget atom-builder-widget %2$s">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
	register_sidebar( $sidebar_args );

}



/**
 * Loads and registers custom widgets for the Atom Builder
 **/
function atom_builder_register_widgets(){

	require ATOM_BUILDER_PLUGIN_PATH . 'widgets/atom-page.php';
	require ATOM_BUILDER_PLUGIN_PATH . 'widgets/atom-post.php';
	require ATOM_BUILDER_PLUGIN_PATH . 'widgets/atom-posts.php';
	register_widget( 'Atom_Builder_Page_Widget' );
	register_widget( 'Atom_Builder_Post_Widget' );
	register_widget( 'Atom_Builder_Posts_Widget' );

}



add_filter( 'body_class', 'atom_builder_body_classes' );
/**
 * Adds atom-builder class to the array of body classes when widgets are registered
 *
 * @param  array  $classes  Classes for the body element.
 * @return array  $classes  Modified body classes
 */
function atom_builder_body_classes( $classes ) {

	if ( atom_builder_has_registered_widgets() ) {
		$classes[] = 'atom-builder';
	}

	return $classes;
}



add_action( 'wp_enqueue_scripts', 'atom_builder_scripts' );
/**
 * Enqueue basic layout stylesheet for the builder
 **/
function atom_builder_scripts(){

	// Enqueue minified styles by default. Enqueue unminified styles if WP_DEBUG is set to true
	$suffix = '.min';
	if ( defined( 'WP_DEBUG' ) && 1 == constant( 'WP_DEBUG' ) ) {
		$suffix = '';
	}

	if( ! is_admin() && atom_builder_has_registered_widgets() ){
		wp_enqueue_style( 'atom-builder-styles', ATOM_BUILDER_PLUGIN_URL . 'css/atom-builder' . $suffix . '.css', array(), null );
	}
	
}




// add_action( 'init', 'atom_builder_parse_theme_support_default' );
/**
 * Parses theme support defaults arguments if theme does support the Atom Builder
 */
function atom_builder_parse_theme_support_default(){
	
	// If the theme doesn't support the Atom Builder, just return
	if( ! current_theme_supports( 'atom-builder' ) ){
		return false;
	}

	// Get the array of arguments passed in to the add_theme_support function call, if any.
	$args = get_theme_support( 'atom-builder' );

	if ( is_array( $args ) ){
		$args = $args[0];
	} else {
		$args = array();
	}

	// Merge them with default args.
	$defaults = atom_builder_get_theme_supports_default_args();
	$args = wp_parse_args( $args, $defaults );
	
	// Update the add_theme_support call.
	add_theme_support( 'atom-builder', apply_filters( 'atom_builder_theme_support_args' , $args ) );
}



/**
 * Parses theme support defaults arguments if theme does support the Atom Builder
 */
function atom_builder_get_theme_supports_default_args(){
	
	$defaults = array();

	return $defaults;
}
