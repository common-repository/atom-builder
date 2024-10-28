<?php
/*
Plugin Name: Atom Builder
Plugin URI:  https://wordpress.org/plugins/atom-builder
Description: This plugin enables you to painlessly build more complex layout, the WordPress Way.
Version:     1.0.2
Author:      Vincent Dubroeucq
Author URI:  https://vincentdubroeucq.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: atom-builder
Domain Path: /languages
*/

/*
Atom Builder is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Atom Builder is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Atom Builder. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/ 
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );



// Define helper constants.
define( 'ATOM_BUILDER_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ATOM_BUILDER_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );



add_action( 'plugins_loaded' , 'atom_builder_load_textdomain' );
/**
 * Load the text domain for the plugin
 */
function atom_builder_load_textdomain(){
	load_plugin_textdomain( 'atom-builder', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}



/**
 * Register the activation and deactivation hooks.
 **/
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );


// Include our main files
include( 'inc/init-functions.php' );
include( 'inc/helper-functions.php' );
include( 'inc/template-functions.php' );
include( 'inc/userhelp-functions.php' );
include( 'inc/cleanup-functions.php' );