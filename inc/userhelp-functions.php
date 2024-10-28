<?php
/**
 * This file contains functions to help users in the admin.
 * It includes documentation and other helper messages. 
 *
 * @package atom-builder
 */
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );



add_action( 'admin_head', 'atom_builder_admin_help_tab' );
/*
 * Add help section for the Atom Builder
 */
function atom_builder_admin_help_tab() {
	global $current_screen;

	// Return early if we're not on a supported post type
	if ( ! atom_builder_is_supported_post_type() ) {
		return;
	}

	// Setup help tab args.
	$args = array(
		'id'      => 'atom_builder_help_tab',
		'title'   => __( 'Atom Builder', 'atom-builder' ),
		'content' => atom_builder_get_help_tab_content(),
	);

	// Add the help tab.
	$current_screen->add_help_tab( $args );

}



/**
 * Displays the help tab content on supported post types' edit screen
 **/
function atom_builder_get_help_tab_content(){

	$post_customizer_url = atom_builder_get_post_customizer_url();

	$html = '<p>' . esc_html__( 'To help you build a custom layout for this post, you can use the Atom Builder. Simply visit this post on the front end and open up the customizer. Then open your widget tabs. You\'ll see a new widget area registered for your post.', 'atom-builder' ) . '</p>';
	$html .= '<p>' . esc_html__( 'You can use any widget or the Atom Builder\'s included widgets to build your post content with unique and more complex layout and content. Just hit \'Save and Publish\' when you\'re done.', 'atom-builder' ) . '</p>';
	$html .= '<p><a href="' . esc_url( $post_customizer_url ) . '">' . esc_html__( 'Go build this post with widgets !', 'atom-builder' ) . '</a></p>';
	
	return $html;
}



/**
 * Retrieves the url to the customizer for the given post, or the current post.
 * @param   int     $post_id  The id of the post to get the link for.
 * @return  string  $post_customizer_url  the unescaped encoded url linking to the customizer for the post passed in, or the current post. 
 **/
function atom_builder_get_post_customizer_url( $post_id = 0 ){
	
	if ( ! $post_id ){
		$post_id = get_the_ID();
	}

	$post_id = (int) $post_id;
	$customizer_url = admin_url( 'customize.php' ); 
	$post_customizer_url = add_query_arg( 'url', urlencode( get_permalink( $post_id ) ), $customizer_url );

	return apply_filters( 'atom_builder_post_customizer_url', $post_customizer_url, $post_id );

}



add_filter( 'post_row_actions', 'atom_builder_add_actions_row_customizer_link', 10, 2 );
add_filter( 'page_row_actions', 'atom_builder_add_actions_row_customizer_link', 10, 2 );
/**
 * Adds a link to the customizer in the admin posts table for supported post types.
 *
 * @param  array    $actions  Array of default action links.
 * @param  WP_Post  $post     The current post in the list.
 * @return array    $actions  The modified action links
 **/
function atom_builder_add_actions_row_customizer_link( $actions, $post ){
	
	// If the post is not supported, just return default actions.
	if ( ! atom_builder_is_supported_post( (int) $post->ID ) ){
		return $actions;
	}

	if ( current_user_can( 'edit_post', (int) $post->ID ) ){
	
		// Build link URL
		$post_customizer_url = atom_builder_get_post_customizer_url( (int) $post->ID );
		$post_customizer_link = '<a href="' . esc_url( $post_customizer_url ) . '">' . esc_html__( 'Edit in the Customizer', 'atom-builder' ) . '</a>';

		// Add the link
		$new_action = array( 'customize' => $post_customizer_link );
		$edit_action = array_splice( $actions, 0, 1 );
		$actions = array_merge( $edit_action, $new_action, $actions );

	}
	
	return $actions;
}



add_filter( 'post_updated_messages', 'atom_builder_supported_post_types_updated_messages' );
/**
 * Prints out helper messages when a supported post is saved or updated
 *
 * @param   array  $messages  Original default messages.
 * @return  array  $messages  Updated messages, with links to the customizer.
 **/
function atom_builder_supported_post_types_updated_messages( $messages ) {

	$post_id = get_the_ID();
	
	if ( atom_builder_is_supported_post( $post_id ) ) {

		// Build the link to append
		$post_customizer_url = atom_builder_get_post_customizer_url();
		$post_customizer_link = '<p><a href="' . esc_url( $post_customizer_url ) . '">' . esc_html__( 'The Atom Builder for this post is available in the customizer.', 'atom-builder' ) . '</a></p>';

		// Get the default messages for this post's post_type
		$post_type = get_post_type();
		$supported_posts_messages = $messages[$post_type];

		// Append links to the customizer
		foreach ( $supported_posts_messages as $index => $message ) {
			$supported_posts_messages[$index] .= $post_customizer_link;
		}

		// Put the modified messages back.
		$messages[$post_type] = $supported_posts_messages;

	}

	return $messages;
}
