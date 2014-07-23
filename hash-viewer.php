<?php
/**
 * Plugin Name: InstagramHashViewer
 * Plugin URI: http://www.hashviewer.net/
 * Description: View and rate Instagram pictures
 * Version: 1.0
 * Author: Jim Frode Hoff
 * Author URI: http://jimtrim.github.io
 */

if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'admin_menu', array('Instagram_Hash_Viewer', 'plugin_menu'));
// register_uninstall_hook( __FILE__, array( 'Plugin_Class_Name', 'uninstall' ) );

class Instagram_Hash_Viewer {
	private static $instance = null;
	private static $values = array(
		'title' => "Instagram HashViewer",
		'menu_title' => "Instagram HashViewer",
		'identifier' => "instagram_page_slug"
	);

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
		register_activation_hook( __FILE__, array( 'Instagram_Hash_Viewer', 'activate' ) );
		register_deactivation_hook( __FILE__, array( 'Instagram_Hash_Viewer', 'deactivate' ) );
	}

	public static function plugin_menu() {
		$opt = self::$values;
		add_media_page( $opt['title'], $opt['menu_title'], 'manage_options', 
			$opt['identifier'], array('Instagram_Hash_Viewer', 'settings_page') ); //TODO find a better placement
	} 

	public static function settings_page() {
		include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );;
	}

	public function register_plugin_scripts() {
		// does nothing atm, but this is the OOP way to do things
		wp_enqueue_script( 'hashviewer_script', plugins_url( 'hash-viewer/js/main.js' ));
	}	
	public function register_plugin_styles() {
		wp_enqueue_style( 'instagram-gallery', plugins_url( 'hash-viewer/css/main.css' ) );
	}

	public function activate() {
		// TODO: add checking for the database table
		$this->hashviewer_install();
	}

	public function deactivate() {}

	private function hashviewer_install() {
		global $wpdb;

		$table_name = $wpdb->prefix . "hashviewer";	
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time";

	}

}


