<?php
/**
 * This file contains cleanup functions
 * The goal here is to reduce database clutter when posts are deleted, 
 * or when plugin is uninstalled
 *
 * @package atom-builder
 */
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );



add_action( 'after_delete_post', 'atom_builder_delete_registered_widgets' );
/**
 * Delete the widgets registered in a post's sidebar when that post is deleted
 * to avoid cluttering the database.
 *
 * @param   int   $post_id   ID of the post being deleted.
 **/
function atom_builder_delete_registered_widgets( $post_id ){
	
	if( atom_builder_has_registered_widgets( $post_id ) ){

		$sidebars_widgets = wp_get_sidebars_widgets();
		$sidebar_id = 'sidebar-page-' . $post_id; 
		unset( $sidebars_widgets[$sidebar_id] );
		wp_set_sidebars_widgets( $sidebars_widgets );
		
	}

}


/**
 * Delete all widgets registered for any supported post
 * This function is triggered when the plugin is uninstalled.
 **/
function atom_builder_cleanup_sidebars(){

	// Get all sidebars and their widgets.
	$sidebars_widgets = wp_get_sidebars_widgets();

	// Unset all widgets registered by the Atom Builder
	foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
		if ( 'sidebar-post-' == substr( $sidebar_id, 0, 13 )) {
			unset( $sidebars_widgets[$sidebar_id] );
		}
	}

	// Save the widgets
	wp_set_sidebars_widgets( $sidebars_widgets );
	
}