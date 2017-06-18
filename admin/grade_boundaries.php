<?php

if(isset($_GET['quizID']))
{
	$quizID= $_GET['quizID'];
}
else
{
	die();
}

$feedback ="";


if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	switch ($action) {
		case "boundaryEdit":
			$feedback= qtl_actions::gradeBoundaryEdit();
			break;
			
		case "boundaryDelete":
			$boundaryID = $_GET['boundaryID'];
			if($boundaryID)
			{
				qtl_actions::gradeBoundaryDelete($boundaryID);
				$feedback = '<div class="updated">'.__('Range Deleted', 'qtl') .'</div>';							

			}
			break;
	}	
	
}



// The draw function that adds the button
function drawAddBoundaryRow($boundaryID, $quizID, $min, $max)
{
	echo '<tr>';
	echo '<td valign="top" colspan="4">';
	echo '<a href="?page=ai-quiz-boundaryEdit&boundaryID='.$boundaryID.'&quizID='.$quizID.'&min='.$min.'&max='.$max.'" class="button-secondary">'.__('Add new grade range', 'qtl').'</a>';
	echo '</td>';	
}


$quizInfo = qtl_queries::getQuizInfo($quizID);
$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
?>

<h1><?php _e('Grade Boundaries', 'qtl') ?></h1>

<a href="?page=ai-quiz-quiz-list" class="backIcon"><?php _e('Back to Quiz List', 'qtl') ?></a>

<?php
if($feedback)
{
	echo $feedback;
}

$graphDataLabels = array();
$dataArray = array();

$previousBoundaryArray=array
(
'minGrade' => '',
'maxGrade' => '',

);
$minGrade = '';
$macGrade='';


