<?php

/*
	FILE DESCRIPTION:
		PATH: backend/includes/login.fnc.php;
		TYPE: fnc (function declaration);
		PURPOSE: declares functions used to generate the Login page HTML and process any requests related to client access to the site;
		REFERENCED IN: index.php, backend/ajax/login.ajax.php;
		FUNCTIONS DECLARED:
			NAME: getLogin;
				PURPOSE: returns the Login page HTML;
				EXECUTED IN: index.php;
			NAME: runLogin;
				PURPOSE: returns '1' to an AJAX request from the Login page if a client has posted correct login data to allow the client's access to the site;
				returns '0' if a client's login data has been invalid to prohibit the client's access to the site;
				EXECUTED IN: backend/ajax/login.ajax.php;
			NAME: getPassword;
				PURPOSE: returns '1' to an AJAX request from the Login/Password Restoration page (file frontend/js/login.js) if a client has provided an existing email address, generates a new password notification email HTML and inserts it into table 'emails' for its further submission to the client;
				returns '0' if a client's email does not exist to alert a client; 
				EXECUTED IN: backend/ajax/login.ajax.php;
			NAME: checkLogin;
				PURPOSE: returns '1' to an AJAX request from file frontend/js/login.js if a session exists;
				returns '' to a regular automatic AJAX request from file frontend/js/login.js if a session has expired to automatically log out a client;
				EXECUTED IN: backend/ajax/login.ajax.php;
		STYLES: frontend/css/login.css; 
*/  

function getLogin() {
	
	$html = '
	<div id="modalLogin" class="modalLogin">
		<form>
			<div class="modal-login-content">
			
				<img class="login_additional_logo" src="{{login_additional_logo}}" onerror="this.style.display = \'none\'">

				<div id="modal_login_page1">

					<p id="modal_login_title">{{login_title}}</p>
					
					<div class="login_row">
						<div class="login_column1">
							<label for="login">{{login_username}}</label>
						</div>
						<div class="login_column2">
							<input type="text" id="login" class="login_input" name="login" tabindex="1">
						</div>
					</div>
					
					<div class="login_row">
						<div class="login_column1">
							<label for="password">{{login_password}}</label>
						</div>
						<div class="login_column2">
							<input type="text" id="password" class="login_input" name="password" tabindex="2" autocomplete="on">
						</div>
					</div>

					<div class="login_row">
						<div class="login_column1">
						</div>
						<div class="login_column2">
							<div class="cookie_input_wrap">
								<input type="checkbox" id="inputCookie" class="cookie_input" name="cookie" tabindex="3">
								<label for="inputCookie">{{login_remember}}</label>
							</div>
							<br>
							<button class="modal_login_button" tabindex="4">{{login_login}} <img class="login_preloader" src="{{login_preloader}}"></button>
						</div>
					</div>
					
					<div class="login_row bottom">
						<div class="login_column1">
							<img class="login_logo" src="{{login_logo}}">
						</div>
						<div class="login_column2">
							<p id="modal_login_forgot_pw">{{login_forgot}}</p>
						</div>
					</div>

				</div>

				<div id="modal_login_page2">

					<p id="modal_restore_title">{{login_restore_title}}</p>
					
					<div class="login_row">
						<div class="login_column1">
							<label for="restore_email">{{login_email}}</label>
						</div>
						<div class="login_column2">
							<input type="text" id="restore_email" class="login_input" name="restore_email" tabindex="1">
							<br><br>
							<button class="modal_restore_button" tabindex="2">{{login_get_pw}}</button>
						</div>
					</div>
					
					<br>
					
					<div class="modal_login_page2_wrap">
						{{user_profile_footer}} <p id="modal_login_back">{{login_back}}<p>  
					</div> 

				</div>

			</div>
		</form>
	</div>
	';
	
	return $html;
	
}

