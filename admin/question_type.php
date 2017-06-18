<?php
require_once AIQUIZ_PATH.'scripts/tabs.php';

/*session_start();

if(isset($_SESSION['questionid']))
{
	unset($_SESSION['questionid']);
}
*/
$potID = $_GET['potID'];
$potInfo = qtl_queries::getPotInfo($potID);
$potName = qtl_utils::convertTextFromDB($potInfo['potName']);

$homeURL =  network_home_url();

if($homeURL =="")
{
	$homeURL = home_url();	
}

$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/';

?>
<div class="wrap">
<h1><?php echo $potName.' - '. __('Add a new question', 'qtl') ; ?></h1>
<a href="admin.php?page=ai-quiz-home" class="backIcon"><?php  _e('Return to all question pots', 'qtl') ?></a>
<hr/>

<h2><?php _e('Please select a question type.', 'qtl') ?></h2>


	<div id="tabs">
		<ul>
			<li><a href="#multichoice"><?php _e('Multiple Choice', 'qtl') ?></a></li>
			<li><a href="#text"><?php _e('Text', 'qtl') ?></a></li>
			<li><a href="#reflective"><?php _e('Reflective', 'qtl') ?></a></li>	
		</ul>

		<div id="multichoice">
			<table>
			<tr>
				<td valign="top" width="400px">
					<?php
					echo '<a href="admin.php?page=ai-quiz-question-edit&qType=radio&tab=question&potID='.$potID.'" >';
					echo __('Single Answer (radio buttons)', 'qtl').'<br/>';
					echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/example_radio.gif">';
					echo '</a><br/>';
					?>
				</td>
				<td valign="top">
					<?php
					echo '<a href="admin.php?page=ai-quiz-question-edit&qType=check&tab=question&potID='.$potID.'" >';
					echo __('Multiple Answer (check boxes)', 'qtl').'<br/>';
					echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/example_check.gif">';
					echo '</a><br/>';
					?>
				</td>
			</tr>
			</table>
		</div>

		<div id="text">
			<table>
			<tr>
				<td valign="top" width="400px">
					<?php
					echo '<a href="admin.php?page=ai-quiz-question-edit&qType=text&tab=question&potID='.$potID.'" >';
					echo __('Free Text', 'qtl'). '<br/>';
					echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/example_text.gif">';
					echo '</a><br/>';
					?>
			</td>
			<td valign="top">
			<?php

// not fully implemented yet
/*
			echo '<a href="admin.php?page=ai-quiz-question-edit&qType=textArea&tab=question&potID='.$potID.'" >';
			echo __('Free Text Box', 'qtl').'<br/>';
			echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/textarea_example.gif">';
			echo '</a><br/>';
*/

				echo '<a href="admin.php?page=ai-quiz-question-edit&qType=blank&tab=question&potID='.$potID.'" >';
				echo __('Fill in the blanks', 'qtl').'<br/>';
				echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/example_blank.png">';
				echo '</a><br/>';
			?>
			</td>
			</tr>
			</table>
		</div>
	
		<div id="reflective">
			<div  id="message" class="error">
			<?php 
			echo __('Reflective questions are not marked - instead, answers can be saved and displayed to students on a seperate page.<br/>
			See the <a href="admin.php?page=ai-quiz-help#shortcodes">shortcode reference</a> for help with this.', 'qtl') ;
			?>
			</div>
			<?php
			echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflection&tab=question&potID='.$potID.'" >';
			echo __('Reflection (no textbox)', 'qtl').'<br/>';
			echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/example_reflection.gif">';
			echo '</a><br/>';
			?>
			<hr/>
			<?php
			echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflectionText&tab=question&potID='.$potID.'" >Reflection (with textbox)<br/>';
			echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/example_reflection_box.gif">';
			echo '</a><br/>';
			?>
		</div>
	</div>
</div>
