<h1><?php _e('Quiz List', 'qtl') ?></h1>

<a href="?page=ai-quiz-quiz-edit" class="button-primary"><?php _e('Add a new quiz', 'qtl') ?></a>
<?php

$feedback = "";
if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	if($action=="quizDelete")
	{
		$quizID = $_GET['quizID'];
		qtl_actions::quizDelete($quizID);
		$feedback = '<div class="updated">'.__('Quiz deleted', 'qtl').'</div>';
	}
}

if($feedback){echo $feedback;}

	global $wpdb;

$quizRS = qtl_queries::getQuizzes($order='DESC');
$quizCount = count($quizRS);

if($quizCount>=1)
{

	echo '<div id="quiztable">';
	echo '<table class="widefat"><thead>';
	echo '<tr><th class="row-title">'.__('Quiz Name', 'qtl').'</th><th>'. __('Shortcode').'</th><th>'.__('Number of Participants', 'qtl').'</th><th>'.__('Grade Boundaries', 'qtl').'</th><th>'.__('View Results', 'qtl').'</th><th>'.__('Delete Quiz', 'qtl').'</th></tr></thead><tbody>';

	foreach ($quizRS as $myQuizzes)
	{		
		$quizName = stripslashes($myQuizzes['quizName']);
		$quizID= $myQuizzes['quizID'];
		
		// Get the count of people who have taken the quiz
		$quizParticipants =  qtl_queries::getQuizResults($quizID);
		$participantCount = count($quizParticipants);
		
		echo '<tr>';
		echo '<td><a href="?page=ai-quiz-quiz-edit&quizID='.$quizID.'">'.$quizName.'</a></td>';
		echo '<td>[QTL-Quiz id='.$quizID.']</td>';
		echo '<td>'.$participantCount.'</td>';
		echo '<td><a href="?page=ai-quiz-boundaries&quizID='.$quizID.'" class="boundaryIcon" >'.__('Grade Boundaries', 'qtl').'</a></td>';
		echo '<td><a href="?page=ai-quiz-results&quizID='.$quizID.'" class="dataIcon">'.__('View Results', 'qtl').'</a></td>';

		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=QuizDeleteCheck'.$quizID.'" class="thickbox deleteIcon" >'.__('Delete Quiz', 'qtl').'</a>';
		echo '<div id="QuizDeleteCheck'.$quizID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>'.__('Are you sure you want to delete quiz:', 'qtl').'<br>'.$quizName.'</h2>';		
		echo '<input type="submit" value="'.__('Yes, delete this quiz', 'qtl').'" onclick="location.href=\'?page=ai-quiz-quiz-list&quizID='.$quizID.'&action=quizDelete&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="'.__('Cancel').'" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	echo '</div>';
}
else
{
	echo '<hr/><span class="greyText">'.__('Create a quiz by clicking the button above', 'qtl').'</span>';
}
?>