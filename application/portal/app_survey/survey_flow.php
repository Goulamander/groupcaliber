<?php

/*
	FILE DESCRIPTION:
		PATH: app_survey/survey_flow.php;
		TYPE: php (URL file);
		PURPOSE: runs function getSurveyFlow in file app_survey/survey.fnk.php to get an HTML table of simultaneous interviews by minutes;
		REFERENCED IN: browser URL;
		FUNCTIONS DECLARED - :
		STYLES: - ; 
*/   

session_start();
require_once ('../root.php');
require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');
require_once (ROOT.'app_survey/survey.fnc.php');

if (isset($_GET['refresh'])) 
{
	unset($_SESSION['trans']);
	unset($_SESSION['label']);
	if(isset($redis)) $redis->flushAll();
	go_to(deleteGET($_SERVER['REQUEST_URI'], "refresh", false));
}

$survey = isset($_GET['survey']) ? mysqli_real_escape_string($con,$_GET['survey']) : '';
if (empty($survey)) 
{
	echo 'Missing "survey" parameter! ';
} else {
	echo getSurveyFlow($survey);
}

