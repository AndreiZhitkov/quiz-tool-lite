<?php
/**
 * Description: Creates database tables used by the quiz
 */
 
//Database table versions
global $this_qtl_db_version;
$this_qtl_db_version = "2.3"; // INcrease this each time a DB change is made

// CHeck the DB version of the plugin
$current_qtl_db_version = get_option( 'qtl-db-version' );


if($current_qtl_db_version==false) // add the option and create DB
{
	AI_Quiz_db_create();
	add_option( 'qtl-db-version', $this_qtl_db_version);
	
}
elseif($current_qtl_db_version<$this_qtl_db_version) // update the option and update DB
{
	AI_Quiz_db_create();
	update_option( 'qtl-db-version', $this_qtl_db_version );
}


//Create / update tables if the version is old
function AI_Quiz_db_create ()
{
    AI_Quiz_create_tables();
}

//Create tables - uses the dbDelta stuff that looks to see if the tables already exist and udpates if not
function AI_Quiz_create_tables()
{
	global $wpdb;
	global $AI_Quiz_db_table_version;
	
	$charSet = $wpdb->get_charset_collate();
	
//	echo 'charset = '.$charSet;
//	die();
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table_name = $wpdb->prefix . "AI_Quiz_tblSettings";		
	$sql = "CREATE TABLE ".$table_name." (
	correctFeedbackBoxColour varchar(255),
	incorrectFeedbackBoxColour varchar(255),
	reflectiveFeedbackBoxColour varchar(255)
	) ".$charSet;
	
	//echo $sql;
	//die();
	dbDelta($sql);
	
	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
//	$RunQry = $wpdb->query($sql);		
	
		
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizzes";		
	$sql = "CREATE TABLE ".$table_name." (
	quizID int NOT NULL AUTO_INCREMENT,
	quizName longtext,
	questionArray longtext,
	lastEditedBy varchar(255),
	lastEditedDate datetime,
	quizOptions longtext,	
	PRIMARY KEY  (quizID)
	) ".$charSet;
	dbDelta($sql);
	
	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);		
	
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblResponseOptions";		
	$sql = "CREATE TABLE ".$table_name." (
	optionID int NOT NULL AUTO_INCREMENT,
	optionValue longtext,
	questionID int,
	isCorrect tinyint,
	responseCorrectFeedback longtext,
	responseIncorrectFeedback longtext,	
	optionOrder int,
	PRIMARY KEY  (optionID)
	) ".$charSet;
	dbDelta($sql);
	
	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);	
	
	
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestions";
	$sql = "CREATE TABLE ".$table_name." (
	questionID int NOT NULL AUTO_INCREMENT,
	question longtext,
	qType varchar(255),
	potID int,
	incorrectFeedback longtext,
	correctFeedback longtext,
	creator varchar(255),
	createDate datetime,
	optionOrderType varchar (50),
	optionOrderType2 varchar (50),
	PRIMARY KEY  (questionID)
	)".$charSet;	
	dbDelta($sql);
	
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;	
	//$RunQry = $wpdb->query($sql);
//	dbDelta($sql);	
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuestionPots";
	$sql = "CREATE TABLE ".$table_name." (
	potID int NOT NULL AUTO_INCREMENT,
	potName varchar(255),
	creator varchar(255),
	createDate datetime,
	lastEditedBy varchar(255),
	lastEditedDate datetime,
	PRIMARY KEY  (potID)
	)".$charSet;
	dbDelta($sql);
	
	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);		
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblQuizAttempts";
	$sql = "CREATE TABLE ".$table_name." (
	attemptID int NOT NULL AUTO_INCREMENT,
	quizID int,
	username varchar(255),
	attemptCount int,
	lastDateStarted datetime,
	questionArray longtext,
	highestScore int,
	highestScoreDate datetime,
	lastAttemptMarked int,
	PRIMARY KEY  (attemptID)
	)".$charSet;
	dbDelta($sql);	

	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);
	
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblSubmittedAnswers";		
	$sql = "CREATE TABLE ".$table_name." (
	resultID int NOT NULL AUTO_INCREMENT,	
	username varchar(255),
	userResponse longtext,
	dateSubmitted datetime,
	questionID int,
	PRIMARY KEY  (resultID)
	)".$charSet;
	dbDelta($sql);
	
	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);		
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblUserQuizResponses";
	$sql = "CREATE TABLE ".$table_name." (
	userAttemptID int NOT NULL AUTO_INCREMENT,
	quizID int,
	username varchar(255),
	dateStarted datetime,
	dateFinished datetime,
	questionArray longtext,
	responseArray longtext,
	score int,
	PRIMARY KEY  (userAttemptID)
	)".$charSet;	
	dbDelta($sql);	
	
	// Force it to be utf8
	//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);		
	
	$table_name = $wpdb->prefix . "AI_Quiz_tblGradeBoundaries";		
	$sql = "CREATE TABLE ".$table_name." (
	boundaryID int NOT NULL AUTO_INCREMENT,	
	quizID int NOT NULL,
	minGrade int NOT NULL,
	maxGrade int NOT NULL,
	feedback longtext,
	PRIMARY KEY  (boundaryID)
	)".$charSet;
	dbDelta($sql);
	
	// Force it to be utf8
//$sql = "ALTER TABLE ".$table_name." CONVERT TO ".$charSet;
	//$RunQry = $wpdb->query($sql);		

	//Add database table versions to options
	//add_option("AI_Quiz_db_table_version", $AI_Quiz_db_table_version);

}



?>