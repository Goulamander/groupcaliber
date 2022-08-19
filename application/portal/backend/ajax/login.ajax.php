<?php  

/*
	FILE DESCRIPTION:
		PATH: backend/ajax/login.ajax.php;
		TYPE: ajax (AJAX responce file);
		PURPOSE: runs function runLogin and getPassword in file backend/includes/login.fnc.php through AJAXcalls from the Login page when loggin in or restoring a password, logs out a client (desroys the session) when button Log Out in the Main Menu is clicked, runs function checkLogin to check if a client is logged in and automatically log him/her out otherwise (file frontend/js/login.js);
		REFERENCED IN: frontend/js/login.js;
		FUNCTIONS DECLARED -;
		STYLES: - ; 
*/  

session_start();
require_once ('../../root.php');
require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/site_version.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/login.fnc.php');
require_once (ROOT.'backend/includes/profile.fnc.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');

$table_clients = TABLE_CLIENTS;

// login procedure
if (isset($_POST['param1']) && isset($_POST['param2'])) {
	
	$login = mysqli_real_escape_string($con, $_POST['param1']);
	$password = mysqli_real_escape_string($con, $_POST['param2']);
	
	//echo runLogin($login,$password);
	$run_login_result = runLogin($login,$password);
	
	if ($run_login_result == 1) {
		$text = 'Произошел логин юзера user_id='.$_SESSION['client_id'].', email='.getValue('client_email', 'clients', 'clients_id', $_SESSION['client_id']);
		$debug_file = ROOT.'logs/logins.txt';
		debug_log($text, $debug_file);
	}
	
	echo $run_login_result;

}

// password restore procedure
if (isset($_POST['param3'])) 
{
	$email = check_in($_POST['param3'], CHECKIN_EMAIL);
	
	echo getPassword($email);
	
}

// log out procedure
if (isset($_POST['logout'])) 
{
	if (isset($_SESSION['client_id'])) 
    {
        $client_id = check_in($_SESSION['client_id'], 3);
		setValue('client_active', 0, 'clients', 'clients_id', $client_id);

		$filter_data_obj = getValue('client_filter_'.$site_version, 'clients', 'clients_id', $client_id);	

		if (!$filter_data_obj) setValue('client_filter_'.$site_version, 'dummy', 'clients', 'clients_id', $client_id);

		$text = "User has logged out client_id=$client_id, email=".getValue('client_email', 'clients', 'clients_id', $client_id);
		$debug_file = ROOT.'logs/logins.txt';
		debug_log($text, $debug_file);
	}
	session_unset();
	session_destroy();
}

// check login status
if (isset($_POST['login_status'])) 
{
	$check_login_result = isset($_SESSION['client_id']) && checkLogin($_SESSION['client_id']);
	
	if (!$check_login_result) 
    {
		$text = 'Autologout of user client_id='.$_SESSION['client_id'].' email='.getValue('client_email', 'clients', 'clients_id', $_SESSION['client_id']);
		$debug_file = ROOT.'logs/logins.txt';
		debug_log($text, $debug_file);
	}
	
	echo $check_login_result;
}

sql_close();

