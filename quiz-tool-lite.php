<?php
/*
Plugin Name: Quiz Tool Lite
Plugin URI: https://wordpress.org/plugins/quiz-tool-lite/
Description: Create questions and quizzes, embed individual questions for formative assessment or deploy entire an quiz
Version: 2.5.0
Author: Alex Furr, Lisha Chen Wilson and Simon Ward
Author URI: https://wordpress.org/plugins/quiz-tool-lite/
License: GPL
Text Domain: qtl
Domain Path: /languages
*/

date_default_timezone_set('UTC');
define('AIQUIZ_PATH', plugin_dir_path(__FILE__)); # inc /
define('AIQUIZ_DIR', plugin_dir_path(__FILE__)); # inc /
define ('AI_Plugin_Path', plugin_basename(__FILE__));
define('AIQUIZ_ABS_PATH', plugin_dir_url(__FILE__));
define( 'QTL_PLUGIN_URL', plugins_url('quiz-tool-lite' , dirname( __FILE__ )) );

require_once AIQUIZ_PATH.'functions.php'; # All the php functions etc...
require_once AIQUIZ_PATH.'scripts/qry-functions.php'; # All the DB queries
require_once AIQUIZ_PATH.'admin/index.php'; # Load admin pages
require_once AIQUIZ_PATH.'scripts/database.php'; # All the create database actions
require_once AIQUIZ_PATH.'scripts/utils.php'; # All the useful utils
require_once AIQUIZ_PATH.'scripts/ajax.php'; #Code for all the ajax calls
require_once AIQUIZ_PATH.'scripts/actions.php'; # All the actinos on the DB
require_once AIQUIZ_PATH.'scripts/export-functions.php'; # All the export function
require_once AIQUIZ_PATH.'quizFrontEnd/draw.php'; #Code that shows the quiz on the front page
require_once AIQUIZ_PATH.'scripts/draw.php'; #Drawing functions for backend

$QTL_frontendHandler = new qtl_quiz_draw(); // Initalise the menu and shortcodes
$initialiseQTL = new qtl_initialise($QTL_frontendHandler);  // Inistalise the front end shortcode drawing functions

class qtl_initialise
{
	public function __construct($Object)
	{
		add_action( 'admin_menu', array( $this, 'QTL_createAdminMenu' ));
		add_action( 'admin_head', array( $this, 'QTL_loadMyPluginScripts' ));

		// Add the shortcodes
		add_shortcode('QTL-Quiz', array( $Object, 'startQuiz'));
		add_shortcode('QTL-Question', array( $Object, 'drawExampleQuestion') );
		add_shortcode('QTL-Response', array( $Object, 'drawUserResponse'));

		// Shortcode to show results to student
		add_shortcode('QTL-Score', array( $Object, 'drawUserScore'));

		// Shortcode to show results to student
		add_shortcode('QTL-Leaderboard', array( $Object, 'drawLeaderboard'));		
		
	}

	// Add the Admin Menu Items
	public static function QTL_createAdminMenu() 
	{
		// Get the wordpress minimum level If it doesn't exist then create it as admin for default
		if(!get_option('qtl-minimum-editor'))
		{
			add_option('qtl-minimum-editor', 'administrator');	
		}
		$minimumAccessLevel = get_option('qtl-minimum-editor');

		switch ($minimumAccessLevel)
		{
			case "administrator":
			{
				$myCapability = 'manage_options';
				//echo 'User must be able to manage_options';
				break;
			}

			case "editor":
			{
				$myCapability = 'delete_others_pages';
				//echo 'User must be able to delete_others_pages';
				break;
			}	

			case "author":
			{
				$myCapability = 'delete_published_posts';
				//echo 'User must be able to delete_published_posts';
				break;
			}	

			case "contributor":
			{
				$myCapability = 'delete_posts';				
				//echo 'User must be able to delete_posts';
				break;
			}
		}

		$myIcon = plugins_url();
		$myIcon.='/quiz-tool-lite/images/quiz_icon.png';

		// Create main menu item
		$page_title=__('Quiz Questions', 'qtl');
		$menu_title=__('Quiz Questions', 'qtl');
		$menu_slug="ai-quiz-home";
		$function="drawAIquiz_home";
		$iconURL=$myIcon;	
		add_menu_page( $page_title, $menu_title, $myCapability, $menu_slug, $function, $iconURL);

		$parentSlug = "ai-quiz-home";
		$page_title=__('Quizzes', 'qtl');
		$menu_title=__('Quizzes', 'qtl');
		$menu_slug="ai-quiz-quiz-list";
		$function="drawAIquiz_QuizList";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);

