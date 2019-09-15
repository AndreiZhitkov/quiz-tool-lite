<div class="wrap">
<h1><?php _e('Help') ?></h1>
<div class="tab" style="float: left; width:30%;">

	<a id="nav-overview" onclick="openTab(event, 'overview')">
	<h3  class="nav-tab">Επισκόπηση</h3></a>
	<a onclick="openTab(event, 'started')">
	<h3 class="nav-tab">Ξεκινώντας : Δημιουργία καταλόγου ερωτήσεων</h3></a>
	<a onclick="openTab(event, 'questions')">
	<h3 class="nav-tab">Δημιουργία ερωτήσεων</h3></a>
	<a onclick="openTab(event, 'question_feedback')">
	<h3 class="nav-tab">Ανατροφοδότηση ερωτήσεων</h3></a>
	<a onclick="openTab(event, 'insertQuestion')" >
	<h3 class="nav-tab">Προσθήκη ερωτήσεων σε σελίδα</h3></a>
	<a onclick="openTab(event, 'quiz')" >
	<h3 class="nav-tab">Δημιουργία εξετάσεων</h3></a>
	<a onclick="openTab(event, 'insertQuiz')">
	<h3 class="nav-tab">Προσθήκη εξέτασης σε σελίδα</h3></a>
	<a onclick="openTab(event, 'shortcodes')">
	<h3 class="nav-tab">Shortcodes</h3></a>
	<a onclick="openTab(event, 'showResponse')">
	<h3 class="nav-tab">Εμφάνιση απαντήσεων χρηστών</h3></a>
	<a onclick="openTab(event, 'results')" >
	<h3 class="nav-tab">Προβολή αποτελεσμάτων</h3></a>
	<a onclick="openTab(event, 'more_help')">
	<h3 class="nav-tab">Περισσότερη Βοήθεια</h3></a>

</div>
<div id="overview" class="tab-content" style="float: right; width:66%;" >

<h2>Επισκόπηση</h2>
<p>This plugin will allow you to create quiz questions and deploy them as a single question on a page, or as an entire quiz.<p>
<p>Questions are stored in 'pots' which allow you to create custom quizzes from different questions in each pot. You can think of a question pot as a bucket in which you store similar questions, for example you could have 3 question pots called 'Easy', 'Medium' and 'Hard'. You can then create a quiz with 5 questions from the 'Easy' pot, 5 questions from the 'Medium' pot and 5 questions from the 'Hard' pot.</p>
<p>Quiz questions are assigned randomly so in the above example if you had 10 questions in each pot you would be displaying 15 random questions, 5 from each difficulty to each individual taking the quiz (see below)</p>

<?php
//echo home_url();
$homeURL =  network_home_url();

if($homeURL =="")
{
	$homeURL = home_url();	
}

$imgSrc =  $homeURL.'/wp-content/plugins/quiz-tool-lite/admin/help/pot-example-el.jpg';
//echo $imgSrc;

echo '<div style="text-align:center"><img src="'.$imgSrc.'" />';
echo '<br/><span class="greyText">An example showing a quiz made up of 2 questions at random from 3 pots.</span>';
echo '</div>';

?>

</div>
<div id="started" class="tab-content" style="float: right; width:66%;">

<h2>Getting started : Creating your question pots</h2>
<p>First off you need to create a question pot to add your questions to. Click 'Quiz Questions' from the menu and then 'Create a new question pot'</p>
<p>You can call your question pot anything you want e.g. 'Geography questions'. People taking the quiz will never see the name of your question pots.</p>
<p>You can also change your question pot names at any point</p>
Once you've created a question pot click the 'Add / edit questions' link to start adding questions

</div>
<div id="questions" class="tab-content" style="float: right; width:66%;">

<h2>Creating questions</h2>
<p>Once you've created a question pot and have clicked 'Add / edit questions' you should see a new link 'Add a new question'. Click this and you're taken through to a page that asks you to pick a question type. There are currently 4 questions types to choose from.</p>

<b>Single Answer (Radio Buttons)</b><br/>
This question type allows participants to select ONE and only one answer. Use this for creating True / False questions, or when only one answer is correct e.g. what is the capital of France.

<b>Multiple Answer (check boxes)</b><br/>
<p>This question type allows participants to select more than one answer e.g. which of the following are true / select all that apply.</p>

<b>Free Text</b><br/>
<p>This question type allows participants to add text into a box. You can identify as many correct answers as you wish. These are not case sensitive. i.e. "Bones" would be accepted as well as "bones".</p>

