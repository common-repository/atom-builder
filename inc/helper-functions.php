<?php
/**
 * This file contains helper functions
 * not related directly to templating or managing widgets.
 *
 * @package atom-builder
 */
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );



/**
 * Gets an array of supported post types.
 * By default, the Atom Builder is usable with basic pages. 
 *
 * @return   array   $supported_post_types   Array of supported post types
 **/
function atom_builder_get_supported_post_types(){
	
	$supported_post_types = array( 'page' ); 
	
	return apply_filters( 'atom_builder_supported_post_types', $supported_post_types );

}



/**
 * Get an array of all post ids supported by the Atom Builder.
 *
 * @return   array   $supported_post_ids   An array of post IDs
 **/
function atom_builder_get_supported_posts_ids(){
    
	$args = array(
        'post_type'      => atom_builder_get_supported_post_types(),
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
    );
	
	$supported_post_ids = get_posts( $args );
	
	return apply_filters( 'atom_builder_supported_posts_ids', $supported_post_ids );
}



/**
 * Returns a boolean indicating whether the post type passed in is supported.
 *
 * @param    string   $post_type      Post type you want to check support for. If not provided, checks the current post.
 * @return   bool     $is_supported   True for supported post types
 **/
function atom_builder_is_supported_post_type( $post_type = null ){
	
	if ( null == $post_type ){
		$post_type = get_post_type();
	}

	return in_array( $post_type, atom_builder_get_supported_post_types() );

}



/**
 * Returns a boolean indicating whether the post id passed in is supported.
 *
 * @param    int     $post_id        Post id you want to check support for. If not provided, checks the current post.
 * @return   bool    $is_supported   True for supported post types
 **/
function atom_builder_is_supported_post( $post_id = 0 ){
	
	if ( ! $post_id ){
		$post_id = (int) get_the_ID();
	}

	return in_array( (int) $post_id, atom_builder_get_supported_posts_ids() );

}



/**
 * Returns a boolean indicating whether the post passed in has registered widgets
 *
 * @param    int     $post_id        Post id you want to check. If not provided, checks the current post.
 * @return   bool    $has_widgets    True if widgets are registerd for this post
 **/
function atom_builder_has_registered_widgets( $post_id = 0 ){
	
	if ( ! $post_id ){
		$post_id = (int) get_the_ID();
	}

	return is_active_sidebar( 'sidebar-post-' . (int) $post_id );

}



/**
 * Sanitize a checkbox value.
 *
 * @param  string    $value     The value of the setting to sanitize.
 * @param  object    $setting   The instance of the customizer setting, if used in the customizer
 * @return string    $value     The sanitized value.
 */
function atom_builder_sanitize_checkbox( $value, $setting = null ){
	
	$valid = array( 0, 1, '', 'on');
	
	if ( in_array( $value, $valid ) ){
		return $value;
	}

	return '';
}




/**
 * Sanitize a widget setting's radio input's value.
 *
 * @param  string    $value     The value of the input to sanitize.
 * @param  array     $valid     An array of valid options.
 * @return string    $value     The sanitized value.
 */
function atom_builder_sanitize_radio( $value, $valid ) {

	if ( array_key_exists( $value, $valid ) ) {
		return $value;
	}

	return '';	

}