<?php
// include tab startup
//require_once AIQUIZ_PATH.'scripts/tabs.php';
$quizOptionsArray=array();
$quizID="";
$feedback="";
$questionArray="";
$quizName="";
$maxAttempts="";
$timeAttemptsDay = "";
$startDate = "";
$endDate = "";
$timeLimit="";
$timeLimitCheck="";
$correctText = __('Correct','qtl');
$incorrectText = __('Incorrect','qtl');
$redirectPage = "";
$emailAdminList = "";
$quizFinishMessage = "";
$randomiseQuestions = "";
$requireUserLoggedIn="";
$emailAdminCheck ="";
$quizQuestionArray = array();

if(isset($_GET['action']))
{
	$action=$_GET['action'];

	if($action=="quizEdit")
	{
		$feedback = '<div class="updated">'.__('Quiz updated', 'qtl').'</div>';
		$quizID= qtl_actions::quizEdit();
	}
}

if(isset($_GET['quizID']))
{
	$quizID= $_GET['quizID'];
}


if($quizID=="")
{
	if(isset($_POST['quizID']))
	{
		$quizID = $_POST['quizID'];
	}

}


if($quizID)
{
	$quizInfo = qtl_queries::getQuizInfo($quizID);
	$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
	$questionArray = $quizInfo['questionArray'];
	$quizOptionsArray = $quizInfo['quizOptions'];

	// Unserialise the array
	$questionArray = unserialize($questionArray);
	$quizOptionsArray = unserialize($quizOptionsArray);
	$maxAttempts = $quizOptionsArray['maxAttempts'];
	$timeAttemptsDay = $quizOptionsArray['timeAttemptsDay'];
	$startDate = $quizOptionsArray['startDate'];
	$endDate = $quizOptionsArray['endDate'];
	$correctText = qtl_utils::convertTextFromDB($quizOptionsArray['correctText']);
	$incorrectText = qtl_utils::convertTextFromDB($quizOptionsArray['incorrectText']);
	if($correctText == ""){$correctText = "Correct";}
	if($incorrectText == ""){$incorrectText = "Incorrect";}

	$feedbackIcon = $quizOptionsArray['feedbackIcon'];


	if(isset($quizOptionsArray['redirectPage']))
	{
		$redirectPage = $quizOptionsArray['redirectPage'];
	}

	if(isset($quizOptionsArray['requireUserLoggedIn']))
	{
		$requireUserLoggedIn = $quizOptionsArray['requireUserLoggedIn'];
	}

	if(isset($quizOptionsArray['timeLimitCheck']))
	{
		$timeLimitCheck = $quizOptionsArray['timeLimitCheck'];
	}

	$quizFinishMessage = qtl_utils::convertTextFromDB($quizOptionsArray['quizFinishMessage']);
	$quizFinishMessage = wpautop($quizFinishMessage);


	$emailAdminList = $quizOptionsArray['emailAdminList'];
	$emailAdminArray = explode(",",$emailAdminList);

	$emailAdminCheck="";
	$userID = get_current_user_id();
	if (in_array($userID, $emailAdminArray))
	{
		$emailAdminCheck="checked";
	}

	if(isset($quizOptionsArray['timeLimitCheck']))
	{
		if ($quizOptionsArray['timeLimitCheck']=="on")
		{
			$timeLimitCheck="checked";
		}
	}

	// Make sure this new option is added to update older versions
	if($quizOptionsArray['questionListType']=="")
	{
		$quizOptionsArray['questionListType']="pot";
	}

	if(isset($quizOptionsArray['randomiseQuestions']))
	{
		$randomiseQuestions = $quizOptionsArray['randomiseQuestions'];
	}

}
else
{

	$quizOptionsArray['showFeedback']="yes";
	$quizOptionsArray['emailUser']="no";
	$quizOptionsArray['questionList']="pot";
	$quizOptionsArray['customQuestionList']="";
	$quizOptionsArray['questionListType']='pot';
	$quizOptionsArray['quizFinishMessage']="";
	$quizOptionsArray['feedbackIcon']="0";
	$quizOptionsArray['requireUserLoggedIn']="1";
	$quizOptionsArray['timeAttemptsHour']="";
	$quizOptionsArray['timeLimitCheck']="";
	$quizOptionsArray['timeLimitMinutes']="";
	$quizOptionsArray['timeLimitSeconds']="";

	$quizQuestionArray=array();
}

