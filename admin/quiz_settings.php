<?php
$feedback = "";

//Initialisation Functions
function color_config()
{
	return array
	( 
		'correctFeedbackBoxColour' => array( 
		"default_color" => '#EBFEE9',
		"label" => __('Correct feedback box color', 'qtl')),
		'correctFeedbacktextColour' => array( 
		"default_color" => '#000000',
		"label" => __('Correct feedback text color', 'qtl')),
		'incorrectFeedbackBoxColour' => array ( 
		"default_color" => '#FEEDED',
		"label" => __('Incorrect feedback box color', 'qtl')),
		'incorrectFeedbacktextColour' => array ( 
		"default_color" => '#000000',
		"label" => __('Incorrect feedback text color', 'qtl')),
		'reflectiveFeedbackBoxColour' => array ( 
		"default_color" => '#EBF2FE',
		"label" => __('Reflective feedback box color', 'qtl')),
		'reflectiveFeedbacktextColour' => array ( 
		"default_color" => '#000000',
		"label" => __('Reflective feedback text color', 'qtl'))
	);
}


//Begin


//initialise options to create new ones if needed
initialise_color_options();


//if the user has submitted new choices, update them
if ( isset($_POST['update_options']))
{ 
	$feedback =  '<div class="updated">'.__('Settings updated', 'qtl').'</div>';
	updateQTL_settings();
}


//return an array containing the names of all the color options
function color_names()
{
	return array_keys(color_config());
}

//Update all the options from the form
function updateQTL_settings()
{
	$minimumEditLevel = $_POST['minimumEditLevel'];
	$fromEmailAddress = $_POST['fromEmailAddress'];
	$fromEmailName = $_POST['fromEmailName'];	
	
	update_option('qtl-minimum-editor', $minimumEditLevel);	
	update_option('qtl-fromEmailAddress', $fromEmailAddress);
	update_option('qtl-fromEmailName', $fromEmailName);
	
	$cols = color_names();

	foreach ($cols as $col)
	{
		//echo $_POST['color_picker_' . $col];
		if (preg_match('/^#[a-f0-9]{6}$/i', $_POST['color_picker_' . $col])){
			update_option($col, esc_html($_POST['color_picker_' . $col])); //perhaps test if there's a valid value here?
		}else{
			echo "<h4>'.__('Invalid color code for ', 'qtl').$col.'!</h4>";
		}
	}
}


//if a new colour option has been introduced, write a default value to it so that the get_option call fills in the input box on the form.
function initialise_color_options()
{
	global $wpdb;

	$cols = color_names();
	$conf = color_config();
		
	foreach ($cols as $col)
	{
		if (!get_option($col))
		{
			update_option($col, $conf[$col]["default_color"]);
		}
	}
}

