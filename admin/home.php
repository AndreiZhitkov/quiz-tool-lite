<div class="wrap">

<h1><?php _e('Question Pots','qtl') ?></h1>
<form action="admin.php?page=ai-quiz-home&action=potCreate" method="post" name="newPotForm">
<a href="javascript:toggleLayerVis('newPotDiv'); document.newPotForm.potName.focus();" class="button-primary">
<?php _e('Create a new question pot','qtl') ?>
</a>
	<div id="newPotDiv" style="display:none; padding-top:5px;">
	<input type="text" name="potName" id="potName" placeholder="<?php _e('Enter Quiz Name','qtl') ?>"  style="width:250px"/>
	<input type="submit" value="<?php _e('Create a new question pot','qtl') ?>" class="button-secondary"/>
	</div>
</form>

<?php

$feedback="";

if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	switch ($action) {
		case "potCreate":
			$feedback = qtl_actions::questionPotCreate();
			break;
			
		case "potEdit":
			$feedback = qtl_actions::questionPotEdit();
			break;	

		case "potDelete":
			$potID = $_GET['potID'];
			$feedback = qtl_actions::potDelete($potID);
			break;
	}
}

if($feedback)
{
	echo $feedback;
}

// Get the current question pots

$potRS = qtl_queries::getQuestionPots();
$potCount = count($potRS);

if($potCount>=1)
{
	
	echo '<div id="quiztable">';
	echo '<table class="widefat"><thead>';
	echo '<tr><th>#</th>
	<th>'.__('Pot Name', 'qtl').'</th>
	<th>'.__('Number of questions', 'qtl').'</th>
	<th></th><th></th><th></th></tr></thead><tbody>';
	
	foreach ($potRS	as $myPots)
	{
		$potName = qtl_utils::convertTextFromDB($myPots['potName']);
		$potID= $myPots['potID'];
		
		// Get the question count from those pots
		$questionRS = qtl_queries::getQuestionsInPot($potID);
		$questionCount = count($questionRS);

		echo '<tr>';
		echo '<td id="pot">'.$potID;
		echo '</td>';
		echo '<td><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'">'.$potName.'</a>';

		echo '<div id="potEdit'.$potID.'" style="display:none;">';
		echo '<form action="admin.php?page=ai-quiz-home&action=potEdit" method="post">';
		echo '<input name="potName" value="'.$potName.'" style="width:250px">';
		echo '<input name="potID" type="hidden" value="'.$potID.'">';
		echo '<input type="submit" value="'.__('Update').'" class="button-primary">';
		echo '<input type="submit" value="'.__('Cancel').'" onclick="toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\'); return false" class="button-secondary">';
		echo '</form>';
		echo '</div>';
		
		echo '</td>';
		echo '<td>';
		echo $questionCount;
		echo '</td>';

		echo '<td class="addIcon"><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'">'.__('Add or edit questions', 'qtl').'</a></td>';
		echo '<td class="editIcon"><a href="javascript:toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\');">'.__('Change Pot Name', 'qtl').'</a></td>';
		echo '<td class="deleteIcon"><a href="#TB_inline?width=400&height=190&inlineId=QuestionPotDeleteCheck'.$potID.'" class="thickbox">'.__('Delete').'</a></td>';
		echo '</tr>';

		// Delete Popup
		echo '<div id="QuestionPotDeleteCheck'.$potID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>'.__('Are you sure you want to delete this question pot?','qtl').'</h2><h2>'.$potName.'</h2>';
		echo '<span class="failText">'.__('This will delete all questions in this pot and cannot be undone!', 'qtl').'</span><br/><br/>';
		echo '<input type="submit" value="'.__('Yes, delete this question pot','qtl') . '" onclick="location.href=\'?page=ai-quiz-home&potID='.$potID.'&action=potDelete&potID='.$potID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="'.__('Cancel').'" onclick="self.parent.tb_remove();return false" class="button-secondary">';
		echo '</div>';
		echo '</div>';
		// End Delete Popup
	}
	echo '</tbody></table>';
	echo '</div>';
}
else
{
	echo '<h2>'. __('Welcome to Quiz Tool Lite', 'qtl') .'</h2>';
	echo '<span>'. __('All questions you create need to be within a question "pot, e.g. "Maths Questions" or "Difficult Questions"<br/><br/>Create a question pot by clicking the button above.','qtl');
	echo '<hr/>'.__('For more information and help please read the <a href="admin.php?page=ai-quiz-help">help pages</a> or visit the <a href="https://wordpress.org/support/plugin/quiz-tool-lite">support forum</a>', 'qtl').'</span>';
}

?>
</div>