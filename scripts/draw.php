<?php


if (!class_exists('qtl_draw'))
{
	
	class qtl_draw
	{
	
		public static function drawTextOptionsEditTable($questionID)
		{
			echo '<form action="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionUpdate&tab=3" method="post">';
		//	echo '<label for="optionValue">Possible answer</label><br/>';
			echo '<input type="text" id="optionValue" name="optionValue">';
			echo '<input type="submit" value="'.__('Add possible answer', 'qtl').'" class="button-secondary">';
			echo '<input type="hidden" value="on" name="isCorrect">';	
			echo '</form>';
			$optionsRS = qtl_queries::getResponseOptions($questionID); // Do not order by rand, even if it as this is the edit screen

			if($optionsRS)
			{
				echo '<h3>'.__('Current Possible Answers', 'qtl').'</h3>';
				foreach ($optionsRS	as $myOptions)
				{
					$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);		
					$optionID= $myOptions['optionID'];
					echo $optionValue.' <span class="deleteIcon" ><a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&tab=3&action=optionDelete&optionID='.$optionID.'">'.__('Delete', 'qtl').'</a></span><br/>';
				}
				echo '<p>'.__('Answers are NOT case sensitive', 'qtl').'</p>';
			}
			
		}

		public static function drawRadioCheckOptionsEditTable($questionID, $qType, $optionOrderType)
		{
			// Firstly load up the script
			?>
			  <script>

		  
		jQuery(document).ready(function()
		{ 
		
			jQuery(function()
			{
				
				jQuery("#responseOptionsEditList ul").sortable
				(
					{
						opacity: 0.6,
						cursor: 'move',
						update: function() 
						{
							
							var order = jQuery(this).sortable("toArray");	
							var myData = 
							{
								action: 'responseOptionReorder',
								myOrder: order,
								qType: '<?php echo $qType?>',
								optionOrderType: '<?php echo $optionOrderType?>',
								questionID: <?php echo $questionID?>
							}
							
							jQuery.post(ajaxurl, myData, function(theResponse)
							{ 
								jQuery("#responseOptionsEditList").html(theResponse);
							}
							);
						}
					}
				);
			});
		
		});  
		  
		</script>
		<?php
			echo '<div id="responseOptionsEditList">';
			echo '<a href="#TB_inline?width=600&height=550&inlineId=optionEditForm" class="thickbox button">'.__('Add a new response option', 'qtl').'</a><br/>';
			
			if($optionOrderType=="random")
			{
				echo '<p>'.__('These responses are shown in a random order', 'qtl').'<p>';
				echo '<a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=responseOrderTypeChange&changeTo=ordered&tab=3" class="button-secondary">';
				echo __('Switch to manual ordering', 'qtl').'</a>';
				echo '</span>';
			}
			else
			{
				echo '<p>'.__('These responses are displayed in the order shown below', 'qtl').'<p>';
				echo '<a href="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=responseOrderTypeChange&changeTo=random&tab=3" class="button-secondary">';
				echo __('Switch to random ordering', 'qtl').'</a>';
				
			}
		
			echo '<div id="quiztable">';
			//echo '<table>'.chr(10);

			echo '<ul>';
			$tempOptionOrder=1;
			$optionsRS = qtl_queries::getResponseOptions($questionID, "ordered"); // Do not order by rand, even if it as this is the edit screen
		
			foreach ($optionsRS	as $myOptions)
			{
				$optionValue = qtl_utils::convertTextFromDB($myOptions['optionValue']);
				
				$optionID= $myOptions['optionID'];	
				$isCorrect= $myOptions['isCorrect'];
				$optionOrder= $myOptions['optionOrder'];
				
				if($optionOrder=="")
				{
					$optionOrder=$tempOptionOrder;
				}
				
				if($optionOrderType<>"random")
				{
					echo '<li id="thisOrder'.$optionID.'" class="ui-state-default">';			
					echo '<b>'.$tempOptionOrder.'.</b> ';
				}
		
				//echo wpautop($optionValue);
				echo $optionValue;
				
				qtl_draw::responseOptionEditForm($questionID, $myOptions);
				
			
				if($isCorrect==1){echo '<br><span class="tickIcon successText">'.__('Correct Answer', 'qtl').'</span>';}				
		
				echo '<br><a href="#TB_inline?width=800&height=550&inlineId=optionEditForm'.$optionID.'" class="thickbox editIcon">'.__('Edit').'</a>'.chr(10);	
				echo '<a href="#TB_inline?width=400&height=150&inlineId=optionDeleteCheck'.$optionID.'" class="thickbox deleteIcon">'.__('Delete').'</a>';
		
				echo '<div id="optionDeleteCheck'.$optionID.'" style="display:none">';
				echo '<div style="text-align:center">';
				echo '<h3> ' . __('Are you sure you want to delete this option?', 'qtl') . '</h3>';		
				echo '<input type="submit" value="'.__('Yes, delete this response', 'qtl').'" onclick="location.href=\'?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionDelete&optionID='.$optionID.'&tab=3\'" class="button-primary">';
				echo '<input type="submit" value="'.__('Cancel', 'qtl').'" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
				echo '</div>';
				echo '</div>';
				
				if($optionOrderType<>"random")
				{	
					echo '</li>';
				}
				else
				{
					echo '<hr/>';	
				}
				$tempOptionOrder++; // Increase the order by 1 for legacy stuff
				
				
			}
			echo '</ul>';
			echo '</div>';
			echo '</div>';
			
			qtl_draw::responseOptionEditForm($questionID);
		
		}
		
		public static function drawBlankOptionsEditTable($questionID, $question)
		{
			echo '<form action="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=blankOptionUpdate&tab=3" method="post">';
		/*	echo '<label for="optionValue">Possible answer </label><br/>'; */
		
		
			$question = qtl_utils::convertTextFromDB($question);
			$question = wpautop($question);				

			$blankCount =  substr_count($question, '[blank]'); // Count the number of blanks

			$newQuestion= str_replace('[blank]', '<input type="text" value="" size="10">', $question);
			
			
			// Get the options from the DB if they exist
			$optionsRS = qtl_queries::getResponseOptions($questionID, "ordered"); // Do not order by rand, even if it as this is the edit screen
		
			$blankOptions=array();
			foreach ($optionsRS	as $myOptions)
			{			
				$blankOptions = unserialize($myOptions['optionValue']);
			}
			
			if(is_array($blankOptions))
			{			
				foreach($blankOptions as $KEY => $blankResponses)
				{
					$theseOptions = $blankResponses[0]; // Get the options
					// Add these options as the values to the input boxes
					$$KEY = $theseOptions;
				}
			}
			
			
			echo '<h4>'.__('Question Preview', 'qtl').'</h4>';
			echo $newQuestion;
			
			echo '<hr/>';
			echo '<span >';
			echo __('Answers are not case sensitive. Separate each valid answer with a comma e.g. "red, yellow"', 'qtl');
			echo '</span><br/>';
			if($blankCount>=1)
			{
				$i=1;
				while($i<=$blankCount)
				{
				
					if(!isset(${'answers'.$i})){${'answers'.$i}='';}
					if(!isset(${'blank_correct_feedback_'.$i})){${'blank_correct_feedback_'.$i}='';}
					if(!isset(${'blank_incorrect_feedback_'.$i})){${'blank_incorrect_feedback_'.$i}='';}

				
					echo '<b>'.__('Blank', 'qtl').' '.$i.' '. __('answers', 'qtl').'</b><br/><input type="text" name="answers'.$i.'" value="'.stripslashes(${'answers'.$i}).'"><br/><br/>';
					echo __('Blank', 'qtl').' '.$i.' ' . __('correct feedback', 'qtl').' '.'('.__('optional', 'qtl').')<br/>
					<textarea name="blank_correct_feedback_'.$i.'" cols="30" rows="4">'.stripslashes(${'blank_correct_feedback_'.$i}).'</textarea><br/>';
					echo __('Blank', 'qtl').' '.$i.' ' . __('correct feedback', 'qtl').' '.'('.__('optional', 'qtl').')<br/>
					<textarea name="blank_incorrect_feedback_'.$i.'" cols="30" rows="4">'.stripslashes(${'blank_incorrect_feedback_'.$i}).'</textarea><br/>';					
					
					echo '<hr/>';
					$i++;
				}
				
				echo '<input type="submit" value="'.__('Save').'" class="button-primary">';
				
			}
			
			
			echo '</form>';
			
		}		
		
		
		public static function responseOptionEditForm($questionID, $optionInfoArray="")
		{
			// Define the vars
			$optionID="";
			$optionValue="";
			$responseCorrectFeedback ="";
			$responseIncorrectFeedback ="";
			$isCorrect ="";
			
			if($optionInfoArray)
			{
				$optionID= $optionInfoArray['optionID'];	
				$optionValue = qtl_utils::convertTextFromDB($optionInfoArray['optionValue']);
		
				$isCorrect= $optionInfoArray['isCorrect'];
				$responseCorrectFeedback= qtl_utils::convertTextFromDB($optionInfoArray['responseCorrectFeedback']);
				$responseIncorrectFeedback= qtl_utils::convertTextFromDB($optionInfoArray['responseIncorrectFeedback']);
			}
			
			// Create the edit div for this option		
			echo '<div id="optionEditForm'.$optionID.'" style="display:none">';
			echo '<form action="?page=ai-quiz-question-edit&questionID='.$questionID.'&action=optionUpdate&tab=3" method="post">';
			// Response		
			echo '<label for="optionValue'.$optionID.'">'.__('Possible answer:', 'qtl').'</label>';
			echo '<textarea rows="3" cols="50" name="optionValue'.$optionID.'" id="optionValue.'.$optionID.'">'.$optionValue.'</textarea>';
			//the_editor($optionValue, 'optionValue'.$optionID, '', false);
			
			// Correct feedback
			echo '<label for="responseCorrectFeedback'.$optionID.'">'.__('Correct Feedback', 'qtl'). ': '.'&nbsp;('.  __( 'optional', 'qtl').')</label>';
			echo '<span>'.__('The feedback shown next to this response if answered correctly', 'qtl').'</span><br/>';
			echo '<textarea rows="3" cols="50" name="responseCorrectFeedback'.$optionID.'" id="responseCorrectFeedback.'.$optionID.'">'.$responseCorrectFeedback.'</textarea>';
			
		//	the_editor($responseCorrectFeedback, 'responseCorrectFeedback'.$optionID, '', false);
					
			// incorrect feedback
			echo '<label for="responseIncorrectFeedback'.$optionID.'">'.__('Incorrect Feedback', 'qtl').': '.'&nbsp;('.  __( 'optional', 'qtl').')</label>';
			echo '<span>'.__('The feedback shown next to this response if answered incorrectly', 'qtl').'</span><br/>';	
			echo '<textarea rows="3" cols="50" name="responseIncorrectFeedback'.$optionID.'" id="responseIncorrectFeedback.'.$optionID.'">'.$responseIncorrectFeedback.'</textarea>';
			
			echo '<br/>';
			echo '<label for="correctAnswer'.$optionID.'"> ';
		
			echo '<input type="checkbox" name="isCorrect'.$optionID.'" id="correctAnswer'.$optionID.'"';
			if($isCorrect==1){echo 'checked ';}		
			echo '> ';
			echo __('Correct Answer', 'qtl').'?'.'</label>';
			echo '<input name="optionID" type="hidden" value="'.$optionID.'"><br/>';
			echo '<input type="submit" value="'.__('Save').'" class="button-primary">';
			echo '<input type="submit" value="'.__('Cancel').'" onclick="self.parent.tb_remove();return false" class="button-secondary"><br/><br/>';
			echo '</form>';
			echo '</div>';	 // End of the edit div for this option	
			
		}
	
	}
}
?>