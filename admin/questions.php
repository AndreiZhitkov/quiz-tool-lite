<div class="wrap">

<h1><?php _e('Settings') ?></h1>

<?php
// Define the variables

$potID = $_GET['potID'];
$potInfo = qtl_queries::getPotInfo($potID);

$potName = qtl_utils::convertTextFromDB($potInfo['potName']);

// Create a drop down list of question pots for the copy function
$potRS = qtl_queries::getQuestionPots();
$potCount = count($potRS);

$copyPotStr = "";

if($potCount==1) // Only one so don't show drop down, just hidden unput
{
	$copyPotStr.= _e('This will copy the question to the current question pot', 'qtl').'<br/><br/>';
	$copyPotStr.= '<input type="hidden" name="copyQuestionPot" value="'.$potID.'">';
}
else
{
	$copyPotStr.= '<select name="copyQuestionPot">';
	foreach ($potRS	as $myPots)
	{
		$copyPotName = qtl_utils::convertTextFromDB($myPots['potName']);		
		$tempPotID= $myPots['potID'];
		$copyPotStr.= '<option value="'.$tempPotID.'"';
		if($tempPotID==$potID){$copyPotStr.=' selected';}
		$copyPotStr.= '>'.$copyPotName.'</option>';
	}

	$copyPotStr.= '</select><br/><br/>';
}

?>
<h1><?php echo $potName?></h1>
<a href="admin.php?page=ai-quiz-home" class="backIcon"><?php _e('Return to all question pots', 'qtl') ?></a>
<hr/>
<?php

$feedback="";

if(isset($_GET['action']))
{
	$myAction = $_GET['action'];
	
	switch ($myAction) {
		case "questionDelete":
			$questionID=$_GET['questionID'];
			qtl_actions::questionDelete($questionID);
			$feedback = '<div class="updated">'._e('Question Deleted', 'qtl').'</div><br/>';
		break;
		
		case "questionCopy":
			qtl_actions::questionCopy();
			$feedback = '<div class="updated">'._e('Question copied succesfully', 'qtl').'</div><br/>';
		break;			
	}	
	
}

if($feedback)
{
	echo $feedback;
}

echo '<a href="admin.php?page=ai-quiz-questionType&potID='.$potID.'" class="button-primary">'.__('Add a new question', 'qtl').'</a>';

// Get the questions in this pot
$questionsRS = qtl_queries::getQuestionsInPot($potID);

$questionCount = count($questionsRS);

if($questionCount==0)
{
	echo '<br/><br/><span>'.__('No questions found', 'qtl').'</span>';
}
else
{
	echo '<div id="quiztable">';
	echo '<table class="widefat"><thead><tr>
	<th>#</th>
	<th>'.__('Question', 'qtl').'</th>
	<th>'.__('Shortcode', 'qtl').'</th>
	<th></th><th></th><th></th></tr></thead>';
	$i = 1; // Increment for question numbner. Meaningless as its randomised buy hey.
		
	foreach ( $questionsRS as $myQuestions ) 
	{
		
		$question = qtl_utils::convertTextFromDB($myQuestions['question']);
		$question = do_shortcode(wpautop($question));
		$question = qtl_utils::limitWords($question, 100);
		$questionID= $myQuestions['questionID'];
		
		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$question.'</td>';
		echo '<td>[QTL-Question id='.$questionID.']</td>';		
		echo '<td>';
		echo '<a href="admin.php?page=ai-quiz-question-edit&questionID='.$questionID.'" class="editIcon">'.__('Edit').'</a>';
		echo '</td>';
		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=200&inlineId=questionCopy'.$questionID.'" class="thickbox copyIcon">'.__('Copy').'</a>';		
		echo '</td>';
		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=120&inlineId=questionDeleteCheck'.$questionID.'" class="thickbox deleteIcon">'.__('Delete').'</a>';

		// Copy popup
		echo '<div id="questionCopy'.$questionID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h3>'. _e('Select destination question pot', 'qtl') .'</h2>';
		echo '<form method="post" action="?page=ai-quiz-question-list&potID='.$potID.'&action=questionCopy">';
		echo $copyPotStr;
		echo '<input type="hidden" name="questionToCopy" value="'.$questionID.'">';
		echo '<input type="submit" value="'.__('Copy question', 'qtl').'" class="button-primary">';
		echo '<input type="submit" value="'. __('Cancel').'" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</form>';
		echo '</div>';
		echo '</div>';
		// End copy popup	

		// Delete Popup
		echo '<div id="questionDeleteCheck'.$questionID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h3>'.__('Are you sure you want to delete question', 'qtl').' '.$i.'?</h3>';
		echo '<input type="submit" value="'.__('Yes, delete this question', 'qtl') . '" onclick="location.href=\'?page=ai-quiz-question-list&potID='.$potID.'&action=questionDelete&questionID='.$questionID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="'.__('Cancel') . '" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		// End delete popup
		
		echo '</td>';
		
		echo '</tr>';
		$i++;
	}
	echo '</table>';
	echo '</div>';
}

?>
</div> <!-- class="wrap" -->