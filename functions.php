<?php


// Adds the ADD QUESTION option to posts and pages in the editor

class AIQuiz_TinyMCE_Button 
{


	static public function tinymce_add_button()
	{
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;

		if ( get_user_option('rich_editing') == 'true')
		{
			add_filter("mce_external_plugins", array("AIQuiz_TinyMCE_Button","tinymce_custom_plugin"));
			add_filter('mce_buttons', array("AIQuiz_TinyMCE_Button",'tinymce_register_button'));
		}
	}

	static public function tinymce_register_button($buttons)
	{
		array_push($buttons, "|", "AIquizButtonAdd");
		return $buttons;
	}

	static public function tinymce_custom_plugin($plugin_array)
	{
		$plugin_array['AIquizButtonAdd'] = WP_PLUGIN_URL.'/quiz-tool-lite/mce/editor_plugin.js';
		return $plugin_array;
	}

	static public function addAI_Button($atts)
	{
		if($atts['id'])
		{
			$id = $atts['id'];
			$width = $atts['width']?$atts['width']:640;
			$height = $atts['height']?$atts['height']:385;
		}
	}
	
}
add_action('init', array('AIQuiz_TinyMCE_Button','tinymce_add_button'));
add_shortcode('kkytv', array('AIQuiz_TinyMCE_Button','addAI_Button'));
// End of Tiny MCE add question icon to bar


if (!class_exists('DownloadCSV'))
{
	class DownloadCSV
	{
		static function on_load()
		{
			add_action('plugins_loaded',array(__CLASS__,'plugins_loaded'));
			register_activation_hook(__FILE__,array(__CLASS__,'activate'));
		}

		static function plugins_loaded()
		{
			global $pagenow;
			global $wpdb;

			if ( current_user_can( 'manage_options' ) )
	//	{) // Are they logged in?
			{
				$downloadType="";
				if(isset($_GET['download']))
				{
					$downloadType = $_GET['download'];
				}


				if ($pagenow=='admin.php' && $downloadType=='csv')
				{
					$fileName = 'quizQuestionsExport.csv';
					 
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-Description: File Transfer');
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename={$fileName}");
					header("Expires: 0");
					header("Pragma: public");
					
					$fh = @fopen( 'php://output', 'w' );
					
					$CSV_array = AI_Quiz_importExport::getQuestionCSVData();

					// Use the keys from $data as the titles
					//fputcsv($fh, $CSV_array);

					foreach ($CSV_array as $fields) {
						fputcsv($fh, $fields);
					}
					
					// Close the file
					fclose($fh);
					// Make sure nothing else is sent, our file is done
					exit;
				}
				elseif ($pagenow=='admin.php' && $downloadType=='stdres'){
					if(isset($_GET['quizID']) && isset($_GET['userID'])){
						$quizID = $_GET['quizID'];
						$userID = $_GET['userID'];	
					}

					$user = get_user_by('id', $userID);		
					$fileName = 'results_user_'.$user->user_login.'.csv';
					$cnt = 1;
					$results = $wpdb->get_results("SELECT * FROM `wp_AI_Quiz_tblUserQuizResponses` WHERE `username`='".$user->user_login."' AND `quizID`=".$quizID." ORDER BY `userAttemptID` ASC");

					$quiz_name = $wpdb->get_var("	SELECT `quizName` FROM `wp_AI_Quiz_tblQuizzes` WHERE `quizID`=".$quizID);

					// var_dump($results);die;

					$fileName = $quiz_name.'_result_'.$user->user_login.'.csv';
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-Description: File Transfer');
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename={$fileName}");
					header("Expires: 0");
					header("Pragma: public");

						
						$fh = @fopen( 'php://output', 'w' );
						foreach($results as $result) { 

							$CSV_array;

							$tempOptionArray = array(__('Student','qtl'),''.$user->display_name.'');
							$CSV_array[] = $tempOptionArray;

							$tempOptionArray = array(__('Exam','qtl'),''.$quiz_name.'');
							$CSV_array[] = $tempOptionArray;

							$tempOptionArray = array(__('Attempt_Num','qtl'),''.$cnt.'');
							$CSV_array[] = $tempOptionArray;
						 
							$dateStarted = $result->dateStarted;
							$dateFinished =  $result->dateFinished; 
							$timeTaken = qtl_utils::dateDiff(strtotime($dateStarted), strtotime($dateFinished));
							$tempOptionArray = array(__('Time','qtl'),''.$timeTaken.'');
							$CSV_array[] = $tempOptionArray;


							$tempOptionArray = array(__('Score','qtl'),''.$result->score.'');
							$CSV_array[] = $tempOptionArray;

							$tempOptionArray = array('');
							$CSV_array[] = $tempOptionArray;
							$cnt++;
						}
				

						foreach ($CSV_array as $fields) {
							fputcsv($fh, $fields);
						}
					
					// Close the file
					fclose($fh);
 					exit;
 
				}
				elseif ($pagenow=='admin.php' && $downloadType=='exmres'){
				 
					if(isset($_GET['quizID'])){
						$quizID = $_GET['quizID'];
					}

					$cnt = 1;
					$results = $wpdb->get_results("SELECT * FROM `wp_AI_Quiz_tblQuizAttempts` WHERE `quizID`=".$quizID);

					$quiz_name = $wpdb->get_var("	SELECT `quizName` FROM `wp_AI_Quiz_tblQuizzes` WHERE `quizID`=".$quizID);
					$fileName = 'results_user_'.$quiz_name.'.csv';

					// var_dump($results);die;

					$fileName = $quiz_name.'_results_.csv';
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-Description: File Transfer');
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename={$fileName}");
					header("Expires: 0");
					header("Pragma: public");

						
						$fh = @fopen( 'php://output', 'w' );
						foreach($results as $result) { 
							$user = get_user_by('login', $result->username);		

							$CSV_array;

							$tempOptionArray = array(__('Student','qtl'),''.$user->display_name.'');
							$CSV_array[] = $tempOptionArray;

							$tempOptionArray = array(__('Attempts_Num','qtl'),''.$result->attemptCount.'');
							$CSV_array[] = $tempOptionArray;
						 
							$tempOptionArray = array(__('Last_attemmpt','qtl'),''.$result->highestScoreDate.'');
							$CSV_array[] = $tempOptionArray;

							$tempOptionArray = array(__('Max_score','qtl'),''.$result->highestScore.'');
							$CSV_array[] = $tempOptionArray;
							
							$tempOptionArray = array('');
							$CSV_array[] = $tempOptionArray;
							$cnt++;
						}
				

						foreach ($CSV_array as $fields) {
							fputcsv($fh, $fields);
						}
					
					// Close the file
					fclose($fh);
 					exit;
				}
			}
		}
	}

 
			DownloadCSV::on_load();
	 
}

?>