//Create javascript to insert a color picker for each colour
function make_javascript()
{
	$JS =  '<script type="text/javascript">
		jQuery(document).ready(function($){';
		
	$cols = color_names();
	foreach ($cols as $col)
	{
		//$JS .= "jQuery('#color_picker_" . $col . "').farbtastic('#" . $col . "');"; 
		$JS .= "jQuery('#" . $col . "').spectrum({ ";
		$JS .= 'clickoutFiresChange: true ';
		$JS .= '}); ';
		
	}
	$JS .= '});
	</script>';

	return $JS;
}

function display_color_picker($col)
{
	$conf = color_config();
?>
	<td style="width:250px;">
		<label style="font-size:13px;" for="<?php echo $col; ?>"><?php echo $conf[$col]["label"]; ?></label>
	</td>
	<td>
		<input type="text" id="<?php echo $col; ?>" value="<?php echo get_option($col); ?>" name="color_picker_<?php echo $col; ?>" />
	</td>
<?php
}
?>

<?php

function display_feedback_example($textColour , $bgColour, $divType="")
{
	echo '<tr/>';
	echo '<td colspan="2">';
	echo  '<div class="'.$divType.'" id="'.$divType.'" style="color:'.$textColour .';background-color:'.$bgColour .'">'.__('Feedback Text Sample', 'qtl').'</div>';
	echo '</td>';
	echo '</tr>';

}
?>


<?php
echo $feedback
?>

<form method="POST" action="">

<?php

// Get user role allowed

$minimumEditLevel = get_option('qtl-minimum-editor');
if($minimumEditLevel=="")
{
	add_option('qtl-minimum-editor', "administrator");
	$minimumEditLevel = "administrator";
}

$fromEmailAddress = get_option('qtl-fromEmailAddress');
if($fromEmailAddress=="")
{
	add_option('qtl-fromEmailAddress', "");
	$fromEmailAddress = "";
}

$fromEmailName = get_option('qtl-fromEmailName');
if($fromEmailName=="")
{
	add_option('qtl-fromEmailName', "");
	$fromEmailName = "";
}
 
?>
<div class="wrap">

<h1><?php _e('Settings') ?></h1>

<table class="form-table">
	<tbody>
		<tr>
		<th scope="row"><label for="minimumEditLevel"><?php _e('Minimum level required to be able to edit quizzes', 'qtl') ?></label>
		</th>
		<td>
			<select name="minimumEditLevel">
				<?php
				$editable_roles =  get_editable_roles() ;

				foreach ( $editable_roles as $role => $details )
				{
					$roleName = translate_user_role($details['name'] );
					$roleValue = $role;
	
					if($roleValue != "subscriber")
					{
						echo '<option value="'.$roleValue.'"';
						if($roleValue == $minimumEditLevel){echo 'selected';}
						echo '>';
						echo $roleName;			
						echo '</option>';
					}
				} ?>
			</select>
		</td>
		</tr>
	</tbody>
</table>
	<h2><?php _e('Email Settings', 'qtl') ?></h2>
	<span> <?php  echo '&nbsp;'.'('. __( 'optional', 'qtl') . ')'?></span>

<table class="form-table">
	<tbody>
	</tr>
	<tr>
		<th>
			<label for="fromEmailAddress"><?php _e('Send Quiz emails from this address:', 'qtl') ?></label>
		</th>
		<td>
			<input type="text" name="fromEmailAddress" id="fromEmailAddress" value="<?php echo $fromEmailAddress; ?>" class="regular-text"/>
		</td>
	</tr>
	<tr>
		<th>
		<label for="fromEmailName"><?php _e('And from this name:', 'qtl') ?> </label>
		</th>
		<td>
			<input type="text" name="fromEmailName" id="fromEmailName" value="<?php echo $fromEmailName; ?>" class="regular-text"/>
			<p class="description"><?php _e('If left blank, emails will be sent from the default donotreply@yourdomain.com address.', 'qtl') ?></p>
		</td>
	</tr>
	</tbody>
</table>

<h2><?php _e('Feedback color settings') ?></h2>

		<h3 style="margin-bottom:10px;"><?php _e('Correct Feedback', 'qtl') ?></h3>
		<table>
			<?php display_feedback_example(get_option('correctFeedbacktextColour'), get_option('correctFeedbackBoxColour'), 'correctFeedbackDiv'); ?>
			<tr>
				<?php display_color_picker('correctFeedbackBoxColour'); ?>
			</tr>
			<tr>
				<?php display_color_picker('correctFeedbacktextColour'); ?>
			</tr>
		</table>

		<br />
		<h3 style="margin-bottom:10px;"><?php _e('Incorrect Feedback', 'qtl') ?></h3>
		<table>
			<?php display_feedback_example(get_option('incorrectFeedbacktextColour'), get_option('incorrectFeedbackBoxColour'), 'incorrectFeedbackDiv'); ?>		
			<tr>
				<?php display_color_picker('incorrectFeedbackBoxColour'); ?>
			</tr>
			<tr>
				<?php display_color_picker('incorrectFeedbacktextColour'); ?>
			</tr>
		</table>

		<br />
		<h3 style="margin-bottom:10px;"><?php _e('Reflective Feedback', 'qtl') ?></h3>
		<table>
			<?php display_feedback_example(get_option('reflectiveFeedbacktextColour'), get_option('reflectiveFeedbackBoxColour'), 'reflectionFeedbackDiv'); ?>		
			<tr>
				<?php display_color_picker('reflectiveFeedbackBoxColour'); ?>
			</tr>
			<tr>
				<?php //display_color_picker('reflectiveFeedbacktextColour'); ?>
				<input type="text" value="#bada55" class="my-color-field" data-default-color="#effeff" />
			</tr>
		</table>


	<!--
	<div style="clear:both;">
		<h4>Correct Feedback</h4>
		<div style="float:left;">
			<?php  //display_color_picker('correctFeedbackBoxColour')?>
		</div>
		<div style="float:left;">
		<?php //display_color_picker('correctFeedbacktextColour')?>
		</div>
	</div>
	

	<div style="clear:both;">
		<br/><br/>
		<h4>Incorrect Feedback</h4>
		<div style="float:left;">
			<?php  //display_color_picker('incorrectFeedbackBoxColour')?>
		</div>
		<div style="float:left;">
		<?php  //display_color_picker('incorrectFeedbacktextColour')?>
		</div>
	</div>

	<div style="clear:both;">
		<br/><br/>
		<h4>Reflective Feedback</h4>
		<div style="float:left;">
			<?php  //display_color_picker('reflectiveFeedbackBoxColour')?>
		</div>
		<div style="float:left;">
		<?php //display_color_picker('reflectiveFeedbacktextColour')?>
		</div>
	</div>
	-->

	 <div style="clear:both; padding-top:35px">	 	
	 <input type="submit" name="update_options" value="<?php _e('Update Options', 'qtl') ?>" class="button-primary"/></div>
</form>
</div>

<?php echo make_javascript(); ?>