<b>Reflection (no textbox)</b>
<p>You can use this question type when you want to give your participants a statement to think about, and then click a button to reveal a model answer. 
This 'click to reveal' question type simply present information to people and does not give any means to enter a response.</p>

<b>Reflection (with textbox)</b>
This question type works in the same way as above (click to reveal a model answer/text) but also allows students to type a response before revealing the answer.
You can use this to collect information from participants and then on a later page present their original response to them to see if it has changed. See the <a href="#shortcodes">'shortcodes'</a> section on how to do this.

</div>
<div id="question_feedback" class="tab-content" style="float: right; width:66%;">

<h2>Question feedback</h2>
<p>Each question has several options for giving feedback. They have an overall correct and incorrect feedback box, but each response option (if applicable) can also have feedback for correct and incorrect feedback.</p>
<p>Feedback is not required and can be left blank. Feedback is given automatically when adding a single question - you do not need to 'enable' it in anyway.</p>

</div>
<div id="insertQuestion" class="tab-content" style="float: right; width:66%;">

<h2>Adding questions to a page</h2>
<p>Add a question to the page using the Quiz Tool Lite wizard icon. You can find this on any page or post tool bar - look for the red 'Q' icon (see below)</p>
<?php
$imgSrc =  $homeURL.'/wp-content/plugins/quiz-tool-lite/admin/help/question-add-el.jpg';
echo '<div style="text-align:center"><img src="'.$imgSrc.'" />';
echo '<br/><span class="greyText">When editing a page, click the Q icon to add a question or a quiz to the page.</span>';
echo '</div>';
?>

<p>When clicked a popup window will appear where you can select either a quiz or a question to insert into the page. Firstly select the pot that contains the questions, then click the question itself and 'Insert into page'. This will insert a 'shortcode' onto the page.</p>

</div>
<div id="quiz" class="tab-content" style="float: right; width:66%;">

<h2>Creating Quizzes</h2>
<p>A quiz is created by pulling in X number of questions from X number of question pots. In its most simple formar, if you had one question pot with ten questions you could create a quiz that pulled in 10 questions from that pot. That would create a quiz with all 10 questions being displayed at random. However, you can combine multiple questions from seperate question pots to make each participant see a slightly different version of the quiz.</p>
<p>Results from quizzes for logged in users are stored and can be viewed in the 'Results' page. The highest score is saved for each participant.</p>

</div>

<div id="insertQuiz" class="tab-content" style="float: right; width:66%;">

<h2>Adding a quiz to a page</h2>
<p>Use the 'Insert Wizard' from the editor toolbar in the same way as you add a single question to add the shortcode for displaying a quiz. Or you can use a shortcode, as shown below.</p>

</div>
<div id="shortcodes" class="tab-content" style="float: right; width:66%;">

<h2>Shortcodes</h2>
<p>To add a question or a quiz to a page you need to use 'shortcodes'. A shortcode is a simple bit of text that is added between to square brackets e.g. [My Shortcode].</p>
On the whole you will not need to know anything else about shortcodes to add questions to a page. Simply use the 'Insert question Wizard' (see above) and the shortcode will be generated and added to the page for you.i
<p>Example shortcodes are shown below</p>

<h4>Standard Shortcode Examples</h4>
<table style="line-height:50px; text-align:left; border-bottom:1px solid #ccc">
<tr>
<th width="350px">Shortcode</th>
<th>Description</th>
</tr>
<tr>
<td>
<span class="codeExample">[QTL-Question id=25]</span>
</td>
<td>
Insert question ID 25.
</td>
</tr>
<tr>
<td>
<span class="codeExample">[QTL-Question id=25 savedata=true]</span>
</td>
<td>
Insert question ID 25 and save the response to the database
</td>
</tr>
<tr>
<td>
<span class="codeExample">[QTL-Question id=25 button="Click Here"]</span>
</td>
<td>
Insert question ID 25 and change the default "Check Answer" button text to "Click Here"
</td>
</tr>

<tr>
<td>
<span class="codeExample">[QTL-Question id=25 correctfeedback="Well done!"]</span>
</td>
<td>
Insert question ID 25 and change the default correct feedback
</td>
</tr>

<tr>
<td><span class="codeExample">[QTL-Response id=25]</span></td>
<td>Shows the response given to question ID 25 by the current logged in user</td>
</tr>
<tr>
<td><span class="codeExample">[QTL-Quiz id=2]</span></td>
<td>Insert quiz ID 2 into the page</td>
</tr>
<tr>
<tr>
<td valign="top"><span class="codeExample">[QTL-Score id=2]</span></td>
<td>Shows the number of attempts and maximum score for quiz ID 2 to the current logged in user<br/>Optional - add "showall=true" to the shortcode to view ALL attempt scores</td>
</tr>
<tr>
<td><span class="codeExample">[QTL-Leaderboard id=2]</span></td>
<td>Draw the leaderboard showing all user scores for quiz ID 2</td>
</tr>
<tr>
<td><span class="codeExample">[QTL-Leaderboard id=2 anonymous=true]</span></td>
<td>Draw the leaderboard showing all user scores for quiz ID 2 but hiding their names</td>
</tr>

