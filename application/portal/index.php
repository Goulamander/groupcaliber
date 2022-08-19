<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/*
	FILE DESCRIPTION:
		PATH: index.php;
		TYPE: php (index file);
		PURPOSE: servers as an index file for the Caliber site integrating all of its components together by running various page generating functions and including the required HTML header links in accordance with a client's login and role status;
		REFERENCED IN: browser URL;
		FUNCTIONS DECLARED - :
		STYLES: frontend/css/index.css; 
*/ 

session_start();

require_once ('root.php');
require_once (ROOT.'config/site_version.php');
require_once (ROOT.'sync/constants.php');
require_once (ROOT.'config/config.php');
if (!isset($file_version)) { $file_version = '?v='.rand(100000, 999999); }
require_once (ROOT.'config/tables.php');
require_once (ROOT.'sync/sql_connect.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/security.fnc.php');
require_once (ROOT.'backend/includes/index.lnk.php');
require_once (ROOT.'backend/includes/login.fnc.php'); // for setUserSession()
shutDown('portal');

$is_admin = isset($_SESSION['client_role']) && $_SESSION['client_role']>0;
$redirect = !$is_admin && !isset($_GET['noredirect']) && get_label('site_redirect')=='1';
$client_id = get_client_id(); // get client_id or false
$user_id = empty($_SESSION['user']['id']) ? 0 : check_user($_SESSION['user']['id']);
$site = get_site();

if (isset($_GET['refresh'])) {
	unset($_SESSION['trans']);
	unset($_SESSION['label']);
	if(isset($redis)) $redis->flushAll();
	go_to(deleteGET($_SERVER['REQUEST_URI'], "refresh", false));
}

if (isset($_GET['logout'])) // added /?logout processing for fixing site redirection POR-1833, POR-1750 
{
    log_out();
    sql_close();
    go_to(deleteGET($_SERVER['REQUEST_URI'], "logout", false));
}

// *** POR-912 - login_as functionality
if (!empty($_GET['uid']) && !empty($_GET['token'])) 
{
    $uid = check_user(check_in($_GET['uid'], 3));   // check if user_id exists
    $token = check_in($_GET['token']);
    if ($uid>0)
    {
        $client_id = getValue('user_client', 'users', 'users_id', $uid, 1);
        $client_disabled = getValue('client_disabled', 'clients', 'clients_id', $client_id, 1);
        $client_role = getValue('client_role', 'clients', 'clients_id', $client_id, 1);
        if ($client_id>0                 // if client of user exists
            && $client_disabled=='0'     // and client not disabled
            && $client_role==0           // and not admin's only
           )
        {
            $client_pwd = getValue('client_md5', 'clients', 'clients_id', $client_id, 1);
            $client_hash = md5($client_pwd.date('Y-m-d'));  // chech hash with current date added to expire link within one day
            if ($client_hash==$token)
            {
                $redirect = false;      // to force no-redirect
                setUserSession($uid);   // autologin by user_id
                $clearedURL = deleteGET(deleteGET($_SERVER['REQUEST_URI'], 'token', false), 'uid', false);
                go_to(addGET($clearedURL, 'noredirect')); // remove parameters from adress line and redirect
            } else {
                die('$client_pwd==$token = '."$client_hash==$token");
            }
        }
    }
}
// *** POR-912 end

$new_lang = lang_GET();
if (!empty($new_lang)) 
{ 
	$_SESSION['user']['lang'] = $new_lang;
    if ($user_id>0) 
    {		
        setValue('language', get_lang_id($new_lang), 'users', 'users_id', $user_id);
    }
} elseif ($user_id>0) {
	$lang_id = getValue('language', 'users', 'users_id', $user_id);
    $_SESSION['user']['lang'] = get_lang_name($lang_id);
}

// load plugin & style always
$links = $links_global;
$content = '';

// determine links/includes based on user login status and role
if ($client_id===false)  
{
	// load links before login
	$links .= $links_before_login;
	$links .= get_label('css_before_login');
	
	// load pades before login
	require_once(ROOT.'backend/includes/login.fnc.php');
	$content .= getLogin();
	
} else {

    if ($redirect)
    {
        $customer_id = getValue('client_customer', 'clients', 'clients_id', $client_id);
            $customer_site = getValue('customer_site', 'customers', 'customers_id', $customer_id);
        $client_rating = getValue('client_default_rating', 'clients', 'clients_id', $client_id);
        $client_project = getValue('rating_project', 'ratings', 'ratings_id', $client_rating);
            $client_site = getValue('project_site', 'projects', 'projects_id', $client_project);
        $https = 'https://';
        if ($customer_site>0 && $customer_site<>$site) 
        { 
            log_out();
            go_to($https.getValue('site_url', 'sites', 'sites_id', $customer_site)."/?logout"); 
                // added /?logout to fix POR-1855 site redirection 
            exit; // exit prevents from staring next redirect - second redirect overrides first 
        } 
        if ($client_site>0 && $client_site<>$site) 
        { 
            log_out();
            go_to($https.getValue('site_url', 'sites', 'sites_id', $client_site)."/?logout"); 
            exit; 
        }
    }
    
	// determine some client admin variables etc
	$_SESSION['client_show_all'] = getValue('client_show_all', TABLE_CLIENTS, 'clients_id', $_SESSION['client_id']);
	$_SESSION['client_subscription_type'] = getValue('client_subscription_type', TABLE_CLIENTS, 'clients_id', $_SESSION['client_id']);
	
	// load links after login
	$links .= $links_after_login_all;
	$links .= get_label('css_after_login');

	// load links if user admin
	if ($_SESSION['client_role'] == 1) $links .= $links_after_login_admin;
	if ($_SESSION['client_role'] == 1 || ($_SESSION['client_subscription_type'] && $_SESSION['client_subscription_type'] != 'Free')) $links .= $links_export_rights;

	// load pages after login
	require_once(ROOT.'backend/includes/profile.fnc.php');
	require_once(ROOT.'backend/includes/menu.fnc.php');
	require_once(ROOT.'backend/includes/console.fnc.php');
	require_once(ROOT.'backend/includes/help.fnc.php');
	getClientCompanies();
	
	$content .= getMenu().getConsole().getProfile().getHelp();

	require_once(ROOT.'backend/includes/admin.fnc.php');
	$content .= getAdmin();
}

$ticketing = '{{index_ticketing}}';
    
    // {{index_ticketing}} = '<script type="text/javascript" src="https://groupcaliber.atlassian.net/s/d41d8cd98f00b204e9800998ecf8427e-T/sb53l8/b/24/a44af77267a987a660377e5c46e0fb64/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=ru-RU&collectorId=12d7ff4f"></script>';

// HTML //

$html = '
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<meta name="mobile-web-app-capable" content="yes">		
		<title>Caliber Dashboard</title>
		<script>var groupCaliberEnvironment="'.ENVIRONMENT.'"; var groupCaliberServerId="'.SERVER_ID.'"</script>
	'.$links.'
	</head>
	<style>
	</style>
	<body class="body_hidden">
		<div class="row bootstrap_fix_row" onclick="void(0);">
			<div class="col bootstrap_fix_column">
	';

	$html .= $content;

	$html .= '
			<!--bootstrap wrap column end-->
			</div>
		<!--bootstrap wrap row end-->
		</div>
        '.$ticketing.'
	</body>
</html>
';

if (isset($_GET['showlabels']))
{
    $_SESSION['showlabels'] = check_in($_GET['showlabels'], 3);
}

echo print_trans($html);

sql_close();
