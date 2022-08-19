<?php 

/*  
	FILE DESCRIPTION:
		PATH: backend/ajax/console_dashboard.ajax.php;
		TYPE: ajax (AJAX responce file);
		PURPOSE: runs function getDashboard in file backend/includes/console_dashboard.fnc.php through an AJAX call from the Dashboard or the Map View selection or parameter changing (files frontend/js/console_dashboard.js and frontend/js/console_map.js) to return the Dashboard or the Map View HTMLs to be inserted into the tool placeholder in the Console page;
		REFERENCED IN: frontend/js/console_dashboard.js, frontend/js/console_map.js;
		FUNCTIONS DECLARED - ;
		STYLES: frontend/css/console_dashboard.css, frontend/css/console_map.css; 
*/   

require_once ('../../root.php');

	$start_time = microtime(true);
	$timing = true;
	$timing_log = ROOT.'/logs/debug_log.txt';
	
	
require_once (ROOT.'backend/includes/security.fnc.php');

session_start();

//sleep(105); 

$param = file_get_contents('php://input');
$param = json_decode($param);

/*if (!empty($_POST['param']) && is_array($_POST['param'])) {
	$param = $_POST['param'];
} else {
//	return 'ajax_empty';	// TODO - make handler in front 
	return '';
}*/

list(
	$company_id,
	$company_compare_id,
	$benchmarking
) = $param;

require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/dashboard_html.fnc.php'); 
require_once (ROOT.'sync/functions.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');

checkAccess(0);

// get parameters

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('console_dashboard.ajax.php time 01: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//? $threads = get_sql_threads(sql_stat()); $max_sql_threads = get_max_sql_threads();
//? if ($threads<$max_sql_threads) 
	session_write_close();
//? else debug_log('console_dashboard.ajax.php: session not closed - $threads='.$threads.', $max_sql_threads='.$max_sql_threads, ROOT.'/logs/debug_log.txt');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('console_dashboard.ajax.php time 02: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

echo '<pre>';
print_r ($company_id);

print_r ($company_compare_id);

print_r ($benchmarking);
// $return = getDashboardHTML(
// 	$company_id,
// 	$company_compare_id,
// 	$benchmarking
// );

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		if ($timing) { debug_log('console_dashboard.ajax.php time 99: '.((microtime(true)-$start_time)*1000).'ms', $timing_log); $start_time = microtime(true); }
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if (empty($return))
{
	debug_log('console_dashboard.ajax.php: empty $return from getDashboard()...');
}

echo $return;

sql_close();
