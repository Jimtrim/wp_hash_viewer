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

register_activation_hook( __FILE__, array( 'HashViewer', 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( 'HashViewer', 'plugin_deactive' ) );
register_uninstall_hook( __FILE__, array( 'HashViewer', 'plugin_uninstall' ) );

$viewer = HashViewer::get_instance();


add_action( 'admin_footer', array( $viewer, 'test_ajax' ) );


function my_action_javascript() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {

	var data = {
		'action': 'my_action',
		'whatever': 1234
	};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	$.post(ajaxurl, data, function(response) {
		console.log('Got this from the server: ' + response);
	});
});
</script>
<?php
}
