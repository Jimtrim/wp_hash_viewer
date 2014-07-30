<?php

class HashViewer {
	private static $instance = null;

	private $twig;
	private $slugs;

	public static function get_instance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self;
		return self::$instance;
	}

	private function __construct() {

		// Load Composer plugins
		require_once HASHVIEWER_PLUGIN_DIR . '/vendor/autoload.php';
		// Load Instagram-PHP-API
		require_once HASHVIEWER_PLUGIN_DIR . '/vendor/Instagram.class.php';

		// set up Twig templating engine for the views
		$loader = new Twig_Loader_Filesystem( HASHVIEWER_PLUGIN_DIR . '/views/' );
		$this->twig = new Twig_Environment( $loader );

		// Add default actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu_setup' ) );
		add_action( 'wp_enqueue_styles', array( $this, 'register_frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ) );

		// define slugs for the menu
		$this->slugs = array(
			'main' => 'hashviewer_main',
			'new_competition' => 'hashviewer_new_competition',
			'browse' => 'hashviewer_browse'
		);
	}

	/**
	 * Initial setup
	 */
	// Frontend
	public function register_frontend_scripts() {
		wp_enqueue_script( 'hashviewer_script', HASHVIEWER_PLUGIN_URL . 'js/main.js' );
		wp_enqueue_script( 'hashviewer_script', HASHVIEWER_PLUGIN_URL . 'js/hashviewer.wp.js', array( 'jquery' ) );
	}
	public function register_frontend_styles() {
		wp_enqueue_style( 'hashviewer-style', HASHVIEWER_PLUGIN_URL . 'css/main.css' );
		wp_enqueue_style( 'bootstrap-style', HASHVIEWER_PLUGIN_URL . 'css/bootstrap.min.css' );
	}

	// Admin
	public function admin_menu_setup() {
		add_menu_page( "HashViewer", "HashViewer", 'manage_options',
			$this->slugs['main'], array( $this, 'all_competitions' ), HASHVIEWER_PLUGIN_URL . '/img/menu_icon.png' );
		add_submenu_page( $this->slugs['main'], "HashViewer - Browse", "All Competitions", 'manage_options',
			$this->slugs['main'] ); // main menu item
		add_submenu_page( "hashviewer_main", "HashViewer - New competition", "New competition", 'manage_options',
			$this->slugs['new_competition'], array( $this, 'new_competition' ) );
		add_submenu_page( "hashviewer_main", "HashViewer - Browse", "Browse Instagram", 'manage_options',
			$this->slugs['browse'], array( $this, 'browse' ) );
	}

	public function admin_init() {
		wp_enqueue_script( 'hashviewer_script', HASHVIEWER_PLUGIN_URL . 'js/main.js', array( 'jquery' ) );
		wp_enqueue_script( 'hashviewer_wp_script', HASHVIEWER_PLUGIN_URL . 'js/hashviewer.wp.js', array( 'jquery' ) );


		wp_register_style( 'bootstrap-style', HASHVIEWER_PLUGIN_URL . 'css/bootstrap.min.css' );
		wp_register_style( 'hashviewer-style', HASHVIEWER_PLUGIN_URL . 'css/main.css' );

		wp_enqueue_style( 'bootstrap-style' );
		wp_enqueue_style( 'hashviewer-style' );

	}

	/**
	 * Views
	 * */
	public function all_competitions() {
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) { //POST
			if ( $_POST["action"] == "delete-competition" && isset( $_POST["compId"] ) ) {
				$this->deleteCompetition( $_POST["compId"] );
				echo $this->twig->render( 'competition_action.twig.html', array(
						"action"  => "deleted",
						"return_url" => get_admin_url() . 'admin.php?page=' . $this->slugs['main']
					) );
			}
		} else { //GET
			$data = array(
				"plugin_url" => HASHVIEWER_PLUGIN_URL,
				"new_comp_url" => get_admin_url() . 'admin.php?page=' . $this->slugs['new_competition'],
				"competitions"  => $this->getAllCompetitions(),
				"browse_url" => get_admin_url() . 'admin.php?page=' . $this->slugs['browse']
			);
			echo $this->twig->render( 'all_competitions.twig.html', $data );
		}
	}
	public function new_competition() {

		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) { //POST
			if ( $_POST["action"] == "create-competition" ) {
				$this->createNewCompetition();
				$return_url = "";
				echo $this->twig->render( 'competition_action.twig.html', array(
						"action"  => "created",
						"return_url" => get_admin_url() . 'admin.php?page=' . $this->slugs['main']
					) );
			}
		} else { //GET
			$data = array(
				"plugin_url" => HASHVIEWER_PLUGIN_URL,
				"admin_url"  => get_admin_url(),
				"request_url" => $_SERVER['REQUEST_URI'],
				"all_comps_url" => get_admin_url() . 'admin.php?page=' . $this->slugs['main']
			);
			echo $this->twig->render( 'new_competition.twig.html', $data );
		}
	}


	public function browse() {

		$data = array();
		if ( isset( $_GET['compId'] ) ) {
			if (isset($_GET['savedOnly'])) {
				echo $this->twig->render( 'browse_saved.twig.html', $data ); // browse only saved if that is selected
				return;
			}
			$data['comp'] = $this->getCompetition( $_GET['compId'] ); //TODO: sanitize input
		}

		echo $this->twig->render( 'browse.twig.html', $data );
		return;
	}


	/**
	 * Installation functions
	 * */
	public static function plugin_activate() {
		if ( get_option( "ihw_db_version", "Missing" ) == "Missing" ) {
			self::db_install();
			add_option( "ihw_db_version", "0.2" );
		}
	}

	public static function plugin_deactivate() {
		delete_option( "ihw_db_version" );
	}
	public static function plugin_uninstall() {
		if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
			exit();
		self::db_uninstall();
	}

	/**
	 * DB functions
	 * */
	public function getCompetition( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";
		$sql = "SELECT id, active, title, startTime, endTime, hashtags, winnerSubmissionId
				FROM $table_name
				WHERE id='$id';";
		$rows = $wpdb->get_results( $sql );

		if ( isset( $rows[0] ) ) {
			return $rows[0];
		} else {
			return NULL;
		}
	}

	public function deleteCompetition( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";
		$rows = $wpdb->delete( $table_name , array( 'id' => $id ) );

		if ( isset( $rows[0] ) ) {
			return $rows[0];
		} else {
			return NULL;
		}
	}

	public function getAllCompetitions() {
		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";
		$sql = "SELECT id, active, title, startTime, endTime, hashtags, winnerSubmissionId
				FROM $table_name;";

		$rows = $wpdb->get_results( $sql );
		return $rows;
	}

	public static function createNewCompetition() {
		// TODO: sanitize input
		$title = ( isset( $_POST['title'] ) ) ? $_POST['title'] : "" ;
		$hashtags = ( isset( $_POST['hashtags'] ) ) ? self::filter_hashtag( $_POST['hashtags'] ): "" ;
		$startTime = ( isset( $_POST['startDay'] ) ) ? $_POST['startDay'] : "" ;
		$endTime = ( isset( $_POST['endDay'] ) ) ? $_POST['endDay'] : "" ;



		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_competition";

		//$startTime = strtotime($startTime);
		//$endTime = strtotime($endTime);


		$competition = array(
			'title'  => $title,
			'hashtags'  => $hashtags,
			'startTime' => $startTime . " 00:00:00",
			'endTime'  => $endTime . " 23:59:59",
			'active'  => 0
		);
		$affected_rows = $wpdb->insert( $table_name, $competition );
		return $affected_rows;
	}

	public static function createNewSubmission() {
		$submission = array( //TODO: add input sanitization
			'compId' => "",
			'mediaId' => "",
			'instagramUsername' => "",
			'instagramImage' => "",
			'createdAt' => "",
		);

		foreach ( $submission as $key => $value )
			if ( isset( $_POST[$key] ) )
				$submission[$key] = $_POST[$key];
			$submission['createdAt'] = date( "Y-m-d H:i:s", $submission['createdAt'] );

		global $wpdb;
		$table_name = $wpdb->prefix . "hashviewer_submission";


		$alreadyGot = $wpdb->get_results(
                        "SELECT COUNT(*) AS TOTALCOUNT
                        FROM {$table_name}
                        WHERE mediaId = '{$submission['mediaId']}'"
                    );
		if ($alreadyGot[0]->TOTALCOUNT == 0) {
			$affected_rows = $wpdb->insert( $table_name, $submission );
			echo "submission created";
		} else {
			echo "Image already saved";
			return 0;
		}
		return $affected_rows;


	}

	private static function db_install() {
		global $wpdb;

		// come back to me if having \only\ 9 999 999 competitions is a problem
		$competition_table_name = $wpdb->prefix . "hashviewer_competition";
		$competition_sql = "CREATE TABLE " . $competition_table_name . "(
			id					mediumint(9) NOT NULL AUTO_INCREMENT,
			title				VARCHAR(50) NOT NULL,
			active				BOOL,
			startTime			DATETIME,
			endTime				DATETIME,
			hashtags			VARCHAR(255),
			winnerSubmissionId	mediumint(12),
			PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_unicode_ci";

		// ... and each of those competitions have over 1000 approved submission
		$submission_table_name = $wpdb->prefix . "hashviewer_submission";
		$submission_sql = "CREATE TABLE " . $submission_table_name . "(
			mediaId				VARCHAR(50) NOT NULL, -- id gathered from Instagram
			compId				mediumint(9) NOT NULL,
			instagramUsername	varchar(30), -- 30 is a limitation from Instagram
			instagramImage		VARCHAR(255),
			createdAt			DATETIME, -- The time when the image was uploaded to Instagram
			PRIMARY KEY (mediaId),
			FOREIGN KEY (compId) REFERENCES $competition_table_name(id)
		) CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $competition_sql );
		dbDelta( $submission_sql );
	}

	private static function db_uninstall() {
		global $wpdb;
		$submission_table_name = $wpdb->prefix . "hashviewer_submission";
		$competition_table_name = $wpdb->prefix . "hashviewer_competition";
		$sql = "DROP TABLE $submission_table_name; DROP TABLE $competition_table_name";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Utilities
	 * */
	public static function filter_hashtag( $tag ) {
		$match = array();
		preg_match( "/\W*(\w*)/", $tag, $match);
		return $match[1];
	}



	/**
	 *  AJAX resources
	 * these need to be registered in hash-viewer.php
	 * example: add_action('wp_ajax_<post_parameter_action>', array( $viewer, '<function>' ) );
	 * */
	public function save_image() {
		$this->createNewSubmission();
		exit();
	}

	public function get_saved_images($id = "") {
		$results = array();
		if (isset($_GET['compId'])) {
			$id = $_GET['compId'];
		}
		if ( $id != "") {
			global $wpdb;
			$table_name = $wpdb->prefix . "hashviewer_submission";
			$id = esc_sql($id);
			$sql = "SELECT mediaId FROM $table_name WHERE compId=$id";
			$results = $wpdb->get_col($sql);

		}
		if (isset($_GET['compId'])) 
			echo json_encode($results);

		exit(); //required to not get 0 at the end of the response
	}

}
