<?php

if(isset($_GET['quizID']) && isset($_GET['username']))
{
	$quizID = $_GET['quizID'];
	$username = $_GET['username'];	
	$quizInfo = qtl_queries::getQuizInfo($quizID);
	$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
	echo '<h1>'.$quizName.'</h1>';
	echo '<a href="?page=ai-quiz-results&quizID='.$quizID.'" class="backIcon">'. __('Back to user results', 'qtl').'</a><hr/>';


	drawUserResults($username, $quizID);
	qtl_utils::loadDatatables();

}

function drawUserResults($username, $quizID)
{
	$userInfo = get_user_by( 'login', $username );
	$fullname =  $userInfo->first_name . ' ' . $userInfo->last_name;
	$userID=  $userInfo->ID;

	echo '<h3>'.get_avatar( $userID, 64 ).' '.$fullname.'</h3>';
	 
	echo '<a href="admin.php?page=ai-quiz-results&download=stdres&userID='.$userID.'&quizID='.$quizID.'" class="button-primary">'.__('Export user results as CSV','qtl').'</a><br><br>';
	
	echo '<table id="userTable" class="display">';
	echo '<thead><tr><th>'.__('Attempt', 'qtl').'</th><th>'. __('Attempt Date', 'qtl').'</th><th>'.__('Time taken', 'qtl').'</th><th>'.__('Score', 'qtl').'</th><th>'.__('Breakdown', 'qtl').'</th></tr></thead>';


	$attemptsRS = qtl_queries::getAllUserAttemptInfo($username, $quizID);
	$i=1;
	foreach($attemptsRS as $attemptInfo)
	{
		$userAttemptID = $attemptInfo['userAttemptID'];
		$dateStarted = $attemptInfo['dateStarted'];
		$niceDateStarted = qtl_utils::formatDate($dateStarted);
		$niceDateStarted = $niceDateStarted[2];
		$dateFinished = $attemptInfo['dateFinished'];
		$score= $attemptInfo['score'];
		
		if($dateFinished)
		{
			$timeTaken = qtl_utils::dateDiff(strtotime($dateStarted), strtotime($dateFinished));
		}
		else
		{
			$timeTaken = '<span class="failText">'.__('Uninished', 'qtl').'</span>';
		}

		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$niceDateStarted.'</td>';
		echo '<td>'.$timeTaken.'</td>';		
		echo '<td>'.$score.'</td>';
		echo '<td><a href="?page=ai-quiz_breakdown&userAttemptID='.$userAttemptID.'">'.__('View Answers', 'qtl').'</a></td>';
		echo '</tr>';
		$i++;
		
	}

	echo '</tbody></table>';
	?>
	<script>
		jQuery(document).ready(function(){	
			if (jQuery('#userTable').length>0)
			{
				jQuery('#userTable').dataTable({
					"bAutoWidth": true,
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"iDisplayLength": 50, // How many numbers by default per page
					"order": [[1, "desc"]]
				});
			}
			
		});
	</script>		

	<?php	
}

?>