function runLogin($login,$password) {

	global $con;

	$sql = "SELECT `clients_id`, `client_name`, `client_role`, `client_md5` ".
				"FROM `clients` ".
			"WHERE ".
				"(LOWER(`client_name`) = '".mb_strtolower($login)."' OR LOWER(`client_email`)='".mb_strtolower($login)."')".
				" AND `client_disabled` <> 1 ".
			"LIMIT 1"
	;
	$result = $con->query($sql);
	if ($result->num_rows > 0) {
		while( $row = $result->fetch_assoc() ) {
			$client_id = $row['clients_id'];
			$client_role = $row['client_role'];
			$client_md5 = $row['client_md5'];
			$client_name = $row['client_name'];
		}
	} else {
		$success = 0;
	}

	if ( $client_md5 == md5($client_id.'.'.$client_name.'.'.$password) ) {
/*- POR-741_20.4 setClientSession() replacement
		$_SESSION['client_id'] = $client_id;
		$_SESSION['client_role'] = $client_role;
		setValue('client_active', 1, 'clients', 'clients_id', $_SESSION['client_id']);
		$user_id = check_user(getValue('users_id', 'users', 'user_client', $client_id, 1));
		$_SESSION['user']['id'] = $user_id;
		$_SESSION['user']['email'] = getValue('email', 'users', 'users_id', $user_id, 1);
*/		
		$success = setClientSession($client_id)>0 ? 1 : 0;

		if ($client_role == 1) {
			$cookie = setcookie('EXPORTSAUTH', 'aA3e2rGPZuVmgWbn', 0, '/exports');
		}

	} else {
		$success = 0;
	}

	return $success;

}

function setUserSession($user_id=0, $set_active=true)
{
	$client_id = getValue('user_client', 'users', 'users_id', $user_id, 1);
	if ($client_id>0)
	{
		$_SESSION['client_id'] = $client_id;
		$_SESSION['user']['id'] = $user_id;
		$_SESSION['user']['lang'] = getValue('language', 'users', 'users_id', $user_id);
		$_SESSION['client_name'] = getValue('client_name', 'clients', 'clients_id', $client_id);
		$_SESSION['user']['name'] = getValue('name', 'users', 'users_id', $user_id);
		$_SESSION['user']['email'] = getValue('email', 'users', 'users_id', $user_id);
		$_SESSION['client_role'] = getValue('client_role', 'clients', 'clients_id', $client_id);
		$_SESSION['client_show_all'] = getValue('client_show_all', 'clients', 'clients_id', $client_id);
		if ($set_active) { setValue('client_active', 1, 'clients', 'clients_id', $client_id); }
	} else $client_id = false;
	return $client_id;
}


function setClientSession($client_id=0, $set_active=true)
{
	$user_id = check_user(getValue('users_id', 'users', 'user_client', $client_id));
	if ($client_id>0 && $user_id>0)
	{
		$_SESSION['client_id'] = $client_id;
		$_SESSION['user']['id'] = $user_id;
		$_SESSION['user']['lang'] = getValue('language', 'users', 'users_id', $user_id);
		$_SESSION['client_name'] = getValue('client_name', 'clients', 'clients_id', $client_id);
		$_SESSION['user']['name'] = getValue('name', 'users', 'users_id', $user_id);
		$_SESSION['user']['email'] = getValue('email', 'users', 'users_id', $user_id);
		$_SESSION['client_role'] = getValue('client_role', 'clients', 'clients_id', $client_id);
		$_SESSION['client_show_all'] = getValue('client_show_all', 'clients', 'clients_id', $client_id);
		if ($set_active) { setValue('client_active', 1, 'clients', 'clients_id', $client_id); }
	} else $user_id = false;
	return $user_id;
}


function getPassword ($email) 
{
	// get user data
    $client_id = getValue('clients_id', 'clients', 'client_email', $email);
    if (!empty($client_id))
    {
		// generate new password and record new hash
		$new_password = rand(1000000000, 9999999999);
		$client_name = getValue('client_name', 'clients', 'clients_id', $client_id);    // POR-1909 fix
		$new_client_md5 = md5($client_id.".".$client_name.".".$new_password);
        setValue('client_md5', $new_client_md5, 'clients', 'clients_id', $client_id);
        
		$user_name = getValue('name', 'users', 'user_client', $client_id, 1);
		emailPW($email, $user_name, $new_password, 'automatic');
        
		$return = 1;
	} else {
		$return = 0;
	}

	return $return;
}

function checkLogin($session_client_id) {
	return $session_client_id ? 1 : '';
}
