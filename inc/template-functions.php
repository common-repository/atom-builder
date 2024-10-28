<?php
/**
 * These functions are all template related.
 * They allow for easy overriding of any widget template the plugin provides,
 * and allow for developpers to add theme support for the Atom Builder
 *
 * @package atom-builder
 */
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );



/**
 * Template loader allowing developers to overrides the widget templates in their themes
  *
 * @param  array   $instance       The instance of the widget, to make the variable available in the template.
 * @param  string  $template_name  Name of the widget template to look for.
 * @param  bool    $load           Whether to load or just return the template file path.
 * @return string  $located        The path to the template file.
 **/
function atom_builder_get_widget_template( $instance, $template_name, $load = true ) {

    // Trim off any slashes from the template name
    $template_name = ltrim( $template_name, '/' ) . '.php';

    // Allow for swapping templates
    $template_name = apply_filters( 'atom_builder_widget_template', $template_name, $instance );

	$located = false;
	
    // Check child theme first
    if ( file_exists( STYLESHEETPATH . '/atom-builder/templates/' . $template_name ) ) {
        $located = STYLESHEETPATH . '/atom-builder/templates/' . $template_name;
        
    // Check parent theme
    } elseif ( file_exists( TEMPLATEPATH . '/atom-builder/templates/' . $template_name ) ) {
        $located = TEMPLATEPATH . '/atom-builder/templates/' . $template_name;
    
    // Take the template from the plugin
    } elseif ( file_exists( trailingslashit( atom_builder_get_templates_dir() ) . $template_name ) ) {
        $located = trailingslashit( atom_builder_get_templates_dir() ) . $template_name;
    }
    
    if ( $load && ! empty( $located ) ) {
        require( $located );
    }
 
    return $located;
 
}



/**
 * Returns the template parts directory within the plugin
 **/
function atom_builder_get_templates_dir(){
    return ATOM_BUILDER_PLUGIN_PATH . 'widgets/templates/';
}



/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function atom_builder_entry_meta() {

	// Get the date of the post.
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	// Get the month archive link.
	$month_archive_link = get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) );

	// If the post has been updated, create another time string.
	$updated_on = '';
	
	if ( get_the_date() !== get_the_modified_date() ) {
		$update_time = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		$update_time = sprintf( $update_time,
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);
		$updated_on = sprintf(
			esc_html_x( ' (Updated on %s)', 'post update date', 'atom-builder' ),
			'<a href="' . esc_url( $month_archive_link ) . '" rel="bookmark">' . $update_time . '</a>'
		);

	}

	// Build the post date meta display.
	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'atom-builder' ),
		'<a href="' . esc_url( $month_archive_link ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	// Build the category meta display.
	$categories = '';

	$categories_list = get_the_category_list( esc_html__( ', ', 'atom-builder' ) );
	if ( $categories_list ) {
		$categories = sprintf( 
			esc_html_x( ' in %s', 'Category list', 'atom-builder' ), 
			$categories_list 
		);
	}

	$output = sprintf( '<span class="posted-on">%1$s %2$s %3$s</span>',
		$posted_on,
		$updated_on,
		$categories
	);

    // Allow overriding the meta info display
    $output = apply_filters( 'atom_builder_entry_meta', $output );

    echo $output;
}



add_filter( 'the_content', 'atom_builder_render_post_widget_area' );
/**
 * This function filters the content for supported posts and
 * displays the registered widgets. 
 *
 * @param  string  $content            The original post content
 * @return string  $content/$widgets   The original content or widgets, if any is registered for this post.
 **/
function atom_builder_render_post_widget_area( $content ){
	
	// IF the current theme supports the Atom Builder, do nothing.
	if ( current_theme_supports( 'atom-builder' ) ){
		return $content;
	 }

	// If the post is not supported or we're not in the main loop, don't filter the content.
	if ( ! atom_builder_is_supported_post() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// If widgets are registered for this page, just print them. Else return standard content.
	if( atom_builder_has_registered_widgets() ){

		// Remove the filter to prevent widgets content from calling the filter again (infinite loop !)
		remove_filter( 'the_content', 'atom_builder_render_post_widget_area' );
		
		// Get the current post ID and fetch the widgets
		$post_id = (int) get_the_ID();

		ob_start();
		dynamic_sidebar( 'sidebar-post-' . $post_id );
		$widgets = ob_get_clean();

		// If there are widgets, return them.
		if ( ! empty( $widgets ) ) {
			return $widgets;
		}
	}	
	
	return $content;

}



/**
 * Custom template tag used to print out widget content for the post
 * This template tag should replace the get_template_part() function in the templates.
 * It takes the same parameters as the get_template_part() function included in WordPress
 *
 * @param  string       $slug  The slug name for the generic template.
 * @param  string|null  $name  The name of the specialized template.
 **/
function atom_builder_get_template_part( $slug, $name = null){

	if( atom_builder_has_registered_widgets() && current_theme_supports( 'atom-builder' ) ){
		$post_id = get_the_ID();
		dynamic_sidebar( 'sidebar-post-' . $post_id );
	} else {
		get_template_part( $slug, $name );
	}

}