// Show all the other grade boundaries now
$myBoundaries = qtl_queries::getGradeBoundaries($quizID);
$boundaryCount = count($myBoundaries);
if($boundaryCount==0)
{
	echo '<hr/><a href="?page=ai-quiz-boundaryEdit&boundaryID=new&quizID='.$quizID.'&min=0&max=100" class="button-primary">'.__('Add new grade range', 'qtl').'</a>';
	
}
else
{
	echo '<div id="quiztable">';
	echo '<table class="widefat"><th>'.__('Grade Range', 'qtl').'</th><th>'.__('Feedback', 'qtl').'</th><th></th><th></th></tr>';
	
	$nothingDefinedText = __('No range defined','qtl');
	$totalChartAreas = 0; // The total number of boundaries including not defined. Incremented to make the chart the right height
	$initialEmptyBoundary=false; // Set this true later if there is a missing boundary from the start
	
	$tempTotal=0;
	
	$currentBoundary=1;
	$previousMaxGrade=0; // set to 0 as no previous grade recored
	foreach($myBoundaries as $feedbackInfo)
	{
		$boundaryID= $feedbackInfo['boundaryID'];
		$minGrade = $feedbackInfo['minGrade'];
		$maxGrade = $feedbackInfo['maxGrade'];
		$feedbackNoBreaks = qtl_utils::convertTextFromDB($feedbackInfo['feedback']);
		$feedback = wpautop($feedbackNoBreaks);
		
		$lastBoundaryIsBlank=false;
		
		// its the first boundary and there is space BEFORe the boundary for a new one
		if($currentBoundary==1 && $minGrade>0)
		{
			drawAddBoundaryRow("new", $quizID, 0, $minGrade-1);
			
			$nextBoundaryArray = $myBoundaries[$currentBoundary-1];
			$nextMinGrade=$nextBoundaryArray['minGrade'];

			// Determine the size of this
			$thisDataValue = $minGrade;
			
			$dataArray[] = array("new", 0, $minGrade-1, $thisDataValue, $feedback, true);

			$initialEmptyBoundary=true;
			$lastBoundaryIsBlank=true;

		}
		elseif($minGrade>($previousMaxGrade+1)) // Its in the MIDDLE of the table and there is space for a boundary
		{
			// Get the NEXT min value
			$nextBoundaryArray = $myBoundaries[$currentBoundary-1];
			$nextMinGrade=$nextBoundaryArray['minGrade'];
			// Also add boundary if the previous min value had a gap for another
			drawAddBoundaryRow("new", $quizID, $previousMaxGrade+1, $nextMinGrade-1);

			// Determine the size of this
			$thisDataValue = ($nextMinGrade-$previousMaxGrade);

			$dataArray[] = array("new", $previousMaxGrade+1, $nextMinGrade-1, $thisDataValue, $feedback, true);
			
			$totalChartAreas++;
			
			$lastBoundaryIsBlank=true;		
		}

		// No data here - just boundary data manipuation for edit page
		if($currentBoundary==1) // Its the FIRST of the boundaries
		{
			$min=0;
			if($boundaryCount==1)
			{
				$max=100;
			}
			else
			{
				$nextBoundaryArray = $myBoundaries[$currentBoundary];
				$nextMinGrade=$nextBoundaryArray['minGrade'];
				$nextMaxGrade=$nextBoundaryArray['maxGrade'];			
				$max = $nextMinGrade-1;
			}
		}
		elseif($currentBoundary==$boundaryCount) // Its the LAST of the boundaries
		{
			$previousBoundaryArray = $myBoundaries[$currentBoundary-1];
			$prevMinGrade=$nextBoundaryArray['minGrade'];
			$prevMaxGrade=$previousMaxGrade;
			$max=100;
			$min=$prevMaxGrade+1;

		}
		else // Its a standard in the middle one
		{
			$previousBoundaryArray = $myBoundaries[$currentBoundary-2];
			$prevMinGrade=$previousBoundaryArray['minGrade'];
			$prevMaxGrade=$previousBoundaryArray['maxGrade'];			
			$nextBoundaryArray = $myBoundaries[$currentBoundary];
			$nextMinGrade=$nextBoundaryArray['minGrade'];
			$nextMaxGrade=$nextBoundaryArray['maxGrade'];			
			$min = $prevMaxGrade+1;
			$max = $nextMinGrade-1;
		}

		if($minGrade==$maxGrade)
		{
			$gradeInfo = 'Exactly '.$minGrade.'%';
		}
		else
		{
			$gradeInfo = $minGrade.'% - '.$maxGrade.'%';
		}

		$graphDataLabels[] = $gradeInfo;
		
		echo '<tr>';
		echo '<td valign="top">'.$gradeInfo.'</td>';
		echo '<td valign="top">'.$feedback.'</td>';	
		echo '<td valign="top"><a href="?page=ai-quiz-boundaryEdit&boundaryID='.$boundaryID.'&quizID='.$quizID.'&min='.$min.'&max='.$max.'" class="editIcon"">'.__('Edit').'</a>';
		echo '</td>';
		echo '<td valign="top">';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=deleteCheck'.$boundaryID.'" class="thickbox deleteIcon">'.__('Delete').'</a>';
		echo '<div id="deleteCheck'.$boundaryID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>'.__('Are you sure you want to delete this grade range?', 'qtl').'</h2>';		
		echo '<input type="submit" value="'.__('Yes').'" onclick="location.href=\'?page=ai-quiz-boundaries&quizID='.$quizID.'&boundaryID='.$boundaryID.'&action=boundaryDelete\'" class="button-primary">';
		echo '<input type="submit" value="'.__('Cancel').'" onclick="self.parent.tb_remove();return false" class="button-secondary">';
		echo '</div>';
		echo '</div>';			
		echo '</td>';

		echo '</tr>';

		// Dealing with graph stuff
		// Regular boundary
		// Determine the width of the boundary
		$prevMinGrade=$previousBoundaryArray['minGrade'];
		$prevMaxGrade=$previousBoundaryArray['maxGrade'];
		
		$thisDataValue = ($maxGrade-$minGrade);
		if(($prevMaxGrade+1)==$minGrade)
		{
			if($lastBoundaryIsBlank==false)
			{
				$thisDataValue=$thisDataValue+1;
			}
		}
			
		if($thisDataValue==0){$thisDataValue=1;}
		$dataArray[] = array($boundaryID, $min, $max, $thisDataValue, $feedback);
		
		$lastBoundaryIsBlank=false;
		
		// Last boundary does not bring it up to 100%
		if($currentBoundary==$boundaryCount) // Add new boundry if its the last one and its NOT up to 100 yet
		{

			if($maxGrade<100)
			{
				drawAddBoundaryRow("new", $quizID, $maxGrade+1, 100);
				$thisDataValue = (100-$maxGrade);
				
				$dataArray[] = array("new", $maxGrade+1, 100, $thisDataValue, $feedback, true);
				
				
				// Set the clour oft his bar to grey
				$totalChartAreas++;	


			}
			else
			{
				if($maxGrade==99)
				{
					$thisDataValue = 1; // it MUST be one as its the top mark of 100
					
				}
				else
				{
					$thisDataValue = 100-$maxGrade;
				}


				$dataArray[] = array($boundaryID, $min, $max, $thisDataValue, $feedback);


				// Set the clour oft his bar to grey
				$totalChartAreas++;

			}
		}		

		// End this loop increment values
		$previousMaxGrade = $maxGrade;
		$currentBoundary++; // Up current boundary by one
		$totalChartAreas++; // Up total chart areas by one
		
	}

	echo '</table>';

	$totalValue=0;
	$divHeight="50px";
	
	echo '<div id="boundaryGraphWrapper" style="margin-left: auto ;  margin-right: auto ; width:800px">';
	echo '<div style="height:'.$divHeight.'; float:left; line-height:350%; margin-right:10px;">0%</div>';

	$colourGradient= qtl_utils::generateRedGreenColourArray($boundaryCount);

	$currentBoundary=0;
	foreach($dataArray as $boundaryData)
	{
		$isBlank='';
		$boundaryID =$boundaryData[0];
		$min=$boundaryData[1];
		$max=$boundaryData[2];		
		$thisValue = $boundaryData[3];
		$feedback= $boundaryData[4];
		if(isset($boundaryData[5]))
		{
			$isBlank = 	$boundaryData[5];		
		}
		$divWidth = $thisValue*6;		
		
		if($feedback=="")
		{
			$feedback = '<p>'.__('No feedback given', 'qtl').'</p>';
		}

		if($isBlank==true)
		{
			$divColour = '#ccc';
			$thisLink = "?page=ai-quiz-boundaryEdit&boundaryID=new&quizID=".$quizID."&min=".$min."&max=".$max;
			$label = __('Undefined Boundary', 'qtl');
		}
		else
		{
			$divColour='';
			$label='';
			if(isset($colourGradient[$currentBoundary])){$divColour = $colourGradient[$currentBoundary];}
			$thisLink = "?page=ai-quiz-boundaryEdit&boundaryID=".$boundaryID."&quizID=".$quizID."&min=".$min."&max=".$max;
			if(isset($graphDataLabels[$currentBoundary])){$label = $graphDataLabels[$currentBoundary];}
			$currentBoundary++;
		}

		$strippedFeedbackText = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $feedback);
		$feedback = $label.'<hr/>'.$strippedFeedbackText;

		echo '<a href="'.$thisLink.'" class="tooltip">';
		echo '<div style="border:1px solid #fff; height:'.$divHeight.'; width:'.($divWidth).'; background:'.$divColour.'; float:left; width:'.$divWidth.'px">';
		echo '<span>'.$feedback.'</span>';
		echo '</div></a>';

		$totalValue = $totalValue+$thisValue;

	}

	echo '<div style="height:'.$divHeight.'; float:left; line-height:350%; margin-left:50px;">100%</div>';
	echo '</div>';
	echo '</div>';

}
?>