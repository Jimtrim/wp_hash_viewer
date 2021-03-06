<?php
/**
 * Plugin Name: Instagram HashViewer
 * Plugin URI: http://www.hashviewer.net/
 * Description: View and rate Instagram pictures
 * Version: 1.0.2
 * Author: Jim Frode Hoff
 * Author URI: http://jimtrim.github.io
 */

// Instagram client_id =
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HASHVIEWER_VERSION', '3.0.1' );
define( 'HASHVIEWER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HASHVIEWER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once plugin_dir_path( __FILE__ ) . 'classes/HashViewer.class.php';
$viewer = HashViewer::get_instance();

register_activation_hook( __FILE__, array( 'HashViewer', 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( 'HashViewer', 'plugin_deactivate' ) );
register_uninstall_hook( __FILE__, array( 'HashViewer', 'plugin_uninstall' ) );


// Register AJAX actions
add_action( 'wp_ajax_save_image', array( $viewer, 'save_image' ) );
add_action( 'wp_ajax_get_saved_images_list', array( $viewer, 'get_saved_images_list' ) );
