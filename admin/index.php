<?php

function drawAIquiz_home()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/home.php'; # Load admin pages
	echo '</div>';
}

function drawAIquiz_results()
{
	echo '<div id="qtl_content">';	
	require_once AIQUIZ_PATH.'admin/results.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_questionList()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/questions.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_questionEdit()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/question_edit.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_quizList()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/quiz_list.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_quizEdit()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/quiz_edit.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_questionType()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/question_type.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_settings()
{
	echo '<div id="qtl_content">';	
	require_once AIQUIZ_PATH.'admin/quiz_settings.php'; # Load admin pages
	echo '</div>';	
}

function drawAIquiz_export()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/export.php'; # Load admin pages
	echo '</div>';
}

function drawAIquiz_help()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/help.php'; # Load admin pages
	echo '</div>';
}

function drawAIquiz_user_results()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/user_results.php'; # Load admin pages
	echo '</div>';
}

function drawAIquiz_user_breakdown()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/quiz_breakdown.php'; # Load admin pages
	echo '</div>';
}

function drawAIquiz_gradeBoundaries()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/grade_boundaries.php'; # Load admin pages
	echo '</div>';
}

function drawAIquiz_boundaryEdit()
{
	echo '<div id="qtl_content">';
	require_once AIQUIZ_PATH.'admin/boundary_edit.php'; # Load admin pages
	echo '</div>';
}

?>