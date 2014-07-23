<?php
/**
 * Plugin Name: InstagramHashViewer
 * Plugin URI: http://www.hashviewer.net/
 * Description: View and rate Instagram pictures
 * Version: 1.0
 * Author: Jim Frode Hoff
 * Author URI: http://jimtrim.github.io
 */


// Instagram client_id = 
if ( ! defined( 'ABSPATH' ) ) exit;

$viewer = new Instagram_Hash_Viewer();
$viewer->db_install();

// register_uninstall_hook( __FILE__, array( 'Plugin_Class_Name', 'uninstall' ) );

class Instagram_Hash_Viewer {
	private static $instance = null;
	private static $values = array(
		'title' => "Instagram HashViewer",
		'menu_title' => "Instagram HashViewer",
		'identifier' => "instagram_page_slug"
	);

	public function __construct() {
		add_action( 'admin_menu', array($this, 'plugin_menu'));
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	public function plugin_menu() {
		$opt = self::$values;
		add_media_page( $opt['title'], $opt['menu_title'], 'manage_options', 
			$opt['identifier'], array($this, 'settings_page') ); //TODO find a better placement
	} 

	public function settings_page() {
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
		$this->db_install();
	}

	public function deactivate() {}

	public function db_install() {
		global $wpdb;

		// come back to me if having \only\ 9 999 999 competitions is a problem
		$competition_table_name = $wpdb->prefix . "hashviewer_competition";		
		$competition_sql = "CREATE TABLE " . $competition_table_name . "(
			id 					mediumint(9) NOT NULL AUTO_INCREMENT,
			active 				BOOL,
			startTime			DATETIME,
			endTime				DATETIME,
			hashtags			VARCHAR(255),
			winnerSubmissionId	mediumint(12),
			PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_unicode_ci";

		// ... and each of those competitions have over 1000 approved submission
		$submission_table_name = $wpdb->prefix . "hashviewer_submission";
		$submission_sql = "CREATE TABLE " . $submission_table_name . "(
			id 					mediumint(12) NOT NULL AUTO_INCREMENT,
			instagramUsername 	varchar(30), -- 30 is a limitation from Instagram
			instagramMediaID 	VARCHAR(255),
			instagramImage 		VARCHAR(255),
			tags 				VARCHAR(255),
			caption 			TEXT,
			approved 			Bool,
			createdAt 			DATETIME, -- The time when the image was uploaded to Instagram 
			PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

	}

}


