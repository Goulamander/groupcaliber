<?php

/*
	FILE DESCRIPTION:
		PATH: backend/ajax/admin_client.ajax.php;
		TYPE: ajax (AJAX responce file);
		PURPOSE: returns and receives data through AJAX calls from the jTable-based Client List table in the Admin page (file frontend/js/admin_client.js);
		REFERENCED IN: frontend/js/admin_client.js;
		FUNCTIONS DECLARED - ;
		STYLES: - ; 
*/  

session_start();
require_once ('../../root.php');
require_once (ROOT.'config/config.php');
require_once (ROOT.'config/site_version.php');
require_once (ROOT.'config/tables.php');
require_once (ROOT.'backend/includes/connections.php');
require_once (ROOT.'backend/includes/service.fnc.php');
require_once (ROOT.'backend/includes/security.fnc.php');
require_once (ROOT.'backend/includes/profile.fnc.php');
require_once (ROOT.'sync/functions1.php');
require_once (ROOT.'sync/functions2.php');
require_once (ROOT.'sync/sql_connect.php');

checkAccess(1);

try
{
	//Escape variables for security
	$clients_id = mysqli_real_escape_string($con, isset($_POST['clients_id']) ? $_POST['clients_id'] : 0);
	$client_name = mysqli_real_escape_string($con, isset($_POST['client_name']) ? $_POST['client_name'] : 0 );
	$client_email = mysqli_real_escape_string($con, isset($_POST['client_email']) ? $_POST['client_email'] : 0 );
	$client_rating = mysqli_real_escape_string($con, isset($_POST['client_rating']) ? $_POST['client_rating'] : 0);
	$client_rand = mysqli_real_escape_string($con, isset($_POST['client_rand']) ? $_POST['client_rand'] : 0);
	$client_role = mysqli_real_escape_string($con, isset($_POST['client_role']) ? $_POST['client_role'] : 0);
	$client_default_rating = mysqli_real_escape_string($con, isset($_POST['client_default_rating']) ? $_POST['client_default_rating'] : 0);
	$client_show_all = mysqli_real_escape_string($con, isset($_POST['client_show_all']) ? $_POST['client_show_all'] : 0);
	$client_disabled = mysqli_real_escape_string($con, isset($_POST['client_disabled']) ? $_POST['client_disabled'] : 0);
	$client_subscription = mysqli_real_escape_string($con, isset($_POST['client_subscription']) ? $_POST['client_subscription'] : 0);
	$client_subscription_type = mysqli_real_escape_string($con, isset($_POST['client_subscription_type']) ? $_POST['client_subscription_type'] : 0);
	$client_country = mysqli_real_escape_string($con, isset($_POST['client_country']) ? $_POST['client_country'] : 0);
	$client_dashboard_model = mysqli_real_escape_string($con, isset($_POST['client_dashboard_model']) ? $_POST['client_dashboard_model'] : 0);
    $client_model = mysqli_real_escape_string($con, isset($_POST['client_model']) ? $_POST['client_model'] : 0);
	$client_map_dashboard_disabled = mysqli_real_escape_string($con, isset($_POST['client_map_dashboard_disabled']) ? $_POST['client_map_dashboard_disabled'] : 0);
	$client_stakeholder = mysqli_real_escape_string($con, isset($_POST['client_stakeholder']) ? $_POST['client_stakeholder'] : 0);
	//$client_sample_boost = mysqli_real_escape_string($con, isset($_POST['client_sample_boost']) ? $_POST['client_sample_boost'] : 0);
	$client_custom_questions = mysqli_real_escape_string($con, isset($_POST['client_custom_questions']) ? $_POST['client_custom_questions'] : 0);
	$client_map_center = mysqli_real_escape_string($con, isset($_POST['client_map_center']) ? $_POST['client_map_center'] : 0);
	$client_uuid = mysqli_real_escape_string($con, isset($_POST['client_uuid']) ? $_POST['client_uuid'] : 0);
	$client_auth = mysqli_real_escape_string($con, isset($_POST['client_auth']) ? $_POST['client_auth'] : 0);
	$client_ip = mysqli_real_escape_string($con, isset($_POST['client_ip']) ? $_POST['client_ip'] : 0);
	$client_filter_sync = mysqli_real_escape_string($con, isset($_POST['client_filter_sync']) ? $_POST['client_filter_sync'] : 0);
	$client_customer = mysqli_real_escape_string($con, isset($_POST['client_customer']) ? $_POST['client_customer'] : 0);
	
	$sql_filter = " WHERE 1=1 ";
	
	$client_filter = mysqli_real_escape_string($con, isset($_POST['client_filter']) ? $_POST['client_filter'] : 0 );
	$client_filter = $client_filter ? " AND (`client_name` LIKE '%$client_filter%' OR `client_email` LIKE '%$client_filter%' OR `clients_id` LIKE '$client_filter') " : " ";
	
	$sql_filter .= $client_filter;
	
	//Getting records (listAction)
	if($_GET['action'] == 'list')
	{
		//Get records from database
		$result = $con->query("SELECT COUNT(*) AS `RecordCount` FROM `clients` $sql_filter");
		$row = mysqli_fetch_array($result);
		$recordCount = $row['RecordCount'];
		$result = $con->query("SELECT * FROM `clients` $sql_filter ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"] . ";");

		//Add all records to an array
		$rows = array();
		while($row = mysqli_fetch_array($result))
		{
		    $rows[] = $row;
		}

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
		print json_encode($jTableResult);
	}
	//Creating a new record (createAction)
	else if($_GET['action'] == 'create')
	{			
		
		$result = $con->query("SELECT * FROM `clients` WHERE client_email = '$client_email'");
		$row = mysqli_fetch_array($result);

		if(count($row) > 0) {
			// If it alreadt have record then shows the error message
			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "ERROR"; 
			$jTableResult['Message'] = "The Email you entered is already exist. Please check you email and try again";
			print json_encode($jTableResult);
		}
		else {
			//Insert record into database	
			$result = $con->query("INSERT INTO `clients` (`client_name`, `client_email`, `client_rating`, `client_role`, `client_default_rating`, `client_show_all`, `client_disabled`, `client_subscription`, `client_subscription_type`, `client_country`, `client_dashboard_model`, `client_model`, `client_map_dashboard_disabled`, `client_stakeholder`, `client_custom_questions`,  `client_map_center`, `client_uuid`,`client_auth`,`client_ip`, `client_filter_sync`, `client_customer`) VALUES ('$client_name', '$client_email', '$client_rating', '$client_role', '$client_default_rating', '$client_show_all', '$client_disabled', '$client_subscription', '$client_subscription_type', '$client_country', '$client_dashboard_model', '$client_model', '$client_map_dashboard_disabled', '$client_stakeholder', '$client_custom_questions', '$client_map_center', '$client_uuid', '$client_auth', '$client_ip', '$client_filter_sync', '$client_customer');");
					
			//Get last inserted record (to return to jTable)
			$result = $con->query("SELECT * FROM `clients` WHERE `clients_id` = LAST_INSERT_ID();");
			$row = mysqli_fetch_array($result);

			//Update login data for new record in database
			$last_client_id = $row['clients_id'];
			//$user_name = getValue('name', 'users', 'user_client', $last_client_id, 1);
			$client_md5 = md5($last_client_id.".".$client_name.".".$client_rand);		
			setValue('client_md5', $client_md5, 'clients', 'clients_id', $last_client_id);
			//emailPW($client_email,$client_name,$client_rand);

			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			$jTableResult['Record'] = $row;
			print json_encode($jTableResult);
		}
		
	}
	//Updating a record (updateAction)
	else if($_GET['action'] == 'update')
	{		
	
		// if client_model has changed - clean filters
		$cur_client_model = getValue('client_model', 'clients', 'clients_id', $clients_id);	
		$client_active = getValue('client_active', 'clients', 'clients_id', $clients_id);	
		if ($client_model!=$cur_client_model) {
			$filter_value = $client_active ? '' : 'dummy';
			setValue('client_filter_'.$site_version, $filter_value, 'clients', 'clients_id', $clients_id);
		}
		
		//Update record in database	
		$cur_client_md5 = getValue('client_md5', 'clients', 'clients_id', $clients_id);

        setValue('client_dashboard_model', $client_dashboard_model, 'clients', 'clients_id', $clients_id);    // POR-722
        setValue('client_model', $client_model, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_rating', $client_rating, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_default_rating', $client_default_rating, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_show_all', $client_show_all, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_country', $client_country, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_map_dashboard_disabled', $client_map_dashboard_disabled, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_role', $client_role, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_name', $client_name, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_email', $client_email, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_disabled', $client_disabled, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_subscription', $client_subscription, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_subscription_type', $client_subscription_type, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_stakeholder', $client_stakeholder, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_custom_questions', $client_custom_questions, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_ip', $client_ip, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_map_center', $client_map_center, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_uuid', $client_uuid, 'clients', 'clients_id', $clients_id);      // POR-722
        setValue('client_auth', $client_auth, 'clients', 'clients_id', $clients_id);      // POR-722
		setValue('client_filter_sync', $client_filter_sync, 'clients', 'clients_id', $clients_id);
		setValue('client_customer', $client_customer, 'clients', 'clients_id', $clients_id);
        if ($client_rand) { 
            $client_md5 = md5($clients_id.".".$client_name.".".$client_rand);
            setValue('client_md5', $client_md5, 'clients', 'clients_id', $clients_id); 
            if ($cur_client_md5!=$client_md5) {
                 $user_name = getValue('name', 'users', 'user_client', $clients_id, 1);
			     $user_name = $user_name ? $user_name : $client_name;
			     emailPW($client_email, $user_name, $client_rand, 'automatic');                
            }
        }
        
/* -POR-722        
        $query = "UPDATE `clients` SET ".
            " `client_name` = '$client_name', ".
            " `client_email` = '$client_email', ".
            " `client_rating` = '$client_rating', ".
            " `client_role` = '$client_role', ".
            " `client_default_rating` = '$client_default_rating', ".
            " `client_show_all` = '$client_show_all', ".
            " `client_disabled` = '$client_disabled', ".
            " `client_subscription` = '$client_subscription', ".
            " `client_subscription_type` = '$client_subscription_type', ".
            " `client_country` = '$client_country', ".
            " `client_dashboard_type` = '$client_dashboard_type', ".
            " `client_model` = '$client_model', ".
            " `client_map_dashboard_disabled` = '$client_map_dashboard_disabled', ".
            " `client_stakeholder` = '$client_stakeholder', ".
            " `client_custom_questions` = '$client_custom_questions', ".
            " `client_yearly_workshop` = '$client_yearly_workshop', ".
            " `client_map_center` = '$client_map_center', ".
            " `client_uuid` = '$client_uuid', ".
            " `client_auth` = '$client_auth', ".
            " `client_ip` = '$client_ip'".
            ($client_rand ? ", `client_md5` = '".md5($clients_id.".".$client_name.".".$client_rand)."'" : "").
            " WHERE `clients_id` = '$clients_id';"
        ;
        
        $result = $con->query($query);

		if ($client_rand && $cur_client_md5 != md5($clients_id.".".$client_name.".".$client_rand)) {
			$user_name = getValue('name', 'users', 'user_client', $clients_id, 1);
			$user_name = $user_name ? $user_name : $client_name;
			emailPW($client_email, $user_name, $client_rand, 'automatic');
		}
*/			
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	//Deleting a record (deleteAction)
	else if($_GET['action'] == 'delete')
	{
		//Delete from database
		$result = $con->query("DELETE FROM `clients` WHERE `clients_id` = '$clients_id';");
		
		$delete_child_records_result = $con->query("DELETE FROM `bindings` WHERE `binding_client` = '$clients_id';");

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}

	//Close database connection
	$con->close();

}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = 'ERROR';
	$jTableResult['Message'] = $ex->getMessage();
	print json_encode($jTableResult);
}
