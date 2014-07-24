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

$viewer = new Instagram_Hash_Viewer();

class Instagram_Hash_Viewer {
	private static $instance = null;

	private $twig_loader;
	private $twig;

	public function __construct() {
		# Load Composer plugins
		require_once(plugin_dir_path( __FILE__ ) . '/vendor/autoload.php');
		$this->twig_loader = new Twig_Loader_Filesystem(plugin_dir_path( __FILE__ ) . '/views/');
		$this->twig = new Twig_Environment($this->twig_loader);

		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu_setup'));
		add_action( 'wp_enqueue_styles', array( $this, 'register_frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_forntent_scripts' ) );

		# Scripts for the admin interface

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Initial setup
	 */
	public function register_frontend_scripts() {
		wp_enqueue_script( 'hashviewer_script', plugins_url( 'js/main.js', __FILE__) );
	}
	public function register_frontend_styles() {
		wp_enqueue_style( 'hashviewer-style', plugins_url( 'css/main.css', __FILE__) );
		wp_enqueue_style( 'bootstrap-style', plugins_url( 'css/bootstrap.min.css', __FILE__) );
	}

	// Admin
	public function admin_menu_setup() {
		add_menu_page( "HashViewer", "HashViewer", 'manage_options', 
			'hashviewer_main_slug', array($this, 'settings_page'), plugin_dir_url( __FILE__ ) . '/img/menu_icon.png' );
		add_submenu_page( "hashviewer_main_slug", "HashViewer - Browse", "Browse", 'manage_options', 
			"hashviewer_browse_slug", array($this, 'browse_page') ); 
	}
	
	public function admin_init() {
		wp_enqueue_script( 'hashviewer_script', plugins_url( 'js/main.js', __FILE__) ); 

		wp_register_style( 'bootstrap-style', plugins_url( 'css/bootstrap.min.css', __FILE__) ); 
		wp_register_style( 'hashviewer-style', plugins_url( 'css/main.css', __FILE__) );

		wp_enqueue_style( 'bootstrap-style' );
		wp_enqueue_style( 'hashviewer-style' );

	}
	public function register_admin_scripts() {

	}
	public function register_admin_styles() {
	}

	/**
	 * Views
	 */
	
	public function settings_page() {
		echo $this->twig->render('main.twig.html', array("data" => $this->getAllCompetitions()) );

	}

	public function browse_page() {
		echo $this->twig->render('browse.twig.html');
	}

	public function activate() {
		if (get_option( "ihw_db_version", "Missing" ) == "Missing") {
			$this->db_install();
			add_option( "ihw_db_version", "0.2");
		}
	}

	public function deactivate() {
		delete_option("ihw_db_version" );
	}

	/**
	 * DB functions 
	 */
	public function getAllCompetitions() {
		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";	
		$sql = "SELECT active, title, startTime, endTime, hashtags, winnerSubmissionId 
				FROM $table_name;";

		$rows = $wpdb->get_results( $sql );
		return $rows;
	}

	public function create_competition($title, $hashtags, $startTime, $endTime) {
		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";	
		//$sql = "SELECT active, startTime, endTime, hashtags, winnerSubmissionId 
		//		FROM $table_name;";

		$rows_affected = $wpdb->insert( $table_name, 
			array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
		return $rows;
	}



	private function db_install() {
		global $wpdb;

		// come back to me if having \only\ 9 999 999 competitions is a problem
		$competition_table_name = $wpdb->prefix . "hashviewer_competition";		
		$competition_sql = "CREATE TABLE " . $competition_table_name . "(
			id 					mediumint(9) NOT NULL AUTO_INCREMENT,
			title 				VARCHAR(50) NOT NULL,
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

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $submission_sql );
		dbDelta( $competition_sql );
	}

	/**
	 * Util
	 */
	public function filter_hashtags($tags) {
		return "";
	}
}


