<?php

if(isset($_GET['userAttemptID']))
{
	$userAttemptID = $_GET['userAttemptID'];
	$attemptInfo= qtl_queries::getUserAttemptInfo($userAttemptID);
	$quizID= $attemptInfo['quizID'];
	$username= $attemptInfo['username'];

	$quizInfo = qtl_queries::getQuizInfo($quizID);
	$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);

	echo '<h1>'.$quizName.'</h1>';
	echo '<a href="?page=ai-user-results&quizID='.$quizID.'&username='.$username.'" class="backIcon">'. __('Back to user results', 'qtl').'</a><hr/>';

	$quizBreakdown = qtl_quiz_draw::markTest($quizID, $attemptInfo);
	echo $quizBreakdown;

}

?>