		$parentSlug = "ai-quiz-question-list";
		$page_title=__('Question List', 'qtl');
		$menu_title=__('Question List', 'qtl');
		$menu_slug="ai-quiz-question-list";
		$function="drawAIquiz_questionList";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug = "ai-quiz-quiz-list";
		$page_title=__('Edit Quiz', 'qtl');
		$menu_title=__('Edit Quiz', 'qtl');
		$menu_slug="ai-quiz-quiz-edit";
		$function="drawAIquiz_quizEdit";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);			
	
		$parentSlug = "ai-quiz-question-list";
		$page_title=__('Edit Question', 'qtl');
		$menu_title=__('Edit Question', 'qtl');
		$menu_slug="ai-quiz-question-edit";
		$function="drawAIquiz_questionEdit";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);

		$parentSlug="ai-quiz-quiz-list";
		$page_title=__('Results', 'qtl');
		$menu_title=__('Results', 'qtl');
		$menu_slug="ai-quiz-results";
		$function="drawAIquiz_results";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);

		$parentSlug="ai-quiz-results";
		$page_title=__('User Results', 'qtl');
		$menu_title=__('User Results', 'qtl');
		$menu_slug="ai-user-results";
		$function="drawAIquiz_user_results";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-user-results";
		$page_title=__('Quiz Breakdown', 'qtl');
		$menu_title=__('Quiz Breakdown', 'qtl');
		$menu_slug="ai-quiz_breakdown";
		$function="drawAIquiz_user_breakdown";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-quiz-questionType";
		$page_title=__('Pick Question Type', 'qtl');
		$menu_title=__('Pick Question Type', 'qtl');
		$menu_slug="ai-quiz-questionType";
		$function="drawAIquiz_questionType";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-quiz-home";
		$page_title=__('Export / Import', 'qtl');
		$menu_title=__('Export / Import', 'qtl');
		$menu_slug="ai-quiz-export";
		$function="drawAIquiz_export";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);		

		$parentSlug="ai-quiz-home";
		$page_title=__('Settings', 'qtl');
		$menu_title=__('Settings', 'qtl');
		$menu_slug="ai-quiz-settings";
		$function="drawAIquiz_settings";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-quiz-home";
		$page_title=__('Help', 'qtl');
		$menu_title=__('Help', 'qtl');
		$menu_slug="ai-quiz-help";
		$function="drawAIquiz_help";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-quiz-quiz-list";
		$page_title=__('Grade Boundaries', 'qtl');
		$menu_title=__('Grade Boundaries', 'qtl');
		$menu_slug="ai-quiz-boundaries";
		$function="drawAIquiz_gradeBoundaries";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);	

		$parentSlug="ai-quiz-boundaries";
		$page_title=__('Edit Grade Boundary', 'qtl');
		$menu_title=__('Edit Grade Boundary', 'qtl');
		$menu_slug="ai-quiz-boundaryEdit";
		$function="drawAIquiz_boundaryEdit";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);			
	}

	// Check if we're on our options page   and only load plugin scripts if so
	public static function QTL_isMyPluginScreen()
	{
		global $overrideAdminCheck; //override for loading up gplugin scripts from front end i.e. doesn't need to check if on admin page

		if($overrideAdminCheck==true)
		{
			return true;
		}
		else
		{
			$myPluginPages = array(
			"toplevel_page_ai-quiz-home",
			"admin_page_ai-quiz-question-list",
			"quiz-questions_page_ai-quiz-quiz-list",
			"admin_page_ai-quiz-quiz-edit",
			"quiz-questions_page_ai-quiz-results",
			"admin_page_ai-quiz-question-edit",
			"admin_page_ai-quiz-questionType",
			"quiz-questions_page_ai-quiz-export",
			"quiz-questions_page_ai-quiz-settings",
			"quiz-questions_page_ai-quiz-help",
			"ai-quiz-results_uos",
			"admin_page_ai-quiz-results",
			"admin_page_ai-user-results",
			"admin_page_ai-quiz_breakdown",
			"admin_page_ai-quiz-boundaries",
			"admin_page_ai-quiz-boundaryEdit"
			);

			// Get the screen name
			global $current_screen;
			$screen = get_current_screen();
			$thisPage = $screen->base;
				error_log($thisPage);
			var_dump (get_current_screen('parent_file') );

			if (in_array($thisPage, $myPluginPages))
			{
				$isMyPluginPage = true;
			}
			else
			{
				$isMyPluginPage = false;
			}

			if (is_object($screen) && $isMyPluginPage==true)
			{
				return true;  

			}else{
				return false;  
			}
		}
	}

	public static function QTL_loadMyPluginScripts()
	{

		// Include JS/CSS only if we're on our options page  | falling back on is_admin() for the screen ID gets localized
		if (is_admin() || is_singular()) //(qtl_initialise::QTL_isMyPluginScreen())
		{  
			wp_enqueue_script('js_custom', plugins_url('/scripts/js-functions.js',__FILE__) ); #Custom JS functions

			wp_register_style( 'QTL_css_custom',  plugins_url('/css/qtl-styles.css',__FILE__) );
			wp_enqueue_style( 'QTL_css_custom' );

			global $wp_scripts;	
			
			// Allow the poopup thickbox to appear all pages
			add_thickbox();

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-position'); 	//drag/drop dependency

			wp_enqueue_script('jquery-ui-widget'); 		//drag/drop dependency
			wp_enqueue_script('jquery-ui-mouse');  		//drag/drop dependency
			wp_enqueue_script('jquery-ui-draggable');  	//drag/drop dependency
			wp_enqueue_script('jquery-ui-droppable');  	//drag/drop dependency
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-tabs'); 

			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('jquery-touch-punch');

			// get the jquery ui object
			$queryui = $wp_scripts->query('jquery-ui-core');

			// load the jquery ui theme
			$url = "https://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";	
			wp_enqueue_style('jquery-ui-smoothness', $url, false, null);

			//spectrum colour picker scripts
			//wp_enqueue_style( 'spectrum_css', plugins_url('', __FILE__) . '/css/spectrum.css' );
			//wp_enqueue_script( 'spectrum_js', plugins_url('', __FILE__) . '/scripts/spectrum.js' );

			//use WP color picker
			add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
function mw_enqueue_color_picker( $hook_suffix ) {
			// first check that $hook_suffix is appropriate for your admin page
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
			}
		}
	}
}

/* load text domain for this plugin | it will not load properly without add_action() */
function qtl_load_textdomain () {
		load_plugin_textdomain('qtl', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'qtl_load_textdomain' );

?>
