<?php


if (!class_exists('qtl_quiz_draw'))
{
	
	
	class qtl_quiz_draw
	{
		public static function drawQuizPage($quizID)
		{
	
			$action="";
			if(isset($_GET['action']))
			{
				$action=$_GET['action'];
			}
	
			if($action=="markTest") //mark the quiz
			{
				$quizStr = qtl_quiz_draw::markTest($quizID);

				// Get the quiz options after taking the quiz
				$quizInfo = qtl_queries::getQuizInfo($quizID);	
				$quizOptionsArray = $quizInfo['quizOptions'];

				// Unserialise the quiz options array
				$quizOptionsArray = unserialize($quizOptionsArray);	
				$redirectPage = $quizOptionsArray['redirectPage'];

				if($redirectPage<>"")
				{
					$redirectPage = qtl_utils::addhttp($redirectPage);
					echo '<script>';
					echo 'window.location.replace("'.$redirectPage.'");';
					echo '</script>';
					$quizStr="";
				}
			}
			else
			{
				$quizStr = qtl_quiz_draw::drawQuiz($quizID); //draw the quiz
			}	
	

			return $quizStr;
	
		}

		public static  function getFieldsArray ( $table = '' )
		{
			return array(
				'attemptID' => '',
				'quizID' => '',
				'username' => '',
				'attemptCount' => '',
				'lastDateStarted' => '',
				'questionArray' => array(),
				'highestScore' => '',
				'highestScoreDate' => '',
				'lastAttemptMarked' => ''
			);
		}



		public static  function drawQuiz($quizID)
		{
	
			global $wpdb;
			global $quizOptionsArray;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";
			$table_name_responses = $wpdb->prefix . "AI_Quiz_tblUserQuizResponses";	
	
			$allowQuizAttempt = true; // By default allow them to take the quiz

			$currentUsername = qtl_utils::getCurrentUsername();
			$currentDate = qtl_utils::getCurrentDate(); // GEt current date AND time
			$currentDate_TS = strtotime($currentDate); // Get current date AND tiem as timestamp	
			$currentDateYMD = date('Y-m-d');	// Get curent Date only
			$currentDateYMD_TS = strtotime($currentDateYMD);// Get current date ONLY timestamp	
	
			$quizFailureReason=""; // set the failure reasons to null to start with.
	
			$quizStr= '<div id="theExam">';
			$quizInfo = qtl_queries::getQuizInfo($quizID);

			$potQuestionArray = $quizInfo['questionArray'];
			$quizOptionsArray = $quizInfo['quizOptions'];
	
			// Unserialise the quiz options array
			$quizOptionsArray = unserialize($quizOptionsArray);	
	
			// Create some basic vars
			$timeLimitCheck='';
			$nonLoggedInString ='';
	
	
	
	
			// Check for data rangem number of attempts etc to see if they can take this test
			if($quizOptionsArray)
			{	
				foreach ($quizOptionsArray as $key => $value) {
					$$key = $value;
				}

				$requireUserLoggedIn = ( isset($requireUserLoggedIn) ) ? $requireUserLoggedIn : ""; //this key isn't necessarily in $quizOptionsArray so can be undefined
			}
			else
			{
				$maxAttempts ="";	
				$startDate ="";
				$endDate ="";
				$requireUserLoggedIn ="";
				$timeAttemptsHour ="";
				$timeAttemptsDay="";
			}
	
			// Set some other defaults
			$attemptCount ="";
			$attemptID="";
			$lastDateStarted="";
			$highestScore ="";
	
			if($currentUsername)
			{
	
				//try and get previous attempt info
				$DB_previousAttemptInfo = qtl_queries::getAttemptInfo($currentUsername, $quizID);
				//if there wasn't any then just get an empty fields array
				$previousAttemptInfo = ( !is_null($DB_previousAttemptInfo) && is_array($DB_previousAttemptInfo) ) ? $DB_previousAttemptInfo : qtl_quiz_draw::getFieldsArray();

				if($previousAttemptInfo['quizID'])
				{

					foreach ($previousAttemptInfo as $key => $value)
					{
						$$key = $value;
					}
	
				}


				$newAttemptCount = ($attemptCount+1);
			}
	
	
	
			// If they are logged in then we can update the DB
			if(is_user_logged_in()) // ony get previous results if they are logged in
			{

				$startTest=""; // Check to see if they have clicked to start the test yet ONly applies to max attempts
				if(isset($_GET['startTest']))
				{
					$startTest=$_GET['startTest'];
				}


				// If max attempts is limited then only update this if they have clicked to start the test ($_GET['
				if($maxAttempts>=1 && $startTest<>true)
				{
					// do not update as they have not clicked to start and max attempts it limisted
				}
				else
				{	
					// Check to see if they've done it all all
					$attemptCheck = qtl_queries::getAttemptInfo($currentUsername, $quizID);
	
					if($attemptID=="")
					{
						// Firstly log the fact they've done the test at all
						$myFields="INSERT into ".$table_name." (attemptCount, quizID, lastDateStarted, username)  ";
						$myFields.="VALUES (%u, %u, '%s', '%s')";	


						$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
							1,
							$quizID,
							$lastDateStarted,
							$currentUsername
						));
					}
					else
					{
						// Update the fact they've done retaken the test
						$myFields ="UPDATE ".$table_name." SET ";
						$myFields.="attemptCount=%u ";
						$myFields.="WHERE username ='%s' AND quizID=%u";
		
						$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
							$newAttemptCount,
							$currentUsername,
							$quizID
						));
					} // end of if previous attempt has been made or not
				} // End of if max attempts are limited

				$lastDateStartedFormatted = qtl_utils::formatDate($lastDateStarted);
				$lastDateStartedFormatted = $lastDateStartedFormatted[2];
	
			} // End if user needs to be logged in check
	

	
			// Check start date
			if($startDate)
			{
				$startDate_TS = strtotime($startDate);
				if($startDate_TS>$currentDateYMD_TS)
				{
					$allowQuizAttempt=false; // not allow them to take the quiz
					$quizFailureReason.="<li>".__('The quiz is not available until ', 'qtl').$startDate."</li>";
				}
			}
	
			// Check to see if they are logged in or not
			if($requireUserLoggedIn=="on")
			{
				if(!is_user_logged_in())
				{
					$siteURL = get_site_url();
					$allowQuizAttempt=false; // not allow them to take the quiz
					$quizFailureReason.="<li><a href=".$siteURL."/wp-login.php>" . __('You need to login before you can take this quiz','qtl') . "</a></li>" ;	
				}
			}
	
			// Check end date
			if($endDate)
			{
				$endDate_TS = strtotime($endDate);
				if($endDate_TS<$currentDateYMD_TS)
				{
					$allowQuizAttempt=false; // not allow them to take the quiz
					$quizFailureReason.="<li>".__('The quiz closed on ',  'qtl').$endDate."</li>";
				}
			}
	
			// Check difference between attempts
			$minTimeBetweenAttempts = 0;
			if($timeAttemptsHour)
			{
				$minTimeBetweenAttempts = ($timeAttemptsHour*60*60);
			}
	
			if($timeAttemptsDay)
			{
				$minTimeBetweenAttempts = $minTimeBetweenAttempts+($timeAttemptsDay*24*60*60);
			}	
	
			if($minTimeBetweenAttempts>0)
			{
				$lastDateStarted_TS = strtotime($lastDateStarted); // Get timestamp of last attempt
				$TStoCheck = $lastDateStarted_TS + $minTimeBetweenAttempts; // Get timestamp of next attempt allowed i./ last attempt + time interval

				// Check to see if current timstamp is greater than the total
				if($currentDate_TS<$TStoCheck)
				{
					$allowQuizAttempt=false; // not allow them to take the quiz
	
					// get the time until the next allowed attempt
					$timeLeft = ($TStoCheck - $currentDate_TS);
	
					$min = floor($timeLeft / 60) % 60;
					$hours = floor($timeLeft / 3600) % 24;
					$days = floor($timeLeft / 86400);
	
					$quizFailureReason = "<li>".__('You can next take this test in '.$min.' minutes, '.$hours.' hours and '.$days.' days', 'qtl')."</li>";
	
					$originalAttemptCount = ($newAttemptCount-1);
	
					// Because the attempt count is auto updated regardless, we need to reset this to minus one if they can't catually take it
					$myFields ="UPDATE ".$table_name." SET ";
					$myFields.="attemptCount=%u ";
					$myFields.="WHERE username ='%s' AND quizID=%u";
	
					$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
						$originalAttemptCount,
						$currentUsername,
						$quizID
					));
				}
			}
	
			// Check the number of attempts. This must be done last as if limited attempts we must give people the options to 'Click to start'
			// THis gest drawn ONLY if the other conditions are met e.g. time between attempts etc.
			$clickToStart=""; // Define this as blank - its its NOT blank then display this
			if($maxAttempts)
			{
				if($maxAttempts<=$attemptCount)
				{
					$allowQuizAttempt=false; // not allow them to take the quiz
					$quizFailureReason.="<li>".__('You have exceeded the number of maximum attmempts', 'qtl')." (".$maxAttempts.")</li>";
				}
				else
				{
					// They are eligiable so check for other problems before displaying the 'click to start' and they HAVEN@T yet clicked it.
					if($allowQuizAttempt==true && $startTest=="")
					{
						$attemptsLeft = $maxAttempts-$attemptCount;
						$clickToStart = "<hr/>".__('Total number of allowed attempts: ', 'qtl').$maxAttempts. "<br/>".__('Number of attempts remaining: ', 'qtl').$attemptsLeft."<br/><br/>";
						$clickToStart.= '<div style="warning">';
						$clickToStart.= __('<b>Please note</b>: Clicking \'refresh\' or using the back or forward buttons on your browser after starting the quiz will count as another attempt.', 'qtl');
						$clickToStart.= '</div><br/>';
						$clickToStart.= '<a href="?startTest=true">'.__('Click here to start the quiz','qtl').'</a><br/><br/>';
					}
				}

			}
	
	
			// Only do this if they are logged in
			if($currentUsername)
			{
				if($highestScore && $clickToStart<>"")
				{
					$quizStr.= __('Number of times you have taken this test :','qtl').$attemptCount.'<br/>'. __('Your highest acheaved score: ', 'qtl').$highestScore.'%</b>.<br/>';
				}
			}
	
			if($clickToStart && $startTest<>true)
			{
				$quizStr.=$clickToStart;
			}
			elseif($allowQuizAttempt==false)
			{
				$quizStr.= '<ul>'.$quizFailureReason.'</ul>';
			}
			else
			{
	

	
				// Now generate the quiz based on the ruleID if it exists. If it doesnt' exist the function will simple generate ten at random from the generic questions	
				$questionArray = qtl_quiz_draw::generateQuizQuestions($potQuestionArray, $quizOptionsArray);

				$thisUsername = '';
				if($currentUsername)
				{
					$thisUsername = $currentUsername;	
				}

				// Log this attempt along with the question order
				$myFields="INSERT into ".$table_name_responses." (quizID, username, dateStarted, questionArray)  ";
				$myFields.="VALUES (%u, '%s', '%s', '%s')";	


				$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
					$quizID,
					$thisUsername,
					date('Y-m-d H:i:s'),
					serialize($questionArray)
				));	

				$userAttemptID = $wpdb->insert_id;	

				if($currentUsername)
				{
					qtl_actions::logAttempt($quizID, $questionArray);// Log this attempt
				}


				// CHeck if a timer is required
				if($timeLimitCheck=="on" && ($timeLimitMinutes>=1 || $timeLimitSeconds >=1))
				{
					echo '<div id="countdownDiv">';
					echo __('This quiz will submit in', 'qtl').'<br/>';
					echo qtl_quiz_draw::showCountdown($timeLimitMinutes, $timeLimitSeconds);
					echo '</div>';
					echo '<br/><br/><br/><br/>'; // Added space so the timer appears above the quiz start
				}

				// Accuont for custom post types with ? already in the string
				$formAction = "?action=markTest";
				$currentFormAction = $_SERVER["REQUEST_URI"];
				$formActionParts = explode('?', $currentFormAction);
				if(isset($formActionParts[1]))
				{
					$formAction = $currentFormAction.'&action=markTest';
				}


				$quizStr.= '<form action="'.$formAction.'" method="post" name="QTL-form" id="QTL-form">';
				$quizStr.= '<input type="hidden" value="'.$userAttemptID.'" name="userAttemptID"/>';

				$currentQuestionNumber=1;

				if($questionArray)
				{
					$nonLoggedInString=""; // Create a var that will be sent via the form if NOT logged in.
					foreach ($questionArray as $key => $value)
					{
		

						$quizStr.= '<div id="questionDiv">';
						$questionID = $value[0];
						$optionOrder = $value[1];
						$quizStr.= '<b class="greyText">'.__('Question', 'qtl'). ' '.$currentQuestionNumber.'</b><br/>';
						$quizStr.= '<div id="question">';	
						$questionStr = qtl_quiz_draw::drawQuestion($questionID, $optionOrder, false, false);
		
		

						$quizStr.= do_shortcode($questionStr);
						$currentQuestionNumber++;
						$quizStr.= '<br/><br/><hr/></div></div>';
		
						$nonLoggedInString.=$questionID.',';
					}
				}

				// Remove the last comma from the string
				$nonLoggedInString = substr($nonLoggedInString, 0, -1);

				$quizStr.= '<div align="right"><input type="submit" value="'.__('Submit answers', 'qtl') .'"></div>';	


				// If they are not required to login and are not logged in then store the serialise the questino array and put it in the qtl_hidden filed serialised
				if($currentUsername=="")
				{
					$quizStr.='<input type="hidden" value="'.$nonLoggedInString.'" name="questionArray">';
				}
				else
				{
					$quizStr.='<input type="hidden" value="'.$newAttemptCount.'" name="attemptCount">';
				}

				$quizStr.= '</form>';


			}
	
			$quizStr.= '</div>';
	
			return $quizStr;

	
		}

		public static  function markTest($quizID, $attemptInfoArray="")
		{
	
	
	
			// Set some vars
			$markedTest ="";
			$previousHighestScore="";
			$currentUsername="";
			$readOnly="";
			$responseArray="";
			$emailAdminArray="";
	
			global $wpdb;
			$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";
			$table_name_responses = $wpdb->prefix . "AI_Quiz_tblUserQuizResponses";	
	
	
			if($attemptInfoArray)
			{

				$currentUsername = $attemptInfoArray['username'];
				$userAttemptID = $attemptInfoArray['userAttemptID'];
				$questionArray = unserialize($attemptInfoArray['questionArray']);
				$responseArray = unserialize($attemptInfoArray['responseArray']);

				$readOnly = true;
			}

			if($currentUsername=="")
			{
				$currentUsername = qtl_utils::getCurrentUsername();
			}
	
			$currentDate = qtl_utils::getCurrentDate();
			$markTest=true; // Be default allow the test to be marked.	
	
			if($currentUsername && $readOnly==false)
			{
				//$lastAttemptInfo = qtl_queries::getAttemptInfo($currentUsername, $quizID);
				$DB_previousAttemptInfo = qtl_queries::getAttemptInfo($currentUsername, $quizID);
				$lastAttemptInfo = ( !is_null($DB_previousAttemptInfo) && is_array($DB_previousAttemptInfo) ) ? $DB_previousAttemptInfo : qtl_quiz_draw::getFieldsArray();

				$previousHighestScore = $lastAttemptInfo['highestScore'];	

				$attemptCount = $_POST['attemptCount'];

				$lastAttemptMarked = $lastAttemptInfo['lastAttemptMarked']; // Set the attempt count to the last attempt count +1	

				// If this isn't a page refresh then update the attempt DB with the current version of this quiz attempt
				if($lastAttemptMarked<$attemptCount)
				{
					$myFields ="UPDATE ".$table_name." SET ";
					$myFields.="lastAttemptMarked=%u ";
					$myFields.="WHERE username ='%s' AND quizID=%u";
	
					$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
						$attemptCount,
						$currentUsername,
						$quizID
					));	
				}


				if($lastAttemptMarked==$attemptCount)
				{
					$markTest=false; // They have refreshed, possible gone back and cheated so don't mark the test.
				}	
	
			}
	
			$quizInfo = qtl_queries::getQuizInfo($quizID);
			$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);	
			$quizOptionsArray = $quizInfo['quizOptions'];
	
			// Unserialise the quiz options array
			$quizOptionsArray = unserialize($quizOptionsArray);
			$showFeedback = $quizOptionsArray['showFeedback'];
			$emailUser = $quizOptionsArray['emailUser'];
			$quizFinishMessage = qtl_utils::convertTextFromDB($quizOptionsArray['quizFinishMessage']);
			$quizFinishMessage = wpautop($quizFinishMessage);
	
			global $incorrectText;
			$incorrectText = $quizOptionsArray['incorrectText'];	

			global $correctText;
			$correctText = $quizOptionsArray['correctText'];	
		

			// Override stylesheet for correct / incorrect
			$feedbackIcon = $quizOptionsArray['feedbackIcon'];
			$correctIcon = QTL_PLUGIN_URL.'/images/icons/correct/tick'.$feedbackIcon.'.png';
				$incorrectIcon = QTL_PLUGIN_URL.'/images/icons/incorrect/cross'.$feedbackIcon.'.png';
			if($feedbackIcon==0)
			{
				?>
				<style>
				.correct, .incorrect
				{
					background-image:"" !important;
					padding: 10px ;
	
				}
				</style>
			<?php
			}
	
			?>

			<style>
			.correct
			{
				background:left no-repeat url(<?php echo $correctIcon; ?>) !important;
			}
			.incorrect
			{
				background:left no-repeat url(<?php echo $incorrectIcon; ?>) !important;
			}
			</style>

			<?php
	
	
			$markTest = true; // Overrider for testing	
	
			if($markTest==false)
			{
				$markedTest.= __('Sorry! There appears to have been a problem submitting this quiz.<br/>Perhaps you accidently pressed the "back" button.', 'qtl');
			}
			else
			{
	
				// Set the ttoal correct seesion to zero
				$_SESSION['totalCorrect']=0;	
				$markedTest= '<div id="theExam">';

				if($quizFinishMessage==""){$quizFinishMessage='<b>'.__('Thank you.', 'qtl').'</b><br/>'.__('Scroll down to check your answers and final score.', 'qtl').'<hr/><br/>';}

				if($readOnly==false){$markedTest.= $quizFinishMessage;} // Only show this message if they've done the quiz, not if showing results


				if(!$responseArray) // Its posting a live quiz i,.e. front end mark from $_POSTS
				{	
					foreach ($_POST as $KEY=>$VALUE)
					{
						$$KEY=$VALUE;
		
						if (strpos($KEY,'_') !== false)
						{
			
							// Get the question ID
							$questionID = substr($KEY, 0, strpos($KEY, "_"));
			
							if($questionID=="blank")
							{
				
								$tempArray = explode('_', $KEY);				
								$questionID = $tempArray[1];

								$tempResponseArray = array();
								$tempBlankCount=0;
								foreach($_POST as $KEY => $VALUE)
								{
									if (strpos($KEY, 'blank_'.$questionID.'_') !== FALSE)
									{
										$tempBlankCount++;
									}
								}
			
								$i=1;
								while($i<=$tempBlankCount)
								{
									$tempResponseArray[] = $_POST['blank_'.$questionID.'_'.$i];
									$i++;
								}
				
								${'question'.$questionID} = $tempResponseArray;
				
				
				
				
							}
							else
							{

			
			
								// Now get the values
								$optionID = substr($KEY, ($pos = strpos($KEY, '_')) !== false ? $pos + 1 : 0);	
								//echo '<br />optionID: ' . $optionID;
				
								// NOw remove 'option' frmo the string to get the checkbox ID
								$optionID = substr($optionID, 6);
								//echo '<br />optionID: ' . $optionID . '';
				
								//$$questionID.=$optionID.',';
								$$questionID = ( isset( $$questionID ) ) ? $$questionID . $optionID . ',' : $optionID . ',';
								//echo '<br />$$questionID: ' . $$questionID;
							}
			
						}
		
						// Add the textbox response as well
						if (strpos($KEY,'textBox') !== false)
						{
							$questionID = substr($KEY, 9);
							${'question'.$questionID} = $VALUE;
						}
					}
				}
				else
				{
					foreach($responseArray as $questionID => $response)
					{
						${'question'.$questionID} = $response;
					}
				}


				if(isset($_POST['userAttemptID']))
				{
					$userAttemptID = $_POST['userAttemptID'];
				}

				$getUserAttemptInfo = qtl_queries::getUserAttemptInfo($userAttemptID);
				$questionArray = unserialize($getUserAttemptInfo['questionArray']);

				$questionCount = count($questionArray);
				$_SESSION['possibleMaxScore']=0; // Set this up as blanks could be worth more
	
				$submittedAnswersArray = array(); // Create blank array of the submitted answers
				$currentQuestionNumber=1;
				if($questionArray)
				{
					foreach ($questionArray as $key => $value)
					{	
						$markedTest.= '<div id="questionDiv">';
						$questionID = $value[0];
						$optionOrder = $value[1];
						$qType= $value[2];		

						if(isset(${'question'.$questionID}))
						{
							$response = ${'question'.$questionID}; // Set the response
			
							// Strip slashes if its a text question
							if($qType=="text")
							{
								$response = stripslashes($response);
							}

							$submittedAnswersArray[$questionID] = $response;
						}
						else
						{
							$response="";	
						}
		

		
		
						$markedTest.= '<b class="greyText">'.__('Question', 'qtl').' '.$currentQuestionNumber.'</b><br/>';
						$markedTest.= '<div id="question">';
						$markedTest.= qtl_quiz_draw::drawMarkedQuestion($questionID, $optionOrder, $response, $showFeedback);
						$currentQuestionNumber++;
						$markedTest.= '</div></div>';
					}
				}



				$markedTest.='<div id="quizResults">';
				$markedTest.= __('Total number of correct anwers:', 'qtl').' '.$_SESSION['totalCorrect'].'/'.$_SESSION['possibleMaxScore'];
				$percentageScore = round($_SESSION['totalCorrect']/$_SESSION['possibleMaxScore'],2)*100;
				$markedTest.= '<h2>'.__('Your score on this attempt:', 'qtl').' '.$percentageScore.'%</h2>';

				// Get the grade boundary for this mark if it exists
				$boundaryFeedback = qtl_queries::getBoundaryFeedback($percentageScore, $quizID);
				$markedTest.=apply_filters('the_content', $boundaryFeedback);


				$markedTest.='</div>';

				$markedTest.= '</div>';// end of exam div

				if($readOnly==false)
				{
	
					// Update the user results database with the results and date finished and score
					$myFields ="UPDATE ".$table_name_responses." SET ";
					$myFields.="dateFinished='%s' ,";
					$myFields.="responseArray='%s', ";
					$myFields.="score='%s' ";
					$myFields.="WHERE userAttemptID =%u";
	
					$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
						date('Y-m-d H:i:s'),
						serialize($submittedAnswersArray),
						$percentageScore,
						$userAttemptID
					));


	
					$emailAdminList='';
					// Check to see if we email admins
					if(isset($quizOptionsArray['emailAdminList'])){$emailAdminList=$quizOptionsArray['emailAdminList'];}
	
					//$emailAdminList = $quizOptionsArray['emailAdminList'];
					if($emailAdminList)
					{
						$emailAdminArray = explode(",",$emailAdminList);
					}
	
	
					if(is_array($emailAdminArray))
					{

						foreach($emailAdminArray as $userID)
						{
							if($userID)
							{
								$userData = get_userdata( $userID );
								$user_email = $userData->user_email;

								$fromEmailAddress = get_option('qtl-fromEmailAddress');
								if($fromEmailAddress=="")
								{
									$fromEmailAddress = 'donotreply@'.preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
					
								}

								$fromEmailName = get_option('qtl-fromEmailName');
								if($fromEmailName=="")
								{					
									$fromEmailName = "DoNotReply";
								}
				
				
								$headers = array('From: '.$fromEmailName.' <'.$fromEmailAddress.'>');
								$subject = __('A Participant has taken the quiz: ', 'qtl') .$quizName;
								$message = __('A Participant has taken the quiz: ', 'qtl') .$quizName."'\n\n";
								$message.= __('Date Taken: ', 'qtl').$currentDate."\n";
								$message.=__('Score', 'qtl') . ':&nbsp;'.$_SESSION['totalCorrect']."/".$_SESSION['possibleMaxScore']." = ".$percentageScore."%\n\n";				
								$message.=__('This message has been generated automatically', 'qtl');						
				
				
								wp_mail($user_email, $subject, $message, $headers );
							}			
						}
					}
	
					if($currentUsername)
					{
						// This score is higher than any previous score so update the DB to reflect this
						if($percentageScore>$previousHighestScore)
						{
							$myFields ="UPDATE ".$table_name." SET ";
							$myFields.="highestScore=%u ,";
							$myFields.="highestScoreDate='%s' ";
							$myFields.="WHERE username ='%s' AND quizID=%u";
			
							$RunQry = $wpdb->query( $wpdb->prepare(	$myFields,
								$percentageScore,
								$currentDate,
								$currentUsername,
								$quizID
							));	
						} // End if this attempt is higher than previous scores
		
						// Finally check to see if they willg et emailed their results or not
						if($emailUser=="yes")
						{
							global $current_user;
							get_currentuserinfo();
							$thisEmail = $current_user->user_email;
							$subject = __('Quiz name: ', 'qtl').$quizName;
							$message = __('This e-mail is a receipt of your quiz results for ','qtl').$quizName."'\n\n";
							$message.= __('Date Taken: ', 'qtl').$currentDate."\n";
							$message.=__('Score', 'qtl') . ':&nbsp;'.$_SESSION['totalCorrect']."/".$_SESSION['possibleMaxScore']." = ".$percentageScore."%\n\n";
							$message.=__('This message has been generated automatically', 'qtl');


							$fromEmailAddress = get_option('qtl-fromEmailAddress');
							if($fromEmailAddress=="")
							{
								$fromEmailAddress = 'donotreply@'.preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
				
							}

							$fromEmailName = get_option('qtl-fromEmailName');
							if($fromEmailName=="")
							{					
								$fromEmailName = "DoNotReply";
							}
			
			
			
			
							$headers = array('From: '.$fromEmailName.' <'.$fromEmailAddress.'>');										
							wp_mail($thisEmail, $subject, $message, $headers );
			
						}
					}
				}
			}
	
			return $markedTest;
		}



		public static  function generateQuizQuestions($questionArray="", $quizOptionsArray="")
		{
	
			if($quizOptionsArray['questionListType']=="custom")
			{
				// Custom question list
				$questionList = $quizOptionsArray['customQuestionList'];
				$tempQuestionArray = explode(',', $questionList);

				// Only add the questions if they exist
				foreach($tempQuestionArray as $thisQuestionID)
				{
	
	
	
					$questionInfo = qtl_queries::getQuestionInfo($thisQuestionID);
					$questionID = $questionInfo['questionID'];
					$qType= $questionInfo['qType'];	
					$optionOrderType= $questionInfo['optionOrderType'];
					if($questionID<>"" && $qType<>"reflectionText" && $qType<>"reflection")
					{
						// Get the responses as well
						$optionsRS = qtl_queries::getResponseOptions($questionID, $optionOrderType);
						$optionArray = array();
						foreach ($optionsRS as $myOptions)
						{	
							$optionID= $myOptions['optionID'];
							$isCorrect= $myOptions['isCorrect'];
							$optionArray[] = array
							(
								"optionID" => $optionID,
								"isCorrect" => $isCorrect
							);
						}		
		
						$quizQuestionArray[] = array($questionID, $optionArray, $qType);
					}
				}

				if($quizOptionsArray['randomiseQuestions']=="on")
				{
					shuffle($quizQuestionArray);
				}
			}
			else
			{
				// Standard pots list
				// unserialise the array
				$quizQuestionArray="";
				$questionArray = unserialize($questionArray);	

				if($questionArray)
				{
					foreach ($questionArray as $key => $value)
					{
						$potID = $key;
						$qCount = $value;
						$questionRS = qtl_queries::getQuestionsInPot($potID, false, "random", $qCount);

						// NOW go through the RS and add the question IDs to an array
						foreach ($questionRS as $myQuestions)
						{
							$questionID = $myQuestions['questionID'];
							$optionOrderType = $myQuestions['optionOrderType'];
							$qType = $myQuestions['qType'];			
			
							// Get the responses as well
							$optionsRS = qtl_queries::getResponseOptions($questionID, $optionOrderType);
							$optionArray = array();
							foreach ($optionsRS as $myOptions)
							{	
								$optionID= $myOptions['optionID'];
								$isCorrect= $myOptions['isCorrect'];
								$optionArray[] = array
								(
									"optionID" => $optionID,
									"isCorrect" => $isCorrect
								);
							}
			
							$quizQuestionArray[] = array($questionID, $optionArray, $qType);
						}
					}

					//Randomise the question array
					shuffle($quizQuestionArray);
				}
			}

			return $quizQuestionArray;
		}


		public static  function drawQuestion($questionID, $optionOrder="", $formative=false, $questionSettingArray=false)
		{
			// Set some defaults
			$questionStr="";
	
			// get the info about that question	
			$questionInfo = qtl_queries::getQuestionInfo($questionID);
			$question = qtl_utils::convertTextFromDB($questionInfo['question']);
			$question = wpautop($question);
			$correctFeedback = qtl_utils::convertTextFromDB($questionInfo['correctFeedback']);
			$correctFeedback = wpautop($correctFeedback);	
			$incorrectFeedback = qtl_utils::convertTextFromDB($questionInfo['incorrectFeedback']);
			$incorrectFeedback = wpautop($incorrectFeedback);	
			$optionOrderType = $questionInfo['optionOrderType'];

			$qType = $questionInfo['qType'];
			$refectionTextBoxID = 'refectiveTextBoxID'.$questionID;
			$textBoxID= 'textBoxID'.$questionID;	
	
	
			$saveResponse=$questionSettingArray['saveResponse']; // Do we want to save this data or not?
			$buttonText=$questionSettingArray['buttonText']; // The text on the button if a single question
			$correctText=$questionSettingArray['correctText']; // The correct text
			$incorrectText=$questionSettingArray['incorrectText']; // The incorrect text
			$iconset=$questionSettingArray['iconset']; // The incorrect text	
	
			$correctIcon = QTL_PLUGIN_URL.'/images/icons/correct/tick'.$iconset.'.png';
			$incorrectIcon = QTL_PLUGIN_URL.'/images/icons/incorrect/cross'.$iconset.'.png';
	
			?>

			<style>
			.QTLCorrectStyle<?php echo $questionID; ?>
			{
				background:left no-repeat url(<?php echo $correctIcon; ?>) !important;
			}
			.QTLIncorrectStyle<?php echo $questionID; ?>
			{
				background:left no-repeat url(<?php echo $incorrectIcon; ?>) !important;
			}
			</style>

			<?php
	
			if($formative==true)
			{
				$questionStr= '<div id="theExam">';
				$questionStr.= '<div id="questionDiv">';
			}
	
	
			if($qType=="blank")
			{
				$blankCount =  substr_count($question, '[blank]'); // Count the number of blanks
				$i = 1;

				while (strpos($question, '[blank]') !== false)
				{
					//$question = preg_replace('\[blank\]', 'replace-me-'.$i++, $question, 1);
					$question = preg_replace('/(\[[^]]*?)(blank)([^]]*?\])/m', '[replace-me-'.$i++.']', $question, 1);	
						
				}

				// Now go through and replace the inserts with actual inputs. Done as preg replace didn't seem to work properly :(

				$i=1;
				while($i<=$blankCount)
				{
					$thisReplacement = '[replace-me-'.$i.']';
					$question = str_replace($thisReplacement, '<input type="text" size="10" name="blank_'.$questionID.'_'.$i.'" id="blank_'.$questionID.'_'.$i.'">', $question);
					$i++;	
				}

			}	
	
	
	
			$questionStr.= $question;
	
			if($qType=="reflectionText")
			{
				$questionStr.= '<textarea rows="4" style="width: 98%" id="'.$refectionTextBoxID.'"></textarea>';
			}
	
			if($qType=="text")
			{
				$questionStr.= '<input type="text" size="10" id="'.$textBoxID.'" name="'.$textBoxID.'"> ';
			}	
	
	
	
			if($qType=="radio" || $qType=="check")
			{
				// get the response options
				$questionStr.= '<table width="90%">'.chr(10);

				$responseOrder = array();
				if($optionOrder)
				{
					foreach($optionOrder as $optionInfo)
					{
						$responseOrder[] = $optionInfo['optionID'];
					}
				}


				// Create the option lookup array
				$optionLookupArray = array();

				$optionsRS = qtl_queries::getResponseOptions($questionID, $optionOrderType);

		
				foreach ($optionsRS as $myOptions)
				{

					$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);	
					$optionID= $myOptions['optionID'];	
					$responseCorrectFeedback = qtl_utils::convertTextFromDB($myOptions['responseCorrectFeedback']);
					$responseIncorrectFeedback = qtl_utils::convertTextFromDB($myOptions['responseIncorrectFeedback']);

					$optionLookupArray[$optionID] = array
					(
						"optionValue" => $optionValue,
						"responseCorrectFeedback" => $responseCorrectFeedback,
						"responseIncorrectFeedback" => $responseIncorrectFeedback
												
					);
	
					if(!$optionOrder)
					{
						$responseOrder[] = $optionID;
					}
	
				}

				foreach($responseOrder as $optionID)
				{
					$optionInfo = $optionLookupArray[$optionID];
					$optionValue = $optionInfo['optionValue'];
					$responseCorrectFeedback = nl2br(qtl_utils::convertTextFromDB($optionInfo['responseCorrectFeedback']));
					$responseIncorrectFeedback = nl2br(qtl_utils::convertTextFromDB($optionInfo['responseIncorrectFeedback']));
	
					$questionStr.= '<tr>'.chr(10);
					$questionStr.= '<td width="8" valign="top">';
					if($qType=="radio")
					{
						$questionStr.= '<input type="radio" id="option'.$optionID.'" name="question'.$questionID.'" value="'.$optionID.'">';
					}
					elseif($qType=="check")
					{
						$questionStr.= '<input type="checkbox" id="option'.$optionID.'" name="question'.$questionID.'_option'.$optionID.'">';
					}
					$questionStr.= '</td>';
					$questionStr.= '<td>';
					$questionStr.= '<label for="option'.$optionID.'"> '.$optionValue.'</label>';
	
	
					if($formative==true) // Add the hidden divs for correct and incorrect feedback
					{
						$questionStr.= ' <span id="correctFeedback'.$optionID.'" class="successText" style="display:none">'.$responseCorrectFeedback.'</span>';
						$questionStr.= ' <span id="incorrectFeedback'.$optionID.'" class="failText" style="display:none">'.$responseIncorrectFeedback.'</span>';
					}
	
					$questionStr.= '</td>'.chr(10);
					$questionStr.= '</tr>'.chr(10);
				}
				$questionStr.= '</table>'.chr(10);
			}
	
	
			// If its formative add this extra bit to show responses toggled ON the page itself
			if($formative==true)
			{

				// Get the correct reponse(s)
				$optionsRS = qtl_queries::getResponseOptions($questionID, $optionOrderType);


				// DEfine the Vars
				$correctStr="";
				$IDStr="";



				foreach ($optionsRS as $myOptions)
				{	
					$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);
					$optionID= $myOptions['optionID'];	
					$optionValue= $myOptions['optionValue'];	
					$correctFeedbackStr='';
					$incorrectFeedbackStr ='';
	
					switch ($qType)
					{
						case "text":
							$IDStr.=strtolower($optionValue).',';
						break;	
		
						case "blank":
//							$correctStr = $optionValue;
							$blankOptions = unserialize($optionValue);
							$correctStr="";
							$incorrectFeedbackStr="";			
							$IDStr=$blankCount.','; // have to add a comma as it gets removed later on inline with other question types
			
							foreach($blankOptions as $KEY => $blankResponses)
							{
								$theseOptions = $blankResponses[0]; // Get the options
								// Add these options as the values to the input boxes
								$$KEY = qtl_utils::convertTextFromDB($theseOptions);
							}
			
							if($blankCount>=1)
							{

								$i=1;
								while($i<=$blankCount)
								{
									$correctStr.= ${'answers'.$i}.'|';
									$tempCorrectFeedback = ${'blank_correct_feedback_'.$i};
									$tempCorrectFeedback = rawurlencode($tempCorrectFeedback);


					
									$tempIncorrectFeedback = ${'blank_incorrect_feedback_'.$i};
									$tempIncorrectFeedback = rawurlencode($tempIncorrectFeedback);
					
					
									$correctFeedbackStr.= $tempCorrectFeedback.'|';
									$incorrectFeedbackStr.= $tempIncorrectFeedback.'|';
									$i++;
								}			
							}	
			
									$myArray = array("test1", "test2", "test3");
					
									$testVar = 'my test';
									?>
									<script type="application/javascript">
									function myFunction(myVar)
									{
										//alert(myVar);	
									}
									myFunction(<?php echo json_encode($myArray); ?>);

									</script>


									<?php			
		
						break;
		
						default:
							$IDStr.=$optionID.','; // Add ALL the optinos to the IDstring aray for checking later
						break;
		
					}
	
	
					$isCorrect = $myOptions['isCorrect'];	
	

					if($isCorrect==1)
					{
						$correctStr.=$optionID.','; // Add only the correct IDs to the array. If its a radio this will be one value
					}
				}	

				// Remove the last comma
				$correctStr = substr($correctStr,0,-1);
				$IDStr = substr($IDStr,0,-1);
	
				$questionStr.= '<input type="submit" value="'.$buttonText.'" onclick="';

				$questionStr.='checkExampleQuestionExampleAnswer('.$questionID.', \''.$qType.'\', \''.$correctStr.'\', \''.$IDStr.'\', \''.$correctFeedbackStr.'\', \''.$incorrectFeedbackStr.'\' );';


				// only call this if they are logged in
				if($saveResponse==true && is_user_logged_in() )
				{
					$current_user = wp_get_current_user();
					$username = $current_user->user_login;
					//$questionStr.='ajaxQuestionResponseUpdate(\''.$refectionTextBoxID.'\', \''.$questionID.'\', \''.$username.'\')';
					$questionStr.='ajaxQuestionResponseUpdate(\''.$refectionTextBoxID.'\', \''.$questionID.'\', \''.$IDStr.'\', \''.$qType.'\', \''.$username.'\')';
				}

				$questionStr.='">';

				if($qType=="blank")
				{
							// Add extra feedback div for this question type			
							$questionStr.='<div id="blank_feedback_'.$questionID.'"></div>';
				}
	

				$questionStr.= '<!--QTLfeedbackStart--><div id="mainFeedbackDiv">';	
	
				$questionStr.= '<div id="exampleQuestionAnswerCorrect'.$questionID.'" class="qtl_hidden">';


				if($qType=="reflection" || $qType=="reflectionText")
				{
					$correctFeedbackDivID = "reflectionFeedbackDiv";
				}
				else
				{
					$correctFeedbackDivID = "exampleQuestionAnswerCorrect".$questionID;	
					$questionStr.= '<span class="correct QTLCorrectStyle'.$questionID.'">'.$correctText.'</span>';	// Don't show 'correct answer if reflection
					//$questionStr.= '<span class="correct" style="color:'.get_option('reflectiveFeedbacktextColour') .';background-color:'.get_option('reflectiveFeedbackBoxColour') .'">Correct</span>';	// Don't show 'correct answer if reflection
				}

				if($correctFeedback)
				{
					$questionStr.= '<div id="'.$correctFeedbackDivID.'" class="correctFeedbackDiv" style="color:'.get_option('correctFeedbacktextColour') .';background-color:'.get_option('correctFeedbackBoxColour') .'">'.$correctFeedback.'</div>';
				}
				$questionStr.= '</div>';

				$questionStr.= '<div id="exampleQuestionAnswerInCorrect'.$questionID.'" class="qtl_hidden">';
				$questionStr.= '<span class="incorrect QTLIncorrectStyle'.$questionID.'">'.$incorrectText.'</span>';

				if($incorrectFeedback)
				{
					$questionStr.= '<div class="incorrectFeedbackDiv" style="color:'.get_option('incorrectFeedbacktextColour') .';background-color:'.get_option('incorrectFeedbackBoxColour') .'">'.$incorrectFeedback.'</div>';
				}
				$questionStr.= '</div>';


				$questionStr.= '</div><!--QTLfeedbackEnd-->'; // End of the feedback div
				$questionStr.= '</div>'; // End of question div
				$questionStr.= '</div>'; // End of the exam div


			}
	
			return $questionStr;
	
	
		}

		public static  function drawMarkedQuestion($questionID, $optionOrder, $response="", $showFeedback="yes")
		{
	
			global $correctText;
			global $incorrectText;
	
	
	
			// Set some vars
			$incorrectCheck="";
	
			// get the info about that question	
			$questionInfo = qtl_queries::getQuestionInfo($questionID);
			$question = qtl_utils::convertTextFromDB($questionInfo['question']);
			$correctFeedback = qtl_utils::convertTextFromDB($questionInfo['correctFeedback']);
			$incorrectFeedback = qtl_utils::convertTextFromDB($questionInfo['incorrectFeedback']);
			$question = wpautop($question);
			$correctFeedback = wpautop($correctFeedback);
			$incorrectFeedback = wpautop($incorrectFeedback);
			$qType = $questionInfo['qType'];
			$optionOrderType = $questionInfo['optionOrderType'];
	
			// Up the max possible score based on question type
	
			if($qType<>"blank")
			{
				$_SESSION['possibleMaxScore']++;
			}
	
	
			// Assume they got it right, then we check for wrong answers later
			$correctResponse = "";
	
			if($qType<>"text")
			{	
				// get the response options and put them in alookup array for the actual order
				$optionArrayLookup = array();
				$optionsRS = qtl_queries::getResponseOptions($questionID);
				foreach ($optionsRS as $myOptions)
				{	
					$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);
					$optionID = $myOptions['optionID'];
					$optionArrayLookup[$optionID] = $optionValue;
				}

			}	
	
	
			if($qType=="check") // If its checkbox turn the values into an array
			{
				$responseArray= explode(",", $response);
			}
	
			// If its fillin the blanks, add the blanks and their response
			if($qType=="blank")
			{


				// Firstly get the correct response options and put in array $answers1, $answers2 and the same for the corret and incorrect feedback

				$blankOptions = array();
				foreach ($optionsRS	as $myOptions)
				{	
					$blankOptions = unserialize($myOptions['optionValue']);
				}


				foreach($blankOptions as $KEY => $blankResponses)
				{
					$theseOptions = $blankResponses[0]; // Get the options
	
					// Add these options as the values to the input boxes
					$$KEY = $theseOptions;
				}

				// Finally replace the BLANKS with the text boxes
				$blankCount =  substr_count($question, '[blank]'); // Count the number of blanks

				// Up the possible max score by the number of blanks available
				$_SESSION['possibleMaxScore'] = $_SESSION['possibleMaxScore']+$blankCount;

				$i = 1;
				while (strpos($question, '[blank]') !== false)
				{
					$question = preg_replace('[blank]', 'replace-me-'.$i++, $question, 1);
				}

				// Now go through and replace the inserts with actual inputs. Done as preg replace didn't seem to work properly :(
				$myBlankResponseArray=array(); // Create blank array for submitted repsonses
				$correctBlankResponsesArray = array();
				$i=1;
				while($i<=$blankCount)
				{
	
					// Turn the possible answers into an array
					$tempBlankCorrectArray = explode(",", ${'answers'.$i});
					$correctBlankResponsesArray[$i] = $tempBlankCorrectArray;
	
					// Trim all whuitepsaces before and after
					$tempBlankCorrectArray = array_map('trim', $tempBlankCorrectArray);
	
					// Get the submitted value of this box
					$thisSubmittedValue = trim(strtolower($response[$i-1]));
					$myBlankResponseArray[] = $thisSubmittedValue;
	
					// Check if its right and colour the boxes acoordingly
					if (in_array($thisSubmittedValue, $tempBlankCorrectArray))
					{
						$thisBoxClass = "correctFeedbackDiv";
					}
					else
					{
						$thisBoxClass = "incorrectFeedbackDiv";		
					}
						
					// Replace the Blank with textbox
					$thisReplacement = '[replace-me-'.$i.']';
					$question = str_replace($thisReplacement, '<input type="text" size="10" id="blank_'.$questionID.'_'.$i.'" value="'.stripslashes($thisSubmittedValue).'" readonly class="'.$thisBoxClass.'">', $question);
					$i++;
				}

				$newQuestion= str_replace('[blank]', '<input type="text" value="" size="10">', $question);
				$markedQuestionStr= $newQuestion;


			}
			else
			{
	
				$markedQuestionStr= $question;
			}
	

	
			switch ($qType)
			{
				case "text":
					if(!$response)
					{
						$response = trim(strtolower($_POST['textBoxID'.$questionID]));
					}
	
	
					$markedQuestionStr.='<input type="text" size="10" value="'.$response.'" readonly /><br/>';
	
	
					$response = trim(strtolower($response));
	
					$possibleAnswerArray = array();
					$optionsRS = qtl_queries::getResponseOptions($questionID);
					foreach ($optionsRS as $myOptions)
					{
						$optionValue = strtolower(qtl_utils::convertTextFromDB($myOptions['optionValue']));
						$possibleAnswerArray[]=$optionValue;
					}
	
	
					if (in_array($response, $possibleAnswerArray))
					{
						$correctResponse=true;
					}	


				break;

				case "radio":
				case "check":	

					$markedQuestionStr.= '<table width="90%">'.chr(10);	
	
					foreach ($optionOrder as $optionInfo)
					{
	//					$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);
	//					$optionID= $myOptions['optionID'];
						$optionID = $optionInfo['optionID'];
						$isCorrect = $optionInfo['isCorrect'];
						$optionValue = $optionArrayLookup[$optionID];
		
						$checked = "";
		
						$markedQuestionStr.= '<tr>'.chr(10);
						$markedQuestionStr.= '<td width="10">';
		
						if($qType=="radio")
						{
							if($response==$optionID){$checked = 'checked';}
							$markedQuestionStr.= '<input type="radio" id="option'.$optionID.'" name="question'.$questionID.'" '.$checked.' disabled="disabled">';
							if($isCorrect==1 && $response==$optionID){$correctResponse=true;}
			
						}
						elseif($qType=="check")
						{
							if (in_array($optionID, $responseArray)){$checked= 'checked';}	
							$markedQuestionStr.= '<input type="checkbox" id="option'.$optionID.'" name="question'.$questionID.'_option'.$optionID.'" '.$checked.' disabled="disabled">';
							if($isCorrect==1 && $checked=='checked') // Its correct and Checked - CORRECT ANSWER
							{
								$correctResponse=true;
							}
							elseif($checked=='checked') // Its incorrect and Checked - INCORRECT ANSWER
							{
								$incorrectCheck=true;
							}
							elseif($isCorrect && $checked=="")// Its correctanswer and UNChecked - INCORRECT ANSWER
							{
								$incorrectCheck=true;
							}
			
						}
						$markedQuestionStr.= '<td><label for="option'.$optionID.'">';
		
						if($checked==true){$markedQuestionStr.= '<b>';}
						$markedQuestionStr.= $optionValue;
						if($checked==true){$markedQuestionStr.= '</b>';}
		
						$markedQuestionStr.= '</td>'.chr(10);
						$markedQuestionStr.= '</tr>'.chr(10);
					}
					$markedQuestionStr.= '</table>'.chr(10);
					if($incorrectCheck==true){$correctResponse=false;} // If its a checkbox and incorrectChecl is TRUE it means they got one wrong			


				break;	


				case "blank":
				$blank_feedback="";

				$i=1;
				while($i<=$blankCount)
				{


					// Lookup the possible answers for this.
					$blank_feedback.='<div style="margin:5px 0px 5px 0px; padding:5px; border:1px solid #ccc"><b>'.__('Blank ', 'qtl').$i.' '.__('Possible Correct Answers', 'qtl').'</b><br/>';
					$blank_feedback.= "<ol>";

					$theseCorrectAnswersArray = $correctBlankResponsesArray[$i];
					$theseCorrectAnswersArray = array_map('trim', $theseCorrectAnswersArray); // Remove white spaces
	
					
	
					// Get the correct words as a string for feedback
					foreach($theseCorrectAnswersArray as $tempCorrectAnswer)
					{
						$blank_feedback.='<li>'.stripslashes($tempCorrectAnswer).'</li>';
					}
					$blank_feedback.='</ol>';
	
					$myResponse = $myBlankResponseArray[$i-1];

					if($myResponse==""){$myResponseText=__('No answer given','qtl');}else{$myResponseText = $myResponse;}
					$blank_feedback.= __('Your answer: ','qtl').'<b>'.stripslashes($myResponseText).'</b><br/>';

	
					// Check if its right
					if (in_array($myResponse, $theseCorrectAnswersArray))
					{
						$thisFeedback = ${'blank_correct_feedback_'.$i};
						$blank_feedback.='<span class="correct">'.__('Correct','qtl').'</span>';
		
		
						if($thisFeedback)
						{
							$blank_feedback.='<div class="correctFeedbackDiv">'.qtl_utils::convertTextFromDB(wpautop($thisFeedback)).'</div>';
						}
						$_SESSION['totalCorrect']++;
		
		
					}
					else
					{
						$thisFeedback = ${'blank_incorrect_feedback_'.$i};		
						$blank_feedback.='<span class="incorrect">'.__('Incorrect','qtl').'</span>';
						if($thisFeedback)
						{
							$blank_feedback.='<div class="incorrectFeedbackDiv">'.qtl_utils::convertTextFromDB(wpautop($thisFeedback)).'</div>';
						}
					}	
	
	
					$i++;
					$blank_feedback.='</div>'; // //Close the answer div for this blank	
	
				}


				$markedQuestionStr.= $blank_feedback;




				break;
			}
	
	
			if($qType<>"blank")
			{
				if($correctResponse==true)
				{
					$_SESSION['totalCorrect']++;
					$markedQuestionStr.= '<span class="correct">'.$correctText.'</span>';
					if($correctFeedback && $showFeedback=="yes")
					{
						$markedQuestionStr.= '<div id="correctFeedbackDiv">'.$correctFeedback.'</div>';
					}
				}
				else
				{
					$markedQuestionStr.= '<span class="incorrect">'.$incorrectText.'</span>';
					if($incorrectFeedback && $showFeedback=="yes")
					{
						$markedQuestionStr.= '<div id="incorrectFeedbackDiv">'.$incorrectFeedback.'</div>';
					}
				}
			}
	
			$markedQuestionStr.='<hr/>';
	
			return $markedQuestionStr;
		}





		public static function startQuiz($atts)
		{
			global $overrideAdminCheck; // we need to load the plugin scripts but override the is admin check
			extract(shortcode_atts(array('id' => '#'), $atts));
			$quizID = $id;
			$overrideAdminCheck=true;
			qtl_initialise::QTL_loadMyPluginScripts(); // Load up the plugin scripts for the front end (define true to override admin check)
	
			$quizStr = self::drawQuizPage($quizID);
			return $quizStr;
		}

		public static  function drawExampleQuestion($atts)
		{
			global $overrideAdminCheck; // we need to load the plugin scripts but override the is admin check
	
			$atts = shortcode_atts(
				array(
					'id'   => '#',
					'savedata'   => '',
					'button'   => '',
					'correctfeedback'   => '',
					'incorrectfeedback'   => '',
					'iconset'   => ''	
				),
				$atts
			);
	
			$questionID = (int) $atts['id'];
			$iconset = (int) $atts['iconset'];	
			$saveResponse = esc_attr($atts['savedata']);
			$button = esc_attr($atts['button']);
			$correctText = esc_attr($atts['correctfeedback']);
			$incorrectText = esc_attr($atts['incorrectfeedback']);
	
			if($iconset==""){$iconset=1;}
			if($correctText==""){$correctText=__('Correct','qtl');}
			if($incorrectText==""){$incorrectText=__('Incorrect','qtl');}	
			if($button==""){$button=__('Check Answer', 'qtl');} // If button is blank default to "check answer" text

			$overrideAdminCheck=true; // Allow scripts to be loaded from front end
			$questionSettingArray = array(
				'saveResponse'=> $saveResponse,
				'buttonText'=> $button,
				'correctText'=> $correctText,
				'incorrectText'=> $incorrectText,
				'iconset'=> $iconset

			);
	
			qtl_initialise::QTL_loadMyPluginScripts(); // Load up the plugin scripts for the front end (define true to override admin check)
	
			$questionStr = qtl_quiz_draw::drawQuestion($questionID, "", true, $questionSettingArray);
			return do_shortcode($questionStr);
		}

		public static  function drawUserResponse($atts)
		{
	
			$response = "";
			$atts = shortcode_atts(
				array(
					'id'   => '#'
				),
				$atts
			);
	
			$questionID = (int) $atts['id'];
	
			// Get the question type
			$questionInfo = qtl_queries::getQuestionInfo($questionID);
			$qType = $questionInfo['qType'];
	
			$current_user = wp_get_current_user();
			$username = $current_user->user_login;
	
			if($username)
			{
				$responseInfo = qtl_queries::getQuestionResponse($questionID, $username);
				$response = qtl_utils::convertTextFromDB($responseInfo['userResponse']);

				// if its anything other than a reflection type then get the actual value form the options database
				if($qType=="radio")
				{
					$responseArray = explode(",", $response, 2);
					$thisOptionID = $responseArray[0];	
					$optionInfo = qtl_queries::getResponseOptionInfo($thisOptionID);	
					$response = $optionInfo->optionValue;
					$response = qtl_utils::convertTextFromDB($response);
				}
			}	
	
			return $response;
		}

		public static  function drawUserScore($atts)
		{
	
			$myScore = "";
			$atts = shortcode_atts(
				array(
					'id'   => '#',
					'showall' => 'false',
				),
				$atts
			);
	
			$quizID = (int) $atts['id'];
			$showall = $atts['showall'];

			$current_user = wp_get_current_user();
			$username = $current_user->user_login;
	
	
			if($username)
			{
	
				// Get previous attempts info
				$previousAttemptInfo = qtl_queries::getAttemptInfo($username, $quizID);

				if($previousAttemptInfo)
				{

					if($showall=="true")
					{


						//dataTables js
						wp_register_script( 'datatables', ( '//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js' ), false, null, true );
						wp_enqueue_script( 'datatables' );
		
						//dataTables css
						wp_enqueue_style('datatables-style','//cdn.datatables.net/1.10.5/css/jquery.dataTables.min.css');	
		
						global $wp_scripts;
						// get the jquery ui object
						$queryui = $wp_scripts->query('jquery-ui-core');
					
						// load the jquery ui theme
						$url = "https://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";	
						wp_enqueue_style('jquery-ui-smoothness', $url, false, null);	
		
						wp_register_style( 'QTL_css_custom',  plugins_url('../css/qtl-styles.css',__FILE__) );
						wp_enqueue_style( 'QTL_css_custom' );



						$myAttempts = qtl_queries::getAllUserAttemptInfo($username, $quizID);
		
						$myScore.='<table id="myScores">';
						$myScore.='<thead><tr><th>'.__('Score', 'qtl').'</th><th>'.__('Finish Date', 'qtl').'</th></thead>';
		
		
		
						$attemptCount = count($myAttempts);
						foreach($myAttempts as $attemptInfo)
						{
							$score = $attemptInfo['score'];
							$dateFinished= $attemptInfo['dateFinished'];
			
							if($dateFinished)
							{
								$myScore.= '<tr>';
								$myScore.= '<td>'.$score.'</td>';
								$myScore.= '<td>'.$dateFinished.'</td>';
								$myScore.= '</tr>';
							}
	
						}
						$myScore.= '</table>';
		
		
		
						?>
						<script>
							jQuery(document).ready(function(){	
								if (jQuery('#myScores').length>0)
								{
									jQuery('#myScores').dataTable({
										"bAutoWidth": true,
										"bJQueryUI": true,
										"bFilter": false,
										"sPaginationType": "full_numbers",
										"iDisplayLength": 10, // How many numbers by default per page
						

									});
								}

							});
						</script>	

						<?php
	
					}
					else
					{
						foreach ($previousAttemptInfo as $key => $value)
						{
							$$key = $value;
						}

						if($highestScore==""){$highestScore=0;}
	
						$myScore = '<div style="border:solid 1px #ccc; padding:5px; background:#f1f1f1">'.__('Number of times you have taken this test :','qtl').$attemptCount.'<br/>'. __('Your highest acheaved score: ', 'qtl').$highestScore.'%</b>.<br/>';
					}

				}	
				else
				{
					$myScore = __('You have not yet attempted this quiz', 'qtl');
				}
			}
	
			return $myScore;
	
		}

		public static  function drawLeaderboard($atts)
		{
			$leaderboardStr = "";
			$atts = shortcode_atts(
				array(
					'id'   => '#',
					'anonymous'   => ''	
				),
				$atts
			);
	
			$quizID = (int) $atts['id'];
			$anonymous = $atts['anonymous'];	

	
			if($quizID)
			{


				//dataTables js
				wp_register_script( 'datatables', ( '//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js' ), false, null, true );
				wp_enqueue_script( 'datatables' );

				//dataTables css
				wp_enqueue_style('datatables-style','//cdn.datatables.net/1.10.5/css/jquery.dataTables.min.css');	

				global $wp_scripts;
				// get the jquery ui object
				$queryui = $wp_scripts->query('jquery-ui-core');
			
				// load the jquery ui theme
				$url = "https://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";	
				wp_enqueue_style('jquery-ui-smoothness', $url, false, null);	

				wp_register_style( 'QTL_css_custom',  plugins_url('../css/qtl-styles.css',__FILE__) );
				wp_enqueue_style( 'QTL_css_custom' );




				// Get the quiz info
				$quizInfo = qtl_queries::getQuizInfo($quizID);
				$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);

				// Get the results
				$quizResults = qtl_queries::getQuizResults($quizID);

				$resultCount = count($quizResults);
				$leaderboardStr = '<h2>'.$quizName.' '._e('Leaderboard', 'qtl') .'</h2>';
				if($resultCount>=1)
				{
	
	
					// Get a list of all users in array
					$blogusers = get_users();	
	
	
					$userLookupArray = array();
					// Array of WP_User objects.
					foreach ( $blogusers as $userInfo )
					{
						$fullname = esc_html( $userInfo->display_name );
						$firstName= esc_html( $userInfo->first_name );
						$surname= esc_html( $userInfo->last_name );
						$username = $userInfo->user_login;	
						$userLookupArray[$username] = $firstName.' '.$surname;
		
					}
	
					$leaderboardStr.='<table id="leaderboard">';
					$leaderboardStr.='<thead><tr><th>'.__('Name', 'qtl').'</th><th>'.__('Attempts', 'qtl').'</th><th>'.__('Highest Score', 'qtl').'</th></thead>';
					foreach($quizResults as $attemptInfo)
					{

						$attemptCount = $attemptInfo['attemptCount'];
						$highestScore = $attemptInfo['highestScore'];	
						$attemptID= $attemptInfo['attemptID'];
						$username = $attemptInfo['username'];
						if($anonymous==true)
						{
							$name = __('Anonymous User ', 'qtl').$attemptID;	
						}		
						$leaderboardStr.= '<tr>';	
						$leaderboardStr.= '<td>'.$userLookupArray[$username].'</td>';
						$leaderboardStr.= '<td>'.$attemptCount.'</td>';
						$leaderboardStr.= '<td>'.$highestScore.'%</td>';
						$leaderboardStr.= '</tr>';
					}
	
					$leaderboardStr.='</table>';
				}
				else
				{
					$leaderboardStr.= -e('Nobody has tried this quiz yet.', 'qtl');
				}

			}
	
			?>
			<script>
				jQuery(document).ready(function(){	
					if (jQuery('#leaderboard').length>0)
					{
						jQuery('#leaderboard').dataTable({
							"bAutoWidth": true,
							"bJQueryUI": true,
							"sPaginationType": "full_numbers",
							"iDisplayLength": 50, // How many numbers by default per page
							"order": [[2, "desc"]]
						});
					}

				});
			</script>	
			<?php
	
			return $leaderboardStr;
	
		}


		public static  function showCountdown($countdownMinutes, $countdownSeconds)
		{
	
			$countdownAction='';
			$startTime = $countdownMinutes*60000 + $countdownSeconds*1000;  //(in milliseconds)



			//Timer Scripts
			$str  = '<script type="text/javascript" src="'.AIQUIZ_ABS_PATH.'/scripts/jquery/timer/jquery.timer.js"></script>';
	
			$str .= '<script type="text/javascript"> ';
	
		//global vars
			$str .= 	' var startTime = "' . $startTime . '"; ';
			$str .=		' var QU_AUTO_PROGRESS = "' . $countdownAction . '"; ';
			$str .= 	' var COUNTDOWN_GLOBAL = "'.$startTime.'"; ';	
	
	
			//cleanup function
			$str .= 	' function clear_interval(id){ ';
			$str .= 	'  clearInterval(id); ';
			$str .= 	' }; ';

			//check timer function
			$str .= 	' function check_jqtimer_time () { ';
			$str .= 	'  if ( COUNTDOWN_GLOBAL == 0 ) { ';
	
			$str .= 	'   clear_interval( THE_HANDLE_ID ); ';
	
			$str .= 	'	submitQTL_quiz(); ';
	
			$str .= 	'  } ';
			$str .= 	' }; ';	
	
			//initialise the interval check
			$str .= 	' var THE_HANDLE_ID = setInterval( check_jqtimer_time, 200 ); ';

			$str .= '</script>';
	
			// Start of the coutndown timer
			$str .= '<script type="text/javascript" src="'.AIQUIZ_ABS_PATH.'/scripts/jquery/timer/timer.js"></script>';
	
	
	
			//draw timer display element
			$str .= '<span id="countdown"></span>';
	

			$str .= '<br/>';
			$str .= '<br/>';	
	
	
			return $str;
		}
	}
}
?>