?>

<div class="wrap">
<h1><?php _e('Edit Quiz', 'qtl') ?></h1>

<a href="admin.php?page=ai-quiz-quiz-list" class="backIcon"><?php _e('Return to my quizzes', 'qtl') ?></a>

<?php
if($feedback)
{
	echo $feedback;
}
?>

	<form action="admin.php?page=ai-quiz-quiz-edit&action=quizEdit" method="post">
	<h2 class="nav-tab-wrapper wp-clearfix">
		<a class="nav-tab" id="Overview" onclick="openTab(event, 'quizOverview')">
		<?php _e('Overview', 'qtl') ?></a>
		<a class="nav-tab" id="Settings" onclick="openTab(event, 'quizOptions')">
		<?php _e('Settings') ?></a>
		<a class="nav-tab" id="Feedback" onclick="openTab(event, 'feedbackOptions')">
		<?php _e('Feedback', 'qtl') ?></a>
		<a class="nav-tab" id="Participation" onclick="openTab(event, 'participantOptions')">
		<?php _e('Participation', 'qtl') ?></a>
	</h2>

	<div id="quizOverview" class="tab-content">
		<h2><label for ="quizName"><?php _e('Quiz Name', 'qtl') ?></label></h2>
		<input type="text" name="quizName" id="quizName" value="<?php echo $quizName ?>" placeholder="<?php _e('Enter Quiz Name', 'qtl') ?>">

		<h2><?php _e('Select Questions for this Quiz', 'qtl') ?></h2>
		<input type="radio" name="questionListType" id="potQuestions" value="pot" onclick="javascript:divDisplayHide('customListDiv'); divDisplayShow('questionPotsDiv');" <?php if ($quizOptionsArray['questionListType']=='pot'){echo 'checked'; }?>/>
		<label for="potQuestions"><?php _e('Add questions from pots', 'qtl') ?></label><br />
		<input type="radio" name="questionListType" id="customQuestions" value="custom" onclick="javascript:divDisplayShow('customListDiv'); divDisplayHide('questionPotsDiv');" <?php if ($quizOptionsArray['questionListType']=='custom'){echo 'checked'; }?>/>
		<label for="customQuestions"><?php _e('Add questions from list', 'qtl') ?></label> <span class="greyText"><?php _e('(advanced)', 'qtl') ?></span><hr />


		<?php
		// Start of custom list div
		echo '<div id="customListDiv"';
		if($quizOptionsArray['questionListType'] != 'custom'){echo ' style="display:none"';}
		echo ' />';
		echo '<label for="customQuestionList">Add question IDs below as a comma separated list</label><br/>';
		echo '<input size="50" type="text" id="customQuestionList" name="customQuestionList" value="'.$quizOptionsArray['customQuestionList'].'"/>';

		if($quizOptionsArray['customQuestionList'])
		{
			$quizQuestionArray = explode(',', $quizOptionsArray['customQuestionList']);
		}
		$questionErrorArray=array();
		$questionTypeErrorArray=array();

		if($quizQuestionArray)
		{
			foreach($quizQuestionArray as $thisQuestionID)
			{
				$questionInfo = qtl_queries::getQuestionInfo($thisQuestionID);
				$questionID = $questionInfo['questionID'];
				$qType = $questionInfo['qType'];
				// Check the question exists
				if($questionID=="")
				{
					$questionErrorArray[] = $thisQuestionID;
				}
				// Check for refletive types. They can't be added
				if($qType=="reflectionText" || $qType=="reflection")
				{
					$questionTypeErrorArray[] = $thisQuestionID;
				}
			}
		}

		if(count($questionErrorArray)>=1 || count($questionTypeErrorArray)>=1)
		{
			echo '<div class="failText" style="border: 2px solid #990000; padding:5px; background:#FEF3F3">';

			if($questionErrorArray)
			{
				echo __('Warning! The following questions do not exist:', 'qtl');
				echo '<ul>';
				foreach($questionErrorArray as $thisQuestionID)
				{
					echo '<li> '.$thisQuestionID.'</li>';
				}
				echo '</ul>';
			}

			if($questionTypeErrorArray)
			{
				echo __('Warning! The following questions are reflective types and therefore cannot be marked:', 'qtl');
				echo '<ul>';
				foreach($questionTypeErrorArray as $thisQuestionID)
				{
					echo '<li>- '.$thisQuestionID.'</li>';
				}
				echo '</ul>';
			}
			echo '</div>';
		}

		echo '<br/><br/><input type="checkbox" id="randomiseQuestions" name="randomiseQuestions"';
		if($randomiseQuestions=="on"){echo ' checked ';}
		echo '/>';
		echo '<label for="randomiseQuestions">'.__('Randomise questions', 'qtl').'</label>';
		echo '</div>'; // End of custom list

		// Start of question pots div
		echo '<div id="questionPotsDiv"';
		if($quizOptionsArray['questionListType']=='custom'){echo ' style="display:none"';}
		echo ' />';
		// Now get the question posts, count the questinos and add drop down options
		echo '<table class="widefat">';
		$potRS = qtl_queries::getQuestionPots();

		foreach ($potRS	as $myPots)
		{

			$potName = stripslashes($myPots['potName']);
			$potID= $myPots['potID'];

			// Ge tthe number of questinos form this pot, if any
			$qCountFromPot="";

			if(isset($questionArray[$potID]))
			{
				$qCountFromPot = $questionArray[$potID];
			}

			// Get the question Count from those pots
			$questionRS = qtl_queries::getQuestionsInPot($potID, false); // fasl referes to ignoring the reflcetion questions
			$questionCount = count($questionRS);

			echo '<tr>';
			echo '<td class="col1">'.$potName.'</td>';
			echo '<td class="col2">';
			echo '<select name="potID'.$potID.'">';
			$i=0;
			while($questionCount>=$i)
			{
				echo '<option value="'.$i.'"';
				if($qCountFromPot==$i){echo ' selected';}
				echo '>';
				echo $i;
				echo '</option>';
				$i++;

			}
			echo '</select>';
			echo '</td>';
			echo '<td class="col3">';
			echo __('questions from this pot', 'qtl');
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>'; // End of question pots
		?>


	</div> <!-- End of tab 1 --->

	<div id="quizOptions" class="tab-content">
		<h2><?php _e('Availability', 'qtl') ?></h2>
		<table>
		<tr>
			<td>
				<label for="startDate"><?php _e('Start Date', 'qtl') ?></label>
			</td>
			<td>
				<input type="text" class="MyDate" name="startDate" id="startDate" size="8" value="<?php echo $startDate; ?>"/><span>(<?php _e( 'optional', 'qtl') ?>)</span><br>
			</td>
		</tr>
		<tr>
			<td>
				<label for="endDate"><?php _e('End Date', 'qtl') ?></label>
			</td>
			<td>
				<input type="text" class="MyDate" name="endDate" id="endDate" size="8" value="<?php echo $endDate; ?>"/><span>(<?php _e( 'optional', 'qtl') ?>)</span><br> 
			</td>
		</tr>
		</table>

		<h2><?php _e('Completion Options', 'qtl') ?></h2>
		<label for="quizFinishMessage"><?php _e('Message displayed to user after quiz has been submitted', 'qtl') ?></label><br/><br/>
		<?php wp_editor($quizFinishMessage, 'quizFinishMessage', '', true);	?>
		<hr/>
		 <label for="redirectPage"><?php _e('Redirect URL after completing the quiz', 'qtl') ?></label><span ><?php _e('Optional. Leave blank to return to current page', 'qtl') ?></span><br/>
		<input type="text" id="redirectPage" name="redirectPage" value="<?php echo $redirectPage ?>" size="70" />
		<hr/>
		<input type="checkbox" name="emailAdminOnCompletion" id="emailAdminOnCompletion" <?php echo $emailAdminCheck;?>/>
		<label for="emailAdminOnCompletion"><?php _e('Email me when a participant has taken the quiz', 'qtl') ?></label>

		<h2><?php _e('Time Limit', 'qtl') ?></h2>
		<input type="checkbox" name="timeLimitCheck" id="timeLimitCheck" onclick="toggleLayerVis('timeLimitOptions')" <?php echo $timeLimitCheck;?>/>
		<label for="timeLimitCheck"><?php _e('Add a time limit to this quiz', 'qtl') ?></label>
		<div id="timeLimitOptions" <?php if ($timeLimitCheck<>'on'){echo ' style="display:none"';}?>><br/>
		<select id="timeLimitMinutes" name="timeLimitMinutes">
		<?php
		$i=0;
		while($i<=90)
		{
			echo '<option value="'.$i.'"';
			if($i==$quizOptionsArray['timeLimitMinutes']){echo ' selected';}
			echo '>'.$i.'</option>';
			$i++;
		}

		?>
		</select><?php _e('minutes') ?>

		<select id="timeLimitSeconds" name="timeLimitSeconds">
		<?php
		$i=0;
		while($i<=59)
		{
			echo '<option value="'.$i.'"';
			if($i==$quizOptionsArray['timeLimitSeconds']){echo ' selected';}
			echo '>'.$i.'</option>';
			$i++;
		}

		?>
		</select><?php _e('seconds') ?>

		<br/><br/><span><?php _e('After the time above the quiz will automatically submit', 'qtl') ?></span>
		</div>
	</div> <!-- id="quizOptions" -->


	<div id="feedbackOptions" class="tab-content">
		<h2><?php _e('Feedback Options', 'qtl') ?></h2>
		<input type="radio" name="showFeedback" id="showFeedbackYes" value="yes" <?php if ($quizOptionsArray['showFeedback']=='yes'){echo 'checked'; }?>/>
		<label for="showFeedbackYes"><?php _e('Display detailed feedback to participants', 'qtl') ?></label><br />
		<input type="radio" name="showFeedback" id="showFeedbackNo" value="no" <?php if ($quizOptionsArray['showFeedback']=='no'){echo 'checked'; }?>/>
		<label for="showFeedbackNo"><?php _e('Hide feedback from participants', 'qtl') ?></label><hr />

		<h2><?php _e('Feedback Text', 'qtl') ?></h2>
		<table>
		<tr>
		<td>
		<label for="correctText"><?php _e('Correct Text', 'qtl') ?></label>
		</td>
		<td>
		<input type="text" name="correctText" id="correctText" value="<?php echo $correctText?>" class="regular-text" >
		</td>
		</tr>
		<tr>
		<td>
		<label for="incorrectText"><?php _e('Incorrect Text', 'qtl') ?></label>
		</td>
		<td>
		<input type="text" name="incorrectText" id="incorrectText" value="<?php echo $incorrectText?>" class="regular-text">
		</td>
		</tr>
		</table>

		<h2><?php _e('Feedback Icons', 'qtl') ?></h2>
		<h3><?php _e('Icon set', 'qtl') ?></h3>
		<?php
		$iconArray = array();
		$currentIcon = 'correct';
		$iconArray = qtl_utils::getQTL_IconArray();
		$correctIconDir = QTL_PLUGIN_URL.'/images/icons/'.$currentIcon;
		$incorrectIconDir = QTL_PLUGIN_URL.'/images/icons/incorrect/';

		$feedbackIcon = $quizOptionsArray['feedbackIcon'];
		if($feedbackIcon==""){$feedbackIcon=1;}

		echo '<table>';
		echo '<td align="center" style="padding:25px">';
		echo '<input type="radio" name="feedbackIcon" id="icon0" value="0"';
		if($feedbackIcon==0){echo 'checked';}
		echo '>';
		echo '<label for="icon0">'.__('No icons').'</label>';
		$i=1;
		foreach($iconArray as $myIcon)
		{
			$currentIconNo = substr($myIcon, 4, -4);
			$correctIconRef = $correctIconDir.'/'.$myIcon;
			$incorrectIconRef = $incorrectIconDir.'/cross'.$currentIconNo.'.png';
			if($i==1){echo '<tr>';}
			echo '<td align="center" style="padding:25px">';
			echo '<label for="icon'.$currentIconNo.'">';
			echo '<img src="'.$correctIconRef.'">';
			echo '<img src="'.$incorrectIconRef.'">';
			echo '</label>';
			echo '<br/>';
			echo '<input type="radio" name="feedbackIcon" id="icon'.$currentIconNo.'" value="'.$currentIconNo.'"';
			if($feedbackIcon==$currentIconNo){echo 'checked';}

			echo '>';
			echo '</td>';
			$i++;
			if($i>=6){$i=1; echo '</tr>';}
		}


		echo '</td>';
		if($i<>1){echo '</tr>';}
		echo '</table>';
		?>

	</div>


	<div id="participantOptions" class="tab-content">
		<h2><?php _e('Participant Options', 'qtl') ?></h2>
		<input onclick="toggleLayerVis('loggedInUserOptions')" type="checkbox" name="requireUserLoggedIn" id="requireUserLoggedIn" <?php if ($requireUserLoggedIn=='on'){echo 'checked'; }?>/>
		<label for="requireUserLoggedIn"><?php _e('Participants must be logged in to take this quiz', 'qtl') ?></label>

		<div id="loggedInUserOptions" <?php if ($requireUserLoggedIn != 'on'){echo ' style="display:none"';} ?> >
		<span class="greyText smallText"><?php _e('All the options below are only applicable if you have ticked \'Require user to be logged in\'', 'qtl') ?></span><br/><br/>
		<label for="maxAttempts"><?php _e('Max number of attempts', 'qtl') ?></label><br/>
		<input type="text" name="maxAttempts" id="maxAttempts" size="3" value="<?php echo $maxAttempts; ?>"/><br>
		<hr/>
		<?php _e('Email user their mark after completing the quiz', 'qtl') ?><br/>
		<label for="emailUserYes"><?php _e('Yes') ?></label>
		<input type="radio" name="emailUser" id="emailUserYes" value="yes" <?php if ($quizOptionsArray['emailUser']=='yes'){echo 'checked'; }?>/>
		<label for="emailUserNo"><?php _e('No') ?></label>
		<input type="radio" name="emailUser" id="emailUserNo" value="no" <?php if ($quizOptionsArray['emailUser']=='no'){echo 'checked'; }?>/><br>
		<hr/>
		<?php _e('Minimum time between attempts', 'qtl') ?><br/>
		<select name="timeAttemptsHour" id="timeAttemptsHour">
		<?php
		$hourRange = range(0, 24, 1);
		foreach ($hourRange as $hour) {
			//echo "<option value='$hour'>$hour </option>";
			echo "<option value='$hour'";
			if(($quizOptionsArray['timeAttemptsHour']==$hour)){
				echo 'selected';
			}
			echo ">$hour </option>";
		}
		?>
		</select><label for="timeAttemptsDay"><?php _e('Hour(s)', 'qtl') ?></label>
		<input type="text" name="timeAttemptsDay" id="timeAttemptsDay" size="3" value="<?php echo $timeAttemptsDay; ?>"/>
		<label for="timeAttemptsDay"><?php _e('Day(s)', 'qtl') ?></label>
		</div>

	</div> <!-- id="participantOptions" -->



	<hr/>
	<input type="hidden" value="<?php echo $quizID?>" name="quizID" />
	<input type="hidden" value="<?php echo $emailAdminList ?>" name="emailAdminList" />

	<input type="submit" value="<?php _e('Update Quiz', 'qtl') ?>" class="button-primary" />
</form>

</div> <!-- id="wrap" -->


<script>
//function for picking a date, e.g. used in edit quiz page
jQuery(document).ready(function() {

	jQuery('.MyDate').datepicker({
		dateFormat : 'dd-mm-yy'
	});

	jQuery("#quizName").focus();

});

</script>

<script>

function openTab(evt, tabName) {

	var i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tab-content");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("nav-tab");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
	}
	document.getElementById(tabName).style.display = "block";
	evt.currentTarget.className += " nav-tab-active";

	//event.preventDefault();
}

// Get the element with id="Overview" and click on it
document.getElementById("Overview").click();

</script>