<?php
/**
 * Plugin Name: InstagramHashViewer
 * Plugin URI: http://www.hashviewer.net/
 * Description: View and rate Instagram pictures
 * Version: 1.0.1
 * Author: Jim Frode Hoff
 * Author URI: http://jimtrim.github.io
 */

// Instagram client_id = 
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HASHVIEWER_VERSION', '3.0.1' );
define( 'HASHVIEWER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HASHVIEWER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once(plugin_dir_path( __FILE__ ) . 'classes/InstagramHashViewer.class.php');

register_activation_hook( __FILE__, array( 'InstagramHashViewer', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'InstagramHashViewer', 'plugin_deactivation' ) );

$viewer = InstagramHashViewer::get_instance();



