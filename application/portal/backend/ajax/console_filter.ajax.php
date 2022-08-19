<?php

/*
	FILE DESCRIPTION:
		PATH: backend/ajax/console_filter.ajax.php;
		TYPE: ajax (AJAX responce file);
		PURPOSE: runs function setFilter in file backend/includes/console_filter.fnc.php through an AJAX call from the Parameter panel in the Console page (file frontend/js/console_filter.js) to record the current Parameters settings into table 'clients' in the database;
		REFERENCED IN: frontend/js/console_filter.js;
		FUNCTIONS DECLARED - ;
		STYLES: - ; 
*/  

session_start();

require_once ('../../root.php');
require_once (ROOT.'config/site_version.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'sync/sql_connect.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/security.fnc.php');
require_once (ROOT.'backend/includes/console_filter.fnc.php');
require_once (ROOT.'sync/functions1.php');   

//checkAccess(0);

// record client filter settings
$filter_settings = mysqli_real_escape_string($con,$_POST['filter_settings']);
$response = '';
if (isset($filter_settings)) $response = setFilter($filter_settings);
$con->close();

if ($response == 'no_filter') {
	$text = 'Произошел логаут по причине пустого фильтра юзера user_id='.$_SESSION['client_id'].', email='.getValue('client_email', 'clients', 'clients_id', $_SESSION['client_id']);
	$debug_file = ROOT.'logs/logins.txt';
	debug_log($text, $debug_file);
}

echo $response;