</table>

<h4>Shortcodes available for the single question [QTL-Question]</h4>
<table style="line-height:50px; text-align:left; border-bottom:1px solid #ccc">

<tr>
<td><span class="codeExample">savedata=true</span></td>
<td>Saves the response to the database so you can display to the user on another page</td>
</tr>

<tr>
<td><span class="codeExample">button="Click here"</span></td>
<td>Changes the default button text of the show answer button</td>
</tr>

<tr>
<td><span class="codeExample">correctfeedback="Well done!"</span></td>
<td>Changes the default correct message to user</td>
</tr>

<tr>
<td><span class="codeExample">incorrectfeedback="Thats wrong"</span></td>
<td>Changes the default incorrect message to user</td>
</tr>

<tr>
<td><span class="codeExample">iconset=3</span></td>
<td>Changes the default icon set. <a href="javascript:toggleLayerVis('iconsets')">View iconsets</a></td>
</tr>
</table>

<div id="iconsets" style="display:none">
<h3>Available Iconsets</h3>
<?php

$iconArray = array();
$iconArray = qtl_utils::getQTL_IconArray();

$correctIconDir = QTL_PLUGIN_URL.'/images/icons/correct/';
$incorrectIconDir = QTL_PLUGIN_URL.'/images/icons/incorrect/';
echo '<table>';
$i=1;
foreach($iconArray as $myIcon)
{
	$currentIconNo = substr($myIcon, 4, -4);
	$correctIconRef = $correctIconDir.'/'.$myIcon;
	$incorrectIconRef = $incorrectIconDir.'/cross'.$currentIconNo.'.png';
	if($i==1){echo '<tr>';}
	echo '<td align="center" style="padding:25px">';
	echo '<img src="'.$correctIconRef.'">';
	echo '<img src="'.$incorrectIconRef.'"><br/>';
	echo 'Iconset '.$currentIconNo;
	if($currentIconNo==1){echo ' (Default)';}
	echo '</td>';
	$i++;
	if($i>=5){$i=1; echo '</tr>';}
}
if($i<>1){echo '</tr>';}
echo '</table>';
?>  

</div>
</div>
<div id="showResponse" class="tab-content" style="float: right; width:66%;">

<h2>Showing user responses</h2>
<p>It is possible to present participants with a response they gave to a previous question. This is particularly useful for the 'Reflective' question types where you wish to present their responses to them at a later stage in their learning journey.</p>
<p>Do this you need to do the following:</p>
<p><b>1. Ensure data is being saved by manually modifying the shortcode</b>
A typical shortcode for a question is as follows:</p>

<span class="codeExample">[QTL-Question id=25]</span>
<p>To make the question save the data simply add 'savedata=true' to the shoprtcode as shown below</p>

<span class="codeExample">[QTL-Question id=25 savedata=true]</span>

<p><b>2. Add the 'Show Response' shortcode</b>
To display the response submitted for question with ID of 25, add the following to your page or post</p>

<span class="codeExample">[QTL-Response id=3]</span>

<p><i>Please note that currently this is only properly supported with reflective question types (text boxes), and only with formative questions i.e. single questions, not quizzes.</i></p>

</div>
<div id="results" class="tab-content" style="float: right; width:66%;">

<h2>Viewing Results</h2>
<p>The results page will show you a list of quizzes you have created. Clicking the 'View Results' link will display all registered users on your site, along with their highest score achieved.</p>
<p>Please note that currently quizzes can be taken as many times as they wish, and only the highest score will be recorded.</p>

</div>
<div id="more_help" class="tab-content" style="float: right; width:66%;">

<h2>Need more help?</h2>
<p>If you require more help please add your question to the <a href="http://wordpress.org/support/plugin/quiz-tool-lite
">support forum</a> where we will be in touch ASAP.</p>

</div>
</div>

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

// Get the element with id="nav-overview" and click on it
document.getElementById("nav-overview").click();
</script>

<style>
	.nav-tab {
    display: block;
    width: 100%;
    margin: 0;
}

    table tbody tr td:nth-child(1) {
        width: 55%;
    }

    table tbody tr td:nth-child(2) {
       line-height: 1.5;
   }
</style>