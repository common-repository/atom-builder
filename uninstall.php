<?php
/**
 * This file is run when the plugin is uninstalled. 
 * Basically cleans up registered widgets areas.
 *
 * @package carbon-builder
 */
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

// If uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Delete all sidebars registered to use with the Carbon Builder.
carbon_builder_cleanup_sidebars();