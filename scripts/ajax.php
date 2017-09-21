<?php

// ESSENTIAL for making ajax work from front end pages
function add_ajaxurl_cdata_to_front(){ ?>
	<script type="text/javascript"> //<![CDATA[
		ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
	//]]> </script>
<?php }
add_action( 'wp_head', 'add_ajaxurl_cdata_to_front', 1);

function addResponseToDatabase()
{
	
	global $wpdb;

	//echo get_bloginfo( 'description', 'display' );
	$userResponse = $_POST['userResponse'];
	$questionID = $_POST['questionID']; 
	//$username = "alexfurr";
	$username = $_POST['currentUser']; 
	$date = date("Y-m-d H:i:s");
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblSubmittedAnswers";	
	
	//check if user has answered this question before
	
	$myFields="SELECT resultID FROM ".$table_name." WHERE username = '%s' AND questionID = %d";	
	
	$resultIDs = $wpdb->get_results( $wpdb->prepare($myFields, 
		$username,
		$questionID
	));

	if($resultIDs){
		foreach ($resultIDs as $row) {	
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$table_name." WHERE resultID=%d", $row->resultID ));
		}
	}
	
	//update the user response
	$myFields="INSERT into ".$table_name." (username, userResponse, dateSubmitted, questionID) ";
	$myFields.="VALUES ('%s', '%s', '%s', '%s')";	
	
	$RunQry = $wpdb->query( $wpdb->prepare($myFields,
		$username,
		$userResponse,
		$date,
		$questionID
	));		
	


	die();
}

add_action( 'wp_ajax_addResponseToDatabase', 'addResponseToDatabase' );



function responseOptionReorder()
{
	
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";		
	
	
	$myOrder = $_POST['myOrder'];	
	$questionID = $_POST['questionID'];
	$qType = $_POST['qType'];
	
	
	$currentOrder=1;
	foreach($myOrder as $myOptionID)
	{
		$myOptionID = substr($myOptionID, 9);
		$myFields ="UPDATE ".$table_name." SET optionOrder=%d WHERE optionID =%d";		
		$RunQry = $wpdb->query( $wpdb->prepare($myFields, $currentOrder, $myOptionID));	
		
		$currentOrder++;		
	}

	?>
    <script>
	jQuery('#responseOptionOrderFeedback').fadeIn(3000).delay(1000).fadeTo("slow",0);
	</script>
    
	<?php

	
	qtl_draw::drawRadioCheckOptionsEditTable($questionID, $qType, "ordered");
	echo '<div id="responseOptionOrderFeedback"><div class="updated">Options reordered</div></div>';
	
	die();
}

add_action('wp_ajax_responseOptionReorder', 'responseOptionReorder');

function responseOptionUpdateViewType()
{
	
	global $wpdb;
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";		

	
	$SQL='Select * FROM '.$table_name.' WHERE questionID='.$questionID.' ORDER by '.$orderBy;	
	
	$myOrder = $_POST['myOrder'];

	$currentOrder = 1;
	foreach ($myOrder as $optionID)
	{	
		$optionID = str_replace('thisOrder', '', $optionID);
		
		$myFields ="UPDATE ".$table_name." SET optionOrder=%d WHERE optionID =%d";		
		$RunQry = $wpdb->query( $wpdb->prepare($myFields,
			$currentOrder,
			$optionID
		));	
		
		$currentOrder++;
		
	}	
	
	$questionID = $_POST['questionID'];
	$qType = $_POST['qType'];
    $optionOrderType=""; // added for missing parameter in qtl_draw::drawRadioCheckOptionsEditTable below
	
	qtl_draw::drawRadioCheckOptionsEditTable($questionID, $qType, $optionOrderType);
	
	
	die();
}

add_action('wp_ajax_responseOptionUpdateViewType', 'responseOptionUpdateViewType');



?>