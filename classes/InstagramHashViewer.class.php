<?php
class InstagramHashViewer {
	private static $instance = null;

	private $twig;
<<<<<<< HEAD
=======
	private $slugs;
>>>>>>> dev

	public static function get_instance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self;
		return self::$instance;
	}

	private function __construct() {

		// Load Composer plugins
		require_once(HASHVIEWER_PLUGIN_DIR. '/vendor/autoload.php');
		$loader = new Twig_Loader_Filesystem(HASHVIEWER_PLUGIN_DIR . '/views/');
		$this->twig = new Twig_Environment($loader);

		// Add default actions
		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu_setup'));
		add_action( 'wp_enqueue_styles', array( $this, 'register_frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ) );
<<<<<<< HEAD
=======

		$this->slugs = array(
			'main' => 'hashviewer_main',
			'new_competition' => 'hashviewer_new_competition',
			'browse' => 'hashviewer_browse'
		);
>>>>>>> dev
	}

	/**
	 * Initial setup
	 */
	// Frontend
	public function register_frontend_scripts() {
		wp_enqueue_script( 'hashviewer_script', HASHVIEWER_PLUGIN_URL . 'js/main.js' );
	}
	public function register_frontend_styles() {
		wp_enqueue_style( 'hashviewer-style', HASHVIEWER_PLUGIN_URL . 'css/main.css' );
		wp_enqueue_style( 'bootstrap-style', HASHVIEWER_PLUGIN_URL . 'css/bootstrap.min.css' );
	}

	// Admin
	public function admin_menu_setup() {
		add_menu_page( "HashViewer", "HashViewer", 'manage_options', 
<<<<<<< HEAD
			'hashviewer_main', array($this, 'all_competitions'), HASHVIEWER_PLUGIN_DIR . '/img/menu_icon.png' );
		add_submenu_page( "hashviewer_main", "HashViewer - Browse", "All Competitions", 'manage_options',
			"hashviewer_main"); // main menu item
		add_submenu_page( "hashviewer_main", "HashViewer - New competition", "New competition", 'manage_options',
			"hashviewer_new_competition", array($this, 'new_competition') );
		add_submenu_page( "hashviewer_main", "HashViewer - Browse", "Browse Instagram", 'manage_options',
			"hashviewer_browse", array($this, 'view') );
=======
			$this->slugs['main'], array($this, 'all_competitions'), HASHVIEWER_PLUGIN_URL . '/img/menu_icon.png' );
		add_submenu_page( $this->slugs['main'], "HashViewer - Browse", "All Competitions", 'manage_options',
			$this->slugs['main']); // main menu item
		add_submenu_page( "hashviewer_main", "HashViewer - New competition", "New competition", 'manage_options',
			$this->slugs['new_competition'], array($this, 'new_competition') );
		add_submenu_page( "hashviewer_main", "HashViewer - Browse", "Browse Instagram", 'manage_options',
			$this->slugs['browse'], array($this, 'browse') );
>>>>>>> dev
	}
	
	public function admin_init() {
		wp_enqueue_script( 'hashviewer_script', HASHVIEWER_PLUGIN_URL . 'js/main.js' );

		wp_register_style( 'bootstrap-style', HASHVIEWER_PLUGIN_URL . 'css/bootstrap.min.css' );
		wp_register_style( 'hashviewer-style', HASHVIEWER_PLUGIN_URL . 'css/main.css' );

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
	public function all_competitions() {
		$data = array(
			"plugin_url"	=> HASHVIEWER_PLUGIN_URL,
			"competitions" 	=> $this->getAllCompetitions() 
		);
		echo $this->twig->render('all_competitions.twig.html', $data);
	}
	public function new_competition() {

		if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
			$this->create_new_competition();
			$return_url = "";
<<<<<<< HEAD
			echo $this->twig->render('competition_created.twig.html', $return_url);
=======
			echo $this->twig->render('competition_created.twig.html', array(
				"return_url" => get_admin_url() . 'admin.php?page=' . $this->slugs['main']
			) );
>>>>>>> dev
		} else {
			$data = array(
				"plugin_url"	=> HASHVIEWER_PLUGIN_URL,
				"admin_url"		=> get_admin_url(),
				"request_url"	=> $_SERVER['REQUEST_URI']
			);
			echo $this->twig->render('new_competition.twig.html', $data);
		}
	}


	public function browse() {
		echo $this->twig->render('browse.twig.html', array("data" => ""));
	}


	/**
	 * Installation functions
	 */
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

	public function create_new_competition() {

		$title = (isset($_POST['title'])) ? $_POST['title'] : "" ;
		$hashtags = (isset($_POST['hashtags'])) ? $_POST['hashtags'] : "" ;
		$startTime = (isset($_POST['startDay'])) ? $_POST['startDay'] : "" ;
		$endTime = (isset($_POST['endDay'])) ? $_POST['endDay'] : "" ;



		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";	

<<<<<<< HEAD
		//$startTime = strtotime($startTime);
		//$endTime = strtotime($endTime);
=======
//		$startTime = strtotime($startTime);
//		$endTime = strtotime($endTime) + 86399; // adds 23 hours, 59 minutes and 59 seconds to endtime
		var_dump($endTime);
>>>>>>> dev


		$competition = array( 
			'title'		=> $title, 
			'hashtags' 	=> $hashtags, 
<<<<<<< HEAD
			'startTime'	=> $startTime,
			'endTime' 	=> $endTime,
			'active' 	=> 0
		);
=======
			'startTime'	=> $startTime . " 00:00:00",
			'endTime' 	=> $endTime . " 23:59:59",
			'active' 	=> 0
		);
		var_dump($competition);

>>>>>>> dev
		$affected_rows = $wpdb->insert( $table_name, $competition );
		return $affected_rows;
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
	 * Utilities
	 */
	public function filter_hashtags($tags) {
		return $tags;